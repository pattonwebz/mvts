<?php
/*
Plugin Name: CTA Plugin
Plugin URI: http://www.pattonwebz.com/resources/
Description: Beginning of the cohorts MVT testing plugin.
Version: 0.1
Author: William Patton
Author URI: http://www.pattonwebz.com/
License: GPL2
*/

/* =============================================================
 * Enqueue Javascript
 * ============================================================= */

// register and enqueue the dependancy scripts - cohorts.js, jquery library

add_action( 'wp_enqueue_scripts', 'load_mvts_scripts' );

if ( !function_exists( 'load_mvts_scripts' ) ) {
    function load_mvts_scripts() {
    	$options = get_option('mvtsBasic');
        // not an admin page and plugin is turned on
        if ( !is_admin() && $options['mvtsOnOff'] == '1' ) { 
			wp_register_script( 'cohorts', get_bloginfo('wpurl') . '/wp-content/plugins/mvts/js/cohorts.js', array(), 1.0, true );
            wp_enqueue_script( 'cohorts' );
            wp_enqueue_script( 'jquery' );
        }
    }
} // end !function_exists( 'load_mvts_scripts' )

add_action( 'wp_footer', 'inline_mvtsTest_scripts' );
if ( !function_exists( 'inline_mvtsTest_scripts' ) ) {
    function inline_mvtsTest_scripts() { 
    	$options = get_option('mvtsBasic');
    	?>
		<script type="text/javascript">
		var $ = jQuery.noConflict();
		$(document).ready(function() {
			var <?php echo $options[testName]; ?> = new Cohorts.Test({
				name: 'MVTS_',
				scope: 1, // Sets the scope for the test and custom variable: 1: Visitor, 2: Session, 3: Page 
				cv_slot: 5, // Sets the custom variable slot used in the GoogleAnalyticsAdapter
				sample: 1,
				cohorts: {
					MVTS_default_: {
						onChosen: function() {
							// nothing is changed here but it's still tracked
						}
					},
					MVTS_variant_: {
						onChosen: function() {
							$('<?php echo $options[target]; ?>').attr( "style", $('<?php echo $options[target]; ?>').attr( "style") + "; <?php echo $options[selectStyle]; ?>: <?php echo $options[styleAtt]; ?>");
						}
					},
				},
				storageAdapter: {
				nameSpace: 'mvts',
				trackEvent: function(category, action, opt_label, opt_value, int_hit, cv_slot, scope) { 	
					var len_ga = $('script[src*="analytics.js"]').length;
						console.log( len_ga );
					var len_gaq = $('script[src*="ga.js"]').length;
						console.log( len_gaq );
					if (len_gaq >= 1) {
						// if using old analytics.js
						_gaq.push(['_trackEvent', category, action, opt_label, opt_value, int_hit]);
					} else if (len_ga >= 1) {
						// if using new ga.js
						// note: using 'ga' should work in most situations however Yoasts GA plugin when in Universal mode sets it to __gaTracker
						__gaTracker('send', 'event', category, action, opt_label, opt_value, int_hit);						
						//ga('send', 'event', category, action, opt_label, opt_value, int_hit);	
					} else {
						console.log ('GA probably not defined or using a different identifier');
					}
					
					
				},
				onInitialize: function(inTest, testName, cohort, cv_slot, scope) {
					if(inTest && scope !== 3) {
						this.trackEvent(this.nameSpace, testName, cohort, 0, true, cv_slot, scope);
					}
				},
				onEvent: function(testName, cohort, eventName) {
					this.trackEvent(this.nameSpace, testName, cohort + ' | ' + eventName, 0, false);
				}
			}
			});	
			$('<?php echo $options[target]; ?>').click(function() {
				<?php echo $options[testName]; ?>.event('Converted'); // Track any evens with your storage adapter
			});

		});
		</script>

		<?php 
	}

} // end !function_exists( 'inject_mvtsTest_scripts' )

// create an admin page for settings

add_action( 'admin_menu', 'register_mvts_menu_page' );

if ( !function_exists( 'register_mvts_menu_page' ) ) {

	function register_mvts_menu_page(){
		add_menu_page( 'MVTS', 'MVTS', 'manage_options', 'mvts', 'mvts_menu_page', 'dashicons-welcome-widgets-menus', 6 );
	}

} // end !function_exists( 'register_mvts_menu_page' )

