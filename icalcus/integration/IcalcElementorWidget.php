<?php
/*
 *
 *   This file is part of the 'iCalcus - Interactive Calculations' project.
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

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * IcalcusElementorWidget class - defines an Elementor widget called Icalcus Custom Widget
 */
class IcalcusElementorWidget extends Widget_Base
{

    /**
     * Returns the name of the widget
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'icalcus-elementor-widget';
    }

    /**
     * Returns the title of the widget
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return __('Icalcus Custom Widget', 'icalcus');
    }

    /**
     * Returns the title of the widget
     *
     * @return string Widget title
     */
    public function get_icon()
    {
        return 'eicon-posts-group';
    }

    /**
     * Returns an array of keywords associated with the widget
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['calculation', 'icalcus', 'i-calc', 'calcus'];
    }

    /**
     * Returns the categories this widget belongs to
     *
     * @return array Widget categories
     */
    public function get_categories()
    {
        return ['general'];
    }

    /**
     * Defines the configuration settings for the widget
     *
     * @return void
     */
    protected function _register_controls()
    {
        $this->start_controls_section(
            'configuration',
            [
                'label' => __('Configuration', 'icalcus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'selected_calculation',
            [
                'label' => __('Select Calculation', 'icalcus'),
                'type' => Controls_Manager::SELECT,
                'options' => \icalcus\fe\Calculation::getConfiguredCalculationAsOptions(),
                'default' => 'default_icalcus_option0',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Renders the output for the widget
     *
     * @return void
     */
    protected function render()
    {
	    session_write_close();
	    $settings = $this->get_settings_for_display();

        $selectedCalc = $settings['selected_calculation'];
        if ($selectedCalc === 'default_icalcus_option0') {
            echo '<p>'.__('No calculation selected',"icalcus").'</p>';
            return;
        }

        $calculation = new \icalcus\fe\Calculation($selectedCalc);
        echo $calculation->render();
    }
}