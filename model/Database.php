<?php
class Database
{
    private $conn;

    public function __construct()
    {
        require_once 'Config.php';

        // Initialize the database connection using mysqli
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check for connection errors
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
