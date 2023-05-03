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

use icalc\fe\Calculation;

function icalc_register_custom_widget()
{
    register_widget('Icalc_Wordpress_Widget');
}

add_theme_support('widgets-block-editor');
add_action('widgets_init', 'icalc_register_custom_widget');

class Icalc_Wordpress_Widget extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array(
            'classname' => 'icalc_widget',
            'description' => __("Icalc Widget for interactive calculations"),
        );
        parent::__construct('icalc_widget', 'Icalc Calculations Widget', $widget_ops);
    }


    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        // Display your custom content here
        $calculation = new Calculation($instance["calculationId"]);
        echo $calculation->render();

        echo $args['after_widget'];
    }


    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('ICalc Custom Widget', 'icalc');
        ?>
        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title:', 'text_domain'); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php

    }

    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }

}

?>