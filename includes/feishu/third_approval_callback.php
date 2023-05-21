<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = file_get_contents('php://input');

    // Convert the JSON string into a PHP object
    $data = json_decode($payload);
    // write data in the database
    $sql = "UPDATE entity SET temp_column = '$payload' WHERE id = 1";
    http_response_code(200);
    exit;
    // if ($data->data->token !== $verification_token) {
    //     // Invalid token, reject the request
    //     http_response_code(400);
    //     exit;
    // }

    // // handle url verification (for the initial verification)

    // // if (isset($data->type) && $data->type === 'url_verification') {
    // //     $challenge = $data->challenge;
    // //     $token = $data->token;
    // //     $type = $data->type;
    // //     $response = ['challenge' => $challenge];
    // //     header('Content-Type: application/json');
    // //     $json = json_encode($response);
    // //     echo $json;
    // //     exit;
    // // }

}

?>