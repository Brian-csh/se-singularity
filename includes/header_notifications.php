<?php
if(!function_exists('getAllSubdepartmentIds')) {
    include 'includes/get_subdepartments.php';
}
if(!function_exists('calculate_price')) {
    include 'includes/calculate_price.php';
}
/* 
 * A notification object should consist of:
 * 1. Notification message
 * 2. Corresponding asset id
 * 3. Notification link (opt)
 * 4. Notification time
 * 5. Notification type (opt)
 *  a. Asset depreciation notification 1
 *  b. Asset request notification (Maybe not) 2
 * 6. Whether or not the notification was viewed
 * 
 * Notifications should be cached locally, storing the past 30 days of notifications
 * 
 * Get all assets in the department
 * For each asset, check if it is expired and not in status 3 4 5 or 8. If so, create a notification
 */

 // if localhost, set url to localhost
    if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'localhost') {
        $primary_url = "http://localhost:8000/";
    } else {
        $primary_url = "https://singularity-eam-singularity.app.secoder.net/";
    }
class Notification
{
    public $title;
    public $message;
    public $asset_id;
    public $asset_name;
    public $link;
    public $time;
    public $type;
    public $viewed;

    // construct
    public function __construct($asset_id, $asset_name, $message, $link, $type, $time)
    {
        $this->asset_id = $asset_id;
        $this->asset_name = $asset_name;
        $this->link = $link;
        // $date = DateTime::createFromFormat('Ymd', $time);
        $this->time = $time;
        $this->viewed = false;

        switch ($type) {
            case 1:
                $this->message = $this->asset_name . " ". $message;
                $this->title = "Depreciation Warning";
                break;
            case 2:
                $this->message = $this->asset_name . " is being requested";
                $this->title = "Asset Request";
                break;
            default:
                $this->message = "Unknown notification";
                break;
        }
    }
}
// Each department has its own warning policy
// Warning policies are not inherited from parent departments
// default warning policy: 
// if asset is 30 days from being expired, and not in status 1 2 6 7 9
// OR
// ＮＶＭ　we dont have amount assets. todo if we do do amt assets: if the asset is a amount asset and the amount reaches 0 
// create a notification

$department = $session_info['department'];
$role = $session_info['role'];

// defaults
$days_to_add = 30;
$included_statuses = [1, 2, 6, 7, 9];
$included_list = implode(',', $included_statuses);
$message = "requries maintenance";
$classes = [];	

// // get warning policy
$sql = "SELECT * FROM department WHERE id = '$department'";
$result = mysqli_query($conn, $sql);
$warning_policy = mysqli_fetch_assoc($result);
if($warning_policy != null)
    $warning_policy = $warning_policy['warning_policy'];
if($warning_policy != null) {
    $warning_policy = json_decode($warning_policy, true);
    $days_to_add = $warning_policy['date_policy'];
    $included_statuses = $warning_policy['status_policies'];
    $message = $warning_policy['message_policy'];
    $included_list = implode(',', $included_statuses);
    $classes = $warning_policy['class_policy'];
}

if(!empty($classes)){
    $classes = implode(',', $classes);
}
else{
    $classes = "";
}

$notifications = array();

if(isset($department) && $role != 4){
    // get all assets in this department, including in subdepartments
    $subdepartmentIds = getAllSubdepartmentIds($department, $conn);
    $department_list = implode(',', $subdepartmentIds);

    $sql = "SELECT * FROM asset 
        WHERE department IN ($department_list) 
        AND status IN ($included_list) 
        AND expire IS NOT NULL
        AND expire < NOW() + INTERVAL $days_to_add DAY";

    if (!empty($classes)) {
        $sql .= " AND class IN ($classes)";
    }

    // Prepare and execute the query with the department ID parameter
    $stmt = $conn->prepare($sql);
    // $stmt->bind_param('i', $department_id); for the recursive query
    $stmt->execute();

    // Execute the query and get the result set
    $asset_results = $stmt->get_result();
    $stmt->close();

    // loop through each asset
    foreach($asset_results as $asset) {
        // create a notification for each asset
        $asset_id = $asset['id'];
        $asset_name = $asset['name'];
        $link = $primary_url."edit_asset.php?id=$asset_id";
        $type = 1; // Asset depreciation warning
        $time = $asset['expire'];
        $notification = new Notification($asset_id, $asset_name, $message, $link, $type, $time);
        $notifications[] = $notification;
    }
}


if(count($notifications) == 0) {
    $notification_icon_path = "assets/img/demo/bell.svg";
} else {
    $notification_icon_path = "assets/img/demo/bell_noti.svg";
}
?>
<div>
    <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
        <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownNotificationImage" role="button"
            aria-expanded="false" onclick="toggleNotificationDropdown()">
            <img class="img-fluid" src=<?= $notification_icon_path ?> />
        </a>
        <ul class="dropdown-menu animated--fade-in-up" id="notificationDropdownMenu"
            aria-labelledby="navbarDropdownUserImage">
            <li>
                <div class="text-center">Notifications</div>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <!-- scrollable notifications -->
                <div class="dropdown-list-group" style="overflow-y: auto; max-height: 200px;">
                    <?php if(count($notifications) == 0): ?>
                        <div class="text-center">
                            No notifications
                        </div>
                    <?php endif; ?>
                    <?php foreach ($notifications as $notification): ?>
                        <a class="dropdown-item" style="margin-right: 1em;" href=<?= $notification->link ?>>
                            <div class="dropdown-item-icon"><i data-feather="alert-circle"></i></div>
                            <div class="dropdown-item-desc">
                                <?= $notification->title ?>
                                <div class="dropdown-item-desc small">
                                    <?= $notification->message ?>
                                </div>
                                <div class="dropdown-item-desc smaller mt-1">
                                    Expire Date: <?= date("Y-m-d", $notification->$time) ?>
                                </div>
                            </div>
                            <!-- <div class="dropdown-item-icon" style="margin-left: 1em;"><i data-feather="x"></i></div> -->
                        </a>
                        <div class="dropdown-divider" style="border-color: #2b323b !important"></div>
                    <?php endforeach; ?>
                </div>
            </li>
        </ul>
    </li>
    <script>
        function toggleNotificationDropdown() {
            var dropdownMenu = document.getElementById("notificationDropdownMenu");
            if (dropdownMenu.style.display === "block") {
                dropdownMenu.style.display = "none";
            } else {
                dropdownMenu.style.display = "block";
            }
        }
    </script>

</div>