<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
$includePath = $rootPath . '/includes/db/connect.php';
include($includePath);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = file_get_contents('php://input');

    // Convert the JSON string into a PHP object
    if(empty($payload)) {
        http_response_code(400);
        exit();
    }
    $data = json_decode($payload, true);

    // print the data
    $actionType = $data["action_type"];

    $instanceId = $data['instance_id']; // from pending_requests database, id column

    if($actionType == "APPROVE") {
        // update the pending_requests table
        // 1 is approved, 2 is rejected
        // set review_time to current time
        $sql = "UPDATE pending_requests SET status = 1, review_time = NOW() WHERE id = $instanceId";
        if (!$conn->query($sql)) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else if($actionType == "REJECT") {
        // update the pending_requests table
        $sql = "UPDATE pending_requests SET status = 2, review_time = NOW() WHERE id = $instanceId";
        if (!$conn->query($sql)) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    http_response_code(200);
    exit();

}

?>