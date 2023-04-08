<?php

namespace icalc\fe;

abstract class AbstractAdminFrontend
{
    const TOKEN_NEEDED = "Token Expired";

    abstract public static function configuration();

    public static function configuredModalEdit($modalId, $id, $formFields): string
    {
        return "";
    }

    public static function configureCreationModal($modalId): string
    {
        return "";
    }

    public static function setIcalcTokenCookie()
    {
        if (!isset($_COOKIE['icalc-expiration'])
            || !isset($_COOKIE['icalc-token'])
            || $_COOKIE['icalc-expiration'] < time()) {
            self::setNewIcalcTokenCookie();
        }
    }

    public static function setNewIcalcTokenCookie()
    {
        $data = array("user" => wp_get_current_user()->ID, "session" => wp_get_session_token());

        $response = wp_remote_post(get_rest_url(null, ICALC_EP_PREFIX . '/token'), array(
            'method' => 'POST',
            'timeout' => 45, // Timeout in seconds
            'redirection' => 5, // Maximum number of redirections
            'blocking' => true,
            'headers' => array(
                'Content-Type' => 'application/json; charset=utf-8',
            ),
            'body' => json_encode($data), // Encode the data as JSON
            'cookies' => array(),
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_m = "Something went wrong: $error_message";
            console_log($error_m);
        } else {
            $response_body = wp_remote_retrieve_body($response);
            $body = json_decode($response_body);
            $expiration_time = time() + 3300; // Set the cookie to expire in 55 minutes
            $_COOKIE['icalc-token'] = $body->token;
            $_COOKIE['icalc-expiration'] = $expiration_time;
        }
    }

}