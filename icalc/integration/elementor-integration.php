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

/**
 * Registers an Elementor widget and enqueues a script on the front-end.
 */

use Elementor\Plugin;

add_action('elementor/widgets/widgets_registered', 'icalc_register_elementor_widgets');

/**
 * Register the IcalcElementorWidget with Elementor.
 */
function icalc_register_elementor_widgets()
{
    if (defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')) {
        require_once(ICALC_PATH . '/integration/IcalcElementorWidget.php');
        $widget_manager = Plugin::$instance->widgets_manager;
        $widget_manager->register(new IcalcElementorWidget());

        wp_enqueue_script('icalc_pages_scripts_el', plugins_url('/scripts/icalc_pages.js', ICALC_FILE), array(), ICALC_VERSION, false);
        add_action('wp_enqueue_scripts', 'icalc_pages_scripts_el');
    }
}