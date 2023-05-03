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

add_action('admin_bar_menu', 'my_admin_bar_menu', 50);

function my_admin_bar_menu($wp_admin_bar)
{

    $wp_admin_bar->add_menu(
        array(
            'id' => 'ccb-admin-menu',
            'title' => __('InterCalc', 'inter-calculator'),
            //phpcs:ignore
            'href' => get_admin_url(null, 'admin.php?page=inter-calc-configuration'),
        )
    );
}