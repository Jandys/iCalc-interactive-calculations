<?php




add_action( 'rest_api_init', 'iclac_plugin_add_endpoints' );

function iclac_plugin_add_endpoints() {
    register_rest_route( ICALC_EP_PREFIX, '/products', array(
        'methods' => 'GET',
        'callback' => 'icalc_getProducts',
        'permission_callback' => '__return_true'
    ) );
}


function icalc_getProducts( $request ){
    $data = array(
        'products' => array(1,2,3,4,5),
    );


    return new WP_REST_Response($data);
}
