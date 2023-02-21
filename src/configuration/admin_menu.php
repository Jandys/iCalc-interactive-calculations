<?php


use icalc\db\DBConnector;

add_action('admin_menu', 'my_admin_menu');

function my_admin_menu()
{
    add_menu_page('Calcus',
        'Inter Calcus',
        'manage_options',
        'my-admin-menu',
        'my_admin_menu_main',
        'dashicons-schedule',
        4);
    add_submenu_page('my-admin-menu',
            'Inter Calcus - Products',
        'IC - Products',
        'manage_options',
        'ic-products-configuration',
        'ic_menu_products_configuration');

}


function my_admin_menu_main()
{
    console_log("inside menu");

    echo '<div class="wrap">
<h2>Inter Calcus</h2>
 

</div>';
}

function ic_menu_products_configuration(){
    console_log("inside menu");

    $db = new DBConnector();
    $additionalSettings =
        '(id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(50),
        email VARCHAR(50),
        PRIMARY KEY (id)
        );';

    echo $db->createTable("icalc",$additionalSettings);

    echo '<div class="wrap">
<h2>InterCalc submenu</h2>
 

</div>';
}