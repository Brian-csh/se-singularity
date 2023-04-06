<?php
require 'includes/db/connect.php';

session_start();

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
// var_dump($response);

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
    $user_info_str = curl_exec( $ch );
    $user_info = json_decode($user_info_str, true);

    // set feishu user id in the database
    if($mode == "bind") {
        $session_user_id = $_SESSION['admin']['id'];
        echo $session_user_id;
        $sub_id = $user_info["sub"];

        // Construct the SQL update statement
        $sql = "UPDATE user SET feishu_id = '$sub_id' WHERE id = '$session_user_id'";

        // Execute the SQL statement
        mysqli_query($conn, $sql);
        $conn->close();
    }

    else if($mode == "signin") {
        // if mode is signin, check if user already exists
        $stmt = $conn->prepare('SELECT COUNT(*) as count FROM user WHERE feishu_id = ?');
        $stmt->bind_param('s', $sub_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        if ($count > 0) {
            // if sub is already in the database, log in as that user
            $_SESSION['admin'] = $row;
        } else {
            // feishu_id does not exist in the database, create new user
        }
        // close the statement
        $stmt->close();
        $conn->close();
    }
    header('Location: index.php');
    die();
}
else{
    $user_info = NULL;
    echo "no access token";
}

curl_close($ch);

?>