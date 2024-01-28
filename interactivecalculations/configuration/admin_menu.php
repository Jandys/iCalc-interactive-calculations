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

use interactivecalculations\fe\MainMenuFrontend;
use interactivecalculations\fe\ProductAdminFrontend;
use interactivecalculations\fe\ServiceAdminFrontend;
use interactivecalculations\fe\StatisticsAdminFrontend;

add_action('admin_menu', 'interactivecalculations_admin_menu');
add_action('admin_init', 'interactivecalculations_set_cookie');

const configurationSites = array(
    'inter-calc-configuration',
    'ic-products-configuration',
    'ic-services-configuration',
    'ic-menu-statistics'
);

global $settingCookie;
$settingCookie = false;


/**
 * Sets an authentication cookie for the interactivecalculations plugin in the admin area.
 *
 * This function checks if the current user has the 'manage_options' capability.
 * If true, it creates or updates a transient based on the current session token and user ID.
 * Then, it ensures a valid token is set and not expired. If the token is missing or expired,
 * it requests a new token from the interactivecalculations API and sets it as a cookie.
 *
 * @return void
 * @global bool $settingCookie A flag to prevent multiple token requests during a single request.
 *
 * @since 1.0.0
 *
 */
function interactivecalculations_set_cookie()
{
    if (current_user_can('manage_options')) {
        $currentSession = wp_get_session_token();
        $transSession = get_transient($currentSession);
        if ($transSession === false || !wp_get_current_user()->ID !== $transSession) {
            set_transient($currentSession, wp_get_current_user()->ID);
        }
    }

    if (isset($_GET['page']) && in_array($_GET['page'], configurationSites)) {
        if (!isset($_COOKIE['interactivecalculations-expiration']) ||
            !isset($_COOKIE['interactivecalculations-token']) ||
            $_COOKIE['interactivecalculations-expiration'] <= time()) {
            $issued_jwt_token = interactivecalculations_issue_jwt_token(wp_get_current_user()->ID, wp_get_session_token());
            $expiration_time = time() + 3300; // Set the cookie to expire in 55 minutes
            setcookie(INTEACTIVECALCULATIONS_PREFIX . 'token', $issued_jwt_token, $expiration_time, '/');
            setcookie(INTEACTIVECALCULATIONS_PREFIX . 'expiration', $expiration_time, $expiration_time, '/');
        }
    }
}

/**
 * Registers the Interactive Calculations plugin menu and submenu pages in the admin area.
 *
 * This function adds the main menu page for the Interactive Calculations plugin with the 'Calcus' title
 * and a set of submenu pages for Products, Services, and Statistics.
 *
 * @return void
 * @since 1.0.0
 *
 */
