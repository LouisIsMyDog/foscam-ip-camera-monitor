<?php

class Permissions extends DatabaseObject
{

    protected static $table_name = "permissions";

    protected $work_cameras;

    protected $all_cameras;

    protected $cameras;

    public function __construct()
    {
        global $group_ids;
        $this->setProperties("Apartment"); // select field to hide
        $this->cameras = $group_ids;
    }

    protected function addPermissions($user, $option)
    {
        if (isset($option) && $option == "work_cameras") {
            $camera = $this->work_cameras;
            $count  = count($camera);
            $sql    = "INSERT INTO " . static::$table_name . " (user_id, group_id) VALUES ";
            for ($x = 0; $x < $count; $x++) {
                if ($x != $count - 1):
                    $sql .= "({$user},{$camera[$x]}),";
                else:
                    $sql .= "({$user},{$camera[$x]})";
                endif;
            }

        }
        if (isset($option) && $option == "all_cameras") {
            $camera = $this->all_cameras;
            $count  = count($camera);
            $sql    = "INSERT INTO " . static::$table_name . " (user_id, group_id) VALUES ";

            for ($x = 0; $x < $count; $x++) {
                if ($x != $count - 1):
                    $sql .= "({$user},{$camera[$x]}),";
                else:
                    $sql .= "({$user},{$camera[$x]})";
                endif;
            }
        }

        return $sql;
    }

    protected function setProperties($value)
    {
        $snapshot = new Snapshot;
        $array    = Snapshot::$groupID_array;
        if (in_array($value, $array)) {
            $private_key = array_search($value, $array);
            unset($array[$private_key]);
            $this->work_cameras = array_keys($array);
        }
        $this->all_cameras = array_keys(Snapshot::$groupID_array);

    }

    public function createPermissions($user, $option)
    {
        global $database;
        $sql = $this->addPermissions($user, $option);
        $database->query($sql);
        return ($database->affectedRows() >= 1) ? true : false;
    }

    public function deletePermissions($user_id)
    {
        global $database;
        $this->disableCheck();
        $sql = "DELETE FROM permissions WHERE user_id={$user_id};";
        logAction('deletePermissions', $sql, 'permissions.log');
        $database->query($sql);
        return ($database->affectedRows() >= 1) ? true : false;
    }

    public function setPermissions($id, $camera)
    {
        global $database;
        $this->disableCheck();
        if (is_array($camera)):
            $sql = "INSERT INTO " . static::$table_name . " (user_id, group_id) VALUES ";
            $x   = count($camera);
            $i   = 0;
            foreach ($camera as $key => $value):
                if ($id != 1):
                    $key = $key + 1;
                endif;
                if ($i != $x - 1):
                    $sql .= "({$id}, {$key}), ";
                else:
                    $sql .= "({$id}, {$key});";
                endif;
                $i++;
            endforeach;
        endif;
        //echo $sql.'<br>';
        $database->query($sql);
        return ($database->affectedRows() >= 1) ? true : false;
    }

    private function disableCheck()
    {
        global $database;
        $sql = "SET FOREIGN_KEY_CHECKS=0;";
        $database->query($sql);
        return ($database->affectedRows() >= 1) ? true : false;
    }

}

$permissions = new Permissions();
