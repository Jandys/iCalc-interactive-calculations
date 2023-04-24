<?php


function renderIcalcCalculation( $atts = array(), $content = null, $tag = '' ) {
	// Extract the shortcode parameters
	$atts = shortcode_atts( array(
		'param1' => 'default_value',
		'param2' => 'default_value'
	), $atts );

	// Code to retrieve and format your data using the parameters goes here
	$formatted_data = 'Param 1: ' . $atts['param1'] . ', Param 2: ' . $atts['param2'];

	// Output the formatted data and JavaScript code
	$output = '<div>' . $formatted_data . '</div>';
	$output .= '<script type="text/javascript">';
	$output .= 'console.log("Hello from my custom shortcode!");';
	$output .= '</script>';

	return $output;
}
function wporg_shortcodes_init() {
	add_shortcode( 'icalc_calculation', 'renderIcalcCalculation' );
}

add_action( 'init', 'wporg_shortcodes_init' );


