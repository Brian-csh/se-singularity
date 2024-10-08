<?php
require "includes/get_subdepartments.php";
include "includes/oss.php";

use OSS\Core\OssException;
session_start();
$session_info = $_SESSION;

$active = 'Add Asset';
$errors = "";
$custom_attribute_errors = "";

$entity_id = $session_info['user']['entity'];
$department_id = $session_info['user']['department'];
$subdepartmentids = getAllSubdepartmentIds($department_id,$conn);
$subdepartmentids = implode(",",$subdepartmentids);

if (isset($_POST['submit_asset'])) {
    $name = $_POST['name'];
    $asset_class = $_POST['asset_class'];
    $department = $_POST['department'];
    $price = $_POST['price'];
    $description = addslashes($_POST['description']);
    $position = $_POST['asset_location'];
    $expire = strtotime($_POST['expiration']);
    $image_url = "";
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
        } catch (OssException $e) {
            echo "<script>alert('Failed to upload the file: " . $e->getMessage() . "')</script>";
        }
    }
    $date_created = time();

    $sql = "INSERT INTO asset (parent, name, class, department, user, price, description, position, custom_attr, date_created, expire,status, image) 
    VALUES (null, '$name', '$asset_class', '$department', null, NULLIF('$price',''), '$description', '$position', null, '$date_created','$expire','1', '$image_url')";
    // echo $sql;
    if ($conn->query($sql)) {
        header('Location: assets.php');
    } else {
        echo "Error inserting asset: " . $conn->error;
        // header('Location: add_asset_by_rm.php?insert_error');
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
    <script>
      tinymce.init({
        selector: '#descriptionTextarea',
        plugins: 'searchreplace autolink directionality visualblocks visualchars image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap emoticons autosave',
        toolbar: 'undo redo print spellcheckdialog formatpainter | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | alignleft aligncenter alignright alignjustify lineheight | checklist bullist numlist indent outdent | removeformat',
        skin: "oxide-dark",
        content_css: "dark"
      });
    </script>
</head>

<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
<script src="js/datatables/datatables-simple-demo.js"></script>
<body class="nav-fixed">
<nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-black border-bottom border-dark" id="sidenavAccordion">
    <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle" onclick="document.body.classList.toggle('sidenav-toggled');localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sidenav-toggled'));
"><i data-feather="menu"></i></button>
    <!-- Navbar Brand-->
    <a class="navbar-brand pe-3 ps-4 ps-lg-2 text-primary" href="index.php">Singularity EAM</a>

    <!-- Navbar Items-->
    <ul class="navbar-nav align-items-center ms-auto">

        <!-- User Dropdown-->
        <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" role="button" data-bs-toggle="modal" data-bs-target="#logoutModal"><img class="img-fluid" src="assets/img/demo/user-placeholder.svg" /></a>
            <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                <h6 class="dropdown-header d-flex align-items-center">
                    <img class="dropdown-user-img" src="assets/img/illustrations/profiles/profile-2.png" />
                </h6>
            </div>
        </li>
    </ul>
</nav>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalTitle">Log out</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Are you sure you want to log out?</div>
            <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button><a href="logout.php" class="btn btn-danger">Log out</a></div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

<div id="layoutSidenav_content">
    <main>
        <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
            <div class="container-fluid px-4">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="briefcase"></i></div>
                                User
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container-xl px-4 mt-4 py-3">
            <!-- Account page navigation-->
            <div class="row">
                <div class="col-xl-12">
                    <!-- Asset details card-->
                    <div class="card mb-4">
                        <div class="card-header">Asset Details</div>
                        <div class="card-body">
                            <?php
                            if ($errors != "") echo  '<div class="alert alert-danger" role="alert">
                                ' . $errors . '</div>'
                            ?>
                            <form method="post" enctype="multipart/form-data">
                                <!-- Form Row-->
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="small mb-1" for="inputName">Asset Name *</label>
                                        <input required class="form-control" id="inputName" type="text" value="" name="name" placeholder="Enter an Asset Name">
                                    </div>

                                <!-- Asset department -->
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="inputDepartment">Department *</label>
                                        <select class="form-control" id="inputDepartment" name="department" required>
                                            <option value="">Select a Department</option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM department WHERE id IN ($subdepartmentids)");
                                                    while ($row = $results->fetch_assoc()) {
                                                            $id = $row['id'];
                                                            $department__ = $row['name'];
                                                            echo '<option value="' . $id . '">' . $department__ . '</option>';
                                                    }
                                                    ?>
                                        </select>
                                    </div>
                                    <!-- Form Group (asset class)-->
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="inputClass">Asset Class *</label>
                                        <select required class="form-control" id="inputClass" name="asset_class">
                                            <option value="">Select an Asset Class</option>
                                            <?php
                                            $results = $conn->query("SELECT id, name FROM asset_class WHERE entity = $entity_id");
                                            while ($row = $results->fetch_assoc()) {
                                                unset($id, $name);
                                                $id = $row['id'];
                                                $name = $row['name'];
                                                echo '<option value="' . $id . '">' . $name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row gx-3 mb-3">

                                    <div class="col-md-4">
                                        <label class="small mb-1" for="inputExpiration">Expiration Date</label>
                                        <input class="form-control" id="inputExpiration" type="date" value="" name="expiration" required>
                                    </div>
                                    
                                        <!-- Asset Location -->
                                        <div class="col-md-4">
                                            <label class="small mb-1" for="inputLocation">Location</label>
                                            <input class="form-control" id="inputLocation" type="text" value="" name="asset_location" placeholder="Enter a Location">
                                        </div>
                                    
                                        <!-- Asset price -->
                                        <div class="col-md-3">
                                            <label class="small mb-1" for="inputPrice">Price</label>
                                            <input type="number" class="form-control" name="price" id="inputPrice" step="0.01" placeholder="1000.00" required>
                                        </div>
                                </div>
                                <input type="file" name="file" id="imageInput" style="color: white">
                                <button onclick="clearFileInput()" class="btn btn-primary text-light float-end" style="background:red; border:none">Clear</button>

                                <div class="row gx-3 mb-3">
                                </div>
                                <!-- Form Row -->
                                <div class="row gx-3 mb-4">
                                    <div class="col-md-12">
                                        <label class="small mb-1" for="descriptionTextarea">Description</label>
                                        <textarea class="form-control" id="descriptionTextarea" name="description" rows="5" placeholder="Enter a description"></textarea>
                                    </div>
                                </div>
                                <button class="btn btn-success float-end mx-1" type="submit" name="submit_asset">Create New Asset</button>
                                <!-- Save changes button-->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function clearFileInput() {
                    document.getElementById('imageInput').value = '';
                }
            </script>
        </div>
    </main>
</div>

</html>
