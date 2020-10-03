<?php

// Find app directory
preg_match('/\b(\w+)$/', __DIR__, $MATCHES);

// Define App Dir
if (!defined('APP_DIR')) {
    define('APP_DIR', $MATCHES[0]);
}

// Define Web Root
if (!defined('__ROOT__')) {
    define('__ROOT__', dirname(__DIR__));
}

// Define App Root
if (!defined('__APP__')) {
    define('__APP__', realpath(__DIR__));
}

require_once __APP__ . '/config.php';

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    if (!$session->isLoggedIn()) {redirectTo("login.php");} else {redirectTo("home.php");}

} else {
    // echo "included/required";
}
