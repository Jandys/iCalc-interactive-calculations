<?php


use icalc\fe\Calculation as CalculationAlias;

/**
 * Renders the iCalc calculation shortcode.
 *
 * @param array $atts     The attributes of the shortcode.
 * @param string|null $content    The content of the shortcode (not used).
 *
 * @return string   The rendered output of the shortcode.
 */
function renderIcalcCalculationShortcode( $atts, $content = null) {

	wp_enqueue_script( 'icalc_pages_scripts', plugins_url( '/scripts/icalc_pages.js', ICALC_FILE ), array(), ICALC_VERSION, false );
	add_action('wp_enqueue_scripts', 'icalc_pages_scripts');

	// Extract the shortcode parameters
	$default_atts = array(
		'id' => -1
	);

	$atts = shortcode_atts($default_atts, $atts, 'icalc_calculation');

	if($atts["id"]==-1){
		$output = "<div class='bg-danger ml-3 pl-3'>
					<p><strong>ERROR:</strong> id parameter is missing or is wrong in shortcode declaration.</p>
					<p class='mb-0'>Try updating your shortcode to be like:</p>
					<p class='ml-3 mr-3 mb-0'><strong>[icalc_calculation id=1]</strong></p>
					<p>Where id determines what calculation you want to show.</p>
					</div>";
	}else{
		$calculation = new CalculationAlias( intval($atts["id"]) );
		$output = $calculation->render();
	}

	return $output;
}

/**
 * Adds the iCalc calculation shortcode handler.
 */
function icalc_calculation_shortcode_handler() {
	add_shortcode( 'icalc_calculation', 'renderIcalcCalculationShortcode' );
}

add_action( 'init', 'icalc_calculation_shortcode_handler' );

?>