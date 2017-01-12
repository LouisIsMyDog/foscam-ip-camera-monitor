<?php
	
// define root directory
if(!defined('__ROOT__')) {
	
	  define('__ROOT__', $_SERVER['DOCUMENT_ROOT']); 
}

require_once( __ROOT__ . '/CCTV/includes/initialize.php');	


if(!$session->is_logged_in()) {	redirect_to("login.php"); }

	
$readFiles->camera_index(Snapshot::$dir_assets); 
redirect_to('/CCTV/index.php');














?>