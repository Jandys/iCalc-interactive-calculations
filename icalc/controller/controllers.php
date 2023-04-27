<?php

/**
 * prefix mapping = /wp-json/icalc/v1/
 */


use function icalc\util\getPossibleCookieValue;

add_action( 'rest_api_init', 'icalc_plugin_add_service_endpoints' );
add_action( 'rest_api_init', 'icalc_plugin_add_product_endpoints' );
add_action( 'rest_api_init', 'icalc_plugin_add_icalculation_descriptions_endpoints' );
add_action( 'rest_api_init', 'icalc_autocomplete_endpoints' );
add_action( 'rest_api_init', 'icalc_plugin_add_jwt_endpoints' );
add_action( 'rest_api_init', 'icalc_plugin_add_public_endpoints' );


const NOT_AUTH_MSG   = "Not authorized, token needed";
const NO_SESSION_MSG = "No such session found for given user.";


function icalc_plugin_add_public_endpoints() {
	register_rest_route( ICALC_EP_PREFIX, '/products/(?P<id>\d+)', array(
		'methods'             => 'GET',
		'callback'            => 'icalc_getProductById',
		'args'                => array( // Argument validation and sanitization.
			'id' => array(
				'validate_callback' => 'my_id_validate_callback',
			),
		),
		'permission_callback' => '__return_true'

	) );
	register_rest_route( ICALC_EP_PREFIX, '/services/(?P<id>\d+)', array(
		'methods'             => 'GET',
		'callback'            => 'icalc_getServiceById',
		'args'                => array( // Argument validation and sanitization.
			'id' => array(
				'validate_callback' => 'my_id_validate_callback',
			),
		),
		'permission_callback' => '__return_true'

	) );

	register_rest_route( ICALC_EP_PREFIX, '/icalculation-descriptions/(?P<id>\d+)', array(
		'methods'             => 'GET',
		'callback'            => 'icalc_getCalculationDescriptionById',
		'args'                => array( // Argument validation and sanitization.
			'id' => array(
				'validate_callback' => 'my_id_validate_callback',
			),
		),
		'permission_callback' => '__return_true'

	) );

	register_rest_route( ICALC_EP_PREFIX, '/icalculations/interactions', array(
		'methods'             => 'POST',
		'callback'            => 'icalc_registerNewCalculationInteraction',
		'permission_callback' => '__return_true'
	) );
}

function my_id_validate_callback( $value, $request, $param ) {
	return is_numeric( $value );
}

function icalc_getProductById( WP_REST_Request $request ) {
	$id      = $request->get_param( 'id' );
	$product = \icalc\db\model\Product::get( "id", $id );

	return new WP_REST_Response( $product );
}


function icalc_getServiceById( WP_REST_Request $request ) {
	$id      = $request->get_param( 'id' );
	$service = \icalc\db\model\Service::get( "id", $id );

	return new WP_REST_Response( $service );
}

function icalc_getCalculationDescriptionById( WP_REST_Request $request ) {
	$id               = $request->get_param( 'id' );
	$icalcDescription = \icalc\db\model\IcalculationsDescription::get( "id", $id );

	return new WP_REST_Response( $icalcDescription );
}

function icalc_registerNewCalculationInteraction( WP_REST_Request $request ) {
	$data = $request->get_json_params();
	error_log( "JSON BODY: " . json_encode( $data ) );


	$calculationId = $data['calculationId'];
	$body          = $data['body'];
	$user          = $data['userId'];


	error_log( "calculationId: " . json_encode( $calculationId ) );
	error_log( "body: " . json_encode( $body ) );
	error_log( "userId: " . json_encode( $user ) );


	$status = \icalc\db\model\Icalculations::insertNew( $calculationId, json_encode( $body ), $user );

	return new WP_REST_Response( $status );
}


function icalc_plugin_add_jwt_endpoints() {
	register_rest_route( ICALC_EP_PREFIX, '/token', array(
		'methods'             => 'POST',
		'callback'            => 'issue_jwt_token_callback',
		'permission_callback' => 'icalc_user_can_manage'

	) );

	register_rest_route( ICALC_EP_PREFIX, '/token-verify', array(
		'methods'             => 'POST',
		'callback'            => 'verify_jwt_token_callback',
		'permission_callback' => '__return_true'

	) );
}

function issue_jwt_token_callback( WP_REST_Request $request ) {
	$body         = $request->get_json_params();
	$user         = $body['user'];
	$session      = $body['session'];
	$transSession = get_transient( $session );

	if ( $transSession != $user ) {
		return new WP_REST_Response( NO_SESSION_MSG );
	}
	$token = issue_jwt_token( $user, $session );

	return new WP_REST_Response( [ 'token' => $token ] );
}

