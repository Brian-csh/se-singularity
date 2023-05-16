<?php
if (isset($_GET['id'])) {
    $asset_id = $_GET['id'];
}

$active = "Asset";
include "includes/header.php";

// Fetch asset name
$asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];

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
                            <?php
                            $sql_asset = "SELECT * FROM asset WHERE id = '$asset_id' LIMIT 1";
                            $result = $conn->query($sql_asset);

                            if ($result&&mysqli_num_rows($result) > 0) {
                                    $asset_data = mysqli_fetch_assoc($result);
                                    $date_create = gmdate("Y.m.d \ | H:i:s",$asset_data['date_created']+28000);
                            }
                            echo "Date Created: {$date_create}<br>";

                            // Fetch logs
                            $sql_log = "SELECT * FROM log WHERE (subject = '$asset_id') ORDER BY date DESC";
                            $result = $conn->query($sql_log);

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
