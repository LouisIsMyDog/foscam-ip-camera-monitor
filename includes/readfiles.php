<?php

class readFiles
{
    private $databaseObjects;

    public function __construct()
    {
        clearstatcache(); // clear cache for function filesize()

        // ini_set('max_execution_time', 300);

    }

    public function cameraIndex($dir_array)
    {

        $index = array();

        $x = 0;

        foreach ($dir_array as $camera => $value) {

            if (!is_dir(__ROOT__ . $value['path'])) {continue;} //If camera directory does not exit then skip to next.

            if ($dir_handle = opendir(__ROOT__ . $value['path'])) {

                while ($filename = readdir($dir_handle)) {

                    if (substr($filename, 0, 1) != '.' && !is_dir($filename)) {
                        $extension = strtolower(substr($filename, strrpos($filename, '.') + 1, strlen($filename)));

                        if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif') {

                            $fullPath = __ROOT__ . $value['path'] . "/" . $filename;
                            $time     = timestamp($filename);
                            $size     = filesize($fullPath);
                            $type     = mime_content_type($fullPath);
                            $location = $value['location'];
                            $group_id = $this->getGroupId($location);
                            $index[]  = array(
                                'type'     => $type,
                                'size'     => $size,
                                'filename' => $filename,
                                'path'     => $value['path'],
                                'time'     => $time['timestamp'],
                                'camera'   => $camera,
                                'channel'  => $time['channel'],
                                'group_id' => $group_id,
                                'unix'     => $time['unix'],
                            );

                            $snap_object = Snapshot::build($index[$x]);

                            if ($snap_object->moveFile()):

                                $snap_object->snapCreate();

                            endif;

                            $x++;

                        }
                    }
                } // End of While

                closedir($dir_handle);

            }
        } // End of Foreach

        if (array_key_exists(0, $index)) {return true;} else {return false;}

    }

    private function getGroupId($camera)
    {
        return array_search($camera, Snapshot::$groupID_array);
    }

    // Checks Database
    public function duplicateExists($needle, $index = 0)
    {

        $this->databaseObjects = Snapshot::find_all();

        foreach ($this->databaseObjects as $key => $value):

            if ($index < 1) {

                $filename_array[] = $value->filename;

            } else {

                $filename_array[] = $value->camera . "_" . $value->filename;

            }
        endforeach;

        if (isset($filename_array)) {

            if (in_array($needle, $filename_array)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function executeCleansing()
    {

        $files = Snapshot::filterOldDatabaseDate(); // Enter number to control

        $action = "executeCleansing";
        $log    = "readFiles.log";

        if ($files != false):

            foreach ($files as $file):

                if (($catch = $file->deleteExistence()) === true) {
                    $message = "{$file->filename} was deleted. ";

                } else { $message = "Error - " . $catch;}

                logAction($action, $message, $log);

            endforeach;

        else:

            $message = "Query has no files selected."; // Query has no files selected.

        endif;

        self::fixNullSize();

        (isset($message)) ? logAction($action, $message, $log) : null;

    }

    public function deleteAllFiles()
    {
        $path              = __APP__ . "/images/";
        $scanned_directory = array_diff(scandir($path), array('..', '.', '.info.txt'));
        foreach ($scanned_directory as $key => $value) {
            $file = $path . $value;
            unlink($file);
        }
        logAction('deleteAllFiles', 'Deleted All Files in images/', 'readFiles.log');
    }

    public function countImageFiles()
    {
        $output = shell_exec('find ' . __APP__ . "/images/" . ' -name "*.jpg" -type f | wc -l');
        return $output;
    }

    public function sizeImageFolder()
    {
        $output  = shell_exec('du -sb ' . __APP__ . "/images/");
        $pattern = "/([\t]).*/";
        $output  = preg_replace($pattern, "", $output);
        return formatBytes($output);
    }

    // Fix Zero Size Rows in DB
    public static function fixNullSize()
    {
        $getNullIDS = Snapshot::snapshotFindSizeNull();
        if (is_array($getNullIDS)):
            foreach ($getNullIDS as $key => $value) {
                $snapshot = Snapshot::findById($value[0]);
                $fullPath = __ROOT__ . $snapshot->path . "/" . $snapshot->filename;
                $size     = filesize($fullPath);
                $log      = "Updated Size of {$snapshot->filename}, from {$snapshot->size} to {$size}.";
                logAction("fixNullSize", $log, "readFiles.log");
                $snapshot->size = $size;
                $snapshot->update();
            }
        endif;
    }

} // end of class

$readFiles = new readFiles();

//  Notes TO Do:
// Eventually find a way to delete individual snapshots
