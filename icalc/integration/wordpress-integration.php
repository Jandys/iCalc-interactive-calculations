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

use icalc\fe\Calculation as CalculationAlias;

/**
 * Renders the iCalc calculation shortcode.
 *
 * @param array $atts The attributes of the shortcode.
 * @param string|null $content The content of the shortcode (not used).
 *
 * @return string   The rendered HTML output of the shortcode.
 * @since 1.0.0
 */
function renderIcalcCalculationShortcode($atts, $content = null)
{

    icalc_enqueue_page_dependencies();


    // Default shortcode parameter
    $default_atts = array(
        'id' => -1
    );

    //Extract shortcode attributes
    $atts = shortcode_atts($default_atts, $atts, 'icalc_calculation');

    if ($atts["id"] == -1) {
        $calculation = -1;
    } else {
        $calculation = new CalculationAlias(intval($atts["id"]));
    }

    if ($calculation == -1) {
        // Display error if id is not passed inside shortcode or calculation with such id is not found.
        $output = "<div class='bg-danger ml-3 pl-3'>
					<p><strong>" . __("ERROR", "icalc") . ": </strong> " . __("id parameter is missing or there is an issue in shortcode declaration") . ".</p>
					<p class='mb-0'>" . __("Try updating your shortcode to be like") . ":</p>
					<p class='ml-3 mr-3 mb-0'><strong>[icalc_calculation id=1]</strong></p>
					<p>" . __("Where 'id' determines what calculation you want to display") . ".</p>
					</div>";
    } else {
        $output = $calculation->render();
    }

    return $output;
}

function icalc_enqueue_page_dependencies()
{
    wp_enqueue_script('icalc_pages_scripts', plugins_url('/scripts/icalc_pages.js', ICALC_FILE), array(), ICALC_VERSION, false);
    wp_enqueue_style('icalc_page_style', plugins_url('/styles/icalc-pages-generic.css', ICALC_FILE), array(), ICALC_VERSION, false);
}

/**
 * Adds the iCalc calculation shortcode handler.
 */
function icalc_calculation_shortcode_handler()
{
    add_shortcode('icalc_calculation', 'renderIcalcCalculationShortcode');
}

icalc_enqueue_page_dependencies();
add_action('wp_enqueue_scripts', 'icalc_enqueue_page_dependencies');
add_action('init', 'icalc_calculation_shortcode_handler');

?>