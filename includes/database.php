<?php

require_once(__ROOT__ . '/CCTV/includes/initialize.php');	

class MySQLDatabase {
	
	private $connection;
	private static $counter;
	private $sql;
	
	function __construct() {
		$this->open_connection();
	}
	
	public function open_connection() {
		$this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);
		if (mysqli_connect_error()) {
		  die("Database connection failed: " .
			mysqli_connect_error() . 
			"(" . mysqli_connect_errno() . ")"
		  );
			
		}
	}
	
	public function close_connection() {
		if (isset($this->connection)) {
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}
	
	public function query($sql, $check=true) {
		
		$this->sql = $sql;
		if ( mysqli_ping($this->connection) ) {
			
		$result = mysqli_query($this->connection, $sql);
			if($check) {
				$this->confirm_query($result);
			}
		} else {
			echo "This is embarrassing. The query could not be sent :( <br />". "Error: ". mysqli_error($this->connection);
			$result = null;
			$this->confirm_query($result);
		}
		
		return $result;
	}
	
	private function confirm_query($result) {
		if(!$result) {
			die("<br />Database query failed: <br /> <p>". $this->sql . "</p>");
		}
	}
	
	public function escape_value($string) {    
	    $escaped_string = mysqli_real_escape_string($this->connection, $string);
	    return $escaped_string;
	}
	
	// database neutral functions
	public function fetch_array($result_set) {
		return mysqli_fetch_array($result_set);
	}
	
	public function num_rows($result_set) {
		return mysqli_num_rows($result_set);
	}
	
	public function insert_id() {
		// get last id inserted over current database connection
		return mysqli_insert_id($this->connection);
	}
	
	public function affected_rows() {
		return mysqli_affected_rows($this->connection);
		
	}
	
}

$database = new MySQLDatabase();
$db =& $database;




	
?>