function interactivecalculations_admin_menu()
{
    $menuIcon = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="svg" width="400"
                     height="430.1507537688442" viewBox="0, 0, 400,430.1507537688442">
                    <g id="svgg">
                        <path id="path0"
                              d="M168.209 25.023 C 148.800 31.945,144.669 50.953,160.764 59.276 C 174.129 66.187,183.982 61.661,182.536 49.274 C 181.580 41.081,184.683 39.960,190.955 46.231 C 199.265 54.541,194.775 62.868,168.447 87.978 C 140.367 114.759,144.424 122.297,187.356 123.110 C 217.378 123.678,225.126 121.314,225.126 111.582 C 225.126 104.911,219.605 102.393,202.076 101.067 L 187.434 99.960 201.384 86.800 C 227.100 62.542,230.559 49.654,215.418 34.513 C 203.713 22.809,184.977 19.042,168.209 25.023 M260.972 27.638 C 254.514 34.629,253.910 36.340,248.053 64.277 C 239.047 107.231,242.118 113.692,268.619 107.546 C 290.754 102.413,295.477 103.646,295.477 114.559 C 295.477 119.789,306.792 128.643,313.476 128.643 C 321.500 128.643,324.130 124.387,323.313 112.722 C 322.739 104.520,323.248 102.549,326.131 101.797 C 331.596 100.371,330.710 90.850,324.864 88.186 C 320.615 86.250,319.925 84.232,318.696 70.145 C 316.041 39.703,298.141 39.639,295.965 70.064 L 294.923 84.626 286.657 84.022 L 278.392 83.417 278.421 70.352 C 278.436 63.166,279.706 50.762,281.241 42.788 C 285.263 21.907,274.026 13.508,260.972 27.638 M72.378 28.742 C 50.870 34.491,56.334 56.101,78.087 51.323 L 83.812 50.066 85.056 66.192 C 85.739 75.061,87.438 91.909,88.830 103.631 L 91.362 124.944 97.440 123.792 C 114.568 120.548,117.505 105.005,110.487 54.735 C 107.042 30.061,95.894 22.457,72.378 28.742 M176.560 130.975 C 141.510 140.516,136.343 192.129,168.795 208.543 C 202.233 225.456,237.294 195.248,227.047 158.354 C 221.536 138.508,197.255 125.342,176.560 130.975 M266.332 131.978 C 235.015 148.458,232.373 189.131,261.373 208.323 C 301.016 234.557,345.169 174.997,310.230 142.417 C 296.344 129.469,278.957 125.334,266.332 131.978 M77.387 133.397 C 58.254 142.270,48.856 169.794,58.050 190.033 C 76.378 230.382,136.777 216.108,136.650 171.457 C 136.566 141.791,104.162 120.981,77.387 133.397 M81.993 222.987 C 61.612 229.525,50.103 253.253,56.370 275.817 C 65.930 310.240,117.682 313.342,132.884 280.402 C 148.068 247.504,116.519 211.911,81.993 222.987 M169.849 224.795 C 131.324 243.102,144.491 304.154,186.893 303.826 C 228.378 303.505,244.035 250.705,209.635 227.134 C 199.970 220.511,181.188 219.407,169.849 224.795 M259.279 246.558 L 252.815 253.920 253.594 315.402 C 254.346 374.839,254.528 377.203,259.043 386.472 C 283.309 436.282,356.300 436.054,379.472 386.096 C 386.368 371.227,388.388 332.646,383.008 318.557 C 375.850 299.815,356.463 298.411,345.848 315.864 L 342.292 321.712 341.016 309.507 C 337.539 276.234,305.114 275.034,301.697 308.052 C 300.933 315.430,299.659 322.117,298.864 322.911 C 295.120 326.656,293.797 317.865,293.116 284.715 L 292.380 248.827 286.781 244.012 C 277.899 236.372,267.367 237.347,259.279 246.558 M82.892 312.231 C 67.304 317.884,54.258 335.052,54.283 349.881 C 54.350 391.599,105.929 409.607,129.556 376.162 C 152.121 344.219,119.169 299.074,82.892 312.231 M178.881 311.338 C 133.896 325.807,137.866 388.282,184.103 393.494 C 228.031 398.445,246.440 335.681,206.967 315.543 C 197.885 310.910,185.826 309.104,178.881 311.338 "
                              stroke="none" fill="black" fill-rule="evenodd"/>
                    </g>
                </svg>';


    add_menu_page(
        __('Interactive Calculations'),
        __('Interactive Calculations'),
        'manage_options',
        'interactivecalculations-configuration',
        'interactivecalculations_main_configuration',
        'data:image/svg+xml;base64,' . base64_encode($menuIcon),
        26);
    add_submenu_page('inter-calc-configuration',
        __('Products - Interactive Calculations'),
        __('Interactive Calculations - Products'),
        'manage_options',
        'ic-products-configuration',
        'interactivecalculations_menu_products_configuration');
    add_submenu_page('inter-calc-configuration',
        __('Services - Interactive Calculations'),
        __('Interactive Calculations - Services'),
        'manage_options',
        'ic-services-configuration',
        'interactivecalculations_menu_services_configuration');
    add_submenu_page('inter-calc-configuration',
        __('Statistics - Interactive Calculations'),
        __('Interactive Calculations - Statistics'),
        'manage_options',
        'ic-menu-statistics',
        'interactivecalculations_menu_statistics');
}

