<?php
require 'db/connect.php';
include 'scripts/functions.php';

$verification_token = "h3weH1jYKSSabdDGLSaKSgucG6UeXFLp";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = file_get_contents('php://input');

    // Convert the JSON string into a PHP object
    $data = json_decode($payload);

    if ($data->header->token !== $verification_token) {
        // Invalid token, reject the request
        http_response_code(400);
        exit;
    }

    // handle url verification (for the initial verification)

    // if (isset($data->type) && $data->type === 'url_verification') {
    //     $challenge = $data->challenge;
    //     $token = $data->token;
    //     $type = $data->type;
    //     $response = ['challenge' => $challenge];
    //     header('Content-Type: application/json');
    //     $json = json_encode($response);
    //     echo $json;
    //     exit;
    // }

    // Access the event header
    $event_id = $data->header->event_id;
    $token = $data->header->token;
    $create_time = $data->header->create_time;
    $event_type = $data->header->event_type;
    $tenant_key = $data->header->tenant_key;
    $app_id = $data->header->app_id;

    // Handle the event
    switch ($event_type) {
        case 'contact.user.created_v3':
            // Handle new user event
            $ou_id = $data->event->object->user->open_id;
            $name = $data->event->object->user->name;
            $hashed_password = password_hash("12345678", PASSWORD_DEFAULT);
            $entity_head = 0;
            $role_id = 4;
            $date_created = time();
            if(isset($_GET['entity_id'])) { // TODO: sync each entity with a feishu org
                $entity_id = $_GET['entity_id'];
            }
            else {
                $entity_id = 1;
            }

            $sql = "INSERT INTO user (date_created, name, password, entity, department, entity_super, role, feishu_id) 
            VALUES ('$date_created', '$name', '$hashed_password', '$entity_id', NULL, '$entity_head', '$role_id', '$ou_id')";

            if ($conn->query($sql)) {
                // get the row of the user
                $sql = "SELECT * FROM user WHERE feishu_id = '$ou_id'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);

                // log the event into logs table
                insert_log_new_feishu_user($conn, $row);
            } 
            break;
    }

    // Send a response back to Feishu
    http_response_code(200);
    echo 'ok';
}
?>
