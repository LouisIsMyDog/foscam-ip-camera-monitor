<?php

//Database Constants
defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
defined('DB_USER') ? null : define("DB_USER", "admin_ardor");
defined('DB_PASS') ? null : define("DB_PASS", "mahmut88");
defined('DB_NAME') ? null : define("DB_NAME", "admin_camerardor");
defined('DB_PORT') ? null : define("DB_PORT", "3306");

/************************************************************/

defined('IMAGE_DIR') ? null : define("IMAGE_DIR", "/CCTV/images");
defined('APP_DIR') ? null : define('APP_DIR', '/CCTV');

$days     = 14; // Number of days to store in database
$per_page = 32; // Number of images per page
$debug    = false; // Turn debug on

$cameras = array(
    'camera01' => array(
        'location' => 'Store',
        'group'    => 2,
        'path'     => '/frederick/R2_00626E6EB5B7/snap',
        'model'    => 'R2',
        'brand'    => 'Foscam',
    ),
    'camera02' => array(
        'location' => 'Store',
        'group'    => 2,
        'path'     => '/frederick/FI9900P_00626E92833E/snap',
        'model'    => 'FI9900P',
        'brand'    => 'Foscam',
    ),
    'camera03' => array(
        'location' => 'Store',
        'group'    => 2,
        'path'     => '/frederick/C1_00626E606BF6/snap',
        'model'    => 'C1',
        'brand'    => 'Foscam',
    ),
    'camera04' => array(
        'location' => 'Store',
        'group'    => 2,
        'path'     => '/frederick/C1-Lite_E8ABFA6BBE4F/snap',
        'model'    => 'C1-Lite',
        'brand'    => 'Foscam',

    ),
    'camera05' => array(
        'location' => 'Store',
        'group'    => 2,
        'path'     => '/frederick/FoscamCamera_00626EEC3AFF/snap',
        'model'    => 'R4S',
        'brand'    => 'Foscam',

    ),
    'camera06' => array(
        'location' => 'Potomac',
        'group'    => 5,
        'path'     => '/frederick/C2_00626E662D4D/snap',
        'model'    => 'C2',
        'brand'    => 'Foscam',

    ),
);

$group_ids = array( // give numbers to directories which controls permissions
    1 => 'NVR',
    2 => 'Store',
    3 => 'Apartment',
    4 => 'POS',
    5 => 'Potomac',

);

$title = "Foscam Monitoring System"; // Login page title

$defaultUser = array(
    'user'      => 'admin',
    'password'  => 'password',
    'firstname' => 'John',
    'lastname'  => 'Doe',
);

require_once __APP__ . '/includes/initialize.php';
