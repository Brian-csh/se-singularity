<?php

require "../db/connect.php";

// Get the DataTables request parameters
$draw = intval($_GET['draw']);
$start = intval($_GET['start']);
$length = intval($_GET['length']);

$department_id = intval($_GET['department_id']);
$userid = intval($_GET['user_id']);


// Fetch data from database table - pending_requests
$sql = "SELECT * FROM pending_requests WHERE department = $department_id";


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

$sql .= " LIMIT $start, $length";

$result = $conn->query($sql);

$data = array();
while($row = $result->fetch_assoc()) {

    if(isset($row['initiator'])){
        $initiator_id = $row['initiator'];
        $initiator = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$initiator_id'"))['name'];
    } else {
        $initiator = "N/A";
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
        $asset = "N/A";
    }

    if (isset($row['type'])) {
        $type_id = $row['type'];
        $type = mysqli_fetch_array($conn->query("SELECT name FROM request_type WHERE id = '$type_id'"))['name'];
    } else {
        $type = "N/A";
    }


    $data[] = array(
        "id" => $row['id'],
        "initiator" => $initiator,
        "participant" => $participant,
        "asset" => $asset,
        "type" => $type,
        "result" => ($row['result'] == 0) ? "Pending" : (($row['result'] == 1) ? "Approved" : "Rejected"),
        "request_time" => date("H:i:s m-d",$row['request_time']),
        "review_time" => $row['review_time']
        // "status" => ($status_id >=6 && $status_id <= 9)? "<button class= 'text-primary handleRequestButton' data-bs-toggle='modal' data-bs-target = '#handleRequestModal'>"."You have pending Request! : ".$status. "</button>" : $status,
        // "actions" => "<a title=\"User Info\" class=\"btn btn-datatable\" href=\"edit_asset.php?id=".$row['id']."&name=".$row['name']."\">
        // Info
        // </a>"
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