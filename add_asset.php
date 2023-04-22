<?php
include "includes/db/connect.php";
session_start();
$session_info = $_SESSION;

$active = 'Add Asset';
$errors = "";


if (isset($_POST['submit_asset'])) {

    $name = $_POST['name'];
    $asset_parent = $_POST['asset_parent'];
    if (empty($asset_parent)) {
        $asset_parent = NULL;
      }
    $expiration = $_POST['expiration'];
    $asset_class = $_POST['asset_class'];
    $department = $_POST['department'];
    $asset_user = $_POST['asset_user'];
    $price = $_POST['price'];
    $description = addslashes($_POST['description']);
    $position = $_POST['asset_location'];
    $expire = $_POST['expiration'];
    // $custom_attributes = $_POST['custom_attributes'];

    $sql = "INSERT INTO asset (parent, name, class, department, user, price, description, position, expire, custom_attr) 
    VALUES (NULLIF('$asset_parent',''), '$name', NULLIF('$asset_class',''), '$department', NULLIF('$asset_user',''), NULLIF('$price',''), '$description', '$position', '$expire', NULL)";
    if ($conn->query($sql)) {
        header('Location: assets.php');
    } else {
        header('Location: add_asset.php?insert_error');
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
        plugins: 'powerpaste casechange searchreplace autolink directionality advcode visualblocks visualchars image link media mediaembed codesample table charmap pagebreak nonbreaking anchor tableofcontents insertdatetime advlist lists checklist wordcount tinymcespellchecker editimage help formatpainter permanentpen charmap linkchecker emoticons advtable export autosave',
        toolbar: 'undo redo print spellcheckdialog formatpainter | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | alignleft aligncenter alignright alignjustify lineheight | checklist bullist numlist indent outdent | removeformat',
        skin: "oxide-dark",
        content_css: "dark"
      });
    </script>
</head>

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
                            <form method="post" action="add_asset.php">
                                <!-- Form Row-->
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="small mb-1" for="inputName">Asset Name</label>
                                        <input required class="form-control" id="inputName" type="text" value="" name="name" placeholder="Enter an Asset Name">
                                    </div>

                                    <!-- Form Group (asset parent)-->
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="inputParent">Asset Parent</label>
                                        <select class="form-control" id="inputParent" name="asset_parent">
                                            <option value="">Select a Parent</option>

                                            <?php
                                            $results = $conn->query("SELECT id, name FROM asset");
                                            while ($row = $results->fetch_assoc()) {
                                                unset($id, $name);
                                                $id = $row['id'];
                                                $name = $row['name'];
                                                echo '<option value="' . $id . '">' . $name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="small mb-1" for="inputExpiration">Expiration Date</label>
                                        <input class="form-control" id="inputExpiration" type="date" value="" name="expiration">
                                    </div>
                                </div>
                                <div class="row gx-3 mb-3">
                                    
                                    <!-- TODO display only asset classes associated with each entity -->
                                    <!-- Form Group (asset class)-->
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="inputClass">Asset Class</label>
                                        <select class="form-control" id="inputClass" name="asset_class">
                                            <option value="">Select an Asset Class</option>

                                            <?php
                                            $results = $conn->query("SELECT id, name FROM asset_class");
                                            while ($row = $results->fetch_assoc()) {
                                                unset($id, $name);
                                                $id = $row['id'];
                                                $name = $row['name'];
                                                echo '<option value="' . $id . '">' . $name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Form Group (entity)-->
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="inputEntity">Entity</label>
                                        <select class="form-control" required id="inputEntity" name="entity" onchange="updateDepartments()">
                                            <option value="">Select an Entity</option>

                                            <?php
                                            $results = $conn->query("SELECT id, name FROM entity");
                                            while ($row = $results->fetch_assoc()) {
                                                unset($id, $name);
                                                $id = $row['id'];
                                                $name = $row['name'];
                                                echo '<option value="' . $id . '">' . $name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- TODO: Notify the user if there are no departments -->
                                    <!-- Asset department (position) -->
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="inputLocation">Location</label>
                                        <input required class="form-control" id="inputLocation" type="text" value="" name="location" placeholder="Enter an Asset Location">
                                    </div>

                                    <!-- Form Group (user)-->
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="inputUser">User</label>
                                        <select class="form-control" id="inputUser" name="asset_user">
                                            <option value="">Select a User</option>
                                            <?php
                                            $results = $conn->query("SELECT id, name FROM user");
                                            while ($row = $results->fetch_assoc()) {
                                                unset($id, $name);
                                                $id = $row['id'];
                                                $name = $row['name'];
                                                echo '<option value="' . $id . '">' . $name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="small mb-1" for="inputLocation">Location</label>
                                        <input class="form-control" id="inputLocation" type="text" value="" name="asset_location" placeholder="Enter a Location">
                                    </div>

                                    <!-- Asset price -->
                                    <div class="col-md-3">
                                        <label class="small mb-1" for="inputPrice">Price</label>
                                        <input type="number" class="form-control" name="price" id="inputPrice" step="0.01" placeholder="10.00">
                                    </div>


                                </div>
                                <!-- Form Row -->
                                <div class="row gx-3 mb-4">
                                    <div class="col-md-12">
                                        <label class="small mb-1" for="descriptionTextarea">Description</label>
                                        <textarea class="form-control" id="descriptionTextarea" name="description" rows="5" placeholder="Enter a description"></textarea>
                                    </div>
                                </div>

                                <!-- Save changes button-->
                                <button class="btn btn-success float-end mx-1" type="submit" name="submit_asset">Create New Asset</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </main>


    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>

    <script>
        function updateDepartments() {
            let entityId = $('#inputEntity').val();

            $.ajax({
                url: 'includes/scripts/ajax.php',
                method: 'POST',
                data: {
                    request: 'get_departments',
                    entity_id: entityId
                },
                dataType: 'json',
                success: function (departments) {
                    var departmentSelect = $('#inputDepartment');
                    departmentSelect.empty();

                    // Add the default "Select a Department" option
                    departmentSelect.append($('<option>', {
                        value: "",
                        text: "Select a Department"
                    }));

                    // Create a map to store parent departments and their subdepartments
                    var departmentMap = {};

                    // Separate parent and subdepartments
                    departments.forEach(function (department) {
                        if (department.parent === null) {
                            departmentMap[department.id] = {
                                name: department.name,
                                subdepartments: []
                            };
                        } else {
                            departmentMap[department.parent].subdepartments.push(department);
                        }
                    });

                    // Add parent departments and their subdepartments to the select element
                    for (var parentId in departmentMap) {
                        // Add parent department
                        departmentSelect.append($('<option>', {
                            value: parentId,
                            text: departmentMap[parentId].name
                        }));

                        // Add subdepartments with indentation
                        departmentMap[parentId].subdepartments.forEach(function (subdepartment) {
                            departmentSelect.append($('<option>', {
                                value: subdepartment.id,
                                text: "— " + subdepartment.name // Indentation using an em dash (—)
                            }));
                        });
                    }
                },
            });
        }
    </script>

</div>

</html>