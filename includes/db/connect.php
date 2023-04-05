<?php

if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost') {
    $DB_Server = "localhost";
    $DB_Username = "root";
    $DB_Password = "";
} else {
    $DB_Server = "singularity-db.Singularity.secoder.local";
    $DB_Username = "singularity";
    $DB_Password = "R&4*h223b5yP";
}

$DB_DBName = "singularity";

$conn = new mysqli($DB_Server, $DB_Username, $DB_Password, $DB_DBName);

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}