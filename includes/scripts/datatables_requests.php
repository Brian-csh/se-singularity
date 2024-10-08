<?php

require "../db/connect.php";
require "../get_subdepartments.php";
include "functions.php";

// Get the DataTables request parameters
$draw = intval($_GET['draw']);
$start = intval($_GET['start']);
$length = intval($_GET['length']);

$userid = intval($_GET['userid']);
$roleid = intval($_GET['roleid']);
$entityid = intval($_GET['entityid']);
$departmentid = intval($_GET['departmentid']);

// Get the total number of records in the table
switch ($roleid){
    case 1: // show all the requests? yes 
        $sql = "SELECT * FROM pending_requests WHERE 1=1";
        $result = $conn->query($sql);
        $total = mysqli_num_rows($result);
        break;
    case 2: // show all the requests in entity
        $departmentids = getAllDepartmentIds($entityid,$conn);
        $departmentids = implode(',',$departmentids);
        $sql = "SELECT * FROM pending_requests WHERE department IN ($departmentids)";
        $result = $conn->query($sql);
        $total = mysqli_num_rows($result);
        break;
    case 3: // show all the requests in the department & sub-departments
        $subdepartmentids = getALLSubdepartmentIds($departmentid,$conn);
        $subdepartmentids = implode(',',$subdepartmentids);
        $sql = "SELECT * FROM pending_requests WHERE department IN ($subdepartmentids)";
        $result = $conn->query($sql);
        $total = mysqli_num_rows($result);
        break;
    case 4:
        $sql = "SELECT * FROM pending_requests WHERE department = $departmentid AND initiator = $userid";
        $result = $conn->query($sql);
        $total = mysqli_num_rows($result);
        break;
    default:
        break;
}

//TODO : searching 
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

$sql .= " ORDER BY result ASC LIMIT $start, $length";

$result = $conn->query($sql);
$data = array();
while($row = $result->fetch_assoc()) {

    if(isset($row['initiator'])){
        $initiator_id = $row['initiator'];
        $initiator = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$initiator_id'"))['name'];
    } else {
        $initiator = "--";
    }

    if (isset($row['participant'])) {
        $participant_id = $row['participant'];
        $participant = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$participant_id'"))['name'];
    } else {
        $participant = "--";
    }

    if (isset($row['asset'])) {
        $asset_id = $row['asset'];
        $asset = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
    } else {
        $asset = "--";
    }

    if (isset($row['type'])) {
        $type_id = $row['type'];
        $type = mysqli_fetch_array($conn->query("SELECT name FROM request_type WHERE id = '$type_id'"))['name'];
    } else {
        $type = "--";
    }

    if ($row['result'] == 0) {
        $request_result = "Pending";
    } else if ($row['result'] == 1) {
        $request_result = "Approved";
    } else if ($row['result'] == 2) {
        $request_result = "Rejected";
    } else if ($row['result'] == 3) {
        $request_result = "Canceled";
    } else {
        $request_result = "--";
    }


    $data[] = array(
        "id" => $row['id'],
        "initiator" => $initiator,
        "participant" => $participant,
        "asset" => $asset,
        "type" => $type,
        "result" => $request_result, // 0 -> pending, 1 -> approved, 2 -> rejected, 3 -> canceled
        "request_time" => date("H:i m-d",$row['request_time']+28200),
        "review_time" =>  $row['review_time']?  date("H:i m-d",$row['review_time']+28200) : "--",
    );
}


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