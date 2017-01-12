<?php

//Database Constants
defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
defined('DB_USER')   ? null : define("DB_USER", "root");
defined('DB_PASS')   ? null : define("DB_PASS", "root");
defined('DB_NAME')   ? null : define("DB_NAME", "test_php");
defined('DB_PORT')   ? null : define("DB_PORT", "8889");

/************************************************************/

defined('IMAGE_DIR')   ? null : define("IMAGE_DIR", "/CCTV/images");

$days_saved = 60;  // Number of days to store in database

$directory = array(								// set directory names and paths for directory, below is example of foscam ip cameras
			 array( "NVR", "/nvr_snapshots/snap" ),
			 array( "Store", "/dynasty_loop/C1_00626E58960C/snap" ),
			 array( "Apartment", "/dynasty_loop/C1_00626E5894EF/snap" ),
			 array( "POS", "/dynasty_loop/C1_00626E6069E2/snap" )
			
			
);

$group_ids = array(				// give numbers to directories which controls permissions 
			 1 => 'NVR',
			 2 => 'Store',  
			 3 => 'Apartment', 
			 4 => 'POS'
			 
);

$title = "Monitoring System";  // Login page title

// Add the admin user to the database for GUI
$admin_user = 'username';
$admin_password = 'password';
$admin_firstname = 'Jane';
$admin_lastname = 'Doe';


?>