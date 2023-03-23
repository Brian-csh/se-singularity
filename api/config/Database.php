<?php
class Database {
    // DB Parameters
    private $host;
    private $db_name = 'singularity';
    private $username = 'singularity';
    private $password = 'R&4*h223b5yP';
    private $conn;

    // Constructor
    public function __construct() {
        if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost') {
            $this->host = 'localhost';
        } else {
            $this->host = 'singularity-db.Singularity.secoder.local';
        }
    }

    // DB Connection
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }
        return $this->conn;
    }
}
