<?php

/**
 * prefix mapping = /wp-json/icalc/v1/
 */


add_action( 'rest_api_init', 'icalc_plugin_add_tag_endpoints');
add_action( 'rest_api_init', 'icalc_plugin_add_service_endpoints');
add_action( 'rest_api_init', 'icalc_autocomplete_endpoints');



function icalc_plugin_add_service_endpoints() {
    register_rest_route( ICALC_EP_PREFIX, '/services', array(
        'methods' => 'POST',
        'callback' => 'icalc_postService',
        'permission_callback' => '__return_true'
    ) );

    register_rest_route( ICALC_EP_PREFIX, '/services', array(
        'methods' => 'GET',
        'callback' => 'icalc_getServices',
        'permission_callback' => '__return_true'
    ) );

    register_rest_route( ICALC_EP_PREFIX, '/services', array(
        'methods' => 'PUT',
        'callback' => 'icalc_putService',
        'permission_callback' => '__return_true'
    ) );


    register_rest_route( ICALC_EP_PREFIX, '/services', array(
        'methods' => 'DELETE',
        'callback' => 'icalc_deleteService',
        'permission_callback' => '__return_true'
    ) );
}

function icalc_plugin_add_tag_endpoints() {
    register_rest_route( ICALC_EP_PREFIX, '/tags', array(
        'methods' => 'POST',
        'callback' => 'icalc_postTag',
        'permission_callback' => '__return_true'
    ) );

    register_rest_route( ICALC_EP_PREFIX, '/tags', array(
        'methods' => 'GET',
        'callback' => 'icalc_getTags',
        'permission_callback' => '__return_true'
    ) );

    register_rest_route( ICALC_EP_PREFIX, '/tags', array(
        'methods' => 'PUT',
        'callback' => 'icalc_putTag',
        'permission_callback' => '__return_true'
    ) );


    register_rest_route( ICALC_EP_PREFIX, '/tags', array(
        'methods' => 'DELETE',
        'callback' => 'icalc_deleteTag',
        'permission_callback' => '__return_true'
    ) );
}


function icalc_autocomplete_endpoints() {
    register_rest_route( ICALC_EP_PREFIX, '/autocomplete/unit', array(
        'methods' => 'POST',
        'callback' => 'icalc_autocompleteUnit',
        'permission_callback' => '__return_true'
    ) );
}


/**
 * POST /autocomplete/id
 */
function icalc_autocompleteUnit( WP_REST_Request $request){

    $data = $request->get_json_params();
    $value = $data["value"];

    return \icalc\db\model\Unit::autocomplete($value);

}


/**
 * POST /services
 */
function icalc_postService( WP_REST_Request $request){

    $data = $request->get_json_params();
    $name = $data['name'];
    $desc = $data['description'];
    $price = $data['price'];
    $unit = $data['unit'];
    $minQuality = $data['minQuality'];
    $tag = $data['tag'];
    $displayType = $data['displayType'];

    $succes = \icalc\db\model\Service::insertNew($name,$desc,$price,$unit,$tag,$minQuality,$displayType);

    return new WP_REST_Response($succes);
}


/**
 * PUT /services
 */
function icalc_putService( WP_REST_Request $request  ){

    $data = $request->get_json_params();
    $id = $data['id'];
    $name = $data['name'];
    $desc = $data['description'];
    $price = $data['price'];
    $unit = $data['unit'];
    $minQuality = $data['minQuality'];
    $tag = $data['tag'];
    $displayType = $data['displayType'];

    $succes = \icalc\db\model\Service::updateById($id,$name,$desc,$price,$unit,$tag,$minQuality,$displayType);

    return new WP_REST_Response($succes);
}


/**
 * GET /services
 */
function icalc_getServices(WP_REST_Request $request){
    $allServices = \icalc\db\model\Service::get_all();

    return new WP_REST_Response($allServices);
}

/**
 * DELETE /services
 */
function icalc_deleteService(WP_REST_Request $request){
    $data = $request->get_json_params();
    $id = $data['id'];

    $allServices = \icalc\db\model\Service::delete($id);

    return new WP_REST_Response($allServices);
}





/**
 * POST /tags
 */
function icalc_postTag( WP_REST_Request $request  ){

    $data = $request->get_json_params();
    $name = $data['name'];
    $desc = $data['description'];

    $succes = \icalc\db\model\Tag::insertNew($name,$desc);

    return new WP_REST_Response($succes);
}


/**
 * PUT /tags
 */
function icalc_putTag( WP_REST_Request $request  ){

    $data = $request->get_json_params();
    $name = $data['name'];
    $desc = $data['description'];
    $id = $data['id'];

    $succes = \icalc\db\model\Tag::updateById($id,$name,$desc);

    return new WP_REST_Response($succes);
}


/**
 * GET /tags
 */
function icalc_getTags(WP_REST_Request $request){
    $allTags = \icalc\db\model\Tag::get_all();

    return new WP_REST_Response($allTags);
}

/**
 * DELETE /tags
 */
function icalc_deleteTag(WP_REST_Request $request){
    $data = $request->get_json_params();
    $id = $data['id'];

    $allTags = \icalc\db\model\Tag::delete($id);

    return new WP_REST_Response($allTags);
}