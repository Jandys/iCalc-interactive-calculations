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

//require dependencies
require __DIR__ . '/vendor/autoload.php';

require_once ICALCUS_PATH . '/frontend/autoload.php';
require_once ICALCUS_PATH . '/util/utilities.php';
require_once ICALCUS_PATH . '/database/autoload.php';
require_once ICALCUS_PATH . '/controller/controllers.php';
require_once ICALCUS_PATH . '/frontend/displayTypes/autoload.php';
require_once ICALCUS_PATH . '/controller/icalcusJWT.php';
//require_once ICALCUS_PATH . '/integration/Icalcus_Wordpress_Widget.php';
require_once ICALCUS_PATH . '/integration/wordpress-integration.php';
require_once ICALCUS_PATH . '/integration/elementor-integration.php';

if (is_admin()) {
    require_once ICALCUS_PATH . '/configuration/admin_menu.php';
    require_once ICALCUS_PATH . '/frontend/admin-autoload.php';
}


// Hook into the 'plugins_loaded' action to make sure WordPress core functions are available.
add_action('plugins_loaded', 'generate_site_specific_secret_key');
