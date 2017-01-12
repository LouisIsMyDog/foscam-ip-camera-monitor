<?php
require_once(__ROOT__ . '/CCTV/includes/initialize.php');	

// if you have not created your database already then this is a fallback procedure. 
class Preperation {
	
	function __construct() {
		$mySQL = new mysqli(DB_SERVER, DB_USER, DB_PASS, '', DB_PORT);
		
		if($mySQL->connect_errno > 0){
			die('Unable to connect to database [' . $mySQL->connect_error . ']');
		}
		
		if( !$result = $mySQL->select_db(DB_NAME) ): $mySQL->query("CREATE DATABASE ". DB_NAME .";");
		endif;
	}
	
	
	
	}
	
$preperation = new Preperation();
	
?>