<?php

class DatabaseObject
{

    // common database methods
    public static function findAll()
    {

        return static::findBySql("SELECT * FROM " . static::$table_name);
    }

    public static function findById($id = 0)
    {
        global $database;
        $result_array = static::findBySql("SELECT * FROM " . static::$table_name . " WHERE id=" . $database->escapeValue($id) . " LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function findBySql($sql = "")
    {
        global $database;
        $result_set  = $database->query($sql);
        $oject_array = array();
        while ($row = $database->fetchArray($result_set)) {
            $oject_array[] = static::instantiate($row);
        }
        return $oject_array;
    }

    public static function countAll($where = '')
    {
        global $database, $session;

        $sql = "SELECT COUNT(*) FROM " . static::$table_name . " AS s, permissions AS p  WHERE p.user_id={$session->user_id} AND s.group_id=p.group_id {$where}";

        $result_set = $database->query($sql);
        $row        = $database->fetchArray($result_set);
        return array_shift($row);

    }

    private static function instantiate($record)
    {
        $class_name = get_called_class();
        $object     = new $class_name;

        foreach ($record as $attribute => $value) {
            if ($object->hasAttribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    private function hasAttribute($attribute)
    {
        $object_vars = get_object_vars($this);
        return array_key_exists($attribute, $object_vars);

    }

    protected function attributes()
    {
        $attributes = array();
        foreach (static::$db_fields as $field) {
            if (property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;

    }

    protected function sanitizedAttributes()
    {
        global $database;
        $clean_attributes = array();
        foreach ($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $database->escapeValue($value);
        }
        return $clean_attributes;
    }

//    Replaced
    public function save()
    {

        return isset($this->id) ? $this->update() : $this->create();
    }

    public function create()
    {
        global $database;

        $attributes = $this->sanitizedAttributes();
        unset($attributes['id']);
        //       $attributes = preg_replace("/''/i", NULL, $attributes);
        //    error_log(implode(",", array_keys($attributes)));
        $sql = "INSERT INTO " . static::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
        if ($database->query($sql)) {
            $this->id = $database->insertId();
            return true;
        } else {
            return false;
        }
    }

    public function update()
    {
        global $database;
        $attributes      = $this->sanitizedAttributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        unset($attribute_pairs['id']);
        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE id=" . $database->escapeValue($this->id);
        logAction('update', $sql, 'database_object.log');
        $database->query($sql);
        return ($database->affectedRows() == 1) ? true : false;
    }

    public function delete()
    {
        global $database;
        $sql = "DELETE FROM " . static::$table_name . " ";
        $sql .= "WHERE id=" . $database->escapeValue($this->id);
        $sql .= " LIMIT 1";
        logAction('delete', $sql, 'database_object.log');
        $database->query($sql);
        return ($database->affectedRows() == 1) ? true : false;
    }

    protected function setForeignKey()
    {
        global $database;
        $sql = "SET FOREIGN_KEY_CHECKS=0";
        return ($database->query($sql) == 1) ? true : false;
    }

    // select old data from mysql
    public static function filterOldDatabaseDate($value = "")
    {
        global $database, $days;
        (isset($value) && is_numeric($value)) ? null : $value = $days;
        $sql                                                  = "SELECT * FROM " . static::$table_name . " ";
        $sql .= "WHERE time <= '" . getPastDate($value, "string") . " 23:59:59' ";
        logAction('filterOldDatabaseDate', $sql, 'database_object.log');
        $result_set  = $database->query($sql);
        $oject_array = array();
        while ($row = $database->fetchArray($result_set)) {
            $oject_array[] = static::instantiate($row);
        }
        return (!empty($oject_array) ? $oject_array : false);
    }

    protected function deleteEverything()
    {
        global $database;
        $sql = "DELETE FROM snapshots";
        return ($database->query($sql) == 1) ? true : false;
    }

    protected function tableCount()
    {
        global $database;
        $sql        = "SELECT COUNT(*) FROM " . static::$table_name;
        $result_set = $database->query($sql);
        $row        = $database->fetchArray($result_set);
        return !empty($row) ? array_shift($row) : false;
    }

}
