<?php

class setDatabase
{

    public function __construct()
    {
    }

    private function permissions()
    {
        global $db;
        $sql = "CREATE TABLE `permissions` ( ";
        $sql .= "`user_id` int(3) NOT NULL, ";
        $sql .= "`group_id` int(3) NOT NULL, ";
        $sql .= "CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`), ";
        $sql .= "CONSTRAINT `permissions_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `snapshots` (`group_id`) ";
        $sql .= ");";
        return $db->query($sql) ? true : false;
    }

    private function snapshots()
    {
        global $db;
        $sql = "CREATE TABLE `snapshots` ( ";
        $sql .= "`id` int(12) NOT NULL AUTO_INCREMENT, ";
        $sql .= "`type` varchar(100) NOT NULL, ";
        $sql .= "`size` int(11) NOT NULL, ";
        $sql .= "`filename` varchar(100) NOT NULL, ";
        $sql .= "`path` varchar(255) NOT NULL, ";
        $sql .= "`time` datetime NOT NULL, ";
        $sql .= "`camera` varchar(100) NOT NULL, ";
        $sql .= "`channel` varchar(100) NOT NULL, ";
        $sql .= "`group_id` int(3) NOT NULL, ";
        $sql .= "`unix` int(11) NOT NULL, ";
        $sql .= "PRIMARY KEY (`id`), ";
        $sql .= "KEY `group_id` (`group_id`) ";
        $sql .= ");";
        return $db->query($sql) ? true : false;
    }

    private function users()
    {
        global $db;
        $sql = "CREATE TABLE `users` ( ";
        $sql .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
        $sql .= "`username` varchar(50) NOT NULL, ";
        $sql .= "`password` varchar(40) NOT NULL, ";
        $sql .= "`first_name` varchar(30) NOT NULL, ";
        $sql .= "`last_name` varchar(30) NOT NULL, ";
        $sql .= "PRIMARY KEY (`id`) ";
        $sql .= ");";
        return $db->query($sql) ? true : false;
    }

    private function insertAdmin()
    {
        global $db, $defaultUser;
        $sql = "INSERT INTO users ";
        $sql .= "(username, password, first_name, last_name ) ";
        $sql .= "VALUES ('" . $defaultUser['user'] . "', '" . $defaultUser['password'] . "', '" . $defaultUser['firstname'] . "', '" . $defaultUser['lastname'] . "' );";
        return $db->query($sql) ? true : false;
    }

    public function start()
    {
        global $db;
        if ($this->users()) {
            if ($this->snapshots()) {
                if ($this->permissions()) {
                    $this->insertAdmin();
                    $this->setPer();
                    $db->closeConnection();
                    return true;
                } else {return 'Table permissions could not be created.';}
            } else {return 'Table snapshots could not be created.';}
        } else {return 'Table users could not be created.';}
    }

    private function setPer()
    {
        global $group_ids, $permissions, $db;
        $sql = 'SET FOREIGN_KEY_CHECKS=0;';
        if ($db->query($sql)): $permissions->setPermissions('1', $group_ids);
        endif;
    }

}

$creat_DB = new setDatabase;
