<?php


use function icalc\util\console_log;

add_action('admin_menu', 'ic_admin_menu');

function ic_admin_menu()
{
    add_menu_page(
        __('Calcus'),
        __('Inter Calcus'),
        'manage_options',
        'inter-calc-configuration',
        'inter_calc_main_configuration',
        'dashicons-schedule',
        4);
    add_submenu_page('inter-calc-configuration',
        __('Products - Inter Calcus'),
        __('IC - Products'),
        'manage_options',
        'ic-products-configuration',
        'ic_menu_products_configuration');
    add_submenu_page('inter-calc-configuration',
        __('Services - Inter Calcus'),
            __('IC - Services'),
        'manage_options',
        'ic-services-configuration',
        'ic_menu_services_configuration');
    add_submenu_page('inter-calc-configuration',
        __('Tags - Inter Calcus'),
        __('IC - Tags'),
        'manage_options',
        'ic-tags-configuration',
        'ic_menu_tags_configuration');
    add_submenu_page('inter-calc-configuration',
        __('Statistics - Inter Calcus'),
        __('IC - Statistics'),
        'manage_options',
        'ic-menu-statistics',
        'ic_menu_statistics');

}


function inter_calc_main_configuration()
{
    if(is_admin()) {


        wp_enqueue_style('icalc_main-styles',  plugins_url('../styles/icalc-main-sheetstyle.css', __FILE__), array(), '0.0.1', false);
        add_action('wp_enqueue_style', 'icalc_main-styles');

        console_log(\icalc\db\DatabaseInit::init());
        console_log("inside menu");


        echo '<div class="wrap">
        <h2>' . __("Inter Calcus Menu", "icalc") . '</h2>';
         

        \icalc\fe\MainMenuFrontend::configuration();

        echo '</div>';


        wp_enqueue_script('icalc_main-script', plugins_url('../scripts/icalc-main.js', __FILE__), array(), '0.0.1', false);
        add_action('wp_enqueue_scripts', 'icalc_main-script');

    }
}

function ic_menu_products_configuration(){
    if(is_admin()){

    console_log(\icalc\db\DatabaseInit::init());

    echo '<div class="wrap">
        <h2>'.__("Product Menu", "icalc").'</h2>';

    \icalc\fe\ProductAdminFrontend::configuration();

    echo '</div>';
    }

}

function ic_menu_services_configuration(){
    if(is_admin()){

    console_log(\icalc\db\DatabaseInit::init());

    echo '<div class="wrap">
        <h2>'.__("Services Menu", "icalc").'</h2>';

    \icalc\fe\ServiceAdminFrontend::configuration();

    echo '</div>';
    }

}

function ic_menu_tags_configuration(){
    if(is_admin()){

    console_log(\icalc\db\DatabaseInit::init());


    echo '<div class="wrap">
        <h2>'.__("Tags Menu", "icalc").'</h2>';

    \icalc\fe\TagAdminFrontend::configuration();

    echo '</div>';
    }
}


function ic_menu_statistics(){
    if(is_admin()){

    console_log(\icalc\db\DatabaseInit::init());


    echo '<div class="wrap">
        <h2>'.__("Statistics Menu", "icalc").'</h2>';

//    \icalc\fe\TagAdminFrontend::configuration();

        echo '</div>';
    }
}