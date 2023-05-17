<?php

require "../db/connect.php";
include "../get_subdepartments.php";
include "functions.php";
// Get the DataTables request parameters
$draw = intval($_GET['draw']);
$start = intval($_GET['start']);
$length = intval($_GET['length']);

$userid = intval($_GET['userid']);
$roleid = intval($_GET['roleid']);
$entityid = intval($_GET['entityid']);
$department_id = intval($_GET['departmentid']); //-1 for superadmin and admin

switch ($roleid){
    case 1: // super admin 
        $sql = "SELECT * FROM asset WHERE 1=1";
        break;
    case 2: // admin
        $departmentids = getAllDepartmentIds($entityid,$conn);
        $departmentids = implode(',',$departmentids);
        $sql = "SELECT * FROM asset WHERE department IN ($departmentids)";
        break;
    case 3: // for resource manager, load assets in the department and sub-department
        $subdepartmentids = getALLSubdepartmentIds($department_id,$conn);
        $subdepartmentids = implode(',',$subdepartmentids);
        $sql = "SELECT * FROM asset WHERE department IN ($subdepartmentids)";
        break;
    case 4: // for user, just load assets in the department
        // TO-IMPROVE : also load assets in the sub-departments?
        $sql = "SELECT * FROM asset WHERE department = $department_id";
        break;
    default:
        break;
}

if (isset($_GET['search']['value'])) {
    $search_string = $_GET['search']['value'];
    if (!empty($search_string)) {
        $class_condition = "";
        $asset_class_sql = "SELECT * FROM asset_class WHERE name LIKE '%$search_string%'";
        $asset_query_result = $conn->query($asset_class_sql);
        if ($asset_query_result->num_rows > 0) {
            $class_array = array();
            while ($row = $asset_query_result->fetch_assoc()) {
                array_push($class_array, $row['id']);
            }
            $class_condition .= " OR class IN (" . implode(", ", $class_array) . ")";
        }
        $sql .= " AND (name LIKE '%$search_string%' OR description LIKE '%$search_string%'" . $class_condition . " OR custom_attr LIKE '%$search_string%')";

    }
}
$sql .= " ORDER BY status ASC LIMIT $start, $length";
$result = $conn->query($sql);

$data = array();
while($row = $result->fetch_assoc()) {
    if(isset($row['status'])){
        $status_id = $row['status'];
        $status = mysqli_fetch_array($conn->query("SELECT status FROM asset_status_class WHERE id = '$status_id'"))['status'];
    } else {
        $status = "--";
    }

    if (isset($row['user'])) {
        $user_id = $row['user']; // asset user id
        $user = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$user_id'"))['name'];
    } else {
        $user = "--";
    }

    if (isset($row['parent'])) {
        $parent_id = $row['parent'];
        $parent = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$parent_id'"))['name'];
    } else {
        $parent = "--";
    }

    if (isset($row['class'])) {
        $class_id = $row['class'];
        $class = mysqli_fetch_array($conn->query("SELECT name FROM asset_class WHERE id = '$class_id'"))['name'];
    } else {
        $class = "--";
    }

    if(isset($row['department'])){
        $department_id_ = $row['department'];
        $department = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$department_id_'"))['name'];
    }else {
        $department = "--";
    }


if($roleid < 4){ // super admin, admin, resource manager
    $data[] = array(
        "id" => $row['id'],
        "parent" => $parent,
        "name" => "<a class='text-primary' href='/asset.php?id=".$row['id']."&name=".$row['name']."'>". $row['name']."</a>",
        "class" => $class,
        "user" => $user,
        "department" => $department,
        // "description" => isset($row['description']) ? strip_tags(substr($row['description'],0,30)) . "..." : '',
        "position" => $row['position'] ? $row['position'] : "--",
        "expire" => $row['expire'], 
        // add Modal for the requests?
        // "status" => ($status_id >=6 && $status_id <= 9)? "<button class= 'text-primary handleRequestButton' data-bs-toggle='modal' data-bs-target = '#handleRequestModal'>"."You have pending Request! : ".$status. "</button>" : $status,
        "status" => $status,
        "actions" => "<a title=\"User Info\" class=\"btn btn-datatable\" href=\"edit_asset.php?id=".$row['id']."&name=".$row['name']."\">
            Edit
        </a>" // TODO: put icon here
    );
} else { // user
    $data[] = array(
        "id" => $row['id'],
        "parent" => $parent,
        "name" => "<a class='text-primary' href='../../asset_info.php?id=".$row['id']."&name=".$row['name']."'>". $row['name']."</a>",
        "class" => $class,
        "user" => $user,
        "department" => $department,
        // "description" => isset($row['description']) ? strip_tags(substr($row['description'],0,30)) . "..." : '',
        "position" => $row['position'],
        "expire" => $row['expire'],
        "status" => $status
        // "actions" => "<a title=\"Asset image\" class=\"btn btn-datatable\" href=\"request_asset_status.php?id=".$row['id']."&name=".$row['name']."\">
        // Image
        // </a>"
    );
}
}

// Get the total number of records in the table

// TODO: change this 
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