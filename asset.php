<?php
include "includes/db/connect.php";
include "includes/scripts/functions.php";

if (isset($_GET['assetid'])) {
    $asset_id_ = $_GET['assetid'];
}

if (isset($_POST['print'])) { //print asset tag
    $tag_url = "asset_tag.php?id=".$asset_id_;

    //redirect to asset tag page
    header("Location: ".$tag_url);
}

$sql_asset = "SELECT * FROM asset WHERE id = '$asset_id_' LIMIT 1";
$result_asset = $conn->query($sql_asset);

if ($result_asset && mysqli_num_rows($result_asset) > 0) {
        $asset_data = mysqli_fetch_assoc($result_asset);
        $date_create = gmdate("Y.m.d \ | H:i:s",$asset_data['date_created']+28000);
        $asset_parent_id = $asset_data['parent'];
        $asset_name = $asset_data['name'];
        $asset_class_id = $asset_data['class'];
        $asset_user_id = $asset_data['user'];
        $asset_price = $asset_data['price'];
        $asset_description = $asset_data['description'];
        $asset_position = $asset_data['position'] == '' ? '--' : $asset_data['position'];
        $asset_status_id = $asset_data['status'];
        $asset_brand = $asset_data['brand'];
        $asset_model = $asset_data['model'];
        $asset_serial_number = $asset_data['serial number'];
        $asset_original_price = $asset_data['price'];
        $asset_current_price = $asset_data['current price'];
        $asset_department_id = $asset_data['department'];
        $custom_attributes = $asset_data['custom_attr'];
        $asset_image = isset($asset_data['image']) ? $asset_data['image'] : "";
}

// Fetch Data

// Fetch parent name
if (isset($asset_parent_id))
    $asset_parent = mysqli_fetch_array($conn->query("SELECT * FROM asset WHERE id = '$asset_parent_id' LIMIT 1"))['name'];
else
    $asset_parent = "--";

// Fetch class name
$asset_class_name = mysqli_fetch_array($conn->query("SELECT * FROM asset_class WHERE id = '$asset_class_id' LIMIT 1"))['name'];
$asset_class_type = mysqli_fetch_array($conn->query("SELECT * FROM asset_class WHERE id = '$asset_class_id' LIMIT 1"))['class_type'];

// Fetch user name
if (isset($asset_user_id))
    $asset_user_name = mysqli_fetch_array($conn->query("SELECT * FROM user WHERE id = '$asset_user_id' LIMIT 1"))['name'];
else
    $asset_user_name = "--";

// Fetch Status
$asset_status = mysqli_fetch_array($conn->query("SELECT * FROM asset_status_class WHERE id = '$asset_status_id' LIMIT 1"))['status'];

// Fetch deaprtment
$asset_department = mysqli_fetch_array($conn->query("SELECT * FROM department WHERE id = '$asset_department_id' LIMIT 1"))['name'];

$active = $asset_name;

include "includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?= $active ?> - Singularity EAM</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
</head>

<body class="nav-fixed">

