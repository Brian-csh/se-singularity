<?php

require "../db/connect.php";

// Get the DataTables request parameters
$draw = intval($_GET['draw']);
$start = intval($_GET['start']);
$length = intval($_GET['length']);
$user_role = strval($_GET['role_id']);

// Fetch data from your database table
$sql = "SELECT * FROM asset LIMIT $start, $length";
$result = $conn->query($sql);

$data = array();
while($row = $result->fetch_assoc()) {
    if ($row['status'] == 1) {
        $status = "IDLE";
    } else if ($row['status'] == 2) {
        $status = "IN USE";
    } else if ($row['status'] == 3) {
        $status = "IN MAINTAIN";
    } else if ($row['status'] == 4) {
        $status = "RETIRED";
    } else if ($row['status'] == 5) {
        $status = "DELETED";
    }

    if (isset($row['user'])) {
        $user_id = $row['user'];
        $user = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$user_id'"))['name'];
    } else {
        $user = "N/A";
    }

    if (isset($row['parent'])) {
        $parent_id = $row['parent'];
        $parent = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$parent_id'"))['name'];
    } else {
        $parent = "N/A";
    }

    if (isset($row['class'])) {
        $class_id = $row['class'];
        $class = mysqli_fetch_array($conn->query("SELECT name FROM asset_class WHERE id = '$class_id'"))['name'];
    } else {
        $class = "N/A";
    }

if( $user_role != '4'){
    $data[] = array(
        "id" => $row['id'],
        "parent" => $parent,
        "name" => "<a class='text-primary' href='/asset.php?id=".$row['id']."&name=".$row['name']."'>". $row['name']."</a>",
        "class" => $class,
        "user" => $user,
        "price" => $row['price'],
        "description" => $row['description'],
        "position" => $row['position'],
        "expire" => $row['expire'],
        "status" => $status,
        "actions" => "<a title=\"User Info\" class=\"btn btn-datatable\" href=\"edit_asset.php?id=".$row['id']."&name=".$row['name']."\">
        Info
        </a>"
    );
} else { // user_role == 4 (user)
    $data[] = array(
        "id" => $row['id'],
        "parent" => $parent,
        "name" => "<a class='text-primary' href='../../asset_info.php?id=".$row['id']."&name=".$row['name']."'>". $row['name']."</a>",
        "class" => $class,
        "user" => $user,
        "price" => $row['price'],
        "description" => $row['description'],
        "position" => $row['position'],
        "expire" => $row['expire'],
        "status" => $status,
        "actions" => "<a title=\"User Info\" class=\"btn btn-datatable\" href=\"request_asset_status.php?id=".$row['id']."&name=".$row['name']."\">
        Info
        </a>"
    );
}
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