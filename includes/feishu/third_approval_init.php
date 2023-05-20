<?php

// $feishu_app_id = "cli_a4a8e931cd79900e";
// $feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";
function initFeishuApproval(
    $conn,
    $entity_id,
    $feishu_app_id = "cli_a4a8e931cd79900e",
    $feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ",
) {
    // get tenant access token
    $token_url = "https://open.feishu.cn/open-apis/auth/v3/tenant_access_token/internal/";
    $post_fields = 'app_id=' . $feishu_app_id . '&app_secret=' . $feishu_app_secret;

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    curl_close($ch);
    $response_json = json_decode($response, false);
    $tenant_access_token = $response_json->tenant_access_token;

    // CREATE APPROVAL
    $external_approval_url = "https://open.feishu.cn/open-apis/approval/v4/external_approvals?department_id_type=open_department_id&user_id_type=open_id";
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $tenant_access_token
    );

    // JSON data to be sent in the request body
    $jsonData = '{
        "approval_name": "Asset Approval",
        "approval_code": "approve_asset",
        "group_code": "singularity",
        "external": {
        "create_link_pc": "https://applink.feishu.cn/client/mini_program/open?mode=appCenter&appId='. $feishu_app_id.'&path=pc/pages/create-form/index?id=9999",
        "create_link_mobile": "https://applink.feishu.cn/client/mini_program/open?appId='. $feishu_app_id .'&path=pages/approval-form/index?id=9999",
        "support_pc": true,
        "support_mobile": true,
        "support_batch_read": false,
        "action_callback_url": "http://feishu.cn/approval/openapi/operate",
        "action_callback_token": "sdjkljkx9lsadf110",
        "action_callback_key": "gfdqedvsadfgfsd",
        "enable_mark_readed": false
        },
        "viewers": [
        {
            "viewer_type": "TENANT"
        }
        ],
        "managers": [
        "ou_29bdc51fbfc84e1401dd9a8ae0316fa5"
        ]
    }';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $external_approval_url);
    // Set the request method to POST
    curl_setopt($curl, CURLOPT_POST, true);
    // Set the request body data as JSON
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
    // Set the request headers
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    // Set the option to receive the response as a string
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $error_message = curl_error($curl);
    }
    curl_close($curl);

    if ($response) {
        $response = json_decode($response, true);
        $approval_code =  $response["data"]["approval_code"];
        // store in entity table
        $sql = "UPDATE entity SET feishu_approval_code = '$approval_code' WHERE id = $entity_id";
        if (!$conn->query($sql)) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "No response received.";
    }
}
?>