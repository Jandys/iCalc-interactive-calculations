<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class IcalcElementorWidget extends Widget_Base {

	public function get_name() {
		return 'icalc-elementor-widget';
	}

	public function get_title() {
		return __( 'Icalc Custom Widget', 'icalc' );
	}

	public function get_icon() {
		return 'fa fa-calculator';
	}

	public function get_keywords() {
		return [ 'calculation', 'icalc', 'i-calc', 'calcus' ];
	}

	public function get_categories() {
		return [ 'general' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'configuration',
			[
				'label' => __( 'Configuration', 'icalc' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'selected_calculation',
			[
				'label'   => __( 'Select Calculation', 'icalc' ),
				'type'    => Controls_Manager::SELECT,
				'options' => \icalc\fe\Calculation::getConfiguredCalculationAsOptions(),
				'default' => 'default_icalc_option0',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$selectedCalc = $settings['selected_calculation'];
		if ( $selectedCalc === 'default_icalc_option0' ) {
			echo '<p>No calculation selected</p>';
			return;
		}

		$calculation = new \icalc\fe\Calculation( $selectedCalc );

		echo $calculation->render();
	}


}