<?php
require 'db/connect.php';
include '../functions.php';
session_start();

// request access token
// TODO: make request access token a separate function

// API creds
$feishu_app_id = "cli_a4a8e931cd79900e";
$feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";

$token_url = "https://passport.feishu.cn/suite/passport/oauth/token";
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost') {
    $singularity_redirect = "http://localhost:8000/includes/entity_sync.php";
}
else {
    $singularity_redirect = "https://singularity-eam-singularity.app.secoder.net/includes/entity_sync.php";
}

$url_components = parse_url($_SERVER['REQUEST_URI']);
parse_str($url_components['query'], $params);

$post_fields = 'grant_type=authorization_code'.'&client_id=' . $feishu_app_id.'&client_secret=' . $feishu_app_secret . '&code=' . $params['code'] . '&redirect_uri=' . $singularity_redirect;

$ch = curl_init( $token_url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );

$arr = json_decode($response, false);

var_dump($arr);

echo "<br></br>";

$info_url = "https://open.feishu.cn/open-apis/tenant/v2/tenant/query";

// if succesful in getting the access token, request for entity information
if(isset($arr->access_token)){
    $header = array();
    $header[] = 'Content-length: 0';
    $header[] = 'Content-type: application/json';
    $header[] = 'Bearer '.$arr->access_token;

    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_HTTPGET, 1);
    curl_setopt($ch, CURLOPT_URL, $info_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
    $entity_info_str = curl_exec( $ch );
    $entity_info = json_decode($entity_info_str, true);

    var_dump($entity_info);
}


?>
