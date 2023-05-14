<?php
if (isset($_GET['id'])) {
    $asset_id = $_GET['id'];
}

// $active = $edit_name;
include "includes/header.php";
include "includes/scripts/functions.php";

$sql_asset = "SELECT * FROM asset WHERE id = '$asset_id' LIMIT 1";
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
        $asset_position = $asset_data['position'];
        $asset_expire = date("Y-m-d", strtotime($asset_data['expire']));
        $asset_status_id = $asset_data['status'];
        $asset_brand = $asset_data['brand'];
        $asset_model = $asset_data['model'];
        $asset_serial_number = $asset_data['serial number'];
        $asset_original_price = $asset_data['price'];
        $asset_current_price = $asset_data['current price'];
        $asset_depreciation_model = $asset_data['depreciation model'];
        $asset_department_id = $asset_data['department'];
        $custom_attributes = $asset_data['custom_attr'];
}

// Fetch Data

// Fetch parent name
$asset_parent = mysqli_fetch_array($conn->query("SELECT * FROM asset WHERE id = '$asset_parent_id' LIMIT 1"))['name'];

// Fetch class name
$asset_class_name = mysqli_fetch_array($conn->query("SELECT * FROM asset_class WHERE id = '$asset_class_id' LIMIT 1"))['name'];
$asset_class_type = mysqli_fetch_array($conn->query("SELECT * FROM asset_class WHERE id = '$asset_class_id' LIMIT 1"))['class_type'];

// Fetch user name
$asset_user_name = mysqli_fetch_array($conn->query("SELECT * FROM user WHERE id = '$asset_user_id' LIMIT 1"))['name'];

// Fetch Status
$asset_status = mysqli_fetch_array($conn->query("SELECT * FROM asset_status_class WHERE id = '$asset_status_id' LIMIT 1"))['status'];

// Fetch deaprtment
$asset_department = mysqli_fetch_array($conn->query("SELECT * FROM department WHERE id = '$asset_department_id' LIMIT 1"))['name'];

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
                    <!-- TODO: SHOW Image-->
                    <div class = "row mb-3">
                        <div class = "col -md-6">
                            <!-- TODO: Asset Image -->
                            IMAGE UPLOADER
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
                                                <td><?php echo $asset_department; ?><td>
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
                    <form method = "POST" action="edit_asset.php?id=<?php echo $asset_id ?>&name=<?php echo $asset_name ?>" >
                        <div class= "row mb-3 gx-3">
                            <div class="col-md-12">
                                <label class="small mb-1" for="descriptionTextarea">Description</label>
                                <textarea class="form-control" id="descriptionTextarea" name="description" rows="20"></textarea>
                            </div>
                        </div>
                    </form>

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
                                            <tr>
                                                <th>Depreciation Model</th>
                                                <td><?php echo $asset_depreciation_model; ?></td>
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
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class = "col">
                                        <table class="table table-hover">
                                            <tbody>
                                                <?php 
                                                    $custom_attribute_obj = json_decode($custom_attributes);
                                                    foreach ($custom_attribute_obj as $custom_key => $custom_value) {
                                                        echo '
                                                            <tr>
                                                                <th>'. $custom_key .'</th>
                                                                <td>'. $custom_value .'</td>
                                                            </tr>
                                                        ';
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
                            <input class="form-control" id="editAssetName" type="text" name="editAssetName" placeholder="<?php echo $asset_name; ?>">
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
                                                        $id = 0; $parent = NULL;
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
                                                        if ($row['name']!= $asset_parent&&$row['name']!= $asset_name) {
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

                            <!-- Edit user -->
                            <!-- <div class="col">
                                <div class="mb-3">
                                    <label for="editAssetUser">Users</label>
                                        <select class="form-control ms-2" id="editUser" name="editAssetUser">
                                            <?php echo '<option value="' . $asset_user_id . '">' . $asset_user_name . '</option>'; ?>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM user");
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name']!= $asset_user_name) {
                                                            unset($id, $user);
                                                            $id = $row['id'];
                                                            $user = $row['name'];
                                                            echo '<option value="' . $id . '">' . $user . '</option>';
                                                        }
                                                    }
                                                    ?>
                                        </select>
                                </div>
                            </div> -->
                            <!-- Edit Status -->
                            <!-- <div class="col">
                                <div class="mb-3">
                                    <label for="editAssetStatus">Status</label>
                                        <select class="form-control ms-2" id="editStatus" name="editAssetStatus">
                                            <?php echo '<option value="' . $asset_status_id . '">' . $asset_status . '</option>';?>
                                                <?php
                                                    $results = $conn->query("SELECT id, status FROM asset_status_class");
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['status']&& $row['status']!= $asset_status) {
                                                            unset($id, $status);
                                                            $id = $row['id'];
                                                            $status = $row['status'];
                                                            echo '<option value="' . $id . '">' . $status . '</option>';
                                                        }
                                                    }
                                                    ?>
                                        </select>
                                </div>
                            </div> -->

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

<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <script>
        $(document).ready(function() {
            tinymce.init({
            selector: '#descriptionTextarea',
            plugins: 'powerpaste casechange searchreplace autolink directionality advcode visualblocks visualchars image link media mediaembed codesample table charmap pagebreak nonbreaking anchor tableofcontents insertdatetime advlist lists checklist wordcount tinymcespellchecker editimage help formatpainter permanentpen charmap linkchecker emoticons advtable export autosave',
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
