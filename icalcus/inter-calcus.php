<?php
/*
 *
 *   This file is part of the 'iCalcus - Interactive Calculations' project.
 *
 *   Copyright (C) 2023, Jakub Jandák
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 *
 */

/*
Plugin Name: icalcus - Interactive Calculations
Plugin URI: https://php.jandys.eu/
Description: Plugin for easy creation of custom calculations.
Version: 1.0.0
Author: Jakub Jandák
Author URI: http://github.com/Jandys
License: GPL3
*/


define('ICALCUS_DIR', __DIR__);
define('ICALCUS_FILE', __FILE__);
define('ICALCUS_VERSION', '0.1');
define('ICALCUS_PATH', dirname(ICALCUS_FILE));
define('ICALCUS_URL', plugins_url('', ICALCUS_FILE));
define('ICALCUS_EP_PREFIX', 'icalcus/v1');


require ICALCUS_PATH . '/loader.php';


add_action('plugins_loaded', 'icalcus_load_textdomain');

add_action('init', 'icalcus_start_session');
function icalcus_start_session()
{
    if (!session_id()) {
        session_start();
    }
}

prefix_enqueue();
function prefix_enqueue(): void
{
    // JS
    wp_register_script('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js');
    wp_enqueue_script('prefix_bootstrap');

    icalcus_load_scripts();

    // CSS
    wp_register_style('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');

    wp_enqueue_style('icalcus_custom_style', plugins_url('/styles/icalcus-custom-style.css', __FILE__), array(), ICALCUS_VERSION, false);
    add_action('wp_enqueue_style', 'icalcus_custom_style');

    wp_enqueue_style('icalcus_page_style', plugins_url('/styles/icalcus-pages-generic.css', __FILE__), array(), ICALCUS_VERSION, false);
    add_action('wp_enqueue_style', 'icalcus_page_style');
}

function icalcus_load_scripts()
{
    if (is_admin()) {
        wp_enqueue_script('icalcus_common_scripts', plugins_url('/scripts/icalcus_common.js', ICALCUS_FILE), array(), ICALCUS_VERSION, false);
        add_action('wp_enqueue_scripts', 'icalcus_common_scripts');
        wp_enqueue_script('icalcus_admin_scripts', plugins_url('/scripts/icalcus_admin.js', ICALCUS_FILE), array(), ICALCUS_VERSION, false);
        add_action('wp_enqueue_scripts', 'icalcus_admin_scripts');
    }
}

function icalcus_load_textdomain()
{
    load_plugin_textdomain('icalcus', false, plugins_url('/localization', __DIR__));
}


function icalcus_plugin_activation($iteration)
{
	if($iteration==null){
		$iteration = 1;
	}else{
		$iteration++;
	}

	if(icalcus\db\DatabaseInit::init()){
		error_log("icalcus plugin database tables successfully initialized");
	}else{
		error_log("There was an error while trying to initialized icalcus plugin database tables");

		error_log("Try to initialize again in 1 second");
		sleep(1);

		if($iteration<6){
			icalcus_plugin_activation($iteration);
		}else{
			error_log("Number of iteration exceeded limit. Database initialization for plugin icalcus Failed");
		}
	}

}

function icalcus_plugin_deactivation()
{
    icalcus\db\DatabaseInit::clearAll();
}

register_activation_hook(ICALCUS_FILE, 'icalcus_plugin_activation');
register_deactivation_hook(ICALCUS_FILE, 'icalcus_plugin_deactivation');
