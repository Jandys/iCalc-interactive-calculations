<?php

/**
 * prefix mapping = /wp-json/icalc/v1/
 */


add_action( 'rest_api_init', 'iclac_plugin_add_endpoints' );

function iclac_plugin_add_endpoints() {
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