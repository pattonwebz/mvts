<?php
/*
Plugin Name: Optiwebz MVTS
Plugin URI: http://www.pattonwebz.com/resources/
Description: A powerful split-testing (MVT) plugin for WordPress. Functional but still in early development.
Version: 0.2
Author: William Patton
Author URI: http://www.pattonwebz.com/
License: GPL2
*/


// The heart of the plugin is the javascript testing library and companion
// testing script. These are enqueued by this function.

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
            wp_register_script( 'mvtsTestScript', get_bloginfo('wpurl') . '/wp-content/plugins/mvts/js/testscript.js', array('cohorts','jquery'), 1.0, true );
        }
    }
}

// The plugin needs an admin page where people can specify settings. The below
// adds the page and then the settings that are used are defined

// create an admin page for settings
add_action( 'admin_menu', 'register_mvts_menu_page' );

if ( !function_exists( 'register_mvts_menu_page' ) ) {
	function register_mvts_menu_page(){
		add_menu_page( 'MVTS', 'MVTS', 'manage_options', 'mvts', 'mvts_menu_page', 'dashicons-welcome-widgets-menus', 6 );
	}
}

add_action( 'admin_init', 'register_mvts_settings' );

function register_mvts_settings() {

	// register a settings group
	register_setting( 'mvtsBasic-group', 'mvtsBasic', 'mvtsBasic_validate' );

    // add a section to the settings group
	add_settings_section('mvtsBasic', 'MVTS Settings', 'basic_section_text', 'mvtsBasic-group');

    // add several fields to the section in the settings group
	add_settings_field('mvtsOnOff', 'Turn On or Off the tests', 'mvtsCheckboxOnOff','mvtsBasic-group', 'mvtsBasic');
	add_settings_field('mvtsTrack', 'Track via Google Analytics', 'mvtsCheckboxTrack','mvtsBasic-group', 'mvtsBasic');
	add_settings_field('testName', 'A Name to identify the test', 'mvtsBasic_testName_string', 'mvtsBasic-group', 'mvtsBasic');
	add_settings_field('target', 'Element to target', 'mvtsBasic_target_string', 'mvtsBasic-group', 'mvtsBasic');
	add_settings_field('selectType', 'Style Test Type', 'mvtsBaisc_type_select', 'mvtsBasic-group', 'mvtsBasic');
	add_settings_field('selectStyle', 'Style Element to Split Test', 'mvtsBaisc_style_select', 'mvtsBasic-group', 'mvtsBasic');
	add_settings_field('styleAtt', 'Attribute to pass', 'mvtsBasic_style_att', 'mvtsBasic-group', 'mvtsBasic');
	add_settings_field('contentChange', 'New content to use in test', 'mvtsBasic_content', 'mvtsBasic-group', 'mvtsBasic');

} // end function register_mvts_settings

// The 'Basic' section of settings has some opening text.
function basic_section_text() {
	// This is the opening message for the settings section.
	echo '<p>The main settings for the MVTS plugin are below.</p>';
} // end basic_section_text

// Main On/Off toggle for plugin functions
// Type: Checkbox
function mvtsCheckboxOnOff() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// create $html variable with markup for the checkbox
	$html = '<input type="checkbox" id="mvtsOnOff" name="mvtsBasic[mvtsOnOff]" value="1"' . checked( 1, $options['mvtsOnOff'], false ) . '/>';
	$html .= '<label for="mvtsOnOff">Checked = On</label>';
	// echo a checkbox
	echo $html;
} // end function mvtsCheckboxOnOff

// Enable or disable the GA event pushing tracking feature
// Type: Checkbox
function mvtsCheckboxTrack () {
	// grab the options array
	$options = get_option('mvtsBasic');
	// create $html variable with markup for the checkbox
	$html = '<input type="checkbox" id="mvtsTrack" name="mvtsBasic[mvtsTrack]" value="1"' . checked( 1, $options['mvtsTrack'], false ) . '/>';
	$html .= '<label for="mvtsTrack">Checked = On</label>';
	// echo a checkbox
	echo $html;
} // end function mvtsCheckboxTrack

// This is the generic test name
// Type: Text
function mvtsBasic_testName_string() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// echo a text box
	// this is an identifier for the test used in actions and
	// labels pushed to the tracker
	$options['testName']=esc_textarea($options['testName']);
	echo "<input id='testName' name='mvtsBasic[testName]' size='40' type='text' value='{$options['testName']}' />";
}

// Should accept the ID or Classname of the element being targeted
// Type: Text
function mvtsBasic_target_string() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// echo a text box
	// expected input will be a CSS classname, an ID or a path to an element on page
	$options['target']=esc_textarea($options['target']);
	echo "<input id='target' name='mvtsBasic[target]' size='40' type='text' value='{$options['target']}' />";
}

// A selectio of the available test types
// Type: Selectbox
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

// Choices for the various style edits available for 'style' test types
// Type: Selectbox
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

// Box that expects exact CSS for a style edit on a target element
// Type: Text
function mvtsBasic_style_att() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// echo a text box
	// expected values are the actual values that would be
	// set for the selected CSS property in the select box
	$options['styleAtt']=esc_textarea($options['styleAtt']);
	echo "<input id='styleAtt' name='mvtsBasic[styleAtt]' class='hide'size='40' type='text' value='{$options['styleAtt']}' />";
}

// If a 'content' test is chosen this is the input for that to be specified
// Type: Text
// NOTE: Should probably be a textbox instaed of a simple text field
function mvtsBasic_content() {
	// grab the options array
	$options = get_option('mvtsBasic');
	// echo a text box
	// expected values are the actual values that would be
	// set for the selected CSS property in the select box
	$options['contentChange']=esc_textarea($options['contentChange']);
	echo "<input id='contentChange' name='mvtsBasic[contentChange]' class='hide' size='40' type='text' value='{$options['contentChange']}' />";
}

// this is the output function for the added menu page

function mvts_menu_page(){
    // this is mostly html with some php to output the settings specified in a
    // different set of functions ?>

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


    jQuery(document).ready(function(){

        // This function checks if a box should he hidden or not and makes it happen
        function timerChecking() {
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
        }

        // run this then rerun every 2.5 seconds
        timerChecking();
        setInterval(timerChecking, 2500);

    });

	</script>
<?php } // end !function mvts_menu_page

// Add an action at the wp_footer call that enqueues the test library and
// test script as well as adding some data to the header with
// wp_localize_script() for use by the test scipt
add_action( 'wp_footer', 'inline_mvtsTest_scripts' );

function inline_mvtsTest_scripts() {
    // get the options array
	$options = get_option('mvtsBasic');
    // if the plugin is turned on
    if ($options['mvtsOnOff'] == '1') {
        // enque the library and test script
        wp_enqueue_script( 'cohorts' );
        wp_enqueue_script( 'mvtsTestScript' );

        // output some of the data from the options array for use by
        // the test script in the <head> section using wp_localize_script
        wp_localize_script( 'mvtsTestScript', 'testVariables', array(
            'testTrack'     => $options[mvtsTrack],
            'testName'  => $options[testName],
            'testType'  => $options[selectType],
            'testTarget'    => $options[target] )
        );
    }
}


?>
