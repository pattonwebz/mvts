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

add_action( 'wp_enqueue_scripts', 'load_cohorts_scripts' );

if ( !function_exists( 'load_cohorts_script' ) ) {
    function load_cohorts_scripts() {
        if ( !is_admin() ) {
			wp_register_script( 'cohorts', get_bloginfo('wpurl') . '/wp-content/plugins/mvts/js/cohorts.js', array(), 1.0, true );
            wp_enqueue_script( 'cohorts' );

        }
    }
}

?>