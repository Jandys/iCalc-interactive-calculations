<?php


/**
 * Registers an Elementor widget and enqueues a script on the front-end.
 */
add_action('elementor/widgets/widgets_registered', 'icalc_register_elementor_widgets');

/**
 * Register the IcalcElementorWidget with Elementor.
 */
function icalc_register_elementor_widgets() {
	if (defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')) {
		require_once(ICALC_PATH . '/integration/IcalcElementorWidget.php');
		$widget_manager = \Elementor\Plugin::$instance->widgets_manager;
		$widget_manager->register(new IcalcElementorWidget());

		wp_enqueue_script( 'icalc_pages_scripts', plugins_url( '/scripts/icalc_pages.js', ICALC_FILE ), array(), ICALC_VERSION, false );
		add_action('wp_enqueue_scripts', 'icalc_pages_scripts');
	}
}