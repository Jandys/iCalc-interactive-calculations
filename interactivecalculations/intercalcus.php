<?php
/*
 *
 *   This file is part of the 'iCalc - Interactive Calculations' project.
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
Plugin Name: Interactive Calculations
Plugin URI: https://php.jandys.eu/
Description: Plugin for easy creation of custom calculations.
Version: 1.0.0
Author: Jakub Jandák
Author URI: http://github.com/Jandys
License: GPL3
*/


define('INTERACTIVECALCULATIONS_DIR', __DIR__);
define('INTERACTIVECALCULATIONS_FILE', __FILE__);
define('INTERACTIVECALCULATIONS_VERSION', '0.1');
define('INTERACTIVECALCULATIONS_PATH', dirname(INTERACTIVECALCULATIONS_FILE));
define('INTERACTIVECALCULATIONS_URL', plugins_url('', INTERACTIVECALCULATIONS_FILE));
define('INTERACTIVECALCULATIONS_EP_PREFIX', 'interactivecalculations/v1');


require INTERACTIVECALCULATIONS_PATH . '/loader.php';


add_action('plugins_loaded', 'interactivecalculations_load_textdomain');

add_action('init', 'interactivecalculations_start_session');
function interactivecalculations_start_session()
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

    interactivecalculations_load_scripts();

    // CSS
    wp_register_style('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');

    wp_enqueue_style('interactivecalculations_custom_style', plugins_url('/styles/interactivecalculations-custom-style.css', __FILE__), array(), INTERACTIVECALCULATIONS_VERSION, false);
    add_action('wp_enqueue_style', 'interactivecalculations_custom_style');

    wp_enqueue_style('interactivecalculations_page_style', plugins_url('/styles/interactivecalculations-pages-generic.css', __FILE__), array(), INTERACTIVECALCULATIONS_VERSION, false);
    add_action('wp_enqueue_style', 'interactivecalculations_page_style');
}

function interactivecalculations_load_scripts()
{
    if (is_admin()) {
        wp_enqueue_script('interactivecalculations_common_scripts', plugins_url('/scripts/interactivecalculations_common.js', INTERACTIVECALCULATIONS_FILE), array(), INTERACTIVECALCULATIONS_VERSION, false);
        add_action('wp_enqueue_scripts', 'interactivecalculations_common_scripts');
        wp_enqueue_script('interactivecalculations_admin_scripts', plugins_url('/scripts/interactivecalculations_admin.js', INTERACTIVECALCULATIONS_FILE), array(), INTERACTIVECALCULATIONS_VERSION, false);
        add_action('wp_enqueue_scripts', 'interactivecalculations_admin_scripts');
    }
}

function interactivecalculations_load_textdomain()
{
    load_plugin_textdomain('interactivecalculations', false, plugins_url('/localization', __DIR__));
}


function interactivecalculations_plugin_activation($iteration)
{
    if ($iteration == null) {
        $iteration = 1;
    } else {
        $iteration++;
    }

    if (interactivecalculations\db\DatabaseInit::init()) {
        error_log("interactivecalculations plugin database tables successfully initialized");
    } else {
        error_log("There was an error while trying to initialized interactivecalculations plugin database tables");

        error_log("Try to initialize again in 1 second");
        sleep(1);

        if ($iteration < 6) {
            interactivecalculations_plugin_activation($iteration);
        } else {
            error_log("Number of iteration exceeded limit. Database initialization for plugin interactivecalculations Failed");
        }
    }

}

function interactivecalculations_plugin_deactivation()
{
    interactivecalculations\db\DatabaseInit::clearAll();
}

register_activation_hook(INTERACTIVECALCULATIONS_FILE, 'interactivecalculations_plugin_activation');
register_deactivation_hook(INTERACTIVECALCULATIONS_FILE, 'interactivecalculations_plugin_deactivation');
