<?php
// define root directory
if(!defined('__ROOT__')) {
	
	  define('__ROOT__', '/path/to/server/root'); // full path to server root example: /home/admin/web/monitor.capa.furniture/public_html/
}

require_once( __ROOT__ . '/CCTV/includes/initialize.php');	

if(!isset($_SESSION['user_id']) && !isset($_SESSION['user_name'])) {
	
		if($readFiles->camera_index(Snapshot::$dir_assets)) {
			// something to do - if you want
		} 
		// delete old images
		$readFiles->execute_cleansing();
		
		log_action('CRON JOB','Ran.', 'cron.log');
	
	}	else {
		
		log_action('CRON JOB','Could not run.', 'cron.log');
		
}



?>