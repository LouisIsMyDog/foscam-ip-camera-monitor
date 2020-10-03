<?php

function stripZeroFromDate($marked_string = "")
{
    //First remove the marked zeros
    $no_zeros = str_replace('*0', '', $marked_string);
    //then remove the remaining marks
    $cleaned_string = str_replace('*', '', $no_zeros);
    return $cleaned_string;

}

function redirectTo($location = null)
{
    if ($location != null) {

        logAction('redirectTo', $location, 'functions.log');
        header("Location: {$location}");
        exit;
    }
}

function outputMessage($message = "")
{
    if (!empty($message)) {
        return "<p class=\"message\">{$message}</p>";
    } else {
        return "";
    }
}

function __autoload($class_name)
{
    $class_name = strtolower($class_name);
    $path       = __APP__ . "/includes/{$class_name}.php";
    if (file_exists($path)) {
        require_once $path;
    } else {
        die("The file {$class_name}.php could not be found.");
    }
}

function includeLayoutTemplate($template = "")
{

    include_once __APP__ . "/templates/" . $template;
}

function logAction($action, $message = "", $log = "user.log")
{
    if (is_array($message)) {
        $message = implode(",", array_keys($message));
    }
    $logfile = __APP__ . '/logs/' . $log;
    $new     = file_exists($logfile) ? false : true;

    if ($handle = fopen($logfile, 'a')) {
        //append

        $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
        $content   = "{$timestamp} | {$action}: {$message}\n";
        fwrite($handle, $content);
        fclose($handle);
        if ($new) {chmod($logfile, 0755);}

    } else {
        echo "Could not open log file for writing.";
    }
}

function datetimeToText($datetime = "")
{
    $unixdatetime = strtotime($datetime);
    return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function timestamp($filename)
{

    $timestamp = '';

    if (preg_match('/(\d{8})-(\d{6})/', $filename, $result)) {

        $timestamp = substr($result[1], 0, 4) . '-' . substr($result[1], 4, 2) . '-' . substr($result[1], 6, 2) . ' ' . substr($result[2], 0, 2) . ':' . substr($result[2], 2, 2) . '.' . substr($result[2], 4, 2); //this is what is being used -emre

        $unix      = strtotime($timestamp);
        $timestamp = strftime("%Y-%m-%d %H:%M:%S", $unix); // MySQL TIME

        $timestampArray = array('channel' => 'N/A', 'unix' => $unix, 'timestamp' => str_replace("-", "/", $timestamp));

        return $timestampArray;

    } else if (preg_match_all('/(\d{2,4})/', $filename, $result)) {

        $timestamp = 'CH-' . substr($result[0][0], 0, 2) . '  ' . substr($result[0][1], 0, 4) . '-' . substr($result[0][2], 0, 2) . '-' . substr($result[0][3], 0, 2) . ' ' . substr($result[0][4], 0, 2) . ':' . substr($result[0][5], 0, 2) . '.' . substr($result[0][6], 0, 2);

        $channel   = substr($timestamp, 0, 5);
        $NoChannel = str_replace($channel, "", $timestamp);
        $unix      = strtotime($NoChannel);
        $timestamp = strftime("%Y-%m-%d %H:%M:%S", $unix); // MySQL TIME

        $timestampArray = array('channel' => $channel, 'unix' => $unix, 'timestamp' => str_replace("-", "/", $timestamp));

        return $timestampArray;
    }
}

function formatBytes($bytes, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    // $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
    // return $bytes;
}

function humanFileSize($size, $unit = "")
{
    if ((!$unit && $size >= 1 << 30) || $unit == "GB") {
        return number_format($size / (1 << 30), 2) . "GB";
    }

    if ((!$unit && $size >= 1 << 20) || $unit == "MB") {
        return number_format($size / (1 << 20), 2) . "MB";
    }

    if ((!$unit && $size >= 1 << 10) || $unit == "KB") {
        return number_format($size / (1 << 10), 2) . "KB";
    }

    return number_format($size) . " bytes";
}

function getPastDate($day, $output = "")
{

    $date = date("Y-m-d"); // get todays date

    $current_date = strtotime($date); // convert date to unix timestamp

    $past_date = strtotime("-$day day", $current_date); // get unix timestamp of past date

    return ($output == "string") ? gmdate("Y-m-d", $past_date) : $past_date; // convert past unix timestamp to string time
}
