<?php

namespace icalc\util;

function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

function callJSFunction($output, $with_script_tags = true){
        $js_code =  $output;
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
}

function getPossibleCookieValue(\WP_REST_Request $request, string $cookieName):string{
    $cookieHeader = $request->get_header( 'Cookie' );

    if ( $cookieHeader ) {
        $cookieHeaderParts = explode( ';', $cookieHeader );
        foreach ( $cookieHeaderParts as $cookiePart ) {
            $cookiePart = trim( $cookiePart );
            if(str_starts_with($cookiePart,$cookieName.'=')){
                return explode( '=', $cookiePart )[1];
            }
        }
    }
    return "";
}