<?php

require_once(__ROOT__ . '/CCTV/includes/initialize.php');

class readFiles  {

	
	public $camera_index;
	
	public $one_array;
	
	public $destination_index;
	
	private $databaseObjects;

	function __construct() {
		
		if(!isset($this->camera_index)) {
		ini_set('max_execution_time', 300);
		}
	}
	
	public function camera_index($dir_array) {
		
	$index = array();
		
		$x=0;
		
		foreach( $dir_array as $sub_array => $path ) {
		  
		  if ( $dir_handle = opendir( __ROOT__.$path[1] ) ) {
			
			while ( $filename = readdir( $dir_handle ) ) {
		    	
		    	if (substr($filename, 0, 1) != '.' && !is_dir($filename)) {
		  			$extension = strtolower(substr($filename, strrpos($filename,'.') + 1, strlen($filename)));
		  				
		  				if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif') {
							
							$fullPath 	= __ROOT__ . $path[1] . "/" . $filename;
							$time 		= timestamp($filename);
							$size 		= filesize($fullPath);
							$type 		= mime_content_type($fullPath);
							$camera     = $path[0];
							$group_id   = $this->get_group_id($camera);
							$index[]    = array( 
												 'type' => $type, 
												 'size' => $size,
											 'filename' => $filename , 
											 	 'path' => $path[1],
											 	 'time' => $time['timestamp'],
											   'camera' => $camera, 
											  'channel' => $time['channel'],
											 'group_id' => $group_id,  
											     'unix' => $time['unix']		  
								          );
							
							$snap_object  = Snapshot::build( $index[$x] );  
							
							$snap_object->create();
							
							$snap_object->move_file();
							
							$x++;	
							
							}
						}
					} // End of While
					
					closedir($dir_handle);
					
				} 
			} // End of Foreach
			
			if(array_key_exists(0,$index)) { return true; } else { return false; }
			
	}
	
	public function single_item($item='',$index=0) {
	
	if($index<1){
			if(isset($this->camera_index)) {
				
				$this->one_array = $this->camera_index[$item];
				
				} else {return false;}
		} else {
			
				$this->set_destination_index();
				return $this->one_array = $this->destination_index[$item];
		}
	}
	
	public function set_destination_index() {
		$this->destination_index = $this->destination_index(IMAGE_DIR);
	}
	
	
	private function get_group_id($camera) {			 
		return array_search($camera, Snapshot::$groupID_array);
	}
			
	private function destination_index($path) {
		$index = array();
		
		if ($dir_handle = opendir(__ROOT__.$path)) {
			while ($filename = readdir($dir_handle)) {
				if (substr($filename, 0, 1) != '.' && !is_dir($filename)) {
		  			$extension = strtolower(substr($filename, strrpos($filename,'.') + 1, strlen($filename)));
		  				if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif') {
							
							preg_match('/^[^_]+(?=_)/', $filename, $result); // JUST ADDED
							
							$fullPath 	= __ROOT__ . $path . "/" . $filename;
							$time 		= timestamp($filename);
							$size 		= filesize($fullPath);
							$type 		= mime_content_type($fullPath);
							$cameraName = $result[0]; // JUST ADDED
							$camera     = $cameraName; //fix 
							$group_id   = isset($cameraName) ? $this->get_group_id($camera) : "" ;
				            $index[]    = array( 
												 'type' => $type, 
												 'size' => $size,
											 'filename' => $filename , 
											 	 'path' => $path,
											 	 'time' => $time['timestamp'],
											   'camera' => $camera, 
											  'channel' => $time['channel'],
											 'group_id' => $group_id,  
											     'unix' => $time['unix']		  
											);
					}
				}
			}
			closedir($dir_handle);
		}
			return	$this->destination_index = $index;
	}
	
		// Checks Database	
	public function duplicate_exists($needle, $index=0) {
	
		$this->databaseObjects = Snapshot::find_all();
	
		foreach($this->databaseObjects as $key => $value ):
		
		if($index<1) {
			
		$filename_array[] = $value->filename;
		
		} else {
			
		$filename_array[] = $value->camera."_".$value->filename;
			
		}
		endforeach;
		
		if(isset($filename_array)) {
			
			if(in_array($needle, $filename_array)) {
				return true;
				} else {
				return false;
				}
	    } else {
		 return false;
		 }
	}
	
	public function execute_cleansing() {
		
		$files = Snapshot::filter_old_database_date(); // Enter number to control
		
		
		$action = "Files Deleted";
		$log    = "deleted_files.log";
		
		if($files != false):
		
			foreach ($files as $file) :
			
				if( ($catch = $file->delete_existence()) === TRUE ) {
					$message = "{$file->filename} was deleted. ";
					
				} else { $message = "Error - " . $catch; }
				
					log_action($action, $message, $log);
			
			endforeach;
			
			else: $message = "Query has no files selected."; // Query has no files selected.
		
		endif;
		
		(isset($message)) ? log_action($action, $message, $log) : NULL ;
		
		}
	
} // end of class

$readFiles = new readFiles();

 

	


?>