<?php

// $feishu_app_id = "cli_a4a8e931cd79900e";
// $feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";
function requestFeishuApproval(
    $conn,
    $entity_id,
    $request_row,
    $feishu_app_id = "cli_a4a8e931cd79900e",
    $feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ",
) {
    // check if approval code exists
    $sql = "SELECT feishu_approval_code FROM entity WHERE id = $entity_id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $approval_code = $row['feishu_approval_code'];
    if ($approval_code == NULL) {
        echo "ERROR! Approval code does not exist";
        return;
    }

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

    // SEND APPROVAL INSTANCE
    $external_instance_url = "https://open.feishu.cn/open-apis/approval/v4/external_instances";
    $initiator_oid = "ou_29bdc51fbfc84e1401dd9a8ae0316fa5"; // TODO get from user table from request_row initiator id
    $approver_oid = "ou_29bdc51fbfc84e1401dd9a8ae0316fa5"; // TODO change to approver id
    $asset_name = $request_row['asset']; // TODO get corresponding asset name from asset table
    $initiator_username = "MICHIOOOOO";
    $instance_id = 100; // $request_row['id'];
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $tenant_access_token
    );

    // JSON data to be sent in the request body
    $jsonData = '{
        "approval_code": "'.$approval_code.'",
        "instance_id": "122",
        "status": "PENDING",
        "extra": "",
        "links": {
          "pc_link": "https://singularity-eam-singularity.app.secoder.net/",
          "mobile_link": "https://singularity-eam-singularity.app.secoder.net/"
        },
        "title": "@i18n@1",
        "form": [
          {
            "name": "@i18n@2",
            "value": "@i18n@3"
          }
        ],
        "user_name": "'.$initiator_username.'",
        "open_id": "'. $initiator_oid.'",
        "start_time": "'.$request_row['request_time'].'",
        "update_time": "'.$request_row['request_time'].'",
        "end_time": 0,
        "update_mode": "REPLACE",
        "task_list": [
          {
            "task_id": "112253",
            "open_id": "'.$approver_oid.'",
            "links": {
              "pc_link": "http://",
              "mobile_link": "http://"
            },
            "status": "PENDING",
            "extra": "",
            "title": "同意",
            "create_time": "'.$request_row['request_time'].'",
            "end_time": 0,
            "update_time": "'.$request_row['request_time'].'",
            "action_context": "123456",
            "action_configs": [
              {
                "action_type": "APPROVE",
                "action_name": "@i18n@1",
                "is_need_reason": true,
                "is_reason_required": false,
                "is_need_attachment": false
              }
            ]
          }
        ],
        "i18n_resources": [
          {
            "locale": "zh-CN",
            "is_default": true,
            "texts": [
              {
                "key": "@i18n@1",
                "value": "Asset Approval Request"
              },
              {
                "key": "@i18n@2",
                "value": "Asset Name"
              },
              {
                "key": "@i18n@3",
                "value": "'.$asset_name.'"
              }
            ]
          }
        ]
      }';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $external_instance_url);
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
        if (!$conn->query($sql)) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "No response received.";
    }
}
?>