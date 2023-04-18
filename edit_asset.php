<?php
if (isset($_GET['id'])) {
    $asset_id = $_GET['id'];
}
if (isset($_GET['name'])) {
    $asset_name = $_GET['name'];
}

$active = $edit_name;
include "includes/header.php";
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
                            <?php
                            $sql_asset = "SELECT * FROM asset WHERE id = '$asset_id' LIMIT 1";
                            $result = $conn->query($sql_asset);

                            if ($result) {
                                if (mysqli_num_rows($result) > 0) {
                                    $asset_data = mysqli_fetch_assoc($result);
                                    $date_create = gmdate("Y.m.d \ | H:i:s",$asset_data['date_created']);
                                    $asset_parent = $asset_dat['parent'];
                                    $asset_name = $asset_data['name'];
                                    $asset_user = $asset_data['user'];
                                    $asset_price = $asset_data['price'];
                                    $asset_description = $asset_data['description'];
                                    $asset_position = $asset_data['position'];
                                    $asset_expire = $asset_data['expire'];
                                    $asset_status = $asset_data['status'];

                                } else {//TODO: if no asset found
                                    $date_create = "N/A";
                                }
                            }
                            echo "Date Created: {$date_create}<br>";
                            ?>

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
                    <div id="tablePreloader">
                        <p class="text-white p-3">Loading...</p>
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