<?php
/*
 *
 *   This file is part of the 'iCalcus - Interactive Calculations' project.
 *
 *   Copyright (C) 2023, Jakub Jandák
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 *
 */


/**
 * prefix mapping = /wp-json/icalcus/v1/
 */


use icalcus\db\model\Icalcusulations;
use icalcus\db\model\IcalcusulationsDescription;
use icalcus\db\model\Product;
use icalcus\db\model\Service;
use icalcus\db\model\Unit;
use function icalcus\util\getPossibleCookieValue;

add_action('rest_api_init', 'icalcus_plugin_add_service_endpoints');
add_action('rest_api_init', 'icalcus_plugin_add_product_endpoints');
add_action('rest_api_init', 'icalcus_plugin_add_icalcusulation_descriptions_endpoints');
add_action('rest_api_init', 'icalcus_autocomplete_endpoints');
add_action('rest_api_init', 'icalcus_plugin_add_jwt_endpoints');
add_action('rest_api_init', 'icalcus_plugin_add_public_endpoints');


const NOT_AUTH_MSG = "Not authorized, token needed";
const NO_SESSION_MSG = "No such session found for given user.";


/**
 * Registers public REST API endpoints for the icalcus plugin.
 *
 * @return void
 * @since 1.0.0
 *
 */
