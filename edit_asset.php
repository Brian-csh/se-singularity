<?php
include "includes/calculate_price.php";

if (isset($_GET['assetid'])) {
    $asset_id = $_GET['assetid'];
}

if (isset($_GET['success'])) {
    $image_operation_status = $_GET['success'];
} else {
    $image_operation_status = 0;
}

$active = "Edit Asset";
include "includes/header.php";
include "includes/db/connect.php";
include "includes/scripts/functions.php";
include "includes/oss.php";
use OSS\Core\OssException;

$sql_asset = "SELECT * FROM asset WHERE id = '$asset_id' LIMIT 1";
$result_asset = $conn->query($sql_asset);

if ($result_asset && mysqli_num_rows($result_asset) > 0) {
        $asset_data = mysqli_fetch_assoc($result_asset);
        $date_create = gmdate("Y.m.d \ | H:i:s",$asset_data['date_created']+28800);
        $asset_parent_id = $asset_data['parent'];
        $asset_name_ = $asset_data['name'];
        $asset_class_id = $asset_data['class'];
        $asset_user_id = $asset_data['user'];
        $asset_price = $asset_data['price'];
        $asset_description = $asset_data['description'];
        $asset_position = $asset_data['position'];
        $asset_expire = date(strtotime($asset_data['expire']));
        $asset_status_id = $asset_data['status'];
        $asset_brand = $asset_data['brand'];
        $asset_model = $asset_data['model'];
        $asset_serial_number = $asset_data['serial number'];
        $asset_original_price = $asset_data['price'];
        $asset_current_price = $asset_data['current price'];
        $asset_depreciation_model = $asset_data['depreciation model'];
        $asset_department_id = $asset_data['department'];
        $custom_attributes = $asset_data['custom_attr'];
        $asset_image = isset($asset_data['image']) ? $asset_data['image'] : "";
}

// Fetch Data

// Fetch parent name
$row_parent = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_parent_id' LIMIT 1"));
$asset_parent = isset($row_parent['name']) ? $row_parent['name'] : "N/A";

// Fetch class name
$row_class = mysqli_fetch_array($conn->query("SELECT * FROM asset_class WHERE id = '$asset_class_id' LIMIT 1"));
$asset_class_name = isset($row_class['name']) ? $row_class['name'] : "N/A";
$asset_class_type = isset($row_class['class_type']) ? $row_class['class_type'] : "N/A";

// Fetch user name
$row_user = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$asset_user_id' LIMIT 1"));
$asset_user_name = isset($row_user['name']) ? $row_user['name'] : "N/A";

// Fetch Status
$row_status = mysqli_fetch_array($conn->query("SELECT status FROM asset_status_class WHERE id = '$asset_status_id' LIMIT 1"));
$asset_status = isset($row_status['status']) ? $row_status['status'] : "N/A";

// Fetch deaprtment
$row_department = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$asset_department_id' LIMIT 1"));
$asset_department = isset($row_department['name']) ? $row_department['name'] : "N/A";


// Update Asset info
if(isset($_POST['edit_asset'])){
    $asset_new_parent = $_POST['editAssetParent'];
    $asset_new_name = $_POST['editAssetName']; if(!$asset_new_name) $asset_new_name = $asset_name_;


    $asset_new_class = $_POST['editAssetClass'];
    $asset_new_expire = $_POST['editAssetExpire']; if(!$asset_new_expire) $asset_new_expire = $asset_expire;

    $asset_new_position = $_POST['editAssetPosition'];
    $sql = "UPDATE asset SET parent =$asset_new_parent,name = '$asset_new_name',class = '$asset_new_class', 
        expire = '$asset_new_expire',position = '$asset_new_position' WHERE id = '$asset_id'";
    $result = $conn->query($sql);
    if($result){
        echo "<script>alert('Asset info updated successfully!')</script>";
        insert_log_edit_asset($conn,$asset_data,$session_info['id'],6);
        // header('Location: edit_asset.php?assetid=' . $asset_id );
        echo "<script>window.location.href = 'edit_asset.php?assetid=$asset_id'</script>";
    }
    else{
        echo "<script>alert('Asset update failed!')</script>";
    }
}


