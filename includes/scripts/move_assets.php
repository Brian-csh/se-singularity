<?php

require "../db/connect.php";
include "functions.php";
// Get asset IDs from POST data
$assetIds = isset($_POST['assets']) ? $_POST['assets'] : [];
$destination = isset($_POST['destination']) ? $_POST['destination'] : -1;
//different move task for user and manager
$user_role = isset($_POST['role_id']) ? intval($_POST['role_id']) : -1;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : -1;


// TODO : add constraints
if($user_role != 4){ // manager move
    // Move assets
    if (empty($assetIds)) {
        echo json_encode(['success' => false, 'message' => 'No assets selected.']);
    } else if ($destination == -1) {
        echo json_encode(['success' => false, 'message' => 'Invalid department.']);
    } else { 
        // TODO : add constraints, can only move idle assets
        $ids = implode(',', $assetIds);
        foreach($assetIds as $asset_id){
            //fetch status
            $status_id = mysqli_fetch_array($conn->query("SELECT status FROM asset WHERE id = $asset_id"))['status'];
            if($status_id == 1){
                $sql = "UPDATE asset SET department = $destination WHERE id = '$asset_id'"; //status won't changed
                $conn->query($sql);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid asset status.']); // make alert
            }
        }
    }
// Close the database connection
$conn->close();
} else { // user move
    // TODO: only can move in use assets
    if (empty($assetIds)) {
        echo json_encode(['success' => false, 'message' => 'No assets selected.']);
    } else if ($destination == -1) {
        echo json_encode(['success' => false, 'message' => 'Invalid user.']);
    } else {
        
        //MAKE request to manager(leaves log at the same time)
        $results = make_request($conn,$user_id,$destination,$assetIds,4);

        $responseData = array('result' => $results);

        //Encode the array as JSON
        $responseJson = json_encode($responseData);

        // Send the response
        echo $responseJson;
    }
    $conn->close();
}
?>
