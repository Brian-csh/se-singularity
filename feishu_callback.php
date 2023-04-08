<?php
require 'includes/db/connect.php';

session_start();

// bind: existing Singularity users can bind their Feishu accs
// signin: sign in to Singularity w Feishu (not sign up)
if(isset($_GET['mode'])) {
    $mode = $_GET['mode'];
    if($mode == "bind" || $mode == "signin"){
        $modeURL = "?mode=".$mode;
    }
}

// API creds
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

// request access token
$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );

$arr = json_decode($response, false);

// if succesful in getting the access token, request for user information
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
    
    // feishu id
    $sub_id = $user_info["sub"];

    
    echo $mode." ".$session_user_id." ".$sub_id;

    // set feishu user id in the database
    if($mode == "bind") {
        // Update sessions
        $session_user_id = $_SESSION['admin']['id'];

        echo $session_user_id." ".$sub_id;

        // Construct the SQL update statement
        $sql = "UPDATE user SET feishu_id = '$sub_id' WHERE id = '$session_user_id'";

        // Execute the SQL statement
        mysqli_query($conn, $sql);
        
        // Redirect
        // header('Location: index.php');
        // exit();
    }

    else if($mode == "signin") {
        // if mode is signin, check if user already exists
        $stmt = $conn->prepare('SELECT COUNT(*) as count FROM user WHERE feishu_id = ?');
        $stmt->bind_param('s', $sub_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->free_result();
        if ($count > 0) {
            // if sub is already in the database, log in as that user
            $stmt = $conn->prepare('SELECT * FROM user WHERE feishu_id = ?');
            $stmt->bind_param('s', $sub_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->free_result();
            $conn->close();

            $_SESSION['admin'] = $row;
            // header('Location: index.php');
            // exit();
        } else {
            // If feishu_id does not exist, cannot sign in.
            // header('Location: signin.php?signin='.urlencode("403"));
            // die();
        }
        // close the statement
        $stmt->close();
    }
    $conn->close();
}
else{
    $user_info = NULL;
    // header('Location: signin.php?signin='.urlencode("403"));
    // die();
}

curl_close($ch);

?>