<?php

require "../db/connect.php";
require "functions.php";
// Get asset IDs from POST data
$assetIds = isset($_POST['assets']) ? $_POST['assets'] : [];

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : -1;


// user request - use
if (empty($assetIds)) {
    echo json_encode(['success' => false, 'message' => 'No assets selected.']);
} else {
    // MAKE request to manager(leaves log at the same time)
    // also notify to feishu 
    $results = make_request($conn,$user_id,null,$assetIds,1); // can only IDLE assets
    $responseData = array('result' => $results);

    // Encode the array as JSON
    $responseJson = json_encode($responseData);

    // Send the response
    echo $responseJson;

}
$conn->close();
