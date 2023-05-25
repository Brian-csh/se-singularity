<?php
if(!function_exists('initFeishuApproval')) {
  include $_SERVER['DOCUMENT_ROOT'] . "/includes/feishu/third_approval_init.php";
}
/**
 * Updates the Feishu Approval 
 *
 * @param {object} $conn - The connection object
 * @param {string} $approval_code - The approval code
 * @param {int} $instance_id - The instance id, which is the same as the id of the request in the pending_requests table.
 * @param {int} $request_time - The time of the initial request, in milliseconds. (time() * 1000)
 *
 * @example
 * updateFeishuApproval($conn, "ABCD-EFGH", 117, time() * 1000)
 */
function updateFeishuApproval(
  $conn,
  $approval_code,  
  $instance_id,
  $feishu_app_id = "cli_a4a8e931cd79900e",
  $feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ"
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

  // SEND APPROVAL INSTANCE
  $external_instance_url = "https://open.feishu.cn/open-apis/approval/v4/external_instances";
  $headers = array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $tenant_access_token
  );

  // JSON data to be sent in the request body
  $jsonData = '{
        "approval_code": "' . $approval_code . '",
        "instance_id": "' . $instance_id . '",
        "status": "APPROVED",
        "extra": "",
        "links": {
          "pc_link": "https://singularity-eam-singularity.app.secoder.net/",
          "mobile_link": "https://singularity-eam-singularity.app.secoder.net/"
        },
        "start_time": "' . time()*1000 . '",
        "update_time": "' . time()*1000 . '",
        "end_time": "' . time()*1000 . '",
        "update_mode": "UPDATE",
        "i18n_resources": [
          {
            "locale": "zh-CN",
            "is_default": true,
            "texts": [
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
  } else {
    echo "No response received.";
  }
  return $response;
}
?>