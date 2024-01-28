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

if (!defined('ABSPATH')) exit;

use interactivecalculations\fe\Calculation as CalculationAlias;

/**
 * Renders the Interactive Calculations calculation shortcode.
 *
 * @param array $atts The attributes of the shortcode.
 * @param string|null $content The content of the shortcode (not used).
 *
 * @return string   The rendered HTML output of the shortcode.
 * @since 1.0.0
 */
function renderInteractiveCalculationsCalculationShortcode($atts, $content = null)
{
    session_write_close();

    interactivecalculations_enqueue_page_dependencies();


    // Default shortcode parameter
    $default_atts = array(
        'id' => -1
    );

    //Extract shortcode attributes
    $atts = shortcode_atts($default_atts, $atts, 'interactivecalculations_calculation');

    if ($atts["id"] == -1) {
        $calculation = -1;
    } else {
        $calculation = new CalculationAlias(intval($atts["id"]));
    }


    if (!$calculation->hasFoundCalculationDescription()) {
        // Display error if id is not passed inside shortcode or calculation with such id is not found.
        return "<div class='bg-danger ml-3 pl-3'>
					<p><strong>" . __("ERROR", "interactivecalculations") . ": </strong> " . __("id parameter is missing or there is an issue in shortcode declaration") . ".</p>
					<p class='mb-0'>" . __("Try updating your shortcode to be like") . ":</p>
					<p class='ml-3 mr-3 mb-0'><strong>[interactivecalculations_calculation id=1]</strong></p>
					<p>" . __("Where 'id' determines what calculation you want to display") . ".</p>
					</div>";
    }

    return $calculation->render();
}

function interactivecalculations_enqueue_page_dependencies()
{
    wp_enqueue_script('interactivecalculations_pages_scripts', plugins_url('/scripts/interactivecalculations_pages.js', INTERACTIVECALCULATIONS_FILE), array(), INTERACTIVECALCULATIONS_VERSION, false);
    wp_enqueue_style('interactivecalculations_page_style', plugins_url('/styles/interactivecalculations-pages-generic.css', INTERACTIVECALCULATIONS_FILE), array(), INTERACTIVECALCULATIONS_VERSION, false);
}

/**
 * Adds the interactivecalculations calculation shortcode handler.
 */
function interactivecalculations_calculation_shortcode_handler()
{
    add_shortcode('interactivecalculations_calculation', 'renderinteractivecalculationsCalculationShortcode');
}

interactivecalculations_enqueue_page_dependencies();
add_action('wp_enqueue_scripts', 'interactivecalculations_enqueue_page_dependencies');
add_action('init', 'interactivecalculations_calculation_shortcode_handler');

?>