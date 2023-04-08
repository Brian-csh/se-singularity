<?php

$feishu_app_id = "cli_a4a8e931cd79900e";
$feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost') {
    $singularity_redirect = "http://localhost/singularity-eam/feishu_callback.php?mode=".$mode;
}
else {
    $singularity_redirect = "https://singularity-eam-singularity.app.secoder.net/feishu_callback.php?mode=".$mode;
}
$feishu_redirect = "https://passport.feishu.cn/suite/passport/oauth/authorize?client_id=".$feishu_app_id."&redirect_uri=".$singularity_redirect."&response_type=code&state=";

function redirect($url) {
    header('Location: '.$url);
    die();
}

redirect($feishu_redirect)
?>