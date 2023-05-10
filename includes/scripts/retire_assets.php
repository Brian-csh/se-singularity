<?php
require "../db/connect.php";

// Get asset IDs from POST data
$assetIds = isset($_POST['assets']) ? $_POST['assets'] : [];
$user_role = isset($_POST['role_id']) ? intval($_POST['role_id']) : -1;

if($user_role != 4){ // manager move
    // Retire assets
    if (!empty($assetIds)) {
        $ids = implode(',', $assetIds);
        //set user to null for the asset and notify user 
        $sql = "UPDATE asset SET status = 4, user = null WHERE id IN ($ids)";
        $result = $conn->query($sql);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Assets retired successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error retiring assets.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No assets selected.']);
    }
    // Close the database connection
    $conn->close();
} else { // user move
    include "functions.php";
    if (!empty($assetIds)){
        $ids = implode(',', $assetIds);
        // Make request to manager
        $result = make_request();
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Assets retire requests made successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error retiring assets.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No assets selected.']);
    }
}
?>
