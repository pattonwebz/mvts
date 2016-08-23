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

// Activation function
register_activation_hook( __FILE__, 'mvts_activation' );
function mvts_activation() {
    // Grab the existing options if they exist so we can add to them or
    // create if needed.
    $optionsBasic = get_option('mvtsBasic');
    $optionsAdvanced = get_option('mvtsAdvanced');

    // If yoast GA is active then we should save an obtion to tell us that the
    // default 'ga' object is probably renamed.
    if( is_yoast_ga_plugin_active() ) {
        $optionsAdvanced[GAObject] = '__gaTracker';
        update_option( 'mvtsAdvanced', $optionsAdvanced );
        add_option( 'mvtst', 'val');
    }
    add_option( 'mvtsf', 'val');
}

// The heart of the plugin is the javascript testing library and companion
// testing script. These are enqueued by this function.

/* =============================================================
 * Enqueue Javascript
 * ============================================================= */

// register the dependancy scripts - enqueued later
add_action( 'wp_enqueue_scripts', 'load_mvts_scripts' );

if ( !function_exists( 'load_mvts_scripts' ) ) {
    function load_mvts_scripts() {
    	$options = get_option('mvtsBasic');
        // not an admin page and plugin is turned on
        if ( !is_admin() && $options['mvtsOnOff'] == '1' ) {
			// wp_register_script( 'cohorts', get_bloginfo('wpurl') . '/wp-content/plugins/mvts/js/cohorts.js', array(), 1.0, true );
			wp_register_script( 'cohorts', plugin_dir_url( __FILE__ ) . 'js/cohorts.min.js', array(), 1.0, true );
            wp_register_script( 'mvtsTestScript', plugin_dir_url( __FILE__ ) . 'js/testscript.js', array('cohorts','jquery'), 1.0, true );
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


// Register basic settings
add_action( 'admin_init', 'register_mvtsBasic_settings' );

function register_mvtsBasic_settings() {

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

} // end function register_mvtsBasic_settings

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


// Register basic settings
add_action( 'admin_init', 'register_mvtsAdvanced_settings' );

function register_mvtsAdvanced_settings() {

	// register a settings group
	register_setting( 'mvtsAdvanced-group', 'mvtsAdvanced', 'mvtsAdvanced_validate' );

    // add a section to the settings group
	add_settings_section('mvtsAdvanced', 'MVTS Adv Settings', 'advanced_section_text', 'mvtsAdvanced-group');

    // add several fields to the section in the settings group
	add_settings_field('GAObject', 'Set the Google Analytics Object Name', 'mvtsGAObject_string','mvtsAdvanced-group', 'mvtsAdvanced');

} // end function register_mvtsBasic_settings

// This is the generic test name
// Type: Text
function mvtsGAObject_string() {
	// grab the options array
	$options = get_option('mvtsAdvanced');
	// echo a text box
	// this is an identifier for the test used in actions and
	// labels pushed to the tracker
	$options['GAObject']=esc_textarea($options['GAObject']);
	echo "<input id='GAObject' name='mvtsBasic[GAObject]' size='40' type='text' value='{$options['GAObject']}' />";
}

// The 'Advanced' section of settings has some opening text.
function advanced_section_text() {
	// This is the opening message for the settings section.
	echo '<p>The advanced settings for the plugin can be found below. Only change these if your comfortable doing so.</p>';
} // end advanced_section_text

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
                <?php
                if( is_yoast_ga_plugin_active() ) {
                    ?><p>Yoast's GA plugin is active. The below should probably be set to '__gaTracker'</p><?php
                }
                ?>
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

function mvtsBasic_validate($input) {
	// NOTE: THIS DOES NOT VALIDATE ALL INPUT!!! BE CAREFUL!

	// Takes the input, sets it to a new variable and then returns it.
	// Validation should take place on the new input before it is returned
	// so that the unsanitized input never touches the database.

	$newinput['mvtsOnOff'] = $input['mvtsOnOff'];
	$newinput['mvtsTrack'] = $input['mvtsTrack'];
	$newinput['target'] = $input['target'];
	$newinput['testName'] = $input['testName'];

	// set allowed values to an array
	$allowed_testType = array('style', 'content');
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
	$allowed_styleType = array('color', 'background-color', 'margin', 'font-size');
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

function mvtsAdvanced_validate($input) {
	// NOTE: NEEDS PROPER VALIDATION
	// Takes the input, sets it to a new variable and then returns it.
	// Validation should take place on the new input before it is returned
	// so that the unsanitized input never touches the database.
	$newinput['GAObject'] = $input['GAObject'];
	// REMEMBER THIS IS STILL NOT VALIDATED/SANITIZED BEFORE IT'S RETURNED
	$newinput = sanitize_text_field($newinput);
	return $newinput;
}


// Add an action at the wp_footer call that enqueues the test library and
// test script as well as adding some data to the header with
// wp_localize_script() for use by the test scipt
add_action( 'wp_footer', 'inline_mvtsTest_scripts' );

function inline_mvtsTest_scripts() {
    // get the options array
	$optionsBasic = get_option('mvtsBasic');
    $optionsAdvanced = get_option('mvtsAdvanced');
	error_log(print_r($optionsBasic, true),0);

    // some debug code
    //print_r($optionsBasic);
    //print_r($mvtsAdvanced);

    // if the plugin is turned on
    if ($optionsBasic['mvtsOnOff'] == '1') {
        // enque the library and test script
        wp_enqueue_script( 'cohorts' );
        wp_enqueue_script( 'mvtsTestScript' );

        // output some of the data from the options array for use by
        // the test script in the <head> section using wp_localize_script
        wp_localize_script( 'mvtsTestScript', 'testVariables',
			array(
	            'testTrack'     	=> $optionsBasic['mvtsTrack'],
	            'testName'  		=> $optionsBasic['testName'],
	            'testType'  		=> $optionsBasic['selectType'],
	            'testTarget'    	=> $optionsBasic['target'],
				'selectStyle'		=> $optionsBasic['selectStyle'],
				'styleAtt'			=> $optionsBasic['styleAtt'],
				'contentChange'		=> $optionsBasic['contentChange'],
	            'customGAObject'  	=> $optionsAdvanced['GAObject']
			)
        );
    }
}

// detect if Yoast's GA plugin is active - it's know to change the default
// tracking object name which is an issue for pushing data reliably
function is_yoast_ga_plugin_active() {
    if( is_plugin_active('google-analytics-for-wordpress/googleanalytics.php') ) {
        return true;
    }
    return false;
}
?>
