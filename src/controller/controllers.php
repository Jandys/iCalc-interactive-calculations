<?php

/**
 * prefix mapping = /wp-json/icalc/v1/
 */


use function icalc\util\getPossibleCookieValue;

add_action('rest_api_init', 'icalc_plugin_add_tag_endpoints');
add_action('rest_api_init', 'icalc_plugin_add_service_endpoints');
add_action('rest_api_init', 'icalc_plugin_add_product_endpoints');
add_action('rest_api_init', 'icalc_plugin_add_icalculation_descriptions_endpoints');
add_action('rest_api_init', 'icalc_autocomplete_endpoints');
add_action('rest_api_init', 'icalc_plugin_add_jwt_endpoints');


const NOT_AUTH_MSG = "Not authorized, token needed";


function icalc_plugin_add_jwt_endpoints(){
    register_rest_route(ICALC_EP_PREFIX, '/token', array(
        'methods' => 'POST',
        'callback' => 'issue_jwt_token_callback',
        'permission_callback' => '__return_true'

    ));

    register_rest_route(ICALC_EP_PREFIX, '/token-verify', array(
        'methods' => 'POST',
        'callback' => 'verify_jwt_token_callback',
        'permission_callback' => '__return_true'

    ));
}

function issue_jwt_token_callback(WP_REST_Request $request)
{
    $body = $request->get_json_params();
    $user = $body['user'];
    $session = $body['session'];

    $token = issue_jwt_token($user,$session);
    return new WP_REST_Response(['token' => $token]);
}

function verify_jwt_token_callback(WP_REST_Request $request){
    $user = $request->get_header('user');
    $session = $request->get_header('session');
    $token = $request->get_header('icalc-token');

    return new WP_REST_Response(['valid' => validate_jwt_token($token,$user,$session)]);

}


function icalc_plugin_add_icalculation_descriptions_endpoints()
{
    register_rest_route(ICALC_EP_PREFIX, '/icalculation-descriptions', array(
        'methods' => 'POST',
        'callback' => 'icalc_postIcalculationDescriptions',
        'permission_callback' => '__return_true'
    ));

    register_rest_route(ICALC_EP_PREFIX, '/icalculation-descriptions', array(
        'methods' => 'GET',
        'callback' => 'icalc_getIcalculationDescriptions',
        'permission_callback' => '__return_true'
    ));

    register_rest_route(ICALC_EP_PREFIX, '/icalculation-descriptions', array(
        'methods' => 'PUT',
        'callback' => 'icalc_putIcalculationDescriptions',
        'permission_callback' => '__return_true'
    ));


    register_rest_route(ICALC_EP_PREFIX, '/icalculation-descriptions', array(
        'methods' => 'DELETE',
        'callback' => 'icalc_deleteIcalculationDescriptions',
        'permission_callback' => '__return_true'
    ));
}


/**
 * POST /products
 */
function icalc_getIcalculationDescriptions(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $allDescriptions = \icalc\db\model\IcalculationsDescription::get_all();
    return new WP_REST_Response($allDescriptions);
}


function icalc_plugin_add_product_endpoints()
{
    register_rest_route(ICALC_EP_PREFIX, '/products', array(
        'methods' => 'POST',
        'callback' => 'icalc_postProduct',
        'permission_callback' => '__return_true'
    ));

    register_rest_route(ICALC_EP_PREFIX, '/products', array(
        'methods' => 'GET',
        'callback' => 'icalc_getProducts',
        'permission_callback' => '__return_true'
    ));

    register_rest_route(ICALC_EP_PREFIX, '/products', array(
        'methods' => 'PUT',
        'callback' => 'icalc_putProduct',
        'permission_callback' => '__return_true'
    ));


    register_rest_route(ICALC_EP_PREFIX, '/products', array(
        'methods' => 'DELETE',
        'callback' => 'icalc_deleteProduct',
        'permission_callback' => '__return_true'
    ));
}


/**
 * POST /products
 */
