<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = file_get_contents('php://input');
    // Convert the JSON string into a PHP object
    $data = json_decode($payload);

    // Access the individual values
    $challenge = $data->challenge;
    $token = $data->token;
    $type = $data->type;

    if ($type === 'url_verification') {
        // Send a response back to the source with the challenge value
        $response = ['challenge' => $challenge];
        header('Content-Type: application/json');
        $json = json_encode($response);
        echo $json;
        exit;
    }
    else {
        echo "Failed\n";
        var_dump($challenge, $token, $type);
        exit;
    }
}
?>
