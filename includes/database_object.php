<?php

require_once(__ROOT__ . '/CCTV/includes/initialize.php');	


class DatabaseObject {
	
	protected static $table_name = "users";
	
	// common database methods
	public static function find_all() {
		
		return static::find_by_sql("SELECT * FROM " . static::$table_name);
	}
	
	public static function find_by_id($id=0) {
		
		global $database;
		$result_array = static::find_by_sql("SELECT * FROM " . static::$table_name ." WHERE id=".$database->escape_value($id)." LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_by_sql($sql="") {
		global $database;
		$result_set = $database->query($sql);
		$oject_array = array();
		while ($row = $database->fetch_array($result_set)){
			$oject_array[] = static::instantiate($row);
		}
		return $oject_array;
	}
	
	public static function count_all($where='') {
		global $database, $session;
		
		$sql = "SELECT COUNT(*) FROM " . static::$table_name . " AS s, permissions AS p  WHERE p.user_id={$session->user_id} AND s.group_id=p.group_id {$where}";
		
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
		
	} 
	
		
	private static function instantiate($record) {
/*
 * 			$object->id 		= $record['id'];
 * 			$object->username 	= $record['username'];
 * 			$object->password   = $record['password'];
 * 			$object->first_name = $record['first_name'];
 * 			$object->last_name  = $record['last_name'];
 */
 
 			$class_name = get_called_class();
			$object = new $class_name;
						
			foreach($record as $attribute => $value){
				if($object->has_attribute($attribute)){
					$object -> $attribute = $value;
				}	
			}
			return $object;
	}
	
	private function has_attribute($attribute) {
		$object_vars = get_object_vars($this);
		return array_key_exists($attribute, $object_vars);
		
	}
	
	protected function attributes() {
		$attributes = array();
		foreach(static::$db_fields as $field) {
			if (property_exists($this, $field)) {
				$attributes[$field] = $this -> $field;
			}
		}
		return $attributes;
		
	}
	
	protected function sanitized_attributes() {
		global $database;
		$clean_attributes = array();
		foreach ($this->attributes() as $key => $value) {
			$clean_attributes[$key] = $database->escape_value($value);
		}
		return $clean_attributes;
	}

//    Replaced	
    public function save() {
		
		return isset($this->id) ? $this->update() : $this->create();
	}
	
	public function create() {
		global $database;
		
		$attributes = $this->sanitized_attributes();
		
		$sql = "INSERT INTO ".static::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		if($database->query($sql)) {
			$this->id = $database->insert_id();
			return true;
		}   else {
			return false;
		}	
	}
	
		
	public function update() {
		global $database;
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
		  $attribute_pairs[] = "{$key}='{$value}'";
		}
		
		$sql = "UPDATE ".static::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=" .$database->escape_value($this->id);
		$database->query($sql);
		return ($database->affected_rows() == 1 ) ? TRUE : FALSE;
	}
	
	public function delete() {
		global $database;
		$sql = "DELETE FROM ".static::$table_name." ";
		$sql .= "WHERE id=" .$database->escape_value($this->id);
		$sql .= " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1 ) ? TRUE : FALSE;
	}
	
	
	protected function set_foreign_key() {
		global $database;
		$sql = "SET FOREIGN_KEY_CHECKS=0";
		return ($database->query($sql) == 1 ) ? TRUE : FALSE;
	}
	
	// select old data from mysql	
	public static function filter_old_database_date($value="") {
		global $database, $days_saved; 
		( isset($value) && is_numeric($value)) ? null : $value = $days_saved; 
		$sql  = "SELECT * FROM ".static::$table_name." ";
		$sql .= "WHERE time <= '".get_past_date($value, "string" )."' ";
		$result_set = $database->query($sql);
		$oject_array = array();
		while ($row = $database->fetch_array($result_set)){
			$oject_array[] = static::instantiate($row);
		}
		return (!empty($oject_array) ? $oject_array : false ) ;
	}
	

	
}



	
?>