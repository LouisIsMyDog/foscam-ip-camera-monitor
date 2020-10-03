<?php

class Snapshot extends DatabaseObject
{

    protected static $table_name = "snapshots";

    protected static $db_fields = array('id', 'type', 'size', 'filename', 'path', 'time', 'camera', 'channel', 'group_id', 'unix');

    public static $dir_assets;

    public static $groupID_array;

    public static $cameras;

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

    public $imagePathApp;
    public $new_filename;
    public $old_path_filename;
    public $new_path_filename;

    public function __construct()
    {
        global $group_ids, $cameras;

        self::$groupID_array = $group_ids;

        self::$cameras = $cameras;

    }

    // $index = 0 is camera_index, $index = 1 is destination_index
    public static function build($array)
    {

        foreach ($array as $key => $value) {

            ${$key} = $value;

        }

        $snapshot = new Snapshot();

        $snapshot->type     = $type;
        $snapshot->size     = (int) $size;
        $snapshot->filename = $filename;
        $snapshot->path     = $path;
        $snapshot->time     = $time;
        $snapshot->camera   = $camera;
        $snapshot->channel  = $channel;
        $snapshot->group_id = (int) $group_id;
        $snapshot->unix     = (int) $unix;

        return $snapshot;

    }

    public function snapCreate()
    {
        $this->createPaths();

        $action = "snapCreate";
        $log    = "snapshot.log";
        if ($this->create()) {
            $message = "Successful. Created Snapshot {$this->filename}.";
            logAction($action, $message, $log);

        } else {
            $message = "**Failed** Could not create Snapshot {$this->filename}.";
            logAction($action, $message, $log);
        }
        $action         = "Update DB Entry Row:";
        $this->path     = $this->imagePathApp; //update DB Path
        $this->filename = $this->new_filename; //update DB Filename
        if ($this->update()) {
            $message = "Successful. Updated data file from {$this->filename} to {$this->new_filename}.";
            logAction($action, $message, $log);
        } else {
            $message = "**Failed** Could not update data file from {$this->filename} to {$this->new_filename}.";
            logAction($action, $message, $log);
        }
    }

    private function createPaths()
    {
        $this->imagePathApp = '/' . APP_DIR . '/images';

        $this->new_filename = $this->camera . "_" . $this->filename;

        $this->old_path_filename = __ROOT__ . $this->path . '/' . $this->filename;

        $this->new_path_filename = __ROOT__ . $this->imagePathApp . '/' . $this->new_filename;

    }

    public function moveFile()
    {
        $this->createPaths();
        $action = "moveFile {$this->filename} to target path {$this->imagePathApp}";
        $log    = "snapshot.log";

        if (rename($this->old_path_filename, $this->new_path_filename)) {
            $message = "Successfully. Moved File.";
            logAction($action, $message, $log);
            return true;
        } else {
            $message = "**Failed** | {$this->old_path_filename}::{$this->new_path_filename} |";
            logAction($action, $message, $log);

            return false;
        }

    }

    public function checkDate()
    {
        global $days;
        $marker = getPastDate($days, "unix");
        logAction('checkDate', "$marker > " . strtotime($this->time), 'snapshot.log');
        return ($marker > strtotime($this->time)) ? true : false; // True if time is older than $days variable = which means save images.

    }

    public function eraseSnapshots()
    {
        if ($this->setForeignKey()) {
            if ($this->deleteEverything()):
                logAction('eraseSnapshots', 'Deleted from DB', 'snapshot.log');
            endif;
        }
    }

    public function deleteExistence()
    {

        if ($this->checkDate()) {

            $new_path_filename = __ROOT__ . $this->path . '/' . $this->filename;

            if (unlink($new_path_filename)) {

                if ($this->setForeignKey()) {
                    logAction('deleteExistence', "Deleted {$this->filename} from Directory", 'snapshot.log');
                    return ($this->delete()) ? true : "Could not delete from database."; // if false could not delete from database.

                } else {return "Could not set foreign key.";} // Could not set foreign key

            } else {return "Could not delete actual file from server.";} // could not delete file from server

        } else {return "Date needs to be saved.";} // date needs to be saved
    }

    public static function countSnapshots()
    {
        $blank = new Snapshot;
        return $blank->tableCount();
    }

    public static function retrievePhotos($array)
    {
        global $session;
        $sql = "SELECT * FROM snapshots AS s, permissions AS p  WHERE p.user_id={$session->user_id} AND s.group_id=p.group_id AND date(time) = '" . date("Y-m-d", strtotime($array['date'])) . "' ORDER BY time {$array['order']} LIMIT {$array['per_page']} OFFSET {$array['offset']}";
        logAction('retrievePhotos', $sql, 'snapshot.log');
        return static::findBySql($sql);
    }

    public static function retrievePhotoIDs($array)
    {
        global $database, $session;
        $sql            = "SELECT id FROM snapshots AS s, permissions AS p  WHERE p.user_id={$session->user_id} AND s.group_id=p.group_id AND date(time) = '" . date("Y-m-d", strtotime($array['date'])) . "' ORDER BY time {$array['order']}";
        $result_set     = $database->query($sql);
        $photo_id_array = mysqli_fetch_all($result_set);
        return $photo_id_array;
    }

    public static function getDates()
    {
        global $database;
        $sql        = "SELECT DISTINCT date(time) from snapshots";
        $result_set = $database->query($sql);
        $date_array = mysqli_fetch_all($result_set);
        return $date_array;
    }

    public static function ajaxRetrievePhotos($array)
    {
        $arrow = ($array['order'] == 'DESC') ? '<=' : '>=';
        global $session;
        logAction('ajaxRetrievePhotos', serialize($array), 'snapshot.log');
        $sql = "SELECT * FROM snapshots AS s, permissions AS p  WHERE p.user_id={$session->user_id} AND s.group_id=p.group_id AND date(time) = '" . date("Y-m-d", strtotime($array['date'])) . "' AND ID {$arrow} {$array['markerid']} ORDER BY time {$array['order']} LIMIT {$array['per_page']} OFFSET {$array['offset']}";
        logAction('ajaxRetrievePhotos', $sql, 'snapshot.log');
        return static::findBySql($sql);
    }

    public static function snapshotFindSizeNull()
    {
        // Fix snapshot size where it equals 0
        global $database;
        $sql        = "SELECT id FROM snapshots WHERE size = 0";
        $result_set = $database->query($sql);
        $id_array   = mysqli_fetch_all($result_set);
        return $id_array;
    }

    public static function getMarkerID($array)
    {
        global $session;
        $sql = "SELECT * FROM snapshots AS s, permissions AS p  WHERE p.user_id={$session->user_id} AND s.group_id=p.group_id AND date(time) = '" . date("Y-m-d", strtotime($array['date'])) . "' ORDER BY time {$array['order']} LIMIT 1 OFFSET 0";
        logAction('getMarkerID', $sql, 'snapshot.log');
        return static::findBySql($sql)[0]->id;
    }

    //SELECT DISTINCT date(time) from snapshots
    //SELECT HOUR('15:13:46');
}
