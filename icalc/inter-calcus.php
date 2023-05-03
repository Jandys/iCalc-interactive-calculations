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
Plugin Name: iCalc - Interactive Calculations
Plugin URI: https://php.jandys.eu/icalc
Description: Plugin for easy creation of custom calculations.
Version: 1.0.0
Author: Jakub Jandák
Author URI: http://github.com/Jandys
License: GPL3
*/


define('ICALC_DIR', __DIR__);
define('ICALC_FILE', __FILE__);
define('ICALC_VERSION', '0.1');
define('ICALC_PATH', dirname(ICALC_FILE));
define('ICALC_URL', plugins_url('', ICALC_FILE));
define('ICALC_EP_PREFIX', 'icalc/v1');


require ICALC_PATH . '/loader.php';


add_action('plugins_loaded', 'icalc_load_textdomain');

add_action('init', 'icalc_start_session');
function icalc_start_session()
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

    icalc_load_scripts();

    // CSS
    wp_register_style('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');

    wp_enqueue_style('icalc_custom_style', plugins_url('/styles/icalc-custom-style.css', __FILE__), array(), ICALC_VERSION, false);
    add_action('wp_enqueue_style', 'icalc_custom_style');

    wp_enqueue_style('icalc_page_style', plugins_url('/styles/icalc-pages-generic.css', __FILE__), array(), ICALC_VERSION, false);
    add_action('wp_enqueue_style', 'icalc_page_style');
}

function icalc_load_scripts()
{
    if (is_admin()) {
        wp_enqueue_script('icalc_common_scripts', plugins_url('/scripts/icalc_common.js', ICALC_FILE), array(), ICALC_VERSION, false);
        add_action('wp_enqueue_scripts', 'icalc_common_scripts');
        wp_enqueue_script('icalc_admin_scripts', plugins_url('/scripts/icalc_admin.js', ICALC_FILE), array(), ICALC_VERSION, false);
        add_action('wp_enqueue_scripts', 'icalc_admin_scripts');
    }
}

function icalc_load_textdomain()
{
    load_plugin_textdomain('icalc', false, plugins_url('/localization', __DIR__));
}


function icalc_plugin_activation()
{
    icalc\db\DatabaseInit::init();
}

function icalc_plugin_deactivation()
{
    icalc\db\DatabaseInit::clearAll();
}

register_activation_hook(ICALC_FILE, 'icalc_plugin_activation');
register_deactivation_hook(ICALC_FILE, 'icalc_plugin_deactivation');
