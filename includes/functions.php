<?php

function strip_zero_from_date($marked_string="") {
	//First remove the marked zeros
	$no_zeros = str_replace('*0', '', $marked_string);
	//then remove the remaining marks
	$cleaned_string = str_replace('*', '', $no_zeros);
	return $cleaned_string;

}

function redirect_to( $location = NULL) {
	if ($location != Null) {
		header("Location: {$location}");
		exit;
	}
}

function output_message($message="") {
	if (!empty($message)) {
		return "<p class=\"message\">{$message}</p>";
	} else {
		return "";
	}
}

function __autoload($class_name) {
	$class_name = strtolower($class_name);
	$path = __ROOT__ . "/CCTV/includes/{$class_name}.php";
	if ( file_exists($path) ) {
		require_once($path);
	} else {
		die("The file {$class_name}.php could not be found.");
	}
}

function include_layout_template($template="") {

	include_once(__ROOT__ . "/CCTV/templates/" . $template);
}

function log_action($action, $message="", $log="user.log") {

	$logfile = __ROOT__ . '/CCTV/logs/'. $log;
	$new = file_exists($logfile) ? FALSE : TRUE;

	if($handle = fopen($logfile, 'a'))  {  //append

		$timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
		$content = "{$timestamp} | {$action}: {$message}\n";
		fwrite($handle, $content);
		fclose($handle);
		if($new) { chmod($logfile, 0755); }

	} else {
		echo "Could not open log file for writing.";
	}
}

function datetime_to_text($datetime="") {
	$unixdatetime = strtotime($datetime);
	return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function timestamp($filename) {

	$timestamp = '';

	if (preg_match('/(\d{8})-(\d{6})/', $filename, $result)) {

		$timestamp =  substr($result[1],0,4) .'-'. substr($result[1],4,2) . '-' . substr($result[1],6,2) . ' ' . substr($result[2],0,2) . ':' . substr($result[2],2,2) . '.' . substr($result[2],4,2);//this is what is being used -emre

		$unix = strtotime( $timestamp );
		$timestamp = strftime("%Y-%m-%d %H:%M:%S", $unix ); // MySQL TIME

		$timestampArray = array( 'channel' => 'N/A', 'unix' => $unix, 'timestamp' => str_replace("-", "/",$timestamp) );

		return $timestampArray;


	} else  if (preg_match_all('/(\d{2,4})/', $filename, $result)) {

			$timestamp = 'CH-'.substr($result[0][0],0,2).'  '. substr($result[0][1],0,4) .'-'. substr($result[0][2],0,2) . '-' . substr($result[0][3],0,2) . ' ' . substr($result[0][4],0,2) . ':' . substr($result[0][5],0,2) . '.' . substr($result[0][6],0,2);

			$channel = substr($timestamp, 0, 5);
			$NoChannel = str_replace($channel ,"",$timestamp);
			$unix = strtotime( $NoChannel );
			$timestamp = strftime("%Y-%m-%d %H:%M:%S", $unix ); // MySQL TIME

			$timestampArray = array( 'channel' => $channel, 'unix' => $unix, 'timestamp' => str_replace("-", "/",$timestamp) );

			return $timestampArray;
		}
}

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
     $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
}

function get_past_date($day,$output="") {
	
	$date = date("Y-m-d"); // get todays date

	$current_date = strtotime($date);  // convert date to unix timestamp

	$past_date = strtotime("-$day day",$current_date);  // get unix timestamp of past date

	return ($output == "string") ? gmdate("Y-m-d", $past_date) : $past_date;  // convert past unix timestamp to string time
}

?>