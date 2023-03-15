<?php


use function icalc\util\console_log;

add_action('admin_menu', 'ic_admin_menu');

function ic_admin_menu()
{
    add_menu_page('Calcus',
        'Inter Calcus',
        'manage_options',
        'inter-calc-configuration',
        'inter_calc_main_configuration',
        'dashicons-schedule',
        4);
    add_submenu_page('inter-calc-configuration',
            'Products - Inter Calcus',
        'IC - Products',
        'manage_options',
        'ic-products-configuration',
        'ic_menu_products_configuration');
    add_submenu_page('inter-calc-configuration',
            'Services - Inter Calcus',
        'IC - Services',
        'manage_options',
        'ic-services-configuration',
        'ic_menu_services_configuration');
    add_submenu_page('inter-calc-configuration',
        'Tags - Inter Calcus',
        'IC - Tags',
        'manage_options',
        'ic-tags-configuration',
        'ic_menu_tags_configuration');

}


function inter_calc_main_configuration()
{
    console_log(\icalc\db\DatabaseInit::init());

    console_log("inside menu");

    echo '<div class="wrap">
<h2>Inter Calcus</h2>
 

</div>';
}

function ic_menu_products_configuration(){
    console_log(\icalc\db\DatabaseInit::init());

    echo '<div class="wrap">
        <h2>InterCalc Products</h2>';

//    \icalc\fe\AdminFrontend::serviceConfiguration();

    echo '</div>';
}

function ic_menu_services_configuration(){
    console_log(\icalc\db\DatabaseInit::init());

    echo '<div class="wrap">
        <h2>InterCalc Services</h2>';

    \icalc\fe\ServiceAdminFrontend::configuration();

    echo '</div>';
}

function ic_menu_tags_configuration(){
    console_log(\icalc\db\DatabaseInit::init());


    echo '<div class="wrap">
        <h2>InterCalc Tags</h2>';

    \icalc\fe\TagAdminFrontend::configuration();

    echo '</div>';
}