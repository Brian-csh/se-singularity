<?php

class Singularity {
    // DB stuff
    private $conn;

    // Constructor with DB
    public function __construct($db) {
        $this -> conn = $db;
    }

    public function getAssets () {
        $stmt = $this->conn->prepare('SELECT name, id FROM asset');
        $stmt->execute();
        $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($assets);
    }

}
