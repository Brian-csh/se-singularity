<?php
$active = "Notification Settings";
include "includes/header.php";
include "includes/settingbar.php";

$department_id = $session_info['department'];
$class_entity_id = $session_info['entity'];

// get current policy
$sql = "SELECT * FROM department WHERE id = '$department_id'";
$result = mysqli_query($conn, $sql);
$warning_policy = mysqli_fetch_assoc($result)['warning_policy'];
// defaults
$days_to_add_placeholder = 30;
$message_placeholder = " requries maintenance";
$min_amt_placeholder = 0;

if($warning_policy != null) {
    $warning_policy = json_decode($warning_policy, true);
    $days_to_add_placeholder = $warning_policy['date_policy'];
    $message_placeholder = $warning_policy['message_policy'];
    $min_amt_placeholder = $warning_policy['min_amt_policy'];
    $class_policy_current = $warning_policy['class_policy'];
}

// get all asset status
$sql = "SELECT * FROM asset_status_class";
$result = mysqli_query($conn, $sql);
$asset_statuses = array();
while($row = mysqli_fetch_assoc($result)) {
    // key value pair, id and status
    $asset_statuses[$row['id']] = $row['status'];
}
$updateSuccess = null;

if(isset($_POST['submit_notification_settings'])) {
    $date_policy = intval($_POST['depreciationDate']);
    $message_policy = $_POST['depreciationMessage'];
    $min_amt_policy = intval($_POST['depreciationMinAmt']);
    $status_policies = array();
    for($i = 1; $i <= count($asset_statuses); $i++) {
        if(isset($_POST['depreciation_toggle_' . $i])) {
            $status_policies[] = $i;
        }
    }

    $class_policy = $_POST['class_policy'];
    if($class_policy != ""){
        $class_policy = explode(",", $class_policy);
        for($i = 0; $i < count($class_policy); $i++) {
            $class_policy[$i] = intval($class_policy[$i]);
        }
    }
    else{
        $class_policy = array();
    }

    // create json
    $json = array(
        "date_policy" => $date_policy,
        "message_policy" => $message_policy,
        "min_amt_policy" => $min_amt_policy,
        "status_policies" => $status_policies,
        "class_policy" => $class_policy,
    );
    $json = json_encode($json);
    // update database
    $sql = "UPDATE department SET warning_policy = '$json' WHERE id = '$department_id'";
    // if success
    if(mysqli_query($conn, $sql)) {
        $updateSuccess = true;
    }
    else {
        $updateSuccess = false;
        $error = mysqli_error($conn);
    }
    
}

$showDiv = true; // Set the initial state of the div
if (isset($_POST['depreciationToggle'])) {
    $showDiv = ! $showDiv; // Toggle the state of the div
}

// get all asset classes
$sql = "SELECT * FROM asset_class WHERE entity = '$class_entity_id'";
$result = mysqli_query($conn, $sql);
$asset_classes = array();
while($row = mysqli_fetch_assoc($result)) {
    $asset_obj = array(
        "value" => intval($row['id']),
        "label" => $row['name'],
        "selected" => false // TODO: get from database
    );
    $asset_classes[] = $asset_obj;
}
$asset_classes = json_encode($asset_classes);

$hidden_name = "class_policy";

echo "<script> 
        var asset_classes = JSON.parse('". $asset_classes ."'); 
        var hidden_name = JSON.parse('". json_encode($hidden_name) ."'); 
    </script>";
?>
<html>

    <div id="layoutSidenav_content" style="margin: 3em;">
        <!-- Success / Failure alert -->
        <?php if (isset($updateSuccess) && $updateSuccess):?>
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>Update success!</strong> Your settings have been updated.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php elseif (isset($updateSuccess) && !$updateSuccess):?>
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Update error!</strong> <?php echo $error?>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif;?>
        <?php if ($session_info['role'] == 3): ?>
            <h2 class="asset-details-header">Asset Depreciation Notifications</h2>
            <div class="card-body">
                <form method="post" action="notification_settings.php">
                    <!-- <div class="row gx-3 mb-3"> -->
                        <!-- toggle whether or not to send asset depreciation warnings -->
                        <!-- <div class="form-check form-switch"> -->
                                <!-- <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" name="depreciationToggle" checked> -->
                                <!-- <label class="form-check-label" for="flexSwitchCheckChecked">Asset depreciation warnings</label> -->
                        <!-- </div> -->
                    <!-- </div> -->
                    <!-- if toggled -->
                    <div class="row gx-3 mb-3" >
                        <!-- Warning date -->
                        <div class="col-md-4">
                            <label class="small mb-1" for="warningDateInput">Warning date *</label>
                            <input required class="form-control" id="warningDateInput" type="number" value="" min="0" name="depreciationDate"
                                placeholder="<?php echo $days_to_add_placeholder; ?> day(s) before expiration"></input>
                        </div>
                        
                        <!-- Warning message -->
                        <div class="col-md-7">
                            <label class="small mb-1" for="warningMessageInput">Warning message *</label>
                            <input required class="form-control" id="warningMessageInput" type="text" value="" name="depreciationMessage"
                                placeholder="<?php echo $message_placeholder; ?>"></input>
                        </div>
                    </div>

                    <!-- Asset classes to warn -->
                    <div class="row gx-3 mb-3" >
                        <div class="col-md-10">
                            <label class="small mb-1" for="multiple-select-search">Asset classes * (If no asset classes are chosen, all asset classes will be used)</label>
                            <select multiple class="form-control" id="multiple-select-search"></select>
                            <input type="hidden" name="<?php echo $hidden_name; ?>" id="<?php echo $hidden_name; ?>" value="">
                        </div>
                    </div>
                    
                    <!-- Amount warning (For amount assets) -->
                    <div class="row gx-3 mb-3" >
                        <div class="col-md-7">
                            <label class="small mb-1" for="warningMinAmt">Minimum warning amount for Amount Assets*</label>
                            <input required class="form-control" id="warningMinAmt" type="number" value="" min="0" name="depreciationMinAmt"
                                placeholder="<?php echo $min_amt_placeholder; ?>"></input>
                        </div>
                    </div>

                    <!-- Included asset statuses -->
                    <div class="row gx-3 mb-3" >
                        <label class="small mb-1">Included statuses to warn</label>
                        <?php
                            for($i = 1; $i <= count($asset_statuses); $i++) {
                                $status = $asset_statuses[$i];
                                echo '<div>';
                                echo '<input class="form-check-input" type="checkbox" id="depreciation_status_'. $status .'" name="depreciation_toggle_'. $i .'">';
                                echo '<label class="form-check-label m-1" for="depreciation_status_'. $status .'">' . $status . '</label>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                        
                    <button class="btn btn-success float-end mx-1" type="submit" name="submit_notification_settings">Save</button>
                </form>
            </div>
        <?php endif; ?>


        <!-- Individual scripts -->
        <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"
        />
        <link rel="stylesheet" href="css/multiselect.css" />
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script src="includes/multiselect_search_class.js"></script>

        <?php include "includes/footer.php"; ?>
    </div>

</html>