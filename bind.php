<?php
// TODO: find a better and more secure way to store API credentials
// TODO: conditional - local vs deployment for redirect
$feishu_app_id = "cli_a4a8e931cd79900e";
$feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";
$singularity_redirect = "http://localhost/singularity-eam/callback.php"; // "https://singularity-eam-singularity.app.secoder.net/bind.php";
$feishu_redirect = "https://passport.feishu.cn/suite/passport/oauth/authorize?client_id=".$feishu_app_id."&redirect_uri=".$singularity_redirect."&response_type=code&state=";

function redirect($url) {
    header('Location: '.$url);
    die();
}
// Handle feishu login form submit
if (isset($_POST['feishu-login_click'])) {
    redirect($feishu_redirect);
}

// $user_info = require 'callback.php'; 
// require 'includes/db/connect.php';

// if(isset($user_info->sub)){
//     $sub_id = $user_info->sub;
 
//     $sql = "UPDATE user SET feishu_id = '$sub_id' WHERE id = 1";
//     if ($conn -> query($sql)) echo "Insertion succeed";
//     else var_dump($conn->error_list);
// }
// else{
//     echo ' Error binding: bad user information ';
// }
?>