<?php

require "../db/connect.php";

// Get the DataTables request parameters
$draw = intval($_GET['draw']);
$start = intval($_GET['start']);
$length = intval($_GET['length']);

// Fetch data from your database table
$sql = "SELECT * FROM asset LIMIT $start, $length";
$result = $conn->query($sql);

$data = array();
while($row = $result->fetch_assoc()) {
    $data[] = array(
        "id" => $row['id'],
        "parent" => $row['parent'],
        "name" => $row['name'],
        "class" => $row['class'],
        "user" => $row['user'],
        "price" => $row['price'],
        "description" => $row['description'],
        "position" => $row['position'],
        "expire" => $row['expire'],
        "actions" => "<a title=\"User Info\" class=\"btn btn-datatable\" href=\"user.php?id=".$row['id']."\">
        Info
        </a>"
    );
}

// Get the total number of records in the table
$sql = "SELECT COUNT(*) as total FROM asset";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total = $row['total'];

// Prepare the JSON response
$response = array(
    "draw" => $draw,
    "recordsTotal" => $total,
    "recordsFiltered" => $total,
    "data" => $data
);

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>