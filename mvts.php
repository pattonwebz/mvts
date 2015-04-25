<?php
/*
Plugin Name: CTA Plugin
Plugin URI: http://www.pattonwebz.com/resources/
Description: A split-testing (MVT) plugin for WordPress. Functional but still in early development.
Version: 0.2
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
			// wp_register_script( 'cohorts', get_bloginfo('wpurl') . '/wp-content/plugins/mvts/js/cohorts.js', array(), 1.0, true );
			wp_register_script( 'cohorts', get_bloginfo('wpurl') . '/wp-content/plugins/mvts/js/cohorts.min.js', array(), 1.0, true );
            wp_enqueue_script( 'cohorts' );
            wp_enqueue_script( 'jquery' );
        }
    }
}

// Create settings used by the admin page
add_action( 'admin_init', 'register_mvts_settings' );
	
if ( !function_exists('register_mvts_settings') ) {
	function register_mvts_settings() {
		// register a settings group
		register_setting( 'mvtsBasic-group', 'mvtsBasic', 'mvtsBasic_validate' );
		// add a section to the settings group
		add_settings_section('mvtsBasic', 'Basic Settings', 'basic_section_text', 'mvtsBasic-group');
		// add several fields to the section in the settings group
		add_settings_field('mvtsOnOff', 'Turn On or Off the tests', 'mvtsCheckboxOnOff','mvtsBasic-group', 'mvtsBasic');
		add_settings_field('mvtsTrack', 'Track via Google Analytics', 'mvtsCheckboxTrack','mvtsBasic-group', 'mvtsBasic');
		add_settings_field('testName', 'A Name to identify the test', 'mvtsBasic_testName_string', 'mvtsBasic-group', 'mvtsBasic');
		add_settings_field('target', 'Element to target', 'mvtsBasic_target_string', 'mvtsBasic-group', 'mvtsBasic');
		add_settings_field('selectType', 'Style Test Type', 'mvtsBaisc_type_select', 'mvtsBasic-group', 'mvtsBasic');
		add_settings_field('selectStyle', 'Style Element to Split Test', 'mvtsBaisc_style_select', 'mvtsBasic-group', 'mvtsBasic');
		add_settings_field('styleAtt', 'Attribute to pass', 'mvtsBasic_style_att', 'mvtsBasic-group', 'mvtsBasic');
		add_settings_field('contentChange', 'New content to use in test', 'mvtsBasic_content', 'mvtsBasic-group', 'mvtsBasic');
	}
} // end !function_exists('register_mvts_settings')

function basic_section_text() {
	// This is the opening message for the settings section.
	echo '<p>This is an informational section on the page.</p>';
}

// Main On/Off toggle for plugin functions
function mvtsCheckboxOnOff () {
	// grab the options array
	$options = get_option('mvtsBasic');
	// create $html variable with markup for the checkbox
	$html = '<input type="checkbox" id="mvtsOnOff" name="mvtsBasic[mvtsOnOff]" value="1"' . checked( 1, $options['mvtsOnOff'], false ) . '/>';
	$html .= '<label for="mvtsOnOff">Checked = On</label>';
	// echo a checkbox
	echo $html;
}

// Enable or disable the GA event pushing tracking feature
function mvtsCheckboxTrack () {
	// grab the options array
	$options = get_option('mvtsBasic');
	// create $html variable with markup for the checkbox
	$html = '<input type="checkbox" id="mvtsTrack" name="mvtsBasic[mvtsTrack]" value="1"' . checked( 1, $options['mvtsTrack'], false ) . '/>';
	$html .= '<label for="mvtsTrack">Checked = On</label>';
	// echo a checkbox
	echo $html;
}

function mvtsBasic_testName_string() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// echo a text box
	// this is an identifier for the test used in actions and
	// labels pushed to the tracker
	$options['testName']=esc_textarea($options['testName']);
	echo "<input id='testName' name='mvtsBasic[testName]' size='40' type='text' value='{$options['testName']}' />";
}

function mvtsBasic_target_string() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// echo a text box
	// expected input will be a CSS classname, an ID or a path to an element on page
	$options['target']=esc_textarea($options['target']);
	echo "<input id='target' name='mvtsBasic[target]' size='40' type='text' value='{$options['target']}' />";
}

function mvtsBaisc_type_select() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// create $html variable with markup for the select box
	// this dictates the type of test to perform
    $html = '<select id="selectType" name="mvtsBasic[selectType]">';
        $html .= '<option value="default">Select a test type...</option>';
        // 'color'
        $html .= '<option value="style"' . selected( $options['selectType'], 'style', false) . '>Style</option>';
        // 'background-color'
        $html .= '<option value="content"' . selected( $options['selectType'], 'content', false) . '>Content</option>';
    $html .= '</select>';
    // echo a select box
    echo $html;
}

function mvtsBaisc_style_select() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// create $html variable with markup for the select box
	// the valies are directly corilated with css properties
    $html = '<select id="selectStyle" name="mvtsBasic[selectStyle]" class="hide">';
        $html .= '<option value="default">Select a style option...</option>';
        // 'color'
        $html .= '<option value="color"' . selected( $options['selectStyle'], 'color', false) . '>Color</option>';
        // 'background-color'
        $html .= '<option value="background-color"' . selected( $options['selectStyle'], 'background-color', false) . '>Background Color</option>';
	    // 'margin'
        $html .= '<option value="margin"' . selected( $options['selectStyle'], 'margin', false) . '>Margin</option>';
    	// 'font-size'
		$html .= '<option value="font-size"' . selected( $options['selectStyle'], 'font-size', false) . '>Font Size</option>';
    $html .= '</select>';
    // echo a select box
    echo $html;
}

function mvtsBasic_style_att() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// echo a text box
	// expected values are the actual values that would be
	// set for the selected CSS property in the select box
	$options['styleAtt']=esc_textarea($options['styleAtt']);
	echo "<input id='styleAtt' name='mvtsBasic[styleAtt]' class='hide'size='40' type='text' value='{$options['styleAtt']}' />";
}
function mvtsBasic_content() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// echo a text box
	// expected values are the actual values that would be
	// set for the selected CSS property in the select box
	$options['contentChange']=esc_textarea($options['contentChange']);
	echo "<input id='contentChange' name='mvtsBasic[contentChange]' class='hide' size='40' type='text' value='{$options['contentChange']}' />";
}

function mvtsBasic_validate($input) {
	// NOTE: THIS DOES NO INPUT VALIDATION!!! BE CAREFUL!
	// UPDATE: SOME VALIDATION DONE
	// Takes the input, sets it to a new variable and then returns it.
	// Validation should take place on the new input before it is returned
	// so that the unsanitized input never touches the database.
	
	$newinput['mvtsOnOff'] = $input['mvtsOnOff'];
	$newinput['mvtsTrack'] = $input['mvtsTrack'];
	$newinput['target'] = $input['target'];
	$newinput['testName'] = $input['testName'];

	// set allowed values to an array
	$allowed_testType = array('style', 'content')
	// loop through allowed values array
	foreach ($allowed_testType as $testType) {
		// compair the input against allowed values
		if ($input['selectType'] === $testType) {
			// if it's an allowed value then save place it in the variable that gets returned
			$newinput['selectType'] = $input['selectType'];
		}	
		// if the input isn't an allowed value then we should not save it
	}

	// set allowed values to an array
	$allowed_styleType = array('color', 'background-color', 'margin', 'font-size')
	// loop through allowed values array
	foreach ($allowed_styleType as $styleType) {
		// compair the input against allowed values
		if ($input['selectStyle'] === $styleType) {
			// if it's an allowed value then save place it in the variable that gets returned
			$newinput['selectStyle'] = $input['selectStyle'];
		}	
		// if the input isn't an allowed value then we should not save it
	}

	$newinput['styleAtt'] = $input['styleAtt'];

	// Change single quotes to double quotes
	// SHOULD THIS MAYBE JUST ESCAPE THEM??? IE: str_replace("'", "\\\'", $input);
	$input['contentChange'] = str_replace("'", '"', $input['contentChange']);
	// Grab the list of allowed html tags for 'post' context
	$allowedTags_content = wp_kses_allowed_html( 'post' );
	// Strip bad tags - allowing the same as what's allowed in posts
	// Posts context is probably not restrictive enough!
	$newinput['contentChange'] = wp_kses($input['contentChange'], $allowedTags_content);

	// REMEMBER THIS IS STILL (some of it) NOT VALIDATED/SANITIZED BEFORE IT'S RETURNED
	return $newinput;
}

// create an admin page for settings
add_action( 'admin_menu', 'register_mvts_menu_page' );

if ( !function_exists( 'register_mvts_menu_page' ) ) {
	function register_mvts_menu_page(){
		add_menu_page( 'MVTS', 'MVTS', 'manage_options', 'mvts', 'mvts_menu_page', 'dashicons-welcome-widgets-menus', 6 );
	}
}

// this is the output function for the added menu page
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
						// outputs the mvtsBasic-group of settings.
						settings_fields( 'mvtsBasic-group' );
						do_settings_sections( 'mvtsBasic-group' );
						submit_button(); 
						?>
					</form>
				</div>
				<div id="advanced-settings" class="tab-panel">
					<p>NOTE: For future use.</p>
					<form method="post" action="options.php"> 
						<?php 
						settings_fields( 'mvtsAdvanced-group' );
						do_settings_sections( 'mvtsAdvanced-group' );
						//submit_button(); 
						?>
					</form>
				</div>
			</div>
		</div>

		<!-- Inline required styles and scripts on the admin page. May no longer be required. -->
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
			.hid {
				display: none;
			}
			.hid.show{
				display:inherit;
			}
		</style>
		<!-- This script adds or removes classes on the tabs based on click/active state -->
		<!-- Also does a show/hide based on value of a select box -->
		<script type="text/javascript">
			jQuery( ".nav-tab-wrapper .nav-tab" ).click(function( event ) {
				event.preventDefault();
	  			var target = jQuery(this).attr("data-target");
	  			jQuery( ".tab-content .active" ).removeClass('active');
	  			jQuery(target).addClass('active');
	  			console.log(target);
			});
			jQuery(".hide").parent().parent().addClass("hid");
			var showHideVal = jQuery( "#selectType" ).val();
			if (showHideVal == "style") {
				jQuery("#selectStyle").parent().parent().removeClass("hid");
				jQuery("#styleAtt").parent().parent().removeClass("hid");
			} else if (showHideVal == "content") {
				jQuery("#contentChange").parent().parent().removeClass("hid");
			}	
		</script>
	<?php }
} // end !function_exists( 'mvts_menu_page' )

// Add an action at the wp_footer call that inlines the javascript test script
add_action( 'wp_footer', 'inline_mvtsTest_scripts' );

if ( !function_exists( 'inline_mvtsTest_scripts' ) ) {
    function inline_mvtsTest_scripts() { 
    	$options = get_option('mvtsBasic');
    	// if the plugin is 'ON' then output the script
    	if ($options['mvtsOnOff'] == '1') {
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
								// Nothing is changed here but it's still tracked
							}
						},
						MVTS_variant_: {
							onChosen: function() {
								<?php if($options['selectType'] == 'style'){ ?> 
									$('<?php echo $options[target]; ?>').attr( "style", $('<?php echo $options[target]; ?>').attr( "style") + "; <?php echo $options[selectStyle]; ?>: <?php echo $options[styleAtt]; ?>");
								<?php } ?>
								<?php if($options['selectType'] == 'content'){ ?> 
									$('<?php echo $options[target]; ?>').html( '<?php echo $options[contentChange]; ?>');
								<?php } ?>
							}
						},
					},
					storageAdapter: {
					nameSpace: 'mvts',
					trackEvent: function(category, action, opt_label, opt_value, int_hit, cv_slot, scope) { 	
						var len_ga = $('script[src*="analytics.js"]').length;
						var len_gaq = $('script[src*="ga.js"]').length;
						<?php // NOTE: This is a PHP if statement
							// if Tracking is 'On' then inline the push to GA 
							if ($options['mvtsTrack'] == '1'){ ?>
							if (len_gaq >= 1) {
								// if using old analytics.js
								_gaq.push(['_trackEvent', category, action, opt_label, opt_value, int_hit]);
							} else if (len_ga >= 1) {
								// if using new ga.js
								// note: using 'ga' should work in most situations however Yoasts GA
								// plugin in Universal mode sets it to __gaTracker to prevent conflits
								// find a way to detect the custom object and set this dynamically
								__gaTracker('send', 'event', category, action, opt_label, opt_value, int_hit);						
								//ga('send', 'event', category, action, opt_label, opt_value, int_hit);	
							} else {
								console.log ('GA probably not defined or using a different identifier');
							}
						<?php } ?>
						
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
					<?php echo $options[testName]; ?>.event('Converted'); // Track any events with your storage adapter
				});

			});
			</script>

			<?php 
		}
	}

} // end !function_exists( 'inline_mvtsTest_scripts' )

?>