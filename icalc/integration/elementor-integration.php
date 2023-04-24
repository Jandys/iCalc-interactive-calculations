<?php

add_action('elementor/widgets/widgets_registered', 'icalc_register_elementor_widgets');

function icalc_register_elementor_widgets() {
	if (defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')) {
		require_once(ICALC_PATH . '/integration/IcalcElementorWidget.php');
		$widget_manager = \Elementor\Plugin::$instance->widgets_manager;
		$widget_manager->register(new IcalcElementorWidget());
	}
}