<?php

require "../db/connect.php";

// Get the DataTables request parameters
$draw = intval($_GET['draw']);
$start = intval($_GET['start']);
$length = intval($_GET['length']);

$departmentid = intval($_GET['departmentid']);
$entityid = intval($_GET['entityid']);

// Fetch data from your database table
if ($departmentid != -1)
    $sql = "SELECT * FROM user WHERE department = $departmentid"; 
else if ($entityid != -1)
    $sql = "SELECT * FROM user WHERE entity = $entityid";
else
    $sql = "SELECT * FROM user WHERE 1=1";

if (isset($_GET['search']['value'])) {
    $search_string = $_GET['search']['value'];
    if (!empty($search_string))
        $sql .= " AND (name LIKE '%$search_string%')";
}

$sql .= " LIMIT $start, $length";

$result = $conn->query($sql);

$data = array();
while($row = $result->fetch_assoc()) {
    if (isset($row['entity'])) {
        $entity_id = $row['entity'];
        $entity = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];
    } else {
        $entity = "N/A";
    }

    if (isset($row['department'])) {
        $department_id = $row['department'];
        $department = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$department_id'"))['name'];
    } else {
        $department = "N/A";
    }

    if (isset($row['role'])) {
        $role_id = $row['role'];
        $role = mysqli_fetch_array($conn->query("SELECT role FROM role WHERE id = '$role_id'"))['role'];
    } else {
        $class = "N/A";
    }

    $entity_super = $row['entity_super'];
    if ($entity_super == '1') {
        $entity = '<span class="badge bg-warning text-white">' . $entity . '</span>';
    } else {
        $entity = '<span class="badge bg-primary text-white">' . $entity . '</span>';
    }

    $data[] = array(
        "id" => $row['id'],
        "date_registered" => gmdate("Y.m.d \ | H:i:s", $row['date_created']),
        "name" => "<a class='text-primary' href='/edit_user.php?id=".$row['id']."'>". $row['name']."</a>",
        "entity" => $entity,
        "department" => $department,
        "role" => $role,
        "actions" => "<a title=\"User Info\" class=\"btn btn-datatable\" href=\"edit_user.php?id=".$row['id']."\">
        Info
        </a>"
    );
}

// Get the total number of records in the table
$sql = "SELECT COUNT(*) as total FROM user";
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