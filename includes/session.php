<?php

class Session {
	
	private $logged_in = false;
	public  $is_admin = false;
	public  $user_id;
	public  $user_name;
	public  $message;
	
	public  $date_filter; // mysql date_filter for filter form
	
	function __construct() {
		session_start();
		$this->check_message();
		$this->check_login();
		$this->check_admin();
		
		$this->check_date_filter();
	}
	
	
	public function is_logged_in() {
		return $this->logged_in;
	}
	
	public function login($user) {
		
		if($user) {
			
			$this->user_id = $_SESSION['user_id'] = $user->id;
			$this->user_name = $_SESSION['user_name'] = $user->username;
			
		if(isset($this->user_id) && $this->user_id == 1) {
			$_SESSION['admin'] = $this->is_admin = true; 
			
			} else {
			$_SESSION['admin'] = $this->is_admin = false;
			}

			$this->logged_in = true;
		}
	}
	
	public function logout() {
		unset($_SESSION['user_id']);
		unset($this->user_id);
		
		unset($_SESSION['user_name']);
		unset($this->user_name);
		
		unset($this->date_filter);
		
		$this->logged_in = false;
		$this->is_admin = false;

	}
	
	public function message($msg="") {
		if(!empty($msg)) {
			$_SESSION['message'] = $msg;
		} else {
			return $this->message;
		}
	}
	
	private function check_login() {
		if(isset( $_SESSION['user_id'] )) {
			$this-> user_id = $_SESSION['user_id'];
			$this-> user_name = $_SESSION['user_name'];
			$this-> logged_in = true;
		} else {
			unset($this->user_id);
			unset($this->user_name);
			$this->logged_in =false;
		}
	}
	
     private function check_message() {
	     
	     if(isset($_SESSION['message'])) {   
		     $this->message = $_SESSION['message'];
		     unset($_SESSION['message']);
	     } else {
		     $this->message = "";
	     }
     }
     
	 private function check_admin() {
		 if(isset($_SESSION['admin'])) {
			 $this->is_admin = $_SESSION['admin'];
			} 
	 }
	 
     private function check_date_filter() {
	     if(isset($_SESSION['date_filter'])) {
		     $this->date_filter = $_SESSION['date_filter'];
		   //unset($_SESSION['date_filter']);
		  } else {
		     $this->date_filter = "";
	      }
	 }
	       
     public function date_filter($date_filter="") {
	     if(!empty($date_filter)) {
			$_SESSION['date_filter'] = $date_filter;
		} else {
			return $this->date_filter;
		}
     }
     
     
}


$session = new Session();
$message = $session->message();
	
?>