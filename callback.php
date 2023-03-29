<?php

$feishu_app_id = "cli_a4a8e931cd79900e";
$feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";
$singularity_redirect = "https://singularity-eam-singularity.app.secoder.net/callback.php"; // "http://localhost/singularity-eam/callback.php"; // 
$feishu_redirect = "https://passport.feishu.cn/suite/passport/oauth/authorize?client_id=".$feishu_app_id."&redirect_uri=".$singularity_redirect."&response_type=code&state=";

$url_components = parse_url($_SERVER['REQUEST_URI']);
parse_str($url_components['query'], $params);
echo ' Code '. $params['code'];

$url = "https://passport.feishu.cn/suite/passport/oauth/token";
$myvars = 'grant_type=authorization_code'.'&client_id=' . $feishu_app_id.'&client_secret=' . $feishu_app_secret . '&code=' . $params['code'] . '&redirect_uri=' . $singularity_redirect;
$info_url = "https://passport.feishu.cn/suite/passport/oauth/userinfo";

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );

echo ' Response ' . $response;

$arr = json_decode($response, false);

if(isset($arr->access_token)){
    $header = array();
    $header[] = 'Content-length: 0';
    $header[] = 'Content-type: application/json';
    $header[] = 'Authorization: Bearer '.$arr->access_token;

    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_HTTPGET, 1);
    curl_setopt($ch, CURLOPT_URL, $info_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
    $user_info = curl_exec( $ch );
    echo ' User Info ' . $user_info;
}
else{
    echo ' Failed to get User Info ';
}

curl_close($ch);
?>