// Update Description
if(isset($_POST['description_change'])){
    $description = $_POST['description']; if(!$description) $description = $asset_description;
    $sql = "UPDATE asset SET description = '$description' WHERE id = '$asset_id'";
    $result = $conn->query($sql);
    if($result){
        echo "<script>alert('Description updated successfully!')</script>";
        insert_log_edit_asset($conn,$asset_data,$session_info['id'],6);
        echo "<script>window.location.href = 'edit_asset.php?assetid=$asset_id'</script>";
    }
    else{
        echo "<script>alert('Description update failed!')</script>";
    }
}

// Update basic info
if(isset($_POST['edit_basic'])){
    $basic_brand = $_POST['basic_brand']; if(!$basic_brand) $basic_brand = $asset_brand;
    $basic_model = $_POST['basic_model']; if(!$basic_model) $basic_model = $asset_model;
    $basic_sn = $_POST['basic_sn']; if(!$basic_sn) $basic_sn = $asset_serial_number;
    $sql = "UPDATE asset SET brand='$basic_brand', model='$basic_model', `serial number`='$basic_sn' WHERE id='$asset_id'";
    $result = $conn->query($sql);
    if($result){
        echo "<script>alert('Basic info updated successfully!')</script>";
        insert_log_edit_asset($conn,$asset_data,$session_info['id'],6);
        echo "<script>window.location.href = 'edit_asset.php?assetid=$asset_id'</script>";
    }
    else{
        echo "<script>alert('Basic info update failed!')</script>";
    }
}

// Update Financial info
if(isset($_POST['edit_financial'])){
    $financial_op = $_POST['financial_op']; if(!$financial_op) $financial_op = $asset_original_price;
    $sql = "UPDATE asset SET `price`='$financial_op' WHERE id='$asset_id'";
    $result = $conn->query($sql);
    if($result){
        echo "<script>alert('Financial info updated successfully!')</script>";
        insert_log_edit_asset($conn,$asset_data,$session_info['id'],6);
        // TODO: Update current asset value
        echo "<script>window.location.href = 'edit_asset.php?assetid=$asset_id'</script>";
    }
    else{
        echo "<script>alert('Financial info update failed!')</script>";
    }
}

// Update custom attribute info
if(isset($_POST['edit_custom_attr'])){

    $ca_fields = [];
    // Loop through the $_POST array
    foreach ($_POST as $key => $value) {
        // Check if the key starts with "ca"
        if (strncmp($key, 'ca', 2) === 0) {
            // Add the key-value pair to the $ca_fields array
            $ca_fields[$key] = $value;
        }
    }
    $custom_attributes = json_encode($ca_fields);

    $sql = "UPDATE asset SET custom_attr='$custom_attributes' WHERE id='$asset_id'";
     if($conn->query($sql)){
         echo "<script>alert('Custom attribute info updated successfully!')</script>";
//         insert_log_edit_asset($conn,$asset_data,6);
        // echo "<script>window.location.href = 'edit_asset.php?id=$asset_id&name=$asset_name'</script>";
     }
     else{
         echo "<script>alert('Custom attribute info update failed!')</script>";
     }
}

//uploading an image for an asset
if (isset($_POST['upload_image'])) {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $localFilePath = $file['tmp_name']; //path in local machine
        $originalFilename = $file['name'];

        // Generate a unique object name for the file in OSS
        $objectName = uniqid() . '-' . $originalFilename;

        try {
            // Upload the file to OSS
            $ossClient->uploadFile($bucket, $objectName, $localFilePath);
            $image_url = 'https://singularity-eam.oss-cn-beijing.aliyuncs.com/' . $objectName;
            $sql = "UPDATE asset SET image='$image_url' WHERE id='$asset_id'";
            if (!$conn->query($sql)) {
                $image_operation_status = -1;
                echo "<script>alert('Update image failed!')</script>";
            }
            echo "<script>window.location.href = 'edit_asset.php?assetid=$asset_id&success=1'</script>";
            // header('Location: edit_asset.php?assetid=' . $asset_id . '&success=1');
        } catch (OssException $e) {
            $image_operation_status = -1;
            echo "<script>alert('Failed to upload the file: " . $e->getMessage() . "')</script>";
        }
    } else {
        echo "<script>alert('No file selected or an error occurred during file upload.')</script>";
    }
}

