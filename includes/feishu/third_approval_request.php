<?php
if(!function_exists('initFeishuApproval')) {
  include $_SERVER['DOCUMENT_ROOT'] . "/includes/feishu/third_approval_init.php";
}
function getUserData($conn, $id) {
  $sql = "SELECT name, feishu_id FROM user WHERE id = $id";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $initiator_username = $row['name'];
  // check if feishu id exists
  $initiator_oid = $row['feishu_id'];
  if(isset($row['feishu_id'])){
    $initiator_oid = $row['feishu_id'];
  }
  else {
    $initiator_oid = "";
  }
  return array($initiator_username, $initiator_oid);
}

// $open_id = "ou_29bdc51fbfc84e1401dd9a8ae0316fa5";
// $feishu_app_id = "cli_a4a8e931cd79900e";
// $feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";

/**
 * Sends the Feishu Approval
 *
 * @param {object} $conn - The connection object
 * @param {int} $department_id - The department id
 * @param {int} $instance_id - The instance id, which is the same as the id of the request in the pending_requests table.
 * @param {string} $asset_name - The name of the asset
 * @param {int} $initiator_id - The id of the initiator, (NOT FEISHU ID, just Singularity ID)
 * @param {int} $approver_id - The id of the approver, (NOT FEISHU ID, just Singularity ID)
 * @param {int} $request_time - The time of the request, in milliseconds. (time() * 1000)
 * @param {int} $request_type - The type of the request, same as request_type make_request.php
 *
 * @example
 * requestFeishuApproval($conn, 1, 117, "Laptop", 1, 2, time() * 1000, 1)
 */
function requestFeishuApproval(
  $conn,
  $department_id,
  $instance_id,
  $asset_name,
  $initiator_id,
  $approver_id,
  $request_time,
  $request_type,
  $feishu_app_id = "cli_a4a8e931cd79900e",
  $feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ"
) {
  // check if approval code exists
  $sql = "SELECT feishu_approval_code FROM department WHERE id = $department_id";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  $approval_code = $row['feishu_approval_code'];
  if ($approval_code == NULL) {
    // try to make a approval code
    if(!initFeishuApproval($conn, $department_id)) {
      return;
    }
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

  // PROCESS PARAMETERS
  // get approver data
  $approver_data = getUserData($conn, $approver_id);
  $approver_oid = $approver_data[1];
  if($approver_oid == "") {
    // if approver does not have feishu id, do not send request
    echo "No approver oid";
    return;
  }

  switch($request_type){
    case 1:
      $request_title = "Approval Request";
      break;
    case 2:
      $request_title = "Return Request";
      break;
    case 3:
      $request_title = "Repair Request";
      break;
    case 4:
      $request_title = "Move Request";
      break;
    default:
      $request_title = "Asset Request";
  }

  // get initiator data
  $initiator_data = getUserData($conn, $initiator_id);
  $initiator_username = $initiator_data[0];
  $initiator_oid = $initiator_data[1];

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
          },
          {
            "name": "Request Number",
            "value": "'.$instance_id .'"
          }
        ],
        "user_name": "' . $initiator_username . '",
        "open_id": "' . $initiator_oid . '",
        "start_time": "' . $request_time . '",
        "update_time": "' . $request_time . '",
        "end_time": 0,
        "update_mode": "REPLACE",
        "task_list": [
          {
            "task_id": "112253",
            "open_id": "' . $approver_oid . '",
            "links": {
              "pc_link": "https://singularity-eam-singularity.app.secoder.net/",
              "mobile_link": "https://singularity-eam-singularity.app.secoder.net/"
            },
            "status": "PENDING",
            "extra": "",
            "title": "'.$request_title.'",
            "create_time": "' . $request_time . '",
            "end_time": 0,
            "update_time": "' . $request_time . '",
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
                "value": "'.$request_title.'"
              },
              {
                "key": "@i18n@2",
                "value": "Asset Name"
              },
              {
                "key": "@i18n@3",
                "value": "' . $asset_name . '"
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
    else{
      return($response);
    }
  } else {
    echo "No response received.";
  }
}
?>