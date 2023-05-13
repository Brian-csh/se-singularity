<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $challenge = $_POST['challenge'];
    $token = $_POST['token'];
    $type = $_POST['type'];

    // Debugging: Output the received values to the console or log
    var_dump($challenge, $token, $type);

    if ($type === 'url_verification') {
        // Send a response back to the source with the challenge value
        $response = ['challenge' => $challenge];
        header('Content-Type: application/json');
        $json = json_encode($response);
        echo $json;
        var_dump($json);
        exit;
    }
}
?>