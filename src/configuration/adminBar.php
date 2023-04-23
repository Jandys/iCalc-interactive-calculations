<?php

add_action( 'admin_bar_menu', 'my_admin_bar_menu', 50 );

function my_admin_bar_menu( $wp_admin_bar ) {

	$wp_admin_bar->add_menu(
		array(
			'id'    => 'ccb-admin-menu',
//            'title' => '<img class="ccb-icon-logo" src="' . CALC_URL . '/frontend/dist/img/ccb-logo.svg' . '"/>' . __( 'Cost Calculator', 'cost-calculator-builder' ), //phpcs:ignore
			'title' => __( 'InterCalc', 'inter-calculator' ),
			//phpcs:ignore
			'href'  => get_admin_url( null, 'admin.php?page=inter-calc-configuration' ),
		)
	);
}