/**
 * Displays the main configuration page for the Interactive Calculations plugin in the admin area.
 *
 * This function checks if the current user is an administrator, then enqueues the required
 * styles and scripts for the Interactive Calculations plugin. It also initializes the database and
 * displays the main configuration menu using the MainMenuFrontend class.
 *
 * @return void
 * @since 1.0.0
 *
 */
function interactivecalculations_main_configuration()
{
    if (is_admin()) {
        wp_enqueue_style('interactivecalculations_main-styles', plugins_url('../styles/interactivecalculations-main-sheetstyle.css', __FILE__), array(), INTERACTIVECALCULATIONS_VERSION, false);
        add_action('wp_enqueue_style', 'interactivecalculations_main-styles');

        echo '<div class="wrap">
        <h2>' . esc_html(__("Interactive Calculations Menu", "interactivecalculations")) . '</h2>';
        MainMenuFrontend::configuration();
        echo '</div>';

        interactivecalculations_main_script_localization();

    }
}

function interactivecalculations_main_script_localization()
{
    wp_enqueue_script('interactivecalculations_main-script', plugins_url('../scripts/interactivecalculations_main.js', __FILE__), array(), INTERACTIVECALCULATIONS_VERSION, false);
    $localization_data = array(
        'id' => __("ID", "interactivecalculations"),
        'name' => __("Name", "interactivecalculations"),
        'description' => __("Description", "interactivecalculations"),
        'pricePerUnit' => __("Price per Unit", "interactivecalculations"),
        'unit' => __("Unit", "interactivecalculations"),
        'minQuantity' => __("Minimal Quantity", "interactivecalculations"),
        'displayType' => __("Display Type", "interactivecalculations"),
        'createNewCalc' => __("Create New Calculation", "interactivecalculations"),
        'newCalcTitle' => __("New Calculation title", "interactivecalculations"),
        'editCurrentConfig' => __("Edit Current Configuration", "interactivecalculations"),
        'saveCalc' => __("Save Calculation", "interactivecalculations"),
        'calcList' => __("Calculation List", "interactivecalculations"),
        'calcName' => __("Calculation Name", "interactivecalculations"),
        'product' => __("Product", "interactivecalculations"),
        'service' => __("Service", "interactivecalculations"),
        'genericComp' => __("Generic Component", "interactivecalculations"),
        'calcComp' => __("Calculation Component", "interactivecalculations"),
        'preview' => __("Preview", "interactivecalculations"),
        'none' => __("-- None --", "interactivecalculations"),
        'showLabel' => __("Show Label", "interactivecalculations"),
        'customLabel' => __("Custom Label", "interactivecalculations"),
        'labelClasses' => __("Label Classes", "interactivecalculations"),
        'inputClasses' => __("Input Classes", "interactivecalculations"),
        'toAddMultipleClassesText' => __("To add multiple classes separate them by using semicolon: ';'", "interactivecalculations"),
        'customCss' => __("Custom CSS", "interactivecalculations"),
        'baseValue' => __("Base Value", "interactivecalculations"),
        'uncheckedValue' => __("Unchecked Value", "interactivecalculations"),
        'listOption' => __("Option", "interactivecalculations"),
        'listValue' => __("Value", "interactivecalculations"),
        'sliderMax' => __("Slider Max", "interactivecalculations"),
        'showValue' => __("Show Value", "interactivecalculations"),
        'sumPrefix' => __("Prefix", "interactivecalculations"),
        'sumSuffix' => __("Suffix", "interactivecalculations"),
        'subtractFromValue' => __("Original Value to Subtract from", "interactivecalculations"),
        'complexCalcConf' => __("Complex calculation configuration", "interactivecalculations"),
        'complexCalcAddComp' => __("Add Component Reference", "interactivecalculations"),
        'personalCustomization' => __("Personal Customization", "interactivecalculations"),
        'wrapperCustomClass' => __("Wrapper custom class", "interactivecalculations"),
        'calcDescription' => __("Calculation Description", "interactivecalculations"),
        'label' => __("Label", "interactivecalculations"),
        'text' => __("Text", "interactivecalculations"),
        'list' => __("List", "interactivecalculations"),
        'numberInput' => __("Number Input", "interactivecalculations"),
        'slider' => __("Slider", "interactivecalculations"),
        'checkBox' => __("Checkbox", "interactivecalculations"),
        'spacer' => __("Spacer", "interactivecalculations"),
        'horizontalRule' => __("Horizontal Rule", "interactivecalculations"),
        'sum' => __("Sum", "interactivecalculations"),
        'productCalculation' => __("Product Calculation", "interactivecalculations"),
        'subtractCalculation' => __("Subtract Calculation", "interactivecalculations"),
        'complexCalculation' => __("Complex Calculation", "interactivecalculations"),
        'errorNoServiceFound' => __("No Service found", "interactivecalculations"),
        'errorNoCalculationComponentFound' => __("No Calculation Component found", "interactivecalculations"),
        'errorNoGenericComponentFound' => __("No Generic Component found", "interactivecalculations"),
        'errorNoProductFound' => __("No Product found", "interactivecalculations"),
        'errorFillPreviousOptions' => __("Please fill previous option and value", "interactivecalculations"),
        'errorNoValidComponents' => __("There are no valid components to be saved to calculation", "interactivecalculations"),
        'defaultDescription' => __("Default calculation description", "interactivecalculations"),
    );
    wp_localize_script('interactivecalculations_main-script', 'interactivecalculationsMainScriptLocalization', $localization_data);

    add_action('wp_enqueue_scripts', 'interactivecalculations_main-script');

}