function verify_jwt_token_callback( WP_REST_Request $request ) {
	$user    = $request->get_header( 'user' );
	$session = $request->get_header( 'session' );
	$token   = $request->get_header( 'icalc-token' );

	return new WP_REST_Response( [ 'valid' => validate_jwt_token( $token, $user, $session ) ] );

}


function icalc_plugin_add_icalculation_descriptions_endpoints() {
	register_rest_route( ICALC_EP_PREFIX, '/icalculation-descriptions', array(
		'methods'             => 'POST',
		'callback'            => 'icalc_postIcalculationDescriptions',
		'permission_callback' => '__return_true'
	) );

	register_rest_route( ICALC_EP_PREFIX, '/icalculation-descriptions', array(
		'methods'             => 'GET',
		'callback'            => 'icalc_getIcalculationDescriptions',
		'permission_callback' => '__return_true'
	) );

	register_rest_route( ICALC_EP_PREFIX, '/icalculation-descriptions', array(
		'methods'             => 'PUT',
		'callback'            => 'icalc_putIcalculationDescriptions',
		'permission_callback' => '__return_true'
	) );


	register_rest_route( ICALC_EP_PREFIX, '/icalculation-descriptions', array(
		'methods'             => 'DELETE',
		'callback'            => 'icalc_deleteIcalculationDescriptions',
		'permission_callback' => '__return_true'
	) );

	register_rest_route( ICALC_EP_PREFIX, '/icalculation-descriptions/next', array(
		'methods'             => 'GET',
		'callback'            => 'icalc_getNextIcalculationDescriptionId',
		'permission_callback' => '__return_true'
	) );
}


/**
 * POST /icalculation-descriptions
 */
function icalc_postIcalculationDescriptions( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$data = $request->get_json_params();

	$name = $data['title'];
	$desc = $data['configuration']['calculation-description'];


	error_log( "SAVE DESC" );
	error_log( "NAME $name" );
	error_log( "DESC $desc" );
	error_log( "Body " . json_encode( $data ) );


	$success = \icalc\db\model\IcalculationsDescription::insertNew( $name, $desc, json_encode( $data ) );

	return new WP_REST_Response( $success );
}

/**
 * PUT /icalculation-descriptions
 */
function icalc_putIcalculationDescriptions( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$data = $request->get_json_params();
	$body = json_decode( $data['body'] );
	$id   = $body->id;
	$name = $body->title;
	$desc = $body->configuration->{'calculation-description'};

	$success = \icalc\db\model\IcalculationsDescription::updateById( $id, $name, $desc, json_encode( $body ) );

	return new WP_REST_Response( "success" );
}

/**
 * GET /icalculation-descriptions/next
 */
function icalc_getNextIcalculationDescriptionId( WP_REST_Request $request ) {

	error_log( "GET NEXT IDDDDDD" );


	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$lastId = \icalc\db\model\IcalculationsDescription::last_id();

	return new WP_REST_Response( $lastId + 1 );
}


/**
 * delete /icalculation-descriptions
 */
function icalc_deleteIcalculationDescriptions( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$data = $request->get_json_params();
	$id   = $data['id'];

	$result = \icalc\db\model\IcalculationsDescription::delete( $id );

	return new WP_REST_Response( $result );
}


/**
 * GET /icalculation-descriptions
 */
function icalc_getIcalculationDescriptions( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$allDescriptions = \icalc\db\model\IcalculationsDescription::get_all();

	return new WP_REST_Response( $allDescriptions );
}


function icalc_plugin_add_product_endpoints() {
	register_rest_route( ICALC_EP_PREFIX, '/products', array(
		'methods'             => 'POST',
		'callback'            => 'icalc_postProduct',
		'permission_callback' => '__return_true'
	) );

	register_rest_route( ICALC_EP_PREFIX, '/products', array(
		'methods'             => 'GET',
		'callback'            => 'icalc_getProducts',
		'permission_callback' => '__return_true'
	) );

	register_rest_route( ICALC_EP_PREFIX, '/products', array(
		'methods'             => 'PUT',
		'callback'            => 'icalc_putProduct',
		'permission_callback' => '__return_true'
	) );


	register_rest_route( ICALC_EP_PREFIX, '/products', array(
		'methods'             => 'DELETE',
		'callback'            => 'icalc_deleteProduct',
		'permission_callback' => '__return_true'
	) );
}


/**
 * POST /products
 */
function icalc_postProduct( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$data        = $request->get_json_params();
	$name        = $data['name'];
	$desc        = $data['description'];
	$price       = $data['price'];
	$unit        = $data['unit'];
	$minQuality  = $data['minQuality'];
	$displayType = $data['displayType'];

	$succes = \icalc\db\model\Product::insertNew( $name, $desc, $price, $unit, $minQuality, $displayType );

	return new WP_REST_Response( $succes );
}


