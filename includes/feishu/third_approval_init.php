<?php

/**
 * Gets all resource managers in a department (excluding subdepartments).
 *
 * @param {object} $conn - The database connection object.
 * @param {number} $dep - The ID of the requested department
 * @return {array} Feishu open_ids of all resource managers in the corresponding department.
 *
 * @example
 * get_RM_FS_OID(4, $conn);
 */
function get_RM_FS_OID($conn, $dep){
    $sql = "SELECT feishu_id FROM user WHERE role = 3 AND department = $dep AND feishu_id IS NOT NULL";
    $result = mysqli_query($conn, $sql);
    $feishu_oids = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $feishu_oids[] = $row['feishu_id'];
    }
    return $feishu_oids;
}

// $feishu_app_id = "cli_a4a8e931cd79900e";
// $feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";
function initFeishuApproval(
    $conn,
    $department_id,
    $feishu_app_id = "cli_a4a8e931cd79900e",
    $feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ"
) {
    echo "HELLO>??";
    // get all RMs in department
    $feishu_oids = get_RM_FS_OID($conn, $department_id);
    if(empty($feishu_oids)){
        return; // No RMs means no need to init
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

    // CREATE APPROVAL
    $external_approval_url = "https://open.feishu.cn/open-apis/approval/v4/external_approvals?department_id_type=open_department_id&user_id_type=open_id";
    
    $action_callback_url = 'https://singularity-eam-singularity.app.secoder.net/includes/feishu/third_approval_callback.php';

    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $tenant_access_token
    );

    $feishu_oids_string = implode('","', $feishu_oids);
    $feishu_oids_string = '"' . $feishu_oids_string . '"';

    // JSON data to be sent in the request body
    $jsonData = '{
        "approval_name": "Asset Approval",
        "approval_code": "approval_code_'.$department_id.'",
        "group_code": "singularity",
        "external": {
            "create_link_pc": "https://singularity-eam-singularity.app.secoder.net/requests.php",
            "create_link_mobile": "https://singularity-eam-singularity.app.secoder.net/requests.php",
            "support_pc": true,
            "support_mobile": true,
            "support_batch_read": false,
            "enable_mark_readed": true,
            "enable_quick_operate": true,
            "action_callback_url": "'.$action_callback_url.'",
            "action_callback_token": "thisisatoken"
        },
        "viewers": [
        {
            "viewer_type": "TENANT"
        }
        ],
        "managers": [
        "' . $feishu_oids_string . '"
        ]
    }';
    // TODO: i have to init third approval for every resource manager with a feishu

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

    $response = json_decode($response, true);
    if ($response["code"] == 0) {
        $approval_code =  $response["data"]["approval_code"];
        // store in entity table
        $sql = "UPDATE department SET feishu_approval_code = '$approval_code' WHERE id = $department_id";
        if (!$conn->query($sql)) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        var_dump($response);
    }
    var_dump($response);
}
?>