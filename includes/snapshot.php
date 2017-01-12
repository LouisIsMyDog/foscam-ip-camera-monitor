<?php

require_once(__ROOT__ . '/CCTV/includes/initialize.php');



class Snapshot extends DatabaseObject {


	protected static $table_name = "snapshots";

	protected static $db_fields  = array('id', 'type', 'size', 'filename', 'path', 'time', 'camera', 'channel', 'group_id', 'unix');

	public static $dir_assets;

	public static $groupID_array;

	public $id;
	public $type;
	public $size;
	public $filename;
	public $path;
	public $time;
	public $camera;
	public $channel;
	public $group_id;
	public $unix;
	
	function __construct() {
		global $directory, $group_ids;
		self::$dir_assets = $directory;
		self::$groupID_array = $group_ids;
		
	}

	// $index = 0 is camera_index, $index = 1 is destination_index
	public static function build($array) { 
						
			foreach( $array as $key => $value) {
				
				${$key} = $value;
				
			}

			$snapshot = new Snapshot();

			$snapshot->type         =      $type;
			$snapshot->size         = (int)$size;
			$snapshot->filename     =      $filename;
			$snapshot->path         =      $path;
			$snapshot->time         =      $time;
			$snapshot->camera       =      $camera;
			$snapshot->channel      =      $channel;
			$snapshot->group_id     = (int)$group_id;
			$snapshot->unix         = (int)$unix;
			
			return $snapshot;
			
			}

	
	protected function update_object_path() {

		$this->path = IMAGE_DIR;
		
		$this->filename = $this->camera."_".$this->filename;
		
		$this->update();
	}

	public function move_file() {
		
		$action = "Moving File {$this->filename}";
		$log    = "moved_files.log";
	
		$new_name     =  $this->camera."_".$this->filename;
			
		$camera_path  = __ROOT__ . $this->path . "/" . $this->filename;
			
		$target_path  = __ROOT__ . IMAGE_DIR . "/" . $new_name;
			
			
			if(rename($camera_path, $target_path)) {
				
				$message = "Successful. New Name: {$new_name}";
				
				log_action($action, $message, $log);
				
				$this->update_object_path();
				
			} else {
				$message = "Failed. -{$camera_path}::{$target_path}";
				log_action($action, $message, $log);
			
			}

		}
		
	public function check_date() {
		global $days_saved;
		$marker = get_past_date($days_saved);
		return (($marker < strtotime($this->time)) && ($marker != strtotime($this->time)) ) ? TRUE : FALSE ;  // True if time is older than $days_saved variable = which means save images.
		
	}	
	
	public function delete_existence() {
		
		if( !$this->check_date() ) {
			
			$target_path = __ROOT__ . $this->path . "/" . $this->filename;
			
			if(unlink($target_path)) {
				
				if( $this->set_foreign_key() )  {
				
				return ($this->delete()) ? TRUE : "Could not delete from database."; // if false could not delete from database. 
				
				} else { return "Could not set foreign key."; } // Could not set foreign key
				
			} else {return "Could not delete actual file from server.";} // could not delete file from server
			
		} else {return "Date needs to be saved.";} // date needs to be saved
	}	

}


?>