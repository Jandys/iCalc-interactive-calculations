<?php
/*
 *
 *   This file is part of the 'iCalc - Interactive Calculations' project.
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
 * prefix mapping = /wp-json/interactivecalculations/v1/
 */

if (!defined('ABSPATH')) exit;

use interactivecalculations\db\model\Icalculations;
use interactivecalculations\db\model\IcalculationsDescription;
use interactivecalculations\db\model\Product;
use interactivecalculations\db\model\Service;
use interactivecalculations\db\model\Unit;
use function interactivecalculations\util\getPossibleCookieValue;

add_action('rest_api_init', 'interactivecalculations_plugin_add_service_endpoints');
add_action('rest_api_init', 'interactivecalculations_plugin_add_product_endpoints');
add_action('rest_api_init', 'interactivecalculations_plugin_add_interactivecalculations_descriptions_endpoints');
add_action('rest_api_init', 'interactivecalculations_autocomplete_endpoints');
add_action('rest_api_init', 'interactivecalculations_plugin_add_jwt_endpoints');
add_action('rest_api_init', 'interactivecalculations_plugin_add_public_endpoints');


const NOT_AUTH_MSG = "Not authorized, token needed";
const NO_SESSION_MSG = "No such session found for given user.";


/**
 * Registers public REST API endpoints for the interactivecalculations plugin.
 *
 * @return void
 * @since 1.0.0
 *
 */