if (isset($_POST['delete_image'])) {
    try {
        $stripped_object_name = substr($asset_image, 52); //remove the bucket and endpoint
        $ossClient->deleteObject($bucket, $stripped_object_name);
        $sql = "UPDATE asset SET image=NULL WHERE id='$asset_id'";
        if (!$conn->query($sql)) {
            $image_operation_status = -1;
            echo "<script>alert('Update image failed!')</script>";
        }
        echo "<script>window.location.href = 'edit_asset.php?assetid=$asset_id&success=2'</script>";
        // header('Location: edit_asset.php?assetid=' . $asset_id . '&success=2');
    } catch (OssException $e) {
        $image_operation_status = -1;
        echo "<script>alert('Failed to upload the file: " . $e->getMessage() . "')</script>";
    }
}
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
    <script src="https://cdn.tiny.cloud/1/asb4xsfiuva8d91yy7xuxeuce9jbpe7tee28ml49p4prl31z/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
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
                                <?php echo $asset_name_ ?>
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
                    <!-- TODO: SHOW Image,RTF description input box,Basic info, Financial Info -->
                    <div class = "row mb-3">
                        <div class = "col -md-6">
                            <!-- Asset Image -->
                            <h1>Image</h1>
                            <?php
                                if ($image_operation_status == 1) {
                                    echo '<div class="alert alert-success" role="alert">Upload Successful!</div>';
                                } else if ($image_operation_status == -1) {
                                    echo '<div class="alert alert-danger" role="alert">Operation Failed</div>';
                                } else if ($image_operation_status == 2) {
                                    echo '<div class="alert alert-success" role="alert">Image Removed!</div>';
                                }
                            ?>
                            <div id="image-container" style="padding: 20px" class = "row mb-3">
                                <script>
                                    window.onload = function() {
                                        var img = document.getElementById('assetImage');
                                        img.src = "<?=$asset_image?>"; // Set the source of the image
                                    }
                                </script>
                                <img src="" id="assetImage">                            
                                
                            </div>
                            <div class = "row mb-3">
                                <form action="edit_asset.php?assetid=<?php echo $asset_id ?>" method="post" enctype="multipart/form-data">
                                <div class = "col -md-6">
                                    <input type="file" name="file" style="color: white">
                                </div>
                                <div class = "col -md-6">
                                    <button type="submit" name="upload_image" class="btn btn-primary text-light float-end" style="background:green; border:none">Upload</button>
                                    <?php
                                        if ($asset_image != "")
                                            echo '<button type="submit" name="delete_image" class="btn btn-primary text-light float-end" style="background:red; border:none; margin-right: 10px;">Delete</button>';
                                    ?>
                                </div>
                                </form>
                            </div>
                        </div>
                        <div class = "col -md-6">
                            <!-- Asset table -->
                            <div class="card">
                                <div class="card-header">
                                    <h3> Asset Info
                                    <button type="button" class="btn btn-sm btn-outline-primary float-end" data-bs-toggle="modal" data-bs-target="#editAssetModal"><i data-feather="edit"></i></button>
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
                                                <th>expire</th>
                                                <th>status</th>
                                            </tr>
                                            <tr>
                                                <td><?php echo $asset_parent; ?></td>
                                                <td><?php
                                                        if($asset_class_type == 0)
                                                            echo "Item Asset";
                                                        else if($asset_class_type == 1)
                                                            echo "Amount Asset"; 
                                                        else 
                                                            echo "NULL";
                                                    ?>
                                                </td>
                                                <td><?php echo $asset_class_name; ?></td>
                                                <td><?php echo $asset_user_name; ?></td>
                                                <td><?php echo $asset_department; ?></td>
                                                <td><?php echo $asset_position; ?></td>
                                                <td><?php echo $asset_expire; ?></td>
                                                <!-- Todo : add color for diff status -->
                                                <td><?php echo $asset_status; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description input box -->
                    <!-- TODO: support RTF -->
                    <form method = "POST" action="edit_asset.php?assetid=<?php echo $asset_id ?>" >
                        <div class= "row mb-3 gx-3">
                            <div class="col-md-12">
                                <label class="small mb-1" for="descriptionTextarea">Description</label>
                                <textarea class="form-control" id="descriptionTextarea" name="description" rows="20"></textarea>
                                <button type="submit" name="description_change" class="btn btn-primary text-light float-end" style="background:green; border:none">Change description</button>
                            </div>
                        </div>
                    </form>


                    <div class = "row mb-3">

                        <!-- Asset Basic Info Table -->
                        <div class="col mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3> Basic Info
                                    <button type="button" class="btn btn-sm btn-outline-primary float-end" data-bs-toggle="modal" data-bs-target="#editBasicInfoModal"><i data-feather="edit"></i></button>
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
                                    <button type="button" class="btn btn-sm btn-outline-primary float-end" data-bs-toggle="modal" data-bs-target="#editFinancialInfoModal"><i data-feather="edit"></i></button>
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
                                                <td><?php 
                                                if(calculate_price($asset_data, time()) == -1){
                                                    echo "No expire date set to calculate current price";
                                                }
                                                else{
                                                    echo number_format((float)calculate_price($asset_data, time()), 2, '.', ''); 
                                                }
                                                    ?></td>
                                            </tr>
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
                                        <button type="button" class="btn btn-sm btn-outline-primary float-end" data-bs-toggle="modal" data-bs-target="#editCustomAttributeModal"><i data-feather="edit"></i></button>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class = "col">
                                        <table class="table table-hover">
                                            <tbody>
                                                <?php
                                                if ($custom_attributes) {
                                                    $custom_attribute_obj = json_decode($custom_attributes);
                                                    foreach ($custom_attribute_obj as $custom_key => $custom_value) {
                                                        $custom_key_id = substr($custom_key, 3);

                                                        // Get the custom key name
                                                        $custom_key_name = $conn->query("SELECT * FROM asset_attribute WHERE id = $custom_key_id")->fetch_assoc()['custom_attribute'];
                                                        echo '
                                                            <tr>
                                                                <th>'. $custom_key_name .'</th>
                                                                <td>'. $custom_value .'</td>
                                                            </tr>
                                                        ';
                                                    }
                                                } else {
                                                    echo '<p class="text-white">No custom attributes available for this asset</p>';
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
        </div>

    </main>
    
    <!-- Edit Modals -->
    <!-- Asset Modal -->
    <div class="modal fade" id="editAssetModal" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style = "max-width: 800px; max-height:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Asset</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Edit asset name -->
                        <div class="mb-3">
                            <label for="editAssetName">Asset Name</label>
                            <input class="form-control" id="editAssetName" type="text" name="editAssetName" placeholder="<?php echo $asset_name_; ?>">
                        </div>
                        <!-- Edit position -->
                        <div class="mb-3">
                            <label for="editAssetPosition">Position</label>
                            <input class="form-control" id="editAssetPosition" type="text" name="editAssetPosition" placeholder="<?php echo $asset_position; ?>">
                        </div>
                        
                        <div class = "row">
                            <!-- Edit CLASS -->
                            <div class="col">
                                <!-- Edit customized class -->
                                <div class="md-3">
                                    <label for="editAssetClass">Class</label>
                                        <select class="form-control ms-2" id="inputParentClass" name="editAssetClass">
                                            <?php echo '<option value="' . $asset_class_id . '">' . $asset_class_name . '</option>'?>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM asset_class");
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name']!=$asset_class_name) {
                                                            unset($id, $class);
                                                            $id = $row['id'];
                                                            $class = $row['name'];
                                                            echo '<option value="' . $id . '">' . $class . '</option>';
                                                        }
                                                    }
                                                    ?>
                                        </select>
                                </div>
                            </div>

                            <div class= "col">
                                <!-- Edit Expiration date -->
                                <div class="md-4">
                                        <label class="small mb-1" for="inputExpiration">Expiration Date</label>
                                        <input class="form-control" id="inputExpiration" type="date" value="" name="editAssetExpire">
                                </div>
                            </div>
                        </div>

                        <div class = "row">
                            <div class="col">
                                <!-- Edit Parent -->
                                <div class="mb-3">
                                    <label for="editAssetParent">Parent</label>
                                        <select class="form-control ms-2" id="editParent" name="editAssetParent">
                                            <option value="NULL">No Parent</option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM asset");
                                                    while ($row= $results->fetch_assoc()) {
                                                        if ($row['name']!= $asset_parent&&$row['name']!= $asset_name_) {
                                                            unset($id, $parent);
                                                            $id = $row['id'];
                                                            $parent = $row['name'];
                                                            echo '<option value="' . $id . '">' . $parent . '</option>';
                                                        }
                                                    }
                                                    ?>
                                        </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="edit_asset">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Basic info Modal -->
    <div class="modal fade" id="editBasicInfoModal" tabindex="-1" role="dialog" aria-labelledby="BasicInfoEditLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Basic Info</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Edit Brand -->
                        <div class="mb-3">
                            <label for="basicEditBrand">Brand Name </label>
                            <input class="form-control" id="basicEditBrand" type="text" name="basic_brand" placeholder="<?php echo $asset_brand; ?>" >
                        </div>
                        <!-- Edit Model-->
                        <div class="mb-3">
                            <label for="basicEditModel">Model </label>
                            <input class="form-control" id="basicEditModel" type="text" name="basic_model" placeholder="<?php echo $asset_model; ?>">
                        </div>
                        <!-- Edit Serial Number -->
                        <div class="mb-3">
                            <label for="basicEditSerialNumber">Serial Number </label>
                            <input class="form-control" id="basicEditSerialNumber" type="text" name="basic_sn" placeholder="<?php echo $asset_serial_number; ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="edit_basic">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Financial info Modal -->
    <div class="modal fade" id="editFinancialInfoModal" tabindex="-1" role="dialog" aria-labelledby="FinancialInfoEditLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Financial Info</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Edit Price -->
                        <div class="mb-3">
                            <label for="editOP">Original Price </label>
                            <input class="form-control" id="financialEditOP" type="text" name="financial_op" placeholder="<?php echo $asset_original_price; ?>" >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="edit_financial">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom attribute Modal -->
    <div class="modal fade" id="editCustomAttributeModal" tabindex="-1" role="dialog" aria-labelledby="CustomAttributeEditLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Custom</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <?php
                        // Get the entity of the asset
                        if (isset($asset_department_id)) {
                            $sql = "SELECT entity FROM department WHERE id = '$asset_department_id'";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_fetch_assoc($result);
                            $entity = $row['entity'];

                            // Get all custom attributes for this entity
                            $sql = "SELECT * FROM asset_attribute WHERE entity_id = '$entity'";
                            $result = mysqli_query($conn, $sql);
                            if ($result -> num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $attribute_id = $row['id'];
                                    $attribute_name = $row['custom_attribute'];
                                    // Display the attribute name and value
                                    echo '<div class="mb-3">';
                                    echo '<label for="customEdit' . $attribute_id . '">' . $attribute_name . '</label>';
                                    echo '<input class="form-control" id="customEdit' . $attribute_id . '" type="text" name="ca_' . $attribute_id . '" placeholder="Attribute Value">';
                                    echo '</div>';
                                }
                            } else {
                                echo "No custom attributes found for this asset's entity.";
                            }
                        }
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="edit_custom_attr">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <script>
        $(document).ready(function() {
            tinymce.init({
            selector: '#descriptionTextarea',
            plugins: 'searchreplace autolink directionality visualblocks visualchars image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap emoticons autosave',
            toolbar: 'undo redo print spellcheckdialog formatpainter | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | alignleft aligncenter alignright alignjustify lineheight | checklist bullist numlist indent outdent | removeformat',
            skin: "oxide-dark",
            content_css: "dark",
            setup: function (editor) {
            editor.on('init', function (e) {
                editor.setContent('<?php echo preg_replace("/\s+/"," ",$asset_description);?>');
            });
            }
        });
        });
    </script>
    <?php
    include "includes/footer.php";
    ?>


</div>

</html>
