<?php
require "../../db/connect.php";

$username = "superadmin";
$password = 'MgrW*8!QYx4$T@T6e3ws35';

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$current_time = time();

$query = "INSERT INTO user (date_created, name, password, entity, department) VALUES ('$current_time', '$username', '$hashed_password', 1, 1)";

if ($conn -> query($query)) echo "Insertion succeed";
else var_dump($conn->error_list);

