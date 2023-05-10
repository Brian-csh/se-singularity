<?php

require "../db/connect.php";
include "functions.php";
// Get asset IDs from POST data
$assetIds = isset($_POST['assets']) ? $_POST['assets'] : [];
$destination = isset($_POST['destination']) ? $_POST['destination'] : -1;
//different move task for user and manager
$user_role = isset($_POST['role_id']) ? intval($_POST['role_id']) : -1;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : -1;

// user request
    // TODO: only can request IDLE assets
if (empty($assetIds)) {
    echo json_encode(['success' => false, 'message' => 'No assets selected.']);
} else if ($destination == -1) {
    echo json_encode(['success' => false, 'message' => 'Invalid user.']);
} else {
    // only can request IDLE assets
    foreach($asset as $assetIds){
        if()
    }
    //MAKE request to manager(leaves log at the same time)
    $result = make_request($conn,$user_id,$destination,$assetIds,4);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Assets moved successfully to another user.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error moving assets to another user.']);
    }

}
$conn->close();
?>
