<?php
require "../db/connect.php";
include "functions.php";
// Get asset IDs from POST data
$assetIds = isset($_POST['assets']) ? $_POST['assets'] : [];
$destination = isset($_POST['destination']) ? $_POST['destination'] : -1;
//different move task for user and manager
$user_role = isset($_POST['role_id']) ? intval($_POST['role_id']) : -1;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : -1;

if($user_role != 4){ // manager move
    // Move assets
    if (empty($assetIds)) {
        echo json_encode(['success' => false, 'message' => 'No assets selected.']);
    } else if ($destination == -1) {
        echo json_encode(['success' => false, 'message' => 'Invalid department.']);
    } else {
        $ids = implode(',', $assetIds);
        $sql = "UPDATE asset SET department = $destination WHERE id IN ($ids)";
        $result = $conn->query($sql);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Assets moved successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error moving assets.']);
        }
    }
// Close the database connection
$conn->close();
} else { // user move
    if (empty($assetIds)) {
        echo json_encode(['success' => false, 'message' => 'No assets selected.']);
    } else if ($destination == -1) {
        echo json_encode(['success' => false, 'message' => 'Invalid user.']);
    } else {
        $ids = implode(',', $assetIds);
        //MAKE request to manager        
        $time = time();

        foreach($assetIds as $asset_id){
            $sql = "INSERT INTO pending_requests (initiator,participant,asset,type,request_time) VALUES
            ('$user_id','$destination','$asset_id','4','$time')";
            $result = $conn->query($sql);
            $sql = "UPDATE asset SET status = 10 WHERE id = '$asset_id'";
            $result = $conn->query($sql);
            //Alert manager? Make pending request page?
        }

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Assets moved successfully to another user.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error moving assets to another user.']);
        }

    }
    $conn->close();
}
?>
