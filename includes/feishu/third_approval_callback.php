<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = file_get_contents('php://input');

    // Convert the JSON string into a PHP object
    $data = json_decode($payload);
    // write data in the database
    $sql = "UPDATE entity SET temp_column = '$payload' WHERE id = 1";
    if (!$conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $instanceId = $data['instance_id']; // from pending_requests database, id column
    $sql = "UPDATE pending_requests SET result = 1 WHERE id = $instanceId";
    if (!$conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Access the individual fields from the data array
    // $actionType = $data['action_type']; // APPROVE or REJECT
    // $actionContext = $data['action_context']; // 123456
    // $userId = $data['open_id'];
    // $approvalCode = $data['approval_code']; // from database, column
    // $instanceId = $data['instance_id']; // from pending_requests database, id column
    // $taskId = $data['task_id']; // ignorable
    // $reason = $data['reason']; // ignorable
    // $attachments = $data['attachments']; // ignorable, no attachable fields in the form
    // $token = $data['token']; // sdjkljkx9lsadf110

    // if($actionType == "APPROVE") {
    //     // update the pending_requests table
    //     // 1 is approved, 2 is rejected
    //     $sql = "UPDATE pending_requests SET status = 1 WHERE id = $instanceId";
    //     if (!$conn->query($sql)) {
    //         echo "Error: " . $sql . "<br>" . $conn->error;
    //     }
    // } else if($actionType == "REJECT") {
    //     // update the pending_requests table
    //     $sql = "UPDATE pending_requests SET status = 2 WHERE id = $instanceId";
    //     if (!$conn->query($sql)) {
    //         echo "Error: " . $sql . "<br>" . $conn->error;
    //     }
    // }

    http_response_code(200);
    exit();

}

?>