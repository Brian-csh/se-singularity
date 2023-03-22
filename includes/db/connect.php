<?php

$DB_Server = "localhost";
$DB_Username = "singularity";
$DB_Password = "R&4*h223b5yP";
$DB_DBName = "singularity";

$conn = new mysqli($DB_Server, $DB_Username, $DB_Password, $DB_DBName);

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}