function interactivecalculations_plugin_add_public_endpoints()
{
    /**
     * Registers a REST API route for retrieving a product by ID.
     *
     * @param string $id The ID of the product to retrieve.
     * @return WP_REST_Response|WP_Error The product data on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/products/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'interactivecalculations_getProductById',
        'args' => array( // Argument validation and sanitization.
            'id' => array(
                'validate_callback' => 'interactivecalculations_id_validate_callback',
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
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/services/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'interactivecalculations_getServiceById',
        'args' => array( // Argument validation and sanitization.
            'id' => array(
                'validate_callback' => 'interactivecalculations_id_validate_callback',
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
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/descriptions/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'interactivecalculations_getCalculationDescriptionById',
        'args' => array( // Argument validation and sanitization.
            'id' => array(
                'validate_callback' => 'interactivecalculations_id_validate_callback',
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
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/interactions', array(
        'methods' => 'POST',
        'callback' => 'interactivecalculations_registerNewCalculationInteraction',
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
function interactivecalculations_id_validate_callback($value, $request, $param)
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
function interactivecalculations_getProductById(WP_REST_Request $request)
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
function interactivecalculations_getServiceById(WP_REST_Request $request)
{
    $id = $request->get_param('id');
    $service = Service::get("id", $id);

    return new WP_REST_Response($service);
}

/**
 * Retrieves an interactivecalculations description by ID from the database.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the ID parameter.
 * @return WP_REST_Response The interactivecalculations description data on success, or a WP_Error object on failure.
 * @since 1.0.0
 */
function interactivecalculations_getCalculationDescriptionById(WP_REST_Request $request)
{
    $id = sanitize_text_field($request->get_param('id'));
    $interactivecalculationsDescription = IcalculationsDescription::get("id", $id);

    return new WP_REST_Response($interactivecalculationsDescription);
}

/**
 * Registers a new calculation interaction in the database.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the calculation data and user information.
 * @return WP_REST_Response The interaction data on success, or a WP_Error object on failure.
 * @since 1.0.0
 */
function interactivecalculations_registerNewCalculationInteraction(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    $calculationId = sanitize_text_field($data['calculationId']);
    $body = sanitize_text_field($data['body']);
    $user = sanitize_text_field($data['userId']);

    $status = Icalculations::insertNew($calculationId, json_encode($body), $user);

    return new WP_REST_Response($status);
}


/**
 * Registers REST API endpoints for issuing and verifying JWT tokens.
 *
 * @return void
 * @since 1.0.0
 */
function interactivecalculations_plugin_add_jwt_endpoints()
{
    /**
     * Registers a REST API route for issuing a JWT token.
     *
     * @return WP_REST_Response|WP_Error The JWT token on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/token', array(
        'methods' => 'POST',
        'callback' => 'interactivecalculations_interactivecalculations_issue_jwt_token_callback',
        'permission_callback' => 'interactivecalculations_user_can_manage'
    ));

    /**
     * Registers a REST API route for verifying a JWT token.
     *
     * @return WP_REST_Response|WP_Error The verification status of the JWT token on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/token-verify', array(
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

function interactivecalculations_interactivecalculations_issue_jwt_token_callback(WP_REST_Request $request)
{
    $body = $request->get_json_params();
    $user = sanitize_text_field($body['user']);
    $session = sanitize_text_field($body['session']);
    $transSession = get_transient($session);

    if ($transSession != $user) {
        return new WP_REST_Response(NO_SESSION_MSG);
    }
    $token = interactivecalculations_issue_jwt_token($user, $session);

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
    $user = sanitize_text_field($request->get_header('user'));
    $session = sanitize_text_field($request->get_header('session'));
    $token = sanitize_text_field($request->get_header('interactivecalculations-token'));

    return new WP_REST_Response(['valid' => interactivecalculations_validate_jwt_token($token, $user, $session)]);
}

/**
 * Registers REST API endpoints for interactivecalculations descriptions.
 *
 * @return void
 * @since 1.0.0
 */
function interactivecalculations_plugin_add_interactivecalculations_descriptions_endpoints()
{
    /**
     * Registers a REST API route for creating a new interactivecalculations description.
     *
     * @return WP_REST_Response|WP_Error The new interactivecalculations description on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/descriptions', array(
        'methods' => 'POST',
        'callback' => 'interactivecalculations_postIcalculationsDescriptions',
        'permission_callback' => '__return_true'
    ));

    /**
     * Registers a REST API route for retrieving all interactivecalculations descriptions.
     * @return WP_REST_Response|WP_Error The interactivecalculations descriptions on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/descriptions', array(
        'methods' => 'GET',
        'callback' => 'interactivecalculations_getIcalculationsDescriptions',
        'permission_callback' => '__return_true'
    ));

    /**
     * Registers a REST API route for updating an existing interactivecalculations description.
     *
     * @return WP_REST_Response|WP_Error The updated interactivecalculations description on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/descriptions', array(
        'methods' => 'PUT',
        'callback' => 'interactivecalculations_putIcalculationsDescriptions',
        'permission_callback' => '__return_true'
    ));


    /**
     * Registers a REST API route for deleting an interactivecalculations description.
     *
     * @return WP_REST_Response|WP_Error The deleted interactivecalculations description on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/descriptions', array(
        'methods' => 'DELETE',
        'callback' => 'interactivecalculations_deleteIcalculationsDescriptions',
        'permission_callback' => '__return_true'
    ));

    /**
     * Registers a REST API route for retrieving the next available interactivecalculations description ID.
     *
     * @return WP_REST_Response|WP_Error The next interactivecalculations description ID on success, or an error on failure.
     * @since 1.0.0
     */
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/descriptions/next', array(
        'methods' => 'GET',
        'callback' => 'interactivecalculations_getNextIcalculationsDescriptionId',
        'permission_callback' => '__return_true'
    ));
}

/**
 * Callback function for creating a new interactivecalculations description.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the interactivecalculations description data in the request body and the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The new interactivecalculations description on success, or an error on failure.
 * @since 1.0.0
 *
 */
function interactivecalculations_postIcalculationsDescriptions(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();

    $name = sanitize_text_field($data['title']);
    $desc = sanitize_text_field($data['configuration']['calculation-description']);

    // Save the interactivecalculations description to the database.
    $success = IcalculationsDescription::insertNew($name, $desc, json_encode($data));

    return new WP_REST_Response($success);
}

/**
 * Callback function for updating an existing interactivecalculations description.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the updated interactivecalculations description data in the request body and the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The updated interactivecalculations description on success, or an error on failure.
 * @since 1.0.0
 */
