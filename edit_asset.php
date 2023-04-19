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
        $asset_parent = $asset_data['parent'];
        $asset_name = $asset_data['name'];
        $asset_user = $asset_data['user'];
        $asset_price = $asset_data['price'];
        $asset_description = $asset_data['description'];
        $asset_position = $asset_data['position'];
        $asset_expire = $asset_data['expire'];
        $asset_status = $asset_data['status'];
        $asset_brand = $asset_data['brand'];
        $asset_model = $asset_data['model'];
        $asset_serial_number = $asset_data['serial number'];
        $asset_original_price = $asset_data['original price'];
        $asset_current_value = $asset_data['current value'];
        $asset_deprecation_model = $asset_data['deprecation model'];
}
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
                                                <th>user</th>
                                                <th>position</th>
                                                <th>expire</th>
                                                <th>status</th>
                                            </tr>
                                            <tr>
                                                <td><?php echo $asset_parent; ?></td>
                                                <td><?php echo $asset_user; ?></td>
                                                <td><?php echo $asset_position; ?></td>
                                                <td><?php echo $asset_expire; ?></td>
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
                            <textarea class="form-control" id="descriptionTextarea" name="description" rows="5" placeholder="Enter a description"></textarea>
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
                                                <td><?php echo $asset_current_value; ?></td>
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



<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <?php
    include "includes/footer.php";
    ?>