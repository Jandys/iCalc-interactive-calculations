<?php

add_action( 'admin_bar_menu', 'my_admin_bar_menu', 50 );

function my_admin_bar_menu( $wp_admin_bar ) {

    $wp_admin_bar->add_menu(
        array(
            'id'    => 'ccb-admin-menu',
//            'title' => '<img class="ccb-icon-logo" src="' . CALC_URL . '/frontend/dist/img/ccb-logo.svg' . '"/>' . __( 'Cost Calculator', 'cost-calculator-builder' ), //phpcs:ignore
            'title' =>  __( 'Cost Calculator', 'cost-calculator-builder' ), //phpcs:ignore
            'href'  => get_admin_url( null, 'admin.php?page=cost_calculator_builder' ),
        )
    );
//
//    if ( defined( 'CCB_PRO_VERSION' ) ) {
//        $wp_admin_bar->add_menu(
//            array(
//                'parent' => 'ccb-admin-menu',
//                'id'     => 'ccb-admin-menu-items-orders',
//                'title'  => __( 'Orders', 'cost-calculator-builder' ),
//                'href'   => get_admin_url( null, 'admin.php?page=cost_calculator_orders' ),
//                'meta'   => array(
//                    'class' => 'ccb-admin-menu-item',
//                ),
//            )
//        );
//    }
//
//    $wp_admin_bar->add_menu(
//        array(
//            'parent' => 'ccb-admin-menu',
//            'id'     => 'ccb-admin-menu-items',
//            'title'  => __( 'Calculators', 'cost-calculator-builder' ),
//            'href'   => get_admin_url( null, 'admin.php?page=cost_calculator_builder' ),
//            'meta'   => array(
//                'class' => 'ccb-admin-menu-item',
//            ),
//        )
//    );
//
//    $wp_admin_bar->add_menu(
//        array(
//            'parent' => 'ccb-admin-menu',
//            'id'     => 'ccb-admin-menu-items-create',
//            'title'  => __( 'Create Calculator', 'cost-calculator-builder' ),
//            'href'   => get_admin_url( null, 'admin.php?page=cost_calculator_builder&create-calc-from-menu=1' ),
//            'meta'   => array(
//                'class' => 'ccb-admin-menu-item',
//            ),
//        )
//    );
//
//    $wp_admin_bar->add_menu(
//        array(
//            'parent' => 'ccb-admin-menu',
//            'id'     => 'ccb-admin-menu-items-settings',
//            'title'  => __( 'Settings', 'cost-calculator-builder' ),
//            'href'   => get_admin_url( null, 'admin.php?page=cost_calculator_builder&tab=settings' ),
//            'meta'   => array(
//                'class' => 'ccb-admin-menu-item',
//            ),
//        )
//    );
//
//    $wp_admin_bar->add_menu(
//        array(
//            'parent' => 'ccb-admin-menu',
//            'id'     => 'ccb-admin-menu-items-community',
//            'title'  => __( 'Community', 'cost-calculator-builder' ),
//            'href'   => 'https://www.facebook.com/groups/costcalculator',
//            'meta'   => array(
//                'class'  => 'ccb-admin-menu-item',
//                'target' => '_blank',
//            ),
//        )
//    );
//
//    $wp_admin_bar->add_menu(
//        array(
//            'parent' => 'ccb-admin-menu',
//            'id'     => 'ccb-admin-menu-items-documentation',
//            'title'  => __( 'Documentation', 'cost-calculator-builder' ),
//            'href'   => 'https://docs.stylemixthemes.com/cost-calculator-builder/',
//            'meta'   => array(
//                'class'  => 'ccb-admin-menu-item',
//                'target' => '_blank',
//            ),
//        )
//    );
//
//    if ( ! defined( 'CCB_PRO_VERSION' ) ) {
//        $wp_admin_bar->add_menu(
//            array(
//                'parent' => 'ccb-admin-menu',
//                'id'     => 'ccb-admin-menu-items-upgrade',
//                'title'  => __( 'Upgrade', 'cost-calculator-builder' ),
//                'href'   => get_admin_url( null, 'admin.php?page=cost_calculator_gopro' ),
//                'meta'   => array(
//                    'class' => 'ccb-admin-menu-item',
//                ),
//            )
//        );
//    }
}
