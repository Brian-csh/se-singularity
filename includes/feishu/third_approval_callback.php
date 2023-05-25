<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
$includePath = $rootPath . '/includes/db/connect.php';
include($includePath);
$updatePath = $rootPath . '/includes/feishu/third_approval_update.php';
include($updatePath);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = file_get_contents('php://input');

    if(empty($payload)) {
        http_response_code(400);
        exit();
    }
    // Convert the JSON string into a PHP object
    $data = json_decode($payload, true);

    // print the data
    $actionType = $data["action_type"];
    $approvalCode = $data["approval_code"];
    $instance_id = $data["instance_id"];

    $instanceId = $data['instance_id']; // from pending_requests database, id column

    if($actionType == "APPROVE") {
        // update the pending_requests table
        // 1 is approved, 2 is rejected
        // set review_time to current time
        $sql = "UPDATE pending_requests SET result = 1, review_time = UNIX_TIMESTAMP() WHERE id = $instanceId";
        if($conn->query($sql)) {
            updateFeishuApproval($conn, $approvalCode, $instance_id);
        } else {
            http_response_code(500);
            exit();
        }
    } else if($actionType == "REJECT") {
        // update the pending_requests table
        $sql = "UPDATE pending_requests SET result = 2, review_time = UNIX_TIMESTAMP() WHERE id = $instanceId";
        $conn->query($sql);
    }

    http_response_code(200);
    exit();

}

?>