function icalcus_plugin_add_public_endpoints()
{
    /**
     * Registers a REST API route for retrieving a product by ID.
     *
     * @param string $id The ID of the product to retrieve.
     * @return WP_REST_Response|WP_Error The product data on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALCUS_EP_PREFIX, '/products/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'icalcus_getProductById',
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
    register_rest_route(ICALCUS_EP_PREFIX, '/services/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'icalcus_getServiceById',
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
    register_rest_route(ICALCUS_EP_PREFIX, '/icalcusulation-descriptions/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'icalcus_getCalculationDescriptionById',
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
    register_rest_route(ICALCUS_EP_PREFIX, '/icalcusulations/interactions', array(
        'methods' => 'POST',
        'callback' => 'icalcus_registerNewCalculationInteraction',
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
function icalcus_getProductById(WP_REST_Request $request)
{
    $id = $request->get_param('id');
    $product = Product::get("id", $id);

    return new WP_REST_Response($product);
}

/**
 * Retrieves a service by ID from the database.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the ID parameter.
 * @return WP_REST_Response The service data on success, or a WP_Error object on failure.
 * @since 1.0.0
 */
function icalcus_getServiceById(WP_REST_Request $request)
{
    $id = $request->get_param('id');
    $service = Service::get("id", $id);

    return new WP_REST_Response($service);
}

/**
 * Retrieves an icalcusulation description by ID from the database.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the ID parameter.
 * @return WP_REST_Response The icalcusulation description data on success, or a WP_Error object on failure.
 * @since 1.0.0
 */
function icalcus_getCalculationDescriptionById(WP_REST_Request $request)
{
    $id = $request->get_param('id');
    $icalcusDescription = IcalcusulationsDescription::get("id", $id);

    return new WP_REST_Response($icalcusDescription);
}

/**
 * Registers a new calculation interaction in the database.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the calculation data and user information.
 * @return WP_REST_Response The interaction data on success, or a WP_Error object on failure.
 * @since 1.0.0
 */
function icalcus_registerNewCalculationInteraction(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    $calculationId = $data['calculationId'];
    $body = $data['body'];
    $user = $data['userId'];

    $status = Icalcusulations::insertNew($calculationId, json_encode($body), $user);

    return new WP_REST_Response($status);
}


/**
 * Registers REST API endpoints for issuing and verifying JWT tokens.
 *
 * @return void
 * @since 1.0.0
 */
function icalcus_plugin_add_jwt_endpoints()
{
    /**
     * Registers a REST API route for issuing a JWT token.
     *
     * @return WP_REST_Response|WP_Error The JWT token on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALCUS_EP_PREFIX, '/token', array(
        'methods' => 'POST',
        'callback' => 'issue_jwt_token_callback',
        'permission_callback' => 'icalcus_user_can_manage'
    ));

    /**
     * Registers a REST API route for verifying a JWT token.
     *
     * @return WP_REST_Response|WP_Error The verification status of the JWT token on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALCUS_EP_PREFIX, '/token-verify', array(
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
    $token = $request->get_header('icalcus-token');

    return new WP_REST_Response(['valid' => validate_jwt_token($token, $user, $session)]);
}

/**
 * Registers REST API endpoints for icalcusulation descriptions.
 *
 * @return void
 * @since 1.0.0
 */
function icalcus_plugin_add_icalcusulation_descriptions_endpoints()
{
    /**
     * Registers a REST API route for creating a new icalcusulation description.
     *
     * @return WP_REST_Response|WP_Error The new icalcusulation description on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALCUS_EP_PREFIX, '/icalcusulation-descriptions', array(
        'methods' => 'POST',
        'callback' => 'icalcus_postIcalcusulationDescriptions',
        'permission_callback' => '__return_true'
    ));

    /**
     * Registers a REST API route for retrieving all icalcusulation descriptions.
     * @return WP_REST_Response|WP_Error The icalcusulation descriptions on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALCUS_EP_PREFIX, '/icalcusulation-descriptions', array(
        'methods' => 'GET',
        'callback' => 'icalcus_getIcalcusulationDescriptions',
        'permission_callback' => '__return_true'
    ));

    /**
     * Registers a REST API route for updating an existing icalcusulation description.
     *
     * @return WP_REST_Response|WP_Error The updated icalcusulation description on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALCUS_EP_PREFIX, '/icalcusulation-descriptions', array(
        'methods' => 'PUT',
        'callback' => 'icalcus_putIcalcusulationDescriptions',
        'permission_callback' => '__return_true'
    ));


    /**
     * Registers a REST API route for deleting an icalcusulation description.
     *
     * @return WP_REST_Response|WP_Error The deleted icalcusulation description on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALCUS_EP_PREFIX, '/icalcusulation-descriptions', array(
        'methods' => 'DELETE',
        'callback' => 'icalcus_deleteIcalcusulationDescriptions',
        'permission_callback' => '__return_true'
    ));

    /**
     * Registers a REST API route for retrieving the next available icalcusulation description ID.
     *
     * @return WP_REST_Response|WP_Error The next icalcusulation description ID on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(ICALCUS_EP_PREFIX, '/icalcusulation-descriptions/next', array(
        'methods' => 'GET',
        'callback' => 'icalcus_getNextIcalcusulationDescriptionId',
        'permission_callback' => '__return_true'
    ));
}

/**
 * Callback function for creating a new icalcusulation description.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the icalcusulation description data in the request body and the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The new icalcusulation description on success, or an error on failure.
 * @since 1.0.0
 *
 */
function icalcus_postIcalcusulationDescriptions(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();

    $name = $data['title'];
    $desc = $data['configuration']['calculation-description'];

    // Save the icalcusulation description to the database.
    $success = IcalcusulationsDescription::insertNew($name, $desc, json_encode($data));

    return new WP_REST_Response($success);
}

/**
 * Callback function for updating an existing icalcusulation description.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the updated icalcusulation description data in the request body and the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The updated icalcusulation description on success, or an error on failure.
 * @since 1.0.0
 */
function icalcus_putIcalcusulationDescriptions(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
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

    $success = IcalcusulationsDescription::updateById($id, $name, $desc, json_encode($body));

    return new WP_REST_Response("success");
}

/**
 * Callback function for getting the next available icalcusulation description ID.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The next available icalcusulation description ID on success, or an error on failure.
 * @since 1.0.0
 */
function icalcus_getNextIcalcusulationDescriptionId(WP_REST_Request $request)
{

    error_log("GET NEXT IDDDDDD");


    $validated = validate_icalcus_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $lastId = IcalcusulationsDescription::last_id();

    return new WP_REST_Response($lastId + 1);
}

/**
 * Callback function for deleting an icalcusulation description.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the ID of the icalcusulation description to delete in the request body and the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The result of the deletion operation on success, or an error on failure.
 * @since 1.0.0
 */
function icalcus_deleteIcalcusulationDescriptions(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];

    $result = IcalcusulationsDescription::delete($id);

    return new WP_REST_Response($result);
}


/**
 * Callback function for getting all icalcusulation descriptions.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the JWT token in the headers.
 * @return WP_REST_Response|WP_Error An array of all icalcusulation descriptions on success, or an error on failure.
 * @since 1.0.0
 *
 */
function icalcus_getIcalcusulationDescriptions(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    // Retrieve all icalcusulation descriptions from the database using the `get_all()` function of the `IcalcusulationsDescription` model class.
    $allDescriptions = IcalcusulationsDescription::get_all();

    return new WP_REST_Response($allDescriptions);
}


/**
 * Registers REST API endpoints for products.
 *
 * @return void
 * @since 1.0.0
 */
function icalcus_plugin_add_product_endpoints()
{
    // Register the 'products' endpoint for creating a new product.
    register_rest_route(ICALCUS_EP_PREFIX, '/products', array(
        'methods' => 'POST',
        'callback' => 'icalcus_postProduct',
        'permission_callback' => '__return_true'
    ));

    // Register the 'products' endpoint for retrieving all products.
    register_rest_route(ICALCUS_EP_PREFIX, '/products', array(
        'methods' => 'GET',
        'callback' => 'icalcus_getProducts',
        'permission_callback' => '__return_true'
    ));

    // Register the 'products' endpoint for updating an existing product.
    register_rest_route(ICALCUS_EP_PREFIX, '/products', array(
        'methods' => 'PUT',
        'callback' => 'icalcus_putProduct',
        'permission_callback' => '__return_true'
    ));

    // Register the 'products' endpoint for deleting an existing product.
    register_rest_route(ICALCUS_EP_PREFIX, '/products', array(
        'methods' => 'DELETE',
        'callback' => 'icalcus_deleteProduct',
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
function icalcus_postProduct(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
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
    $succes = Product::insertNew($name, $desc, $price, $unit, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}


/**
 * Updates a product in the database.
 *
 * @param WP_REST_Request $request The REST request object.
 * @return WP_REST_Response The REST response object.
 * @since 1.0.0
 */
function icalcus_putProduct(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
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

    $succes = Product::updateById($id, $name, $desc, $price, $unit, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}


/**
 * Retrieves all products from the database.
 *
 * @param WP_REST_Request $request The REST request object.
 * @return WP_REST_Response Returns a response containing an array of all products retrieved from the database.
 * @since 1.0.0
 */
function icalcus_getProducts(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $allProducts = Product::get_all_with_unit();


    return new WP_REST_Response($allProducts);
}

/**
 * Deletes an existing product from the database
 *
 * @param WP_REST_Request $request The REST request object
 * @return WP_REST_Response The REST response object
 * @since 1.0.0
 */
function icalcus_deleteProduct(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];

    $allProducts = Product::delete($id);

    return new WP_REST_Response($allProducts);
}

/**
 * Registers REST API endpoints for a service in a WordPress plugin.
 *
 * @return void
 */
function icalcus_plugin_add_service_endpoints()
{
    // Registers a POST endpoint for creating a new service.
    register_rest_route(ICALCUS_EP_PREFIX, '/services', array(
        'methods' => 'POST',
        'callback' => 'icalcus_postService',
        'permission_callback' => '__return_true'
    ));

    // Registers a GET endpoint for retrieving all services.
    register_rest_route(ICALCUS_EP_PREFIX, '/services', array(
        'methods' => 'GET',
        'callback' => 'icalcus_getServices',
        'permission_callback' => '__return_true'
    ));

    // Registers a PUT endpoint for updating an existing service.
    register_rest_route(ICALCUS_EP_PREFIX, '/services', array(
        'methods' => 'PUT',
        'callback' => 'icalcus_putService',
        'permission_callback' => '__return_true'
    ));

    // Registers a DELETE endpoint for deleting a service.
    register_rest_route(ICALCUS_EP_PREFIX, '/services', array(
        'methods' => 'DELETE',
        'callback' => 'icalcus_deleteService',
        'permission_callback' => '__return_true'
    ));
}


function icalcus_autocomplete_endpoints()
{
    register_rest_route(ICALCUS_EP_PREFIX, '/autocomplete/unit', array(
        'methods' => 'POST',
        'callback' => 'icalcus_autocompleteUnit',
        'permission_callback' => '__return_true'
    ));
}


/**
 * POST /autocomplete/id
 */
function icalcus_autocompleteUnit(WP_REST_Request $request)
{
    $data = $request->get_json_params();
    $value = $data["value"];

    return Unit::autocomplete($value);
}


/**
 * Callback function to handle POST requests to create a new service in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The response containing the newly created service.
 * @since 1.0.0
 */
function icalcus_postService(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
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

    $succes = Service::insertNew($name, $desc, $price, $unit, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}


/**
 * Callback function to handle PUT requests to update an existing service in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The response containing the updated service.
 * @since 1.0.0
 */
function icalcus_putService(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
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

    $succes = Service::updateById($id, $name, $desc, $price, $unit, $minQuality, $displayType);

    return new WP_REST_Response($succes);
}

/**
 * Callback function to handle GET requests to retrieve all services in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The response containing all services.
 * @since 1.0.0
 */
function icalcus_getServices(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $allServices = Service::get_all_with_unit();


    return new WP_REST_Response($allServices);
}

/**
 * Callback function to handle DELETE requests to delete a specific service in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The response containing the result of the deletion.
 * @since 1.0.0
 */
function icalcus_deleteService(WP_REST_Request $request)
{
    $validated = validate_icalcus_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = $data['id'];

    $allServices = Service::delete($id);

    return new WP_REST_Response($allServices);
}

/**
 * Validates a icalcus JWT token for a given REST API request in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return bool|WP_REST_Response Returns true if the token is valid, a WP_REST_Response if it's invalid, or false if it's not present.
 * @since 1.0.0
 */
function validate_icalcus_jwt_token(WP_REST_Request $request)
{
    $user = $request->get_header('user');
    $session = $request->get_header('session');
    $token = $request->get_header('icalcus-token');

    if (empty($token)) {
        $token = getPossibleCookieValue($request, 'icalcus-token');
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
function icalcus_user_can_manage(WP_REST_Request $request)
{
    $body = $request->get_json_params();
    $user = $body['user'];

    return user_can($user, 'manage_options');
}