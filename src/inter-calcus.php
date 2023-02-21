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

add_action('admin_menu', 'icalcPluginConfiguration');
add_action('admin_menu', 'icalcConfigurableVariablesMenu');
add_action('admin_init', 'my_configurable_variables_save_options');

require ICALC_PATH . '/loader.php';
