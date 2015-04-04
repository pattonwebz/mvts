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

// register and enqueue the dependancy scripts - cohorts.js

add_action( 'wp_enqueue_scripts', 'load_mvts_scripts' );

if ( !function_exists( 'load_mvts_scripts' ) ) {
    function load_mvts_scripts() {
        if ( !is_admin() ) {
			wp_register_script( 'cohorts', get_bloginfo('wpurl') . '/wp-content/plugins/mvts/js/cohorts.js', array(), 1.0, true );
            wp_enqueue_script( 'cohorts' );

        }
    }
} // end !function_exists( 'load_mvts_scripts' )


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
<<<<<<< HEAD
		register_setting( 'mvtsBasic-group', 'mvtsBasic', 'mvtsBasic_validate' );
		add_settings_section('mvtsBasic', 'Basic Settings', 'basic_section_text', 'mvtsBasic-group');
		add_settings_field('mvtsOnOff', 'Turn On or Off the tests', 'mvtsCheckboxOnOff','mvtsBasic-group', 'mvtsBasic');
		add_settings_field('target', 'Element to target', 'mvtsBasic_target_string', 'mvtsBasic-group', 'mvtsBasic');
		
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




function mvtsBasic_target_string() {
	$options = get_option('mvtsBasic');
	// commented out code for debug
	// print_r($options);
	echo "<input id='target' name='mvtsBasic[target]' size='40' type='text' value='{$options['target']}' />";
}


function mvtsBasic_validate($input) {
	$newinput['mvtsOnOff'] = $input['mvtsOnOff'];
	$newinput['target'] = $input['target'];
	/*if(!preg_match('/^[a-z0-9]{32}$/i', $newinput['basic_section1'])) {
	$newinput['basic_section1'] = '';
	}*/
	return $newinput;
=======
		// example syntax: register_setting( 'my_options_group', 'my_option_name', 'intval' ); 
		register_setting( 'mvtsBasic-group', 'mvtsBasic1', 'mvtsBasic1_validate' );
		register_setting( 'mvtsBasic-group', 'mvtsBasic2', 'mvtsBasic2_validate' );
		add_settings_section('mvtsBasic', 'Basic Settings', 'basic_section_text', 'mvtsBasic-group');
		add_settings_field('mvtsBaisc1_id', 'MVTS Basic Text Input', 'mvtsBasic_setting_string', 'mvts', 'mvtsBasic');
	}
} // end !function_exists('register_mvts_settings')

function basic_section_text() {
echo '<p>Main description of this section here.</p>';
}

function mvtsBasic_setting_string() {
$options = get_option('plugin_options');
echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
>>>>>>> f351420695d9c3af6b676f98877a12f87b334014
}

?>