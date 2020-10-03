<?php

// Starting clock time in seconds
$start_time = microtime(true);

require_once 'index.php';

if (!isset($_SESSION['settings'])) {

    if ($readFiles->cameraIndex(Snapshot::$cameras)) {
        // something to do - if you want
    }
    // delete old images
    $readFiles->executeCleansing();
    // End clock time in seconds
    $end_time       = microtime(true);
    $execution_time = ($end_time - $start_time); // returns time in secs
    $execution_time = number_format($execution_time, 2, '.', ''); // format execution time
    logAction('CRON JOB', "The script was executed in $execution_time sec.", 'cron.log');

} else {

    logAction('CRON JOB', '**FAILED** The script could not be executed', 'cron.log');

}
