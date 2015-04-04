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
				<a href="#advanced-settings" class="nav-tab" data-target="#advanced-settings">Certification</a>
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

?>