<?php

namespace icalc\fe;

use function icalc\util\callJSFunction;
use function icalc\util\console_log;

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
        $maxWait = 10;
        $iter = 0;
        while(!isset($_COOKIE['icalc-token']) && $iter < $maxWait ){
            sleep(0.1);
            $iter++;
        }
    }

    public static function populateIcalcJSData(){
        $output = "populateIcalcSettings(\"". wp_get_current_user()->ID ."\",\"". wp_get_session_token() ."\");";
        callJSFunction($output);
    }

    protected static function callGetOnEPWithAuthCookie($endpoint){
            $endpointWithPrefix = ICALC_EP_PREFIX . $endpoint;
            $url = get_rest_url(null, $endpointWithPrefix);
            self::setIcalcTokenCookie();
            $headers = array(
                'Content-Type' => 'application/json',
                'user' => wp_get_current_user()->ID,
                'session' => wp_get_session_token(),
                'icalc-token' => $_COOKIE['icalc-token']
            );
            $args = array(
                'headers' => $headers
            );;
            $response = wp_remote_get($url, $args);
            if (is_array($response)) {
                $body = wp_remote_retrieve_body($response);
                error_log("BODY: ". $body);
                $data = json_decode($body);
                return $data;
            }
            return null;
    }

}