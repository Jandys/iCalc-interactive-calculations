<?php
/*
Plugin Name: Inter Calcus - Calculation Plugin
Plugin URI: http://temp.uri
Description: A brief description of the Plugin.
Version: 0.1
Author: Jakub Jandák
Author URI: http://github.com/Jandys
License: GPL2
*/

define( 'ICALC_DIR', __DIR__ );
define( 'ICALC_FILE', __FILE__ );
define( 'ICALC_VERSION', '0.1' );
define( 'ICALC_PATH', dirname( ICALC_FILE ) );
define( 'ICALC_URL', plugins_url( '', ICALC_FILE ) );
define( 'ICALC_EP_PREFIX', 'icalc/v1' );


require ICALC_PATH . '/loader.php';


add_action( 'plugins_loaded', 'icalc_load_textdomain' );

prefix_enqueue();
function prefix_enqueue(): void {
	// JS
	wp_register_script( 'prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js' );
	wp_enqueue_script( 'prefix_bootstrap' );

	icalc_load_scripts();

	// CSS
	wp_register_style( 'prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css' );
	wp_enqueue_style( 'prefix_bootstrap' );

	wp_enqueue_style( 'icalc_custom_style', plugins_url( '/styles/icalc-custom-style.css', __FILE__ ), array(), ICALC_VERSION, false );
	add_action( 'wp_enqueue_style', 'icalc_custom_style' );
}

function icalc_load_scripts() {
	if ( is_admin() ) {
		wp_enqueue_script( 'icalc_common_scripts', plugins_url( '/scripts/icalc_common.js', ICALC_FILE ), array(), ICALC_VERSION, false );
		add_action('wp_enqueue_scripts', 'icalc_common_scripts');
		wp_enqueue_script( 'icalc_admin_scripts', plugins_url( '/scripts/icalc_admin.js', ICALC_FILE ), array(), ICALC_VERSION, false );
		add_action( 'wp_enqueue_scripts', 'icalc_admin_scripts' );
	}
}


function icalc_load_textdomain() {
	load_plugin_textdomain( 'icalc', false, plugins_url( '/localization', __DIR__ ) );
}