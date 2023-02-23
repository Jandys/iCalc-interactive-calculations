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
define( 'ICALC_VERSION', '0.0.1' );
define( 'ICALC_PATH', dirname( ICALC_FILE ) );
define( 'ICALC_URL', plugins_url( '', ICALC_FILE ) );

require ICALC_PATH . '/loader.php';

add_action('admin_menu', 'icalcPluginConfiguration');
add_action('admin_menu', 'icalcConfigurableVariablesMenu');
add_action('admin_init', 'my_configurable_variables_save_options');


if(icalc\db\DatabaseInit::init()){
    \icalc\util\console_log("inited");
}

prefix_enqueue();

function prefix_enqueue()
{
    // JS
    wp_register_script('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js');
    wp_enqueue_script('prefix_bootstrap');

    // CSS
    wp_register_style('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');
}