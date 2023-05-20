<?php

function sendFeishuMessage($conn, $receive_id, $message_content) {
    // get tenant access token
    $feishu_app_id = "cli_a4a8e931cd79900e";
    $feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";

    $token_url = "https://open.feishu.cn/open-apis/auth/v3/tenant_access_token/internal/";
    $post_fields = 'app_id=' . $feishu_app_id.'&app_secret=' . $feishu_app_secret;

    $ch = curl_init( $token_url );
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec( $ch );
    curl_close($ch);
    $response_json = json_decode($response, false);
    $tenant_access_token = $response_json->tenant_access_token;

    // send message
    $message_url = "https://open.feishu.cn/open-apis/im/v1/messages?receive_id_type=open_id";
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $tenant_access_token
    );
    
    $data = array(
        'receive_id' => $receive_id,
        'msg_type' => 'text',
        'content' => '{"text":"'.$message_content.'"}',
        'uuid' => uniqid()
    );
    
    $body = json_encode($data);
    
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, 'https://open.feishu.cn/open-apis/im/v1/messages?receive_id_type=open_id');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($curl);
    
    if (curl_errno($curl)) {
        $error_message = curl_error($curl);
        echo 'Error with curl: ' . $error_message . '<br/>';
    }
    
    curl_close($curl);

    return $response;
}
?>
