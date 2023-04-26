<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../models/Singularity.php';
require_once '../config/Database.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Initialize class
$singularity = new Singularity($db);
$data = $singularity->getAssets();

echo json_encode($data);
