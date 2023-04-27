<?php
require 'db/connect.php';
include '../functions.php';
// get tenant access token
$feishu_app_id = "cli_a4a8e931cd79900e";
$feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";

$token_url = "https://open.feishu.cn/open-apis/auth/v3/tenant_access_token/internal/";
$post_fields = 'app_id=' . $feishu_app_id.'&app_secret=' . $feishu_app_secret;

$ch = curl_init( $token_url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );
curl_close($ch);
$response_json = json_decode($response, false);
$tenant_access_token = $response_json->tenant_access_token;

// import all users who are in feishu but not alrealdy in singularity
$users_url = "https://open.feishu.cn/open-apis/ehr/v1/employees/";
$header = 'Authorization: Bearer '.$tenant_access_token;
$ch = curl_init( $users_url );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array($header));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

$users_response = curl_exec( $ch );
var_dump($users_response);
curl_close($ch);


$users = json_decode($users_response, true)['data']['items'];

// user details
$hashed_password = password_hash("12345678", PASSWORD_DEFAULT);
$entity_head = 0;
$role_id = 4;
$date_created = time();

if(isset($_GET['entity_id'])) {
    $entity_id = $_GET['entity_id'];
}
else {
    $entity_id = 1;
}


foreach ($users as $user) {
    $user_id = $user['user_id']; // The feishu_id to search for
    $user_name = $user['system_fields']['name'];

    $sql = "SELECT COUNT(*) FROM user WHERE feishu_id = '$user_id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row_count = mysqli_fetch_row($result)[0];
        if ($row_count <= 0) {
            echo $user_id;
            // A row with the feishu_id does not exists in the table
            $sql = "INSERT INTO user (date_created, name, password, entity, department, entity_super, role, feishu_id) 
            VALUES ('$date_created', '$user_name', '$hashed_password', '$entity_id', NULL, '$entity_head', '$role_id', '$user_id')";

            if ($conn->query($sql)) {
            } else {
                header('Location: ../entities.php&sync_error');
            }
        }
    } else {
    // Error executing the query
    // Handle the error here
    }
}

header('Location: ../entities.php');

?>
