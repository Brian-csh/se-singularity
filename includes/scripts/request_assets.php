<?php

require "../db/connect.php";
include "functions.php";
// Get asset IDs from POST data
$assetIds = isset($_POST['assets']) ? $_POST['assets'] : [];

$user_role = isset($_POST['role_id']) ? intval($_POST['role_id']) : -1; //actually we don't need this..?


// user request
if (empty($assetIds)) {
    echo json_encode(['success' => false, 'message' => 'No assets selected.']);
} else {
    //MAKE request to manager(leaves log at the same time)
    $result = make_request($conn,$user_id,null,$assetIds,1); // can only IDLE assets

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Assets requested successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error requesting asset.']);
    }

}
$conn->close();
?>
