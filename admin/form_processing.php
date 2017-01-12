<?php

// define root directory
if(!defined('__ROOT__')) {
	define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
}

require_once( __ROOT__ . '/CCTV/includes/initialize.php');
if(!$session->is_logged_in()) {	redirect_to("login.php"); }

if(isset($_POST['post'])) {
	$user = new User();
	$user->username = $_POST['username'];
	$user->password = $_POST['password'];
	$user->first_name = $_POST['first_name'];
	$user->last_name = $_POST['last_name'];
		
		if($user->save()) {
			if(isset($permissions)) {	
			//$p = $permissions->set_permissions($user->id, $_POST['camera']) ? " Permissions were created" : " Permissions were not created.";
			$permissions->set_permissions($user->id, $_POST['camera']);
			$p = "";
			$session->message("User {$user->username} added successfully. {$p}");
			redirect_to('admin.php');
			}  else {
			$message = "Failed to add user {$user->username}.";
			}
		}
}

	if(isset($_GET['id'])) {
			$user_d = User::find_by_id($_GET['id']);
			
		if($permissions->delete_permissions($_GET['id'])) {	
			if($user_d && $user_d->delete()) {
				$session->message("The user {$user_d->username} was deleted.");
				redirect_to('admin.php');
			} else {
				$session->message("The user could not be deleted.");
				redirect_to('admin.php');
			}	
		} else {
			$session->message("The user permissions could not be deleted.");
			redirect_to('admin.php');
		}
				
	}	
		
   if(isset($database)) { $database->close_connection(); } 
   
?>