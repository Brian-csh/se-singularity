<?php
if (isset($_GET['id'])) {
    $asset_id = $_GET['id'];
}
if (isset($_GET['name'])) {
    $asset_name = $_GET['name'];
}

$active = $asset_name;
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
                                    $date_create = new DateTime();
                                    $date_create->setTimestamp($asset_data['date_created']);
                                    $asset_name = $asset_data['name'];
                                } else {
                                    $date_create = "N/A";
                                }
                            }
                            $date_create->setTimezone(new DateTimeZone('Asia/Shanghai'));
                            echo "Date Created: {$date_create->format('Y-m-d H:i:s')}<br>";
                            
                            //fetch logs
                            $sql_log = "SELECT * FROM log WHERE (subject = '$asset_name') ORDER BY date_created DESC";
                            $result = $conn->query($sql_log);

                            if ($result) {
                                if (mysqli_num_rows($result) > 0) {
                                    $user_data = mysqli_fetch_assoc($result);
                                    echo "Entity Head: $user_data <br>";
                                }
                            }
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
                    <table id="datatablesSimple" style="display: none">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Parent</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>User</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Position</th>
                                <th>Expiration Date</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Parent</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>User</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Position</th>
                                <th>Expiration Date</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            // select all the logs for the asset
                            $sql_department = "SELECT * FROM department WHERE id = '$asset_id'";
                            $result = $conn->query($sql_department);

                            if ($result) {
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $department_id = $row['id'];
                                        $department_name = $row['name'];
                                        $department_parent = $row['parent'];

                                        echo "<tr data-id='$department_id' ><td>$department_id</td><td>$department_name</td><td>$department_parent</td></tr>";
                                    }
                                } else {
                                    header('Location: entity.php');
                                }
                            }
                            ?>
                        </tbody>
                    </table>
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