<?php


use Firebase\JWT\JWT;


function generate_site_specific_secret_key()
{
    $site_url = get_site_url();
    $auth_salt = wp_salt();
    $site_specific_key = hash('sha256', $site_url . $auth_salt);

    define('JWT_SECRET_KEY', $site_specific_key);
}

// Issue a new JWT token
function issue_jwt_token($user_id, $session): string
{
    $issued_at = time();
    $expiration_time = $issued_at + (60 * 60); // Token valid for 1 hour

    $randomCharacters = wp_generate_password();

    $payload = [
        'iat' => $issued_at,
        'exp' => $expiration_time,
        'uid' => $user_id,
        'session' => $session,
        'secret' => $randomCharacters
    ];

    $token = JWT::encode($payload, JWT_SECRET_KEY);
    set_site_transient('icalc-secret-' . $user_id . $session, $randomCharacters, 60 * 60);
    return $token;
}

// Validate a JWT token
function validate_jwt_token($token, $userid, $session)
{
    try {
        $decoded = JWT::decode($token, JWT_SECRET_KEY, ['HS256']);
        $uid = $decoded->uid;
        $sessionId = $decoded->session;
        if ($userid == $uid && $sessionId == $session) {
            $secret = get_site_transient('icalc-secret-' . $userid . $sessionId);
            if ($secret != $decoded->secret) {
                return false;
            }

            if(time() >= $decoded->exp){
                return new WP_REST_Response(['msg'=>"Token Expired"],401);
            }

            return true;
        }
    } catch (Exception $e) {
        return false;
    }
    return false;
}
