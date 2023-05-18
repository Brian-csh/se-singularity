<?php
require "../db/connect.php";
include "functions.php";
// Get asset IDs from POST data

$requestIds = isset($_POST['requestIds']) ? $_POST['requestIds'] : [];
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : -1;
$handle_type = isset($_POST['handle_type']) ? intval($_POST['handle_type']) : -1;// 1 -> approve, 2 -> reject

// user request - use
if (empty($requestIds)) {
    echo json_encode(['success' => false, 'message' => 'No assets selected.']);
} else {

    $results = handle_request($conn,$user_id,$requestIds,$handle_type);

    $responseData = array('result' => $results);

    //Encode the array as JSON
    $responseJson = json_encode($responseData);

    // Send the response
    // header('Content-Type : application/json');
    echo $responseJson;

}
$conn->close();
?>