function interactivecalculations_putIcalculationsDescriptions(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $body = json_decode($data['body']);
    $id = sanitize_text_field($body->id);
    $name = sanitize_text_field($body->title);
    $desc = sanitize_text_field($body->configuration->{'calculation-description'});

    $success = IcalculationsDescription::updateById($id, $name, $desc, sanitize_text_field(json_encode($body)));

    return new WP_REST_Response("success");
}

/**
 * Callback function for getting the next available interactivecalculations description ID.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The next available interactivecalculations description ID on success, or an error on failure.
 * @since 1.0.0
 */
function interactivecalculations_getNextIcalculationsDescriptionId(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $lastId = IcalculationsDescription::last_id();

    return new WP_REST_Response($lastId + 1);
}

/**
 * Callback function for deleting an interactivecalculations description.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the ID of the interactivecalculations description to delete in the request body and the JWT token in the headers.
 * @return WP_REST_Response|WP_Error The result of the deletion operation on success, or an error on failure.
 * @since 1.0.0
 */
function interactivecalculations_deleteIcalculationsDescriptions(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = sanitize_text_field($data['id']);

    $result = IcalculationsDescription::delete($id);

    return new WP_REST_Response($result);
}


/**
 * Callback function for getting all interactivecalculations descriptions.
 *
 * @param WP_REST_Request $request The current REST API request object, containing the JWT token in the headers.
 * @return WP_REST_Response|WP_Error An array of all interactivecalculations descriptions on success, or an error on failure.
 * @since 1.0.0
 *
 */
function interactivecalculations_getIcalculationsDescriptions(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    // Retrieve all interactivecalculations descriptions from the database using the `get_all()` function of the `IcalculationsDescription` model class.
    $allDescriptions = IcalculationsDescription::get_all();

    return new WP_REST_Response($allDescriptions);
}


/**
 * Registers REST API endpoints for products.
 *
 * @return void
 * @since 1.0.0
 */
function interactivecalculations_plugin_add_product_endpoints()
{
    // Register the 'products' endpoint for creating a new product.
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/products', array(
        'methods' => 'POST',
        'callback' => 'interactivecalculations_postProduct',
        'permission_callback' => '__return_true'
    ));

    // Register the 'products' endpoint for retrieving all products.
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/products', array(
        'methods' => 'GET',
        'callback' => 'interactivecalculations_getProducts',
        'permission_callback' => '__return_true'
    ));

    // Register the 'products' endpoint for updating an existing product.
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/products', array(
        'methods' => 'PUT',
        'callback' => 'interactivecalculations_putProduct',
        'permission_callback' => '__return_true'
    ));

    // Register the 'products' endpoint for deleting an existing product.
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/products', array(
        'methods' => 'DELETE',
        'callback' => 'interactivecalculations_deleteProduct',
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
function interactivecalculations_postProduct(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $name = sanitize_text_field($data['name']);
    $desc = sanitize_text_field($data['description']);
    $price = sanitize_text_field($data['price']);
    $unit = sanitize_text_field($data['unit']);
    $minQuality = sanitize_text_field($data['minQuality']);
    $displayType = sanitize_text_field($data['displayType']);

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
function interactivecalculations_putProduct(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = sanitize_text_field($data['id']);
    $name = sanitize_text_field($data['name']);
    $desc = sanitize_text_field($data['description']);
    $price = sanitize_text_field($data['price']);
    $unit = sanitize_text_field($data['unit']);
    $minQuality = sanitize_text_field($data['minQuality']);
    $displayType = sanitize_text_field($data['displayType']);

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
function interactivecalculations_getProducts(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
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
function interactivecalculations_deleteProduct(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = sanitize_text_field($data['id']);

    $allProducts = Product::delete($id);

    return new WP_REST_Response($allProducts);
}

/**
 * Registers REST API endpoints for a service in a WordPress plugin.
 *
 * @return void
 */
function interactivecalculations_plugin_add_service_endpoints()
{
    // Registers a POST endpoint for creating a new service.
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/services', array(
        'methods' => 'POST',
        'callback' => 'interactivecalculations_postService',
        'permission_callback' => '__return_true'
    ));

    // Registers a GET endpoint for retrieving all services.
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/services', array(
        'methods' => 'GET',
        'callback' => 'interactivecalculations_getServices',
        'permission_callback' => '__return_true'
    ));

    // Registers a PUT endpoint for updating an existing service.
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/services', array(
        'methods' => 'PUT',
        'callback' => 'interactivecalculations_putService',
        'permission_callback' => '__return_true'
    ));

    // Registers a DELETE endpoint for deleting a service.
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/services', array(
        'methods' => 'DELETE',
        'callback' => 'interactivecalculations_deleteService',
        'permission_callback' => '__return_true'
    ));
}


