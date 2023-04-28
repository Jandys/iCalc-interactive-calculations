<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * IcalcElementorWidget class - defines an Elementor widget called Icalc Custom Widget
 */
class IcalcElementorWidget extends Widget_Base {


    /**
     * Returns the name of the widget
     *
     * @return string Widget name
     */
	public function get_name() {
		return 'icalc-elementor-widget';
	}

    /**
     * Returns the title of the widget
     *
     * @return string Widget title
     */
	public function get_title() {
		return __( 'Icalc Custom Widget', 'icalc' );
	}

    /**
     * Returns the title of the widget
     *
     * @return string Widget title
     */
	public function get_icon() {
		return 'eicon-posts-group';
	}

    /**
     * Returns an array of keywords associated with the widget
     *
     * @return array Widget keywords
     */
	public function get_keywords() {
		return [ 'calculation', 'icalc', 'i-calc', 'calcus' ];
	}

    /**
     * Returns the categories this widget belongs to
     *
     * @return array Widget categories
     */
	public function get_categories() {
		return [ 'general' ];
	}

    /**
     * Defines the configuration settings for the widget
     *
     * @return void
     */
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

    /**
     * Renders the output for the widget
     *
     * @return void
     */
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