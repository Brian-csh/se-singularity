<?php
class Database {
    // DB Parameters
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    // Constructor
    public function __construct() {
        if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost') {
            $this->host = 'localhost';
            $this->db_name = 'singularity';
            $this->username = 'root';
            $this->password = '';
        } else {
            $this->host = 'singularity-db.Singularity.secoder.local';
            $this->db_name = 'singularity';
            $this->username = 'singularity';
            $this->password = 'R&4*h223b5yP';
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