/**
 *
 * Displays the Product Menu configuration page in the WordPress admin area.
 *
 * If the user is an admin, the function initializes the database, outputs the HTML for the page title, and
 * calls the configuration method of the ProductAdminFrontend class to display the menu settings.
 *
 * @return void
 * @since 1.0.0
 */
function interactivecalculations_menu_products_configuration()
{
    session_write_close();
    if (is_admin()) {

        echo '<div class="wrap">
        <h2>' . __("Product Menu", "interactivecalculations") . '</h2>';

        ProductAdminFrontend::configuration();

        echo '</div>';
    }
}


/**
 *
 *Displays the Services Menu configuration page in the WordPress admin area.
 *
 * If the user is an admin, the function initializes the database, outputs the HTML for the page title, and
 * calls the configuration method of the ServiceAdminFrontend class to display the menu settings.
 * @return void
 * @since 1.0.0
 *
 */
function interactivecalculations_menu_services_configuration()
{
    session_write_close();
    if (is_admin()) {
        echo '<div class="wrap">
        <h2>' . __("Services Menu", "interactivecalculations") . '</h2>';
        ServiceAdminFrontend::configuration();
        echo '</div>';
    }
}

/**
 * Displays the Statistics Menu page in the WordPress admin area.
 *
 * If the user is an admin, the function initializes the database, outputs the HTML for the page title, and
 * calls the configuration method of the StatisticsAdminFrontend class to display the statistics data.
 *
 * @return void
 * @since 1.0.0
 */
function interactivecalculations_menu_statistics()
{
    if (is_admin()) {

        echo '<div class="wrap">
        <h2>' . __("Statistics Menu", "interactivecalculations") . '</h2>';

        StatisticsAdminFrontend::configuration();

        echo '</div>';
    }
}