function icalc_postProduct(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }
    
    $data = $request->get_json_params();
    $name = $data['name'];
    $desc = $data['description'];
    $price = $data['price'];
    $unit = $data['unit'];
    $minQuality = $data['minQuality'];
    $tag = $data['tag'];
    $displayType = $data['displayType'];

    $succes = \icalc\db\model\Product::insertNew($name, $desc, $price, $unit, $tag, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}


/**
 * PUT /products
 */
function icalc_putProduct(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];
    $name = $data['name'];
    $desc = $data['description'];
    $price = $data['price'];
    $unit = $data['unit'];
    $minQuality = $data['minQuality'];
    $tag = $data['tag'];
    $displayType = $data['displayType'];

    $succes = \icalc\db\model\Product::updateById($id, $name, $desc, $price, $unit, $tag, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}


/**
 * GET /products
 */
function icalc_getProducts(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $allProducts = \icalc\db\model\Product::get_all();
    return new WP_REST_Response($allProducts);
}

/**
 * DELETE /products
 */
function icalc_deleteProduct(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];

    $allProducts = \icalc\db\model\Product::delete($id);

    return new WP_REST_Response($allProducts);
}


function icalc_plugin_add_service_endpoints()
{
    register_rest_route(ICALC_EP_PREFIX, '/services', array(
        'methods' => 'POST',
        'callback' => 'icalc_postService',
        'permission_callback' => '__return_true'
    ));

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
function icalc_postService( WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $name = $data['name'];
    $desc = $data['description'];
    $price = $data['price'];
    $unit = $data['unit'];
    $minQuality = $data['minQuality'];
    $tag = $data['tag'];
    $displayType = $data['displayType'];

    $succes = \icalc\db\model\Service::insertNew($name, $desc, $price, $unit, $tag, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}


/**
 * PUT /services
 */
function icalc_putService( WP_REST_Request $request  )
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }
    $data = $request->get_json_params();
    
    $id = $data['id'];
    $name = $data['name'];
    $desc = $data['description'];
    $price = $data['price'];
    $unit = $data['unit'];
    $minQuality = $data['minQuality'];
    $tag = $data['tag'];
    $displayType = $data['displayType'];

    $succes = \icalc\db\model\Service::updateById($id, $name, $desc, $price, $unit, $tag, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}


/**
 * GET /services
 */
function icalc_getServices(WP_REST_Request $request){
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }
    
    $allServices = \icalc\db\model\Service::get_all();
    return new WP_REST_Response($allServices);
}

/**
 * DELETE /services
 */
function icalc_deleteService(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];

    $allServices = \icalc\db\model\Service::delete($id);

    return new WP_REST_Response($allServices);
}





/**
 * POST /tags
 */
function icalc_postTag( WP_REST_Request $request  )
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $name = $data['name'];
    $desc = $data['description'];

    $succes = \icalc\db\model\Tag::insertNew($name, $desc);

    return new WP_REST_Response($succes);

}


/**
 * PUT /tags
 */
function icalc_putTag( WP_REST_Request $request  )
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $name = $data['name'];
    $desc = $data['description'];
    $id = $data['id'];

    $succes = \icalc\db\model\Tag::updateById($id, $name, $desc);

    return new WP_REST_Response($succes);
}


/**
 * GET /tags
 */
function icalc_getTags(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }
    
    $allTags = \icalc\db\model\Tag::get_all();
    return new WP_REST_Response($allTags);
}

/**
 * DELETE /tags
 */
function icalc_deleteTag(WP_REST_Request $request)
{

    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if(!$validated){
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];

    $allTags = \icalc\db\model\Tag::delete($id);

    return new WP_REST_Response($allTags);

}

function validate_icalc_jwt_token(WP_REST_Request $request) {
    $user = $request->get_header('user');
    $session = $request->get_header('session');
    $token = $request->get_header('icalc-token');

    if(empty($token)){
        $token = getPossibleCookieValue($request,'icalc-token');
    }

    return validate_jwt_token($token,$user,$session);
}