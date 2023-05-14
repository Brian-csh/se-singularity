<?php
require "../db/connect.php";
include "functions.php";
// Get asset IDs from POST data

// $requestTypes = isset($_POST['requestTypes']) ? $_POST['requestTypes'] : [];
// $assetIds = isset($_POST['assetIds']) ? $_POST['assetIds'] : [];

// $department_id = isset($_POST['department_id']) ? intval($_POST['department_id']) : -1;
$requestIds = isset($_POST['requestIds']) ? $_POST['requestIds'] : [];
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : -1;

// user request - use
if (empty($requestIds)) {
    echo json_encode(['success' => false, 'message' => 'No assets selected.']);
} else {
    //MAKE request to manager(leaves log at the same time)
    $results = approve_request($conn,$user_id,$requestIds); // can only IDLE assets

    $responseData = array('result' => $results);

    //Encode the array as JSON
    $responseJson = json_encode($responseData);

    // Send the response
    header('Content-Type : application/json');
    echo $responseJson;

}
$conn->close();
?>