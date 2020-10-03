<?php

class User extends DatabaseObject
{

    public $id;
    public $username;
    public $password;
    public $first_name;
    public $last_name;

    protected static $table_name = "users";

    protected static $db_fields = array("id", "username", "password", "first_name", "last_name");

    public static function authenticate($username = "", $password = "")
    {
        global $database;
        $username = $database->escapeValue($username);
        $password = $database->escapeValue($password);

        $sql = "SELECT * FROM users ";
        $sql .= "WHERE username = '{$username}' ";
        $sql .= "AND password = '$password' ";
        $sql .= "LIMIT 1";

        $result_array = self::findBySql($sql);
        return !empty($result_array) ? array_shift($result_array) : false;

    }

    public function fullName()
    {

        if (isset($this->first_name) && isset($this->last_name)) {
            return $this->first_name . " " . $this->last_name;
        } else {
            return "";
        }
    }

    public function populateValues($user_id)
    {

        $sql = "SELECT * FROM users ";
        $sql .= "WHERE id= '$user_id'";
        $object = self::findBySql($sql);
        $this->id = $object[0]->id;
        $this->username = $object[0]->username;
        $this->password = '';
        $this->first_name = $object[0]->first_name;
        $this->last_name = $object[0]->last_name;
    }
}