if ( !function_exists( 'mvts_menu_page' ) ) {
	function mvts_menu_page(){ ?>

		<div class="wrap">
			<h2>MVTS Plugin</h2>
			<div class="nav-tab-wrapper">
				<a href="#basic-settings" class="nav-tab" data-target="#basic-settings">Basic Settings</a>
				<a href="#advanced-settings" class="nav-tab" data-target="#advanced-settings">Advanced Settings</a>
			</div>
			<div class="tab-content">
				<div id="basic-settings" class="tab-panel active">
					<form method="post" action="options.php"> 
						<?php 
						settings_fields( 'mvtsBasic-group' );
						do_settings_sections( 'mvtsBasic-group' );
						submit_button(); 
						?>
					</form>
				</div>
				<div id="advanced-settings" class="tab-panel">
					<form method="post" action="options.php"> 
						<?php 
						settings_fields( 'mvtsAdvanced-group' );
						do_settings_sections( 'mvtsAdvanced-group' );
						submit_button(); 
						?>
					</form>
				</div>
			</div>
		</div>

		<!-- Inline required styles and scripts on the admin page -->
		<style type="text/css">
			.tab-content {
				/*max-width:650px;*/
				margin:0 auto;
			}
			.tab-content .tab-panel {
				display:none;
			}
			.tab-content .tab-panel.active {
				display:block;
			}
		</style>
		<script type="text/javascript">
			jQuery( ".nav-tab-wrapper .nav-tab" ).click(function( event ) {
				event.preventDefault();
	  			var target = jQuery(this).attr("data-target");
	  			jQuery( ".tab-content .active" ).removeClass('active');
	  			jQuery(target).addClass('active');
	  			console.log(target);
			});
		</script>
	<?php }
} // end !function_exists( 'mvts_menu_page' )

// add action to trigger settings creation used by the admin page

add_action( 'admin_init', 'register_mvts_settings' );
	
if ( !function_exists('register_mvts_settings') ) {
	function register_mvts_settings() {
		register_setting( 'mvtsBasic-group', 'mvtsBasic', 'mvtsBasic_validate' );
		add_settings_section('mvtsBasic', 'Basic Settings', 'basic_section_text', 'mvtsBasic-group');
		add_settings_field('mvtsOnOff', 'Turn On or Off the tests', 'mvtsCheckboxOnOff','mvtsBasic-group', 'mvtsBasic');
		add_settings_field('testName', 'A Name to identify the test', 'mvtsBasic_testName_string', 'mvtsBasic-group', 'mvtsBasic');
		add_settings_field('target', 'Element to target', 'mvtsBasic_target_string', 'mvtsBasic-group', 'mvtsBasic');
		add_settings_field('selectStyle', 'Style Element to Split Test', 'mvtsBaisc_style_select', 'mvtsBasic-group', 'mvtsBasic');
		add_settings_field('styleAtt', 'Attribute to pass', 'mvtsBasic_style_att', 'mvtsBasic-group', 'mvtsBasic');
		
	}
} // end !function_exists('register_mvts_settings')

function mvtsCheckboxOnOff () {
$options = get_option('mvtsBasic');
/*if( $options['mvtsOnOff'] == '1' ) { 
    echo '<p>The checkbox has been checked.</p>';
} else {
    echo '<p>The checkbox has not been checked.</p>';
} // end if */
$html = '<input type="checkbox" id="mvtsOnOff" name="mvtsBasic[mvtsOnOff]" value="1"' . checked( 1, $options['mvtsOnOff'], false ) . '/>';
$html .= '<label for="mvtsOnOff">Checked = On</label>';

echo $html;
}

function basic_section_text() {
	echo '<p>Main description of this section here.</p>';
}

function mvtsBasic_style_att() {
	$options = get_option('mvtsBasic');
	// commented out code for debug
	// print_r($options);
	echo "<input id='styleAtt' name='mvtsBasic[styleAtt]' size='40' type='text' value='{$options['styleAtt']}' />";
}
function mvtsBaisc_style_select() {
 
   $options = get_option('mvtsBasic');
	// commented out code for debug
	// print_r($options);
     
    $html = '<select id="selectStyle" name="mvtsBasic[selectStyle]">';
        $html .= '<option value="default">Select a style option...</option>';
        $html .= '<option value="color"' . selected( $options['selectStyle'], 'color', false) . '>Color</option>';
        $html .= '<option value="background-color"' . selected( $options['selectStyle'], 'background-color', false) . '>Background Color</option>';
    $html .= '</select>';
     
    echo $html;
 
} // end sandbox_radio_element_callback 

function mvtsBasic_target_string() {
	$options = get_option('mvtsBasic');
	// commented out code for debug
	// print_r($options);
	echo "<input id='target' name='mvtsBasic[target]' size='40' type='text' value='{$options['target']}' />";
}

function mvtsBasic_testName_string() {
	$options = get_option('mvtsBasic');
	// commented out code for debug
	// print_r($options);
	echo "<input id='testName' name='mvtsBasic[testName]' size='40' type='text' value='{$options['testName']}' />";
}


function mvtsBasic_validate($input) {

	// NOTE: THIS DOES NO VALIDATION!!!
	// It takes the input sets it into a new variable and then returns it
	// validation should take place on the new input before it is returned
	$newinput['mvtsOnOff'] = $input['mvtsOnOff'];
	$newinput['target'] = $input['target'];
	$newinput['testName'] = $input['testName'];
	$newinput['selectStyle'] = $input['selectStyle'];
	$newinput['styleAtt'] = $input['styleAtt'];
	/*if(!preg_match('/^[a-z0-9]{32}$/i', $newinput['basic_section1'])) {
	$newinput['basic_section1'] = '';
	}*/
	return $newinput;
}

?>