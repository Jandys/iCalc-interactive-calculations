<?php


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

    wp_enqueue_script('icalc_pages_scripts', plugins_url('/scripts/icalc_pages.js', ICALC_FILE), array(), ICALC_VERSION, false);
    add_action('wp_enqueue_scripts', 'icalc_pages_scripts');

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

/**
 * Adds the iCalc calculation shortcode handler.
 */
function icalc_calculation_shortcode_handler()
{
    add_shortcode('icalc_calculation', 'renderIcalcCalculationShortcode');
}

add_action('init', 'icalc_calculation_shortcode_handler');

?>