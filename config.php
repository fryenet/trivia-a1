<?php
// Database configuration.
//
// Keep credentials out of source control. Configure these values as
// environment variables on the server before running the application.

function required_env($name) {
    $value = getenv($name);
    if ($value === false || trim($value) === '') {
        throw new RuntimeException(
            "Missing required environment variable: ".$name
        );
    }
    return $value;
}

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', required_env('DB_NAME'));
define('DB_USER', required_env('DB_USER'));
define('DB_PASS', required_env('DB_PASS'));

define('APP_NAME', getenv('APP_NAME') ?: 'Trivia');
define('APP_URL', getenv('APP_URL') ?: '');

// Session settings
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Lax');

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

session_name('trivia_sess');
?>
