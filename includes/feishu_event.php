<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $challenge = $_POST['challenge'];
    $token = $_POST['token'];
    $type = $_POST['type'];

    if ($type === 'url_verification') {
        // Send a response back to the source with the challenge value
        echo json_encode(['challenge' => $challenge]);
        exit;
    }
}
?>