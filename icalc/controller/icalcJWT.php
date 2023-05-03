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

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Generates a site-specific secret key for use with JSON Web Tokens (JWT) in WordPress.
 * @since 1.0.0
 */
function generate_site_specific_secret_key()
{
    // Retrieve the site URL and authentication salt.
    $site_url = get_site_url();
    $auth_salt = wp_salt();

    // Concatenate the site URL and authentication salt and hash the result using SHA-256.
    $site_specific_key = hash('sha256', $site_url . $auth_salt);

    // Define a constant JWT_SECRET_KEY with the site-specific key.
    define('JWT_SECRET_KEY', $site_specific_key);
}

/**
 * Issues a JWT token for a given user ID and session in a WordPress plugin.
 *
 * @param int $user_id The ID of the user to issue the token for.
 * @param string $session The session ID to associate with the token.
 * @return string The encoded JWT token.
 * @since 1.0.0
 *
 */
function issue_jwt_token($user_id, $session): string
{
    // Set the issued and expiration times for the token and generate a random string.
    $issued_at = time();
    $expiration_time = $issued_at + (60 * 60); // Token valid for 1 hour

    $randomCharacters = wp_generate_password();

    // Create a payload array with the issued and expiration times, user ID, session, and random string as the secret.
    $payload = [
        'iat' => $issued_at,
        'exp' => $expiration_time,
        'uid' => $user_id,
        'session' => $session,
        'secret' => $randomCharacters
    ];

    // Encode the payload into a JWT token using the site-specific secret key.
    $token = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');

    // Store the random string in a site transient with a unique key for the user ID and session.
    delete_site_transient('icalc-secret-' . $user_id . $session);
    set_site_transient('icalc-secret-' . $user_id . $session, $randomCharacters, 60 * 60);
    return $token;
}

/**
 * Validates a JWT token for a given user ID and session in a WordPress plugin.
 *
 * @param string $token The JWT token to validate.
 * @param int $userid The ID of the user associated with the token.
 * @param string $session The session ID associated with the token.
 *
 * @return bool|WP_REST_Response Returns true if the token is valid, a WP_REST_Response if it's invalid, or false if there is an error decoding the token.
 * @since 1.0.0
 */
function validate_jwt_token($token, $userid, $session)
{
    try {
        // Decode the JWT token using the site-specific secret key.
        $key = new Key(JWT_SECRET_KEY, 'HS256');
        $decoded = JWT::decode($token, $key);

        // Check if the user ID and session in the decoded token match the provided user ID and session.
        $uid = $decoded->uid;
        $sessionId = $decoded->session;
        if ($userid == $uid && $sessionId == $session) {

            // Retrieve the random string from the site transient and check if it matches the secret in the decoded token.
            $secret = get_site_transient('icalc-secret-' . $userid . $sessionId);
            if ($secret != $decoded->secret) {
                return false;
            }

            // Check if the token has expired and return a WP_REST_Response with an error message if it has.
            if (time() >= $decoded->exp) {
                return new WP_REST_Response(['msg' => "Token Expired"], 401);
            }

            // Return true indicating that the token is valid.
            return true;
        }
    } catch (Exception $e) {
        // Return false if there is an error decoding the token.
        return false;
    }
    return false;
}
