<?php

if(isset($_GET['mode'])) {
    $mode = $_GET['mode'];
    if($mode == "bind" || $mode == "signin"){
        $modeURL = "?mode=".$mode;
    }
}

$feishu_app_id = "cli_a4a8e931cd79900e";
$feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost') {
    $singularity_redirect = "http://localhost/singularity-eam/feishu_callback.php".$modeURL;
}
else {
    $singularity_redirect = "https://singularity-eam-singularity.app.secoder.net/feishu_callback.php";
}

$url_components = parse_url($_SERVER['REQUEST_URI']);
parse_str($url_components['query'], $params);

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
var_dump($response);

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
    var_dump($user_info);

    if($mode == "bind") {
        # set feishu user id in the database
    }
    else if($mode == "signin") {
        # create new user, update values, set feishu user id
    }

    // include 'users.php';
}
else{
    $user_info = NULL;
    echo "no access token";
}

curl_close($ch);

?>