/**
 * PUT /products
 */
function icalc_putProduct( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$data        = $request->get_json_params();
	$id          = $data['id'];
	$name        = $data['name'];
	$desc        = $data['description'];
	$price       = $data['price'];
	$unit        = $data['unit'];
	$minQuality  = $data['minQuality'];
	$displayType = $data['displayType'];

	$succes = \icalc\db\model\Product::updateById( $id, $name, $desc, $price, $unit, $minQuality, $displayType );

	return new WP_REST_Response( $succes );
}


/**
 * GET /products
 */
function icalc_getProducts( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$allProducts = \icalc\db\model\Product::get_all();

	return new WP_REST_Response( $allProducts );
}

/**
 * DELETE /products
 */
function icalc_deleteProduct( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$data = $request->get_json_params();
	$id   = $data['id'];

	$allProducts = \icalc\db\model\Product::delete( $id );

	return new WP_REST_Response( $allProducts );
}


function icalc_plugin_add_service_endpoints() {
	register_rest_route( ICALC_EP_PREFIX, '/services', array(
		'methods'             => 'POST',
		'callback'            => 'icalc_postService',
		'permission_callback' => '__return_true'
	) );

	register_rest_route( ICALC_EP_PREFIX, '/services', array(
		'methods'             => 'GET',
		'callback'            => 'icalc_getServices',
		'permission_callback' => '__return_true'
	) );

	register_rest_route( ICALC_EP_PREFIX, '/services', array(
		'methods'             => 'PUT',
		'callback'            => 'icalc_putService',
		'permission_callback' => '__return_true'
	) );


	register_rest_route( ICALC_EP_PREFIX, '/services', array(
		'methods'             => 'DELETE',
		'callback'            => 'icalc_deleteService',
		'permission_callback' => '__return_true'
	) );
}


function icalc_autocomplete_endpoints() {
	register_rest_route( ICALC_EP_PREFIX, '/autocomplete/unit', array(
		'methods'             => 'POST',
		'callback'            => 'icalc_autocompleteUnit',
		'permission_callback' => '__return_true'
	) );
}


/**
 * POST /autocomplete/id
 */
function icalc_autocompleteUnit( WP_REST_Request $request ) {
	$data  = $request->get_json_params();
	$value = $data["value"];

	return \icalc\db\model\Unit::autocomplete( $value );
}


/**
 * POST /services
 */
function icalc_postService( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$data        = $request->get_json_params();
	$name        = $data['name'];
	$desc        = $data['description'];
	$price       = $data['price'];
	$unit        = $data['unit'];
	$minQuality  = $data['minQuality'];
	$displayType = $data['displayType'];

	$succes = \icalc\db\model\Service::insertNew( $name, $desc, $price, $unit, $minQuality, $displayType );

	return new WP_REST_Response( $succes );
}


/**
 * PUT /services
 */
function icalc_putService( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}
	$data = $request->get_json_params();

	$id          = $data['id'];
	$name        = $data['name'];
	$desc        = $data['description'];
	$price       = $data['price'];
	$unit        = $data['unit'];
	$minQuality  = $data['minQuality'];
	$displayType = $data['displayType'];

	$succes = \icalc\db\model\Service::updateById( $id, $name, $desc, $price, $unit, $minQuality, $displayType );

	return new WP_REST_Response( $succes );
}


/**
 * GET /services
 */
function icalc_getServices( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$allServices = \icalc\db\model\Service::get_all();

	return new WP_REST_Response( $allServices );
}

/**
 * DELETE /services
 */
function icalc_deleteService( WP_REST_Request $request ) {
	$validated = validate_icalc_jwt_token( $request );
	if ( $validated instanceof WP_REST_Response ) {
		return $validated;
	}

	if ( ! $validated ) {
		return new WP_REST_Response( [ 'msg' => NOT_AUTH_MSG ], 401 );
	}

	$data = $request->get_json_params();
	$id   = $data['id'];

	$allServices = \icalc\db\model\Service::delete( $id );

	return new WP_REST_Response( $allServices );
}

function validate_icalc_jwt_token( WP_REST_Request $request ) {
	$user    = $request->get_header( 'user' );
	$session = $request->get_header( 'session' );
	$token   = $request->get_header( 'icalc-token' );

	if ( empty( $token ) ) {
		$token = getPossibleCookieValue( $request, 'icalc-token' );
	}

	return validate_jwt_token( $token, $user, $session );
}

function icalc_user_can_manage( WP_REST_Request $request ) {
	$body = $request->get_json_params();
	$user = $body['user'];

	return user_can( $user, 'manage_options' );
}