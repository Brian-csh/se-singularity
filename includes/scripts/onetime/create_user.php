<?php
require "../../db/connect.php";

$username = "user1";
$password = '12345';

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$current_time = time();

$query = "INSERT INTO user (date_created, name, password, entity, department, role) VALUES ('$current_time', '$username', '$hashed_password', 1, 1, 4)";

if ($conn -> query($query)) echo "Insertion succeed";
else var_dump($conn->error_list);
