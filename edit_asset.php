<?php
if (isset($_GET['id'])) {
    $asset_id = $_GET['id'];
}
if (isset($_GET['name'])) {
    $asset_name = $_GET['name'];
}

$active = $edit_name;
include "includes/header.php";

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
        $asset_expire = gmdate("Y.m.d \ | H:i:s",$asset_data['expire']+28000);
        $asset_status_id = $asset_data['status'];
        $asset_brand = $asset_data['brand'];
        $asset_model = $asset_data['model'];
        $asset_serial_number = $asset_data['serial number'];
        $asset_original_price = $asset_data['price'];
        $asset_current_price = $asset_data['current price'];
        $asset_deprecation_model = $asset_data['deprecation model'];
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

?>

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
                    <!-- TODO: SHOW Image,RTF description input box,Basic info, Financial Info -->
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
                                                <th>position</th>
                                                <th>expire</th>
                                                <th>status</th>
                                            </tr>
                                            <tr>
                                                <td><?php echo $asset_parent; ?></td>
                                                <td><?php
                                                        if($asset_class_type == 0)
                                                            echo "Item Asset";
                                                        else 
                                                            echo "Amount Asset"; 
                                                    ?>
                                                </td>
                                                <td><?php echo $asset_class_name; ?></td>
                                                <td><?php echo $asset_user_name; ?></td>
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
                    <div class= "row mb-3 gx-3">
                        <div class="col-md-12">
                            <label class="small mb-1" for="descriptionTextarea">Description</label>
                            <button type="submit" name="description_change" class="btn btn-primary text-light float-end" style="background:green; border:none">Change description</button>
                            <textarea class="form-control" id="descriptionTextarea" name="description" rows="20" placeholder="<?php echo $asset_description; ?>"></textarea>
                        </div>
                    </div>

                    <div class = "row mb-3">

                        <!-- TODO: Asset Basic Info Table -->
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

                        <!-- TODO: Asset Financial Info Table -->
                        <div class = "col mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3> Financial Info
                                    <button type="button" class="btn btn-sm btn-outline-primary float-end" data-bs-toggle="modal" data-bs-target="#editFiancialInfoModal"><i data-feather="edit"></i></button>
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
                                                <th>Deprecation Model</th>
                                                <td><?php echo $asset_deprecation_model; ?></td>
                                            </tr>
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
        <div class="modal-dialog" role="document" style = "max-width: 800px; max-heigth:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Asset</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="assets.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Edit asset name -->
                        <div class="mb-3">
                            <label for="classAddName">Asset Name *</label>
                            <!-- TODO: submit placeholder when no input-->
                            <input class="form-control" id="classAddName" type="text" name="class_name" placeholder="<?php echo $asset_name; ?>" required>
                        </div>
                        <!-- Edit position -->
                        <div class="mb-3">
                            <label for="classAddName">Position</label>
                            <!-- TODO: submit placeholder when no input-->
                            <input class="form-control" id="classAddName" type="text" name="class_name" placeholder="<?php echo $asset_position; ?>" required>
                        </div>

                        <!-- Edit CLASS -->
                        <div class = "row">
                            <div class="col">
                                <!-- Edit class type -->
                                <div class="mb-3">
                                        <label for="classAddType">Class Type * &nbsp</label>
                                        <input type="radio" id="classItemType" name="class_type" value="ItemAsset" checked>
                                        <label for="classItemType">Item Asset</label>
                                        <input type="radio" id="classValueType" name="class_type" value="ValueAsset">
                                        <label for="classValueType">Amount Asset</label>
                                </div>
                            </div>
                            
                            <div class="col">
                                <!-- Edit customized class -->
                                <div class="mb-3">
                                    <label for="classAddName">Class<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_customized">
                                            <option value=""><?php echo $asset_class_name?></option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM asset_class");
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name']&& $row['name']!=$asset_class_name) {
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
                        </div>

                        <div class = "row">
                            <div class="col">
                                <!-- Edit Parent -->
                                <div class="mb-3">
                                    <label for="classAddName">Parent<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_parent">
                                            <option value=""><?php echo $asset_parent;?></option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM asset");
                                                    $id = 0; $parent = NULL;
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name']&& $row['name']!= $asset_parent) {
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

                            <div class="col">
                                <!-- Edit user -->
                                <div class="mb-3">
                                    <label for="classAddName">Users<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_user">
                                            <option value=""><?php echo $asset_user_name; ?></option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM user");
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name'] && $row['name']!= $asset_user_name) {
                                                            unset($id, $user);
                                                            $id = $row['id'];
                                                            $user = $row['name'];
                                                            echo '<option value="' . $id . '">' . $user . '</option>';
                                                        }
                                                    }
                                                    ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col">
                                <!-- Edit Status -->
                                <div class="mb-3">
                                    <label for="classAddName">Status<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_parent">
                                            <option value=""><?php echo $asset_status;?></option>
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
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="add_class">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Asset Modal -->
    <div class="modal fade" id="editAssetModal" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style = "max-width: 800px; max-heigth:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Asset</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="assets.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Edit asset name -->
                        <div class="mb-3">
                            <label for="classAddName">Asset Name *</label>
                            <!-- TODO: submit placeholder when no input-->
                            <input class="form-control" id="classAddName" type="text" name="class_name" placeholder="<?php echo $asset_name; ?>" required>
                        </div>
                        <!-- Edit position -->
                        <div class="mb-3">
                            <label for="classAddName">Position</label>
                            <!-- TODO: submit placeholder when no input-->
                            <input class="form-control" id="classAddName" type="text" name="class_name" placeholder="<?php echo $asset_position; ?>" required>
                        </div>

                        <!-- Edit CLASS -->
                        <div class = "row">
                            <div class="col">
                                <!-- Edit class type -->
                                <div class="mb-3">
                                        <label for="classAddType">Class Type * &nbsp</label>
                                        <input type="radio" id="classItemType" name="class_type" value="ItemAsset" checked>
                                        <label for="classItemType">Item Asset</label>
                                        <input type="radio" id="classValueType" name="class_type" value="ValueAsset">
                                        <label for="classValueType">Amount Asset</label>
                                </div>
                            </div>
                            
                            <div class="col">
                                <!-- Edit customized class -->
                                <div class="mb-3">
                                    <label for="classAddName">Class<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_customized">
                                            <option value=""><?php echo $asset_class_name?></option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM asset_class");
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name']&& $row['name']!=$asset_class_name) {
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
                        </div>

                        <div class = "row">
                            <div class="col">
                                <!-- Edit Parent -->
                                <div class="mb-3">
                                    <label for="classAddName">Parent<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_parent">
                                            <option value=""><?php echo $asset_parent;?></option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM asset");
                                                    $id = 0; $parent = NULL;
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name']&& $row['name']!= $asset_parent) {
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

                            <div class="col">
                                <!-- Edit user -->
                                <div class="mb-3">
                                    <label for="classAddName">Users<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_user">
                                            <option value=""><?php echo $asset_user_name; ?></option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM user");
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name'] && $row['name']!= $asset_user_name) {
                                                            unset($id, $user);
                                                            $id = $row['id'];
                                                            $user = $row['name'];
                                                            echo '<option value="' . $id . '">' . $user . '</option>';
                                                        }
                                                    }
                                                    ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col">
                                <!-- Edit Status -->
                                <div class="mb-3">
                                    <label for="classAddName">Status<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_parent">
                                            <option value=""><?php echo $asset_status;?></option>
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
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="add_class">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Basic info Modal -->
    <div class="modal fade" id="editAssetModal" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style = "max-width: 800px; max-heigth:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Asset</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="assets.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Edit asset name -->
                        <div class="mb-3">
                            <label for="classAddName">Asset Name *</label>
                            <!-- TODO: submit placeholder when no input-->
                            <input class="form-control" id="classAddName" type="text" name="class_name" placeholder="<?php echo $asset_name; ?>" required>
                        </div>
                        <!-- Edit position -->
                        <div class="mb-3">
                            <label for="classAddName">Position</label>
                            <!-- TODO: submit placeholder when no input-->
                            <input class="form-control" id="classAddName" type="text" name="class_name" placeholder="<?php echo $asset_position; ?>" required>
                        </div>

                        <!-- Edit CLASS -->
                        <div class = "row">
                            <div class="col">
                                <!-- Edit class type -->
                                <div class="mb-3">
                                        <label for="classAddType">Class Type * &nbsp</label>
                                        <input type="radio" id="classItemType" name="class_type" value="ItemAsset" checked>
                                        <label for="classItemType">Item Asset</label>
                                        <input type="radio" id="classValueType" name="class_type" value="ValueAsset">
                                        <label for="classValueType">Amount Asset</label>
                                </div>
                            </div>
                            
                            <div class="col">
                                <!-- Edit customized class -->
                                <div class="mb-3">
                                    <label for="classAddName">Class<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_customized">
                                            <option value=""><?php echo $asset_class_name?></option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM asset_class");
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name']&& $row['name']!=$asset_class_name) {
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
                        </div>

                        <div class = "row">
                            <div class="col">
                                <!-- Edit Parent -->
                                <div class="mb-3">
                                    <label for="classAddName">Parent<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_parent">
                                            <option value=""><?php echo $asset_parent;?></option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM asset");
                                                    $id = 0; $parent = NULL;
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name']&& $row['name']!= $asset_parent) {
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

                            <div class="col">
                                <!-- Edit user -->
                                <div class="mb-3">
                                    <label for="classAddName">Users<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_user">
                                            <option value=""><?php echo $asset_user_name; ?></option>
                                                <?php
                                                    $results = $conn->query("SELECT id, name FROM user");
                                                    while ($row = $results->fetch_assoc()) {
                                                        if ($row['name'] && $row['name']!= $asset_user_name) {
                                                            unset($id, $user);
                                                            $id = $row['id'];
                                                            $user = $row['name'];
                                                            echo '<option value="' . $id . '">' . $user . '</option>';
                                                        }
                                                    }
                                                    ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col">
                                <!-- Edit Status -->
                                <div class="mb-3">
                                    <label for="classAddName">Status<label>
                                        <select class="form-control ms-2" id="inputParentClass" name="class_parent">
                                            <option value=""><?php echo $asset_status;?></option>
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
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="add_class">Submit</button>
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
    <?php
    include "includes/footer.php";
    ?>