<?php

require "../db/connect.php";


if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
}

if (isset($_FILES['csvFile'])) {
    $csvFile = $_FILES['csvFile']['tmp_name'];
    $timestamp = time();

    if (($handle = fopen($csvFile, 'r')) !== false) {
        $columns = fgetcsv($handle); // assuming the first row contains column headers
        $row_number = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $row_number++;
            $sql = "INSERT INTO asset (
                date_created,
                name, 
                class, 
                user, 
                department, 
                status
            ) VALUES ('$timestamp', ";

            foreach ($row as $i => $value) {
                $sql .= "'" . mysqli_real_escape_string($conn, $value) . "',";
            }

            $sql = rtrim($sql, ','); // remove the last comma
            $sql .= ");";

            try {
                $conn->query($sql);
            } catch (Exception $e) {
                header("Location: ../../assets.php?error=".$row_number);
                exit();
            }
        }

        fclose($handle);
    }
} else {
    echo "No file provided.";
}

$conn->close();
header("Location: ../../assets.php");