function interactivecalculations_autocomplete_endpoints()
{
    register_rest_route(INTERACTIVECALCULATIONS_EP_PREFIX, '/autocomplete/unit', array(
        'methods' => 'POST',
        'callback' => 'interactivecalculations_autocompleteUnit',
        'permission_callback' => '__return_true'
    ));
}


/**
 * POST /autocomplete/id
 */
function interactivecalculations_autocompleteUnit(WP_REST_Request $request)
{
    $data = $request->get_json_params();
    $value = sanitize_text_field($data["value"]);

    return Unit::autocomplete($value);
}


/**
 * Callback function to handle POST requests to create a new service in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return WP_REST_Response The response containing the newly created service.
 * @since 1.0.0
 */
function interactivecalculations_postService(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $name = sanitize_text_field($data['name']);
    $desc = sanitize_text_field($data['description']);
    $price = sanitize_text_field($data['price']);
    $unit = sanitize_text_field($data['unit']);
    $minQuality = sanitize_text_field($data['minQuality']);
    $displayType = sanitize_text_field($data['displayType']);

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
function interactivecalculations_putService(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }
    $data = $request->get_json_params();

    $id = sanitize_text_field($data['id']);
    $name = sanitize_text_field($data['name']);
    $desc = sanitize_text_field($data['description']);
    $price = sanitize_text_field($data['price']);
    $unit = sanitize_text_field($data['unit']);
    $minQuality = sanitize_text_field($data['minQuality']);
    $displayType = sanitize_text_field($data['displayType']);

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
function interactivecalculations_getServices(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
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
function interactivecalculations_deleteService(WP_REST_Request $request)
{
    $validated = validate_interactivecalculations_jwt_token($request);
    if ($validated instanceof WP_REST_Response) {
        return $validated;
    }

    if (!$validated) {
        return new WP_REST_Response(['msg' => NOT_AUTH_MSG], 401);
    }

    $data = $request->get_json_params();
    $id = sanitize_text_field($data['id']);

    $allServices = Service::delete($id);

    return new WP_REST_Response($allServices);
}

/**
 * Validates a interactivecalculations JWT token for a given REST API request in a WordPress plugin.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return bool|WP_REST_Response Returns true if the token is valid, a WP_REST_Response if it's invalid, or false if it's not present.
 * @since 1.0.0
 */
function validate_interactivecalculations_jwt_token(WP_REST_Request $request)
{
    $user = sanitize_text_field($request->get_header('user'));
    $session = sanitize_text_field($request->get_header('session'));
    $token = sanitize_text_field($request->get_header('interactivecalculations-token'));

    if (empty($token)) {
        $token = getPossibleCookieValue($request, 'interactivecalculations-token');
    }

    return interactivecalculations_validate_jwt_token($token, $user, $session);
}

/**
 * Checks if a user has the capability to manage options in WordPress.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return bool Returns true if the user has the capability, false otherwise.
 * @since 1.0.0
 */
function interactivecalculations_user_can_manage(WP_REST_Request $request)
{
    $body = $request->get_json_params();
    $user = sanitize_text_field($body['user']);

    return user_can($user, 'manage_options');
}