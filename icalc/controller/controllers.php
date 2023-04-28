<?php

/**
 * prefix mapping = /wp-json/icalc/v1/
 */


use function icalc\util\getPossibleCookieValue;

add_action('rest_api_init', 'icalc_plugin_add_service_endpoints');
add_action('rest_api_init', 'icalc_plugin_add_product_endpoints');
add_action('rest_api_init', 'icalc_plugin_add_icalculation_descriptions_endpoints');
add_action('rest_api_init', 'icalc_autocomplete_endpoints');
add_action('rest_api_init', 'icalc_plugin_add_jwt_endpoints');
add_action('rest_api_init', 'icalc_plugin_add_public_endpoints');


const NOT_AUTH_MSG = "Not authorized, token needed";
const NO_SESSION_MSG = "No such session found for given user.";


/**
 * Registers public REST API endpoints for the iCalc plugin.
 *
 * @return void
 * @since 1.0.0
 *
 */
function icalc_plugin_add_public_endpoints()
{
    /**
     * Registers a REST API route for retrieving a product by ID.
     *
     * @param string $id The ID of the product to retrieve.
     * @return WP_REST_Response|WP_Error The product data on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/products/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'icalc_getProductById',
        'args' => array( // Argument validation and sanitization.
            'id' => array(
                'validate_callback' => 'my_id_validate_callback',
            ),
        ),
        'permission_callback' => '__return_true'

    ));
    /**
     * Registers a REST API route for retrieving a service by ID.
     *
     * @param string $id The ID of the service to retrieve.
     * @return WP_REST_Response|WP_Error The service data on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/services/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'icalc_getServiceById',
        'args' => array( // Argument validation and sanitization.
            'id' => array(
                'validate_callback' => 'my_id_validate_callback',
            ),
        ),
        'permission_callback' => '__return_true'

    ));

    /**
     * Registers a REST API route for retrieving a calculation description by ID.
     *
     * @param string $id The ID of the calculation description to retrieve.
     * @return WP_REST_Response|WP_Error The calculation description data on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/icalculation-descriptions/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'icalc_getCalculationDescriptionById',
        'args' => array( // Argument validation and sanitization.
            'id' => array(
                'validate_callback' => 'my_id_validate_callback',
            ),
        ),
        'permission_callback' => '__return_true'

    ));
    /**
     * Registers a REST API route for registering a new calculation interaction.
     *
     * @param array $request The request data, including the calculation data and user information.
     * @return WP_REST_Response|WP_Error The interaction data on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/icalculations/interactions', array(
        'methods' => 'POST',
        'callback' => 'icalc_registerNewCalculationInteraction',
        'permission_callback' => '__return_true'
    ));
}

/**
 * Validation callback for validating an ID parameter in a REST API request.
 *
 * @param mixed $value The value of the parameter being validated.
 * @param WP_REST_Request $request The current REST API request object.
 * @param string $param The parameter name.
 *
 * @return boolean        Whether the value is a numeric value.
 * @since 1.0.0
 *
 */
function my_id_validate_callback($value, $request, $param)
{
    return is_numeric($value);
}

/**
 * Retrieves a product by ID from the database.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the ID parameter.
 * @return WP_REST_Response The product data on success, or a WP_Error object on failure.
 * @since 1.0.0
 */
function icalc_getProductById(WP_REST_Request $request)
{
    $id = $request->get_param('id');
    $product = \icalc\db\model\Product::get("id", $id);

    return new WP_REST_Response($product);
}

/**
 * Retrieves a service by ID from the database.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the ID parameter.
 * @return WP_REST_Response The service data on success, or a WP_Error object on failure.
 * @since 1.0.0
 */
function icalc_getServiceById(WP_REST_Request $request)
{
    $id = $request->get_param('id');
    $service = \icalc\db\model\Service::get("id", $id);

    return new WP_REST_Response($service);
}

/**
 * Retrieves an iCalculation description by ID from the database.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the ID parameter.
 * @return WP_REST_Response The iCalculation description data on success, or a WP_Error object on failure.
 * @since 1.0.0
 */
function icalc_getCalculationDescriptionById(WP_REST_Request $request)
{
    $id = $request->get_param('id');
    $icalcDescription = \icalc\db\model\IcalculationsDescription::get("id", $id);

    return new WP_REST_Response($icalcDescription);
}

/**
 * Registers a new calculation interaction in the database.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the calculation data and user information.
 * @return WP_REST_Response The interaction data on success, or a WP_Error object on failure.
 * @since 1.0.0
 */
function icalc_registerNewCalculationInteraction(WP_REST_Request $request)
{
    $data = $request->get_json_params();
    error_log("JSON BODY: " . json_encode($data));


    $calculationId = $data['calculationId'];
    $body = $data['body'];
    $user = $data['userId'];


    error_log("calculationId: " . json_encode($calculationId));
    error_log("body: " . json_encode($body));
    error_log("userId: " . json_encode($user));


    $status = \icalc\db\model\Icalculations::insertNew($calculationId, json_encode($body), $user);

    return new WP_REST_Response($status);
}


/**
 * Registers REST API endpoints for issuing and verifying JWT tokens.
 *
 * @return void
 * @since 1.0.0
 */
function icalc_plugin_add_jwt_endpoints()
{
    /**
     * Registers a REST API route for issuing a JWT token.
     *
     * @return WP_REST_Response|WP_Error The JWT token on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/token', array(
        'methods' => 'POST',
        'callback' => 'issue_jwt_token_callback',
        'permission_callback' => 'icalc_user_can_manage'

    ));

    /**
     * Registers a REST API route for verifying a JWT token.
     *
     * @return WP_REST_Response|WP_Error The verification status of the JWT token on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/token-verify', array(
        'methods' => 'POST',
        'callback' => 'verify_jwt_token_callback',
        'permission_callback' => '__return_true'

    ));
}

/**
 * Callback function for issuing a JWT token for a user.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the user and session information.
 * @return WP_REST_Response The JWT token on success, or a WP_Error object on failure.
 * @since 1.0.0
 */

function issue_jwt_token_callback(WP_REST_Request $request)
{
    $body = $request->get_json_params();
    $user = $body['user'];
    $session = $body['session'];
    $transSession = get_transient($session);

    if ($transSession != $user) {
        return new WP_REST_Response(NO_SESSION_MSG);
    }
    $token = issue_jwt_token($user, $session);

    return new WP_REST_Response(['token' => $token]);
}

/**
 * Callback function for verifying a JWT token.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the user, session, and token information in the request headers.
 * @return WP_REST_Response The verification status of the JWT token on success, or a WP_Error object on failure.
 * @since 1.0.0
 */
function verify_jwt_token_callback(WP_REST_Request $request)
{
    $user = $request->get_header('user');
    $session = $request->get_header('session');
    $token = $request->get_header('icalc-token');

    return new WP_REST_Response(['valid' => validate_jwt_token($token, $user, $session)]);
}

/**
 * Registers REST API endpoints for iCalculation descriptions.
 *
 * @return void
 * @since 1.0.0
 */
function icalc_plugin_add_icalculation_descriptions_endpoints()
{
    /**
     * Registers a REST API route for creating a new iCalculation description.
     *
     * @return WP_REST_Response|WP_Error The new iCalculation description on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/icalculation-descriptions', array(
        'methods' => 'POST',
        'callback' => 'icalc_postIcalculationDescriptions',
        'permission_callback' => '__return_true'
    ));

    /**
     * Registers a REST API route for retrieving all iCalculation descriptions.
     * @return WP_REST_Response|WP_Error The iCalculation descriptions on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/icalculation-descriptions', array(
        'methods' => 'GET',
        'callback' => 'icalc_getIcalculationDescriptions',
        'permission_callback' => '__return_true'
    ));

    /**
     * Registers a REST API route for updating an existing iCalculation description.
     *
     * @return WP_REST_Response|WP_Error The updated iCalculation description on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/icalculation-descriptions', array(
        'methods' => 'PUT',
        'callback' => 'icalc_putIcalculationDescriptions',
        'permission_callback' => '__return_true'
    ));


    /**
     * Registers a REST API route for deleting an iCalculation description.
     *
     * @return WP_REST_Response|WP_Error The deleted iCalculation description on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/icalculation-descriptions', array(
        'methods' => 'DELETE',
        'callback' => 'icalc_deleteIcalculationDescriptions',
        'permission_callback' => '__return_true'
    ));

    /**
     * Registers a REST API route for retrieving the next available iCalculation description ID.
     *
     * @return WP_REST_Response|WP_Error The next iCalculation description ID on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALC_EP_PREFIX, '/icalculation-descriptions/next', array(
        'methods' => 'GET',
        'callback' => 'icalc_getNextIcalculationDescriptionId',
        'permission_callback' => '__return_true'
    ));
}

/**
 * Callback function for creating a new iCalculation description.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the iCalculation description data in the request body and the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The new iCalculation description on success, or an error on failure.
 * @since 1.0.0
 *
 */
function icalc_postIcalculationDescriptions(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();

    $name = $data['title'];
    $desc = $data['configuration']['calculation-description'];

    // Save the iCalculation description to the database.
    $success = \icalc\db\model\IcalculationsDescription::insertNew($name, $desc, json_encode($data));

    return new WP_REST_Response($success);
}

/**
 * Callback function for updating an existing iCalculation description.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the updated iCalculation description data in the request body and the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The updated iCalculation description on success, or an error on failure.
 * @since 1.0.0
 */
function icalc_putIcalculationDescriptions(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $body = json_decode($data['body']);
    $id = $body->id;
    $name = $body->title;
    $desc = $body->configuration->{'calculation-description'};

    $success = \icalc\db\model\IcalculationsDescription::updateById($id, $name, $desc, json_encode($body));

    return new WP_REST_Response("success");
}

/**
 * Callback function for getting the next available iCalculation description ID.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The next available iCalculation description ID on success, or an error on failure.
 * @since 1.0.0
 */
function icalc_getNextIcalculationDescriptionId(WP_REST_Request $request)
{

    error_log("GET NEXT IDDDDDD");


    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $lastId = \icalc\db\model\IcalculationsDescription::last_id();

    return new WP_REST_Response($lastId + 1);
}

/**
 * Callback function for deleting an iCalculation description.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the ID of the iCalculation description to delete in the request body and the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The result of the deletion operation on success, or an error on failure.
 * @since 1.0.0
 */
function icalc_deleteIcalculationDescriptions(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];

    $result = \icalc\db\model\IcalculationsDescription::delete($id);

    return new WP_REST_Response($result);
}


/**
 * Callback function for getting all iCalculation descriptions.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the JWT token in the headers.
 * @return WP_REST_Response|WP_Error An array of all iCalculation descriptions on success, or an error on failure.
 * @since 1.0.0
 *
 */
function icalc_getIcalculationDescriptions(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    // Retrieve all iCalculation descriptions from the database using the `get_all()` function of the `IcalculationsDescription` model class.
    $allDescriptions = \icalc\db\model\IcalculationsDescription::get_all();

    return new WP_REST_Response($allDescriptions);
}


/**
 * Registers REST API endpoints for products.
 *
 * @return void
 * @since 1.0.0
 */
function icalc_plugin_add_product_endpoints()
{
    // Register the 'products' endpoint for creating a new product.
    register_rest_route(ICALC_EP_PREFIX, '/products', array(
        'methods' => 'POST',
        'callback' => 'icalc_postProduct',
        'permission_callback' => '__return_true'
    ));

    // Register the 'products' endpoint for retrieving all products.
    register_rest_route(ICALC_EP_PREFIX, '/products', array(
        'methods' => 'GET',
        'callback' => 'icalc_getProducts',
        'permission_callback' => '__return_true'
    ));

    // Register the 'products' endpoint for updating an existing product.
    register_rest_route(ICALC_EP_PREFIX, '/products', array(
        'methods' => 'PUT',
        'callback' => 'icalc_putProduct',
        'permission_callback' => '__return_true'
    ));

    // Register the 'products' endpoint for deleting an existing product.
    register_rest_route(ICALC_EP_PREFIX, '/products', array(
        'methods' => 'DELETE',
        'callback' => 'icalc_deleteProduct',
        'permission_callback' => '__return_true'
    ));
}


/**
 * Creates a new product.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The REST API response object.
 * @since 1.0.0
 */
function icalc_postProduct(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $name = $data['name'];
    $desc = $data['description'];
    $price = $data['price'];
    $unit = $data['unit'];
    $minQuality = $data['minQuality'];
    $displayType = $data['displayType'];

    // Insert the new product into the database.
    $succes = \icalc\db\model\Product::insertNew($name, $desc, $price, $unit, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}


/**
 * Updates a product in the database.
 *
 * @param WP_REST_Request $request The REST request object.
 * @return WP_REST_Response The REST response object.
 * @since 1.0.0
 */
function icalc_putProduct(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];
    $name = $data['name'];
    $desc = $data['description'];
    $price = $data['price'];
    $unit = $data['unit'];
    $minQuality = $data['minQuality'];
    $displayType = $data['displayType'];

    $succes = \icalc\db\model\Product::updateById($id, $name, $desc, $price, $unit, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}


/**
 * Retrieves all products from the database.
 *
 * @param WP_REST_Request $request The REST request object.
 * @return WP_REST_Response Returns a response containing an array of all products retrieved from the database.
 * @since 1.0.0
 */
function icalc_getProducts(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $allProducts = \icalc\db\model\Product::get_all();

    return new WP_REST_Response($allProducts);
}

/**
 * Deletes an existing product from the database
 *
 * @param WP_REST_Request $request The REST request object
 * @return WP_REST_Response The REST response object
 * @since 1.0.0
 */
function icalc_deleteProduct(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];

    $allProducts = \icalc\db\model\Product::delete($id);

    return new WP_REST_Response($allProducts);
}

/**
 * Registers REST API endpoints for a service in a WordPress plugin.
 *
 * @return void
 */
function icalc_plugin_add_service_endpoints()
{
    // Registers a POST endpoint for creating a new service.
    register_rest_route(ICALC_EP_PREFIX, '/services', array(
        'methods' => 'POST',
        'callback' => 'icalc_postService',
        'permission_callback' => '__return_true'
    ));

    // Registers a GET endpoint for retrieving all services.
    register_rest_route(ICALC_EP_PREFIX, '/services', array(
        'methods' => 'GET',
        'callback' => 'icalc_getServices',
        'permission_callback' => '__return_true'
    ));

    // Registers a PUT endpoint for updating an existing service.
    register_rest_route(ICALC_EP_PREFIX, '/services', array(
        'methods' => 'PUT',
        'callback' => 'icalc_putService',
        'permission_callback' => '__return_true'
    ));

    // Registers a DELETE endpoint for deleting a service.
    register_rest_route(ICALC_EP_PREFIX, '/services', array(
        'methods' => 'DELETE',
        'callback' => 'icalc_deleteService',
        'permission_callback' => '__return_true'
    ));
}


function icalc_autocomplete_endpoints()
{
    register_rest_route(ICALC_EP_PREFIX, '/autocomplete/unit', array(
        'methods' => 'POST',
        'callback' => 'icalc_autocompleteUnit',
        'permission_callback' => '__return_true'
    ));
}


/**
 * POST /autocomplete/id
 */
function icalc_autocompleteUnit(WP_REST_Request $request)
{
    $data = $request->get_json_params();
    $value = $data["value"];

    return \icalc\db\model\Unit::autocomplete($value);
}


/**
 * Callback function to handle POST requests to create a new service in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The response containing the newly created service.
 * @since 1.0.0
 */
function icalc_postService(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $name = $data['name'];
    $desc = $data['description'];
    $price = $data['price'];
    $unit = $data['unit'];
    $minQuality = $data['minQuality'];
    $displayType = $data['displayType'];

    $succes = \icalc\db\model\Service::insertNew($name, $desc, $price, $unit, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}


/**
 * Callback function to handle PUT requests to update an existing service in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The response containing the updated service.
 * @since 1.0.0
 */
function icalc_putService(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }
    $data = $request->get_json_params();

    $id = $data['id'];
    $name = $data['name'];
    $desc = $data['description'];
    $price = $data['price'];
    $unit = $data['unit'];
    $minQuality = $data['minQuality'];
    $displayType = $data['displayType'];

    $succes = \icalc\db\model\Service::updateById($id, $name, $desc, $price, $unit, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}

/**
 * Callback function to handle GET requests to retrieve all services in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The response containing all services.
 * @since 1.0.0
 */
function icalc_getServices(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $allServices = \icalc\db\model\Service::get_all();

    return new WP_REST_Response($allServices);
}

/**
 * Callback function to handle DELETE requests to delete a specific service in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The response containing the result of the deletion.
 * @since 1.0.0
 */
function icalc_deleteService(WP_REST_Request $request)
{
    $validated = validate_icalc_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];

    $allServices = \icalc\db\model\Service::delete($id);

    return new WP_REST_Response($allServices);
}

/**
 * Validates a iCalc JWT token for a given REST API request in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return bool|WP_REST_Response Returns true if the token is valid, a WP_REST_Response if it's invalid, or false if it's not present.
 * @since 1.0.0
 */
function validate_icalc_jwt_token(WP_REST_Request $request)
{
    $user = $request->get_header('user');
    $session = $request->get_header('session');
    $token = $request->get_header('icalc-token');

    if (empty($token)) {
        $token = getPossibleCookieValue($request, 'icalc-token');
    }

    return validate_jwt_token($token, $user, $session);
}

/**
 * Checks if a user has the capability to manage options in WordPress.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return bool Returns true if the user has the capability, false otherwise.
 * @since 1.0.0
 */
function icalc_user_can_manage(WP_REST_Request $request)
{
    $body = $request->get_json_params();
    $user = $body['user'];

    return user_can($user, 'manage_options');
}