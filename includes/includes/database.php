<?php

class MySQLDatabase
{

    private $connection;
    private static $counter;
    private $sql;

    public function __construct()
    {
        $this->openConnection();
    }

    public function openConnection()
    {
        $this->connection = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($this->connection->connect_errno) {
            die("Database connection failed: " .
                $this->connection->connect_errno .
                "(" . $this->connection->connect_errno . ")"
            );

        }
    }

    public function closeConnection()
    {
        if (isset($this->connection)) {
            $this->connection->close();
            unset($this->connection);
        }
    }

    public function query($sql, $check = true)
    {

        $this->sql = $sql;
        if ($this->connection->ping()) {
            $result = $this->connection->query($sql);
            if ($check) {
                $this->confirmQuery($result);
            }
        } else {
            echo "This is embarrassing. The query could not be sent :( <br />" . "Error: " . $this->connection->connect_error;
            $result = null;
            $this->confirmQuery($result);
        }

        return $result;
    }

    private function confirmQuery($result)
    {
        if (!$result) {
            die("<br />Database query failed: <br /> <p>" . $this->sql . "</p>");
        }
    }

    public function escapeValue($string)
    {
        $escaped_string = $this->connection->real_escape_string($string);
        return $escaped_string;
    }

    // database neutral functions
    public function fetchArray($result_set)
    {
        return $result_set->fetch_array();
    }

    public function numRows($result_set)
    {
        return $result_set->num_rows;
    }

    public function insertId()
    {
        // get last id inserted over current database connection
        return $this->connection->insert_id;
    }

    public function affectedRows()
    {
        return $this->connection->affected_rows;

    }

}

$database = new MySQLDatabase();
$db = &$database;
