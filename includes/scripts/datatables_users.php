<?php

require "../db/connect.php";
include "../get_subdepartments.php";
// Get the DataTables request parameters

//TODO : change the column for rm, (entity -> possesing assets)
$draw = intval($_GET['draw']);
$start = intval($_GET['start']);
$length = intval($_GET['length']);

$roleid = intval($_GET['roleid']);
$entityid = intval($_GET['entityid']);
$departmentid = intval($_GET['departmentid']);

if($roleid == 1){
    if($departmentid == -1){
        $sql = "SELECT * FROM user WHERE 1=1";
    } else {//access through manage user
        $subdepartmentids = getALLSubdepartmentIds($departmentid,$conn);
        $subdepartmentids = implode(',',$subdepartmentids);
        $sql = "SELECT * FROM user WHERE department IN ($subdepartmentids)";
    }
} else if ($roleid == 2){
    if($departmentid == -1){//access through navbar
        $sql = "SELECT * FROM user WHERE entity = $entityid";
    } else {//access through manage user
        $subdepartmentids = getALLSubdepartmentIds($departmentid,$conn);
        $subdepartmentids = implode(',',$subdepartmentids);
        $sql = "SELECT * FROM user WHERE department IN ($subdepartmentids) AND entity = $entityid";
    }
} else if ($roleid == 3){
    $subdepartmentids = getALLSubdepartmentIds($departmentid,$conn);
    $subdepartmentids = implode(',',$subdepartmentids);
    $sql = "SELECT * FROM user WHERE department IN ($subdepartmentids)";
}

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
        $entity = "--";
    }

    if (isset($row['department'])) {
        $department_id = $row['department'];
        $department = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$department_id'"))['name'];
    } else {
        $department = "--";
    }

    if (isset($row['role'])) {
        $role_id = $row['role'];
        $role = mysqli_fetch_array($conn->query("SELECT role FROM role WHERE id = '$role_id'"))['role'];
    } else {
        $class = "--";
    }

    $entity_super = $row['entity_super'];
    if ($entity_super == '1') {
        $entity = '<span class="badge bg-warning text-white">' . $entity . '</span>';
    } else {
        $entity = '<span class="badge bg-primary text-white">' . $entity . '</span>';
    }

    if($roleid == 3){ // resource manager can't edit users
        $data[] = array(
            "id" => $row['id'],
            "date_registered" => gmdate("Y.m.d \ | H:i:s", $row['date_created']),
            "name" => $row['name'],
            "entity" => $entity,
            "department" => $department,
            "role" => $role
        );
    } else {
        $data[] = array(
            "id" => $row['id'],
            "date_registered" => gmdate("Y.m.d \ | H:i:s", $row['date_created']),
            // "name" => "<a class='text-primary' href='/edit_user.php?id=".$row['id']."'>". $row['name']."</a>",
            "name" => $row['name'],
            "entity" => $entity,
            "department" => $department,
            "role" => $role,
            "actions" => "<a title=\"User Info\" class=\"btn btn-datatable\" href=\"edit_user.php?id=".$row['id']."\">
            edit
            </a>"
        );
    }
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
// header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>