<div id="layoutSidenav_content">
    <main>
    <header class="page-header pt-10 page-header-dark bg-gradient-primary-to-secondary pb-5">
            <div class="container-xl px-4">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="package"></i></div>
                                <?php echo $asset_name ?>
                            </h1>
                            <div class="page-header-subtitle">
                                <?php echo "Date Created: {$date_create}<br>";?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container-fluid pt-5 px-4">
            <div class="card">
                <div class="card-body">
                    <div class = "row mb-3">
                        <div class = "col -md-6">
                            <!-- Asset Image (aliyun) -->
                            <div class="card-header">
                                    <h3> Asset Image
                                    </h3>
                                </div>
                            <div id="image-container" style="padding: 20px">
                                <script>
                                    window.onload = function() {
                                        var img = document.getElementById('assetImage');
                                        img.src = "<?php echo ($asset_image === "") ? "assets/img/asset_placeholder.png" : $asset_image; ?>"; // Set the source of the image
                                    }
                                </script>
                                <img src="" id="assetImage" alt="image not available">                            
                            </div>
                        </div>
                        <div class = "col -md-6">
                            <!-- TODO: Asset table -->
                            <div class="card">
                                <div class="card-header">
                                    <h3> Asset Info
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class = "row">
                                        <table class="table table-hover">
                                            <tbody>
                                                <tr>
                                                    <th>parent</th> 
                                                    <th>type</th>
                                                    <th>class</th>
                                                    <th>user</th>
                                                    <th>department</th>
                                                    <th>position</th>
                                                    <th>status</th>
                                                </tr>
                                                <tr>
                                                    <td><?php echo $asset_parent; ?></td>
                                                    <td><?php
                                                            if($asset_class_type == 0)
                                                                echo "Item Asset";
                                                            else if($asset_class_type == 1)
                                                                echo "Amount Asset"; 
                                                        ?>
                                                    </td>
                                                    <td><?php echo $asset_class_name; ?></td>
                                                    <td><?php echo $asset_user_name; ?></td>
                                                    <td><?php echo $asset_department; ?></td>
                                                    <td><?php echo $asset_position; ?></td>
                                                    <!-- IMPROVE : add color for diff status -->
                                                    <td><?php echo $asset_status; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description box -->
                    <div class= "row mb-3 gx-3">
                        <div class="col-md-12">
                            <label class="small mb-1" for="descriptionTextarea">Description</label>
                            <div style="color: white; border: 1px solid white; padding: 10px">
                                <?=$asset_description?>
                            </div>
                        </div>
                    </div>

                    <div class = "row mb-3">
                        <!-- Asset Basic Info Table -->
                        <div class="col mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3> Basic Info
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class = "col">
                                        <table class="table table-hover">
                                            <tbody>
                                                <tr>
                                                    <th>Brand</th>
                                                    <td><?php echo $asset_brand; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Model</th>
                                                    <td><?php echo $asset_model; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Serial Number</th>
                                                    <td><?php echo $asset_serial_number; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Asset Financial Info Table -->
                        <div class = "col mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3> Financial Info
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class = "col">
                                        <table class="table table-hover">
                                            <tbody>
                                                <tr>
                                                    <th>Original Price</th>
                                                    <td><?php echo $asset_original_price; ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Current Price</th>
                                                    <td><?php echo $asset_current_price; ?></td>
                                                </tr>
                                                <!-- <tr> -->
                                                    <!-- <th>Depreciation Model</th> -->
                                                    <!-- <td><?php // echo $asset_depreciation_model; ?></td> -->
                                                <!-- </tr> -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class = "row mb-3">

                        <!-- Asset Custom Attribute Info Table -->
                        <div class="col mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3> Custom Attributes
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class = "col">
                                        <table class="table table-hover">
                                            <tbody>
                                                <?php 
                                                    if (isset($custom_attributes) && !empty($custom_attributes)) {
                                                        $custom_attribute_obj = json_decode($custom_attributes);
                                                        foreach ($custom_attribute_obj as $custom_key => $custom_value) {
                                                            echo '
                                                                <tr>
                                                                    <th>'. $custom_key .'</th>
                                                                    <td>'. $custom_value .'</td>
                                                                </tr>
                                                            ';
                                                        }
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            <div class="card">
                <div class="card-header">
                    <h3>History</h3>
                </div>
                <div class="card-body">
                    <div id="tablePreloader">
                        <p class="text-white p-3">Loading...</p>
                    </div>
                    <table id="datatablesSimple" style="display: none">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Log</th>
                            <th>Type</th>
                            <th>By</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Date</th>
                            <th>Log</th>
                            <th>Type</th>
                            <th>By</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php
                            // Fetch logs
                            $sql_log = "SELECT * FROM log WHERE (subject = '$asset_id_') ORDER BY date DESC";
                            $result = $conn->query($sql_log);
                            while ($row = $result->fetch_assoc()) {                                                          
                                //Fetch Log Type
                                $type_id = $row["log_type"];
                                $type = mysqli_fetch_array($conn->query("SELECT type FROM log_type WHERE id = '$type_id'"));
                                if(isset($type['type']))
                                    $type = $type['type'];
                                if($type_id>=1 && $type_id <=3)continue;

                                $date = gmdate("Y.m.d \ | H:i:s", $row["date"]+28800);
                                $log_id = $row["id"];
                                $text = $row["text"];

                                // Fetch user name
                                $user_id = $row["By"];
                                $by = "";
                                if ($user_id != '') {
                                    $by = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$user_id'"));
                                    if(isset($by['name']))
                                        $by = $by['name'];
                                }

                                echo "<tr data-id='$log_id' >
                                <td class='text-primary'>$date</td>
                                <td class='text-white'>$text</td>
                                <td class='text-white'>$type</td>
                                <td class='text-white'>$by</td>
                                </tr>";
                            }

                            ?>
                        </tbody>
                    </table>
                    <form method="post" action="asset.php?assetid=<?=$asset_id_?>">
                        <button type="submit" name="print" class="btn btn-primary btn-xs float-end">Asset Tag</a>
                    </form>
                </div>
            </div>
        </div>
    </main>




    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <?php
    include "includes/footer.php";
    ?>
</div>

</html>
