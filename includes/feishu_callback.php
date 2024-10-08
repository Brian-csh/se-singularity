<?php
require 'db/connect.php';

session_start();
include '../includes/scripts/functions.php';
if(!function_exists('initFeishuApproval')){
    include $_SERVER['DOCUMENT_ROOT'] . "/includes/feishu/third_approval_init.php";
}

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
    $singularity_redirect = "http://localhost:8000/includes/feishu_callback.php".$modeURL;
}
else {
    $singularity_redirect = "https://singularity-eam-singularity.app.secoder.net/includes/feishu_callback.php".$modeURL;
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

    // set feishu user id in the database
    if($mode == "bind") {
        // check if feishu account is already binded
        $stmt = $conn->prepare('SELECT COUNT(*) as count FROM user WHERE feishu_id = ?');
        $stmt->bind_param('s', $sub_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();
        if ($count > 0) {
            header('Location: ../index.php?bind_err='.urlencode("403"));
            die();
        }
        // Update sessions
        $session_user_id = $_SESSION['user']['id'];
        $_SESSION['feishu_bind'] = true;

        // Construct the SQL update statement
        $sql = "UPDATE user SET feishu_id = '$sub_id' WHERE id = '$session_user_id'";

        // Insert Log (Bind Feishu Account)
        $query = "SELECT * FROM user WHERE id = '$session_user_id'";
        $result = $conn->query($query);
        $row = $result -> fetch_array(MYSQLI_ASSOC);
        insert_log_login($conn,$row,3);

        // Execute the SQL statement
        mysqli_query($conn, $sql);

        if($_SESSION['user']['role'] == 3){
            // if user is RM, init approval
            initFeishuApproval($conn, $_SESSION['user']['department']);
        }
        
        // Redirect
        header('Location: ../index.php');
        exit();
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

            //Insert Log (Log in with Feishu)
            $username = $row['name'];
            insert_log_login($conn,$row,2);

            $conn->close();
            $stmt->close();

            $_SESSION['user']['id'] = $row['id'];
            $_SESSION['user']['name'] = $row['name'];
            $_SESSION['user']['role'] = $row['role'];
            $_SESSION['user']['feishu_id'] = $row['feishu_id'];
            $_SESSION['user']['entity'] = $row['entity'];
            $_SESSION['user']['department'] = $row['department'];
            header('Location: ../index.php');
            exit();
        } else {
            // If feishu_id does not exist, cannot sign in.
            header('Location: ../signin.php?signin='.urlencode("403"));
            $stmt->close();
            $conn->close();
            die();
        }
    }
}
else{
    $user_info = NULL;
    header('Location: ../signin.php?signin='.urlencode("403"));
    die();
}

curl_close($ch);

?>
