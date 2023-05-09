<?php
$sql = "SELECT * FROM entity ORDER BY id DESC";
$active = "Entities";

include "includes/header.php";
include "includes/navbar.php";
?>

<div id="layoutSidenav_content">
    <main>
        <header class="page-header page-header-compact page-header-light border-bottom bg-black mb-4">
            <div class="container-fluid px-4">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3 d-inline w-100">
                            <h1 class="page-header-title text-white d-inline">
                                <div class="page-header-icon text-white"><i data-feather="home"></i></div>
                                <?=$active?>
                            </h1>
                            <a href="/includes/add/new_entity.php" class="btn btn-primary btn-xs float-end">+ Add entity</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container-fluid px-4">
            <div class="card">
                <div class="card-body">
                    <div id="tablePreloader">
                        <p class="text-white p-3">Loading...</p>
                    </div>
                    <table id="datatablesSimple" style="display: none">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php


                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $entity_id = $row['id'];
                                $name = $row['name'];

                                echo "<tr data-id='$entity_id' ><td>$entity_id</td><td><a class='text-primary' href='entity.php?id=$entity_id'>" . $name . "</a></td>
                                        <td>" . "
                                        <a title=\"User Info\" class=\"btn btn-datatable btn-icon btn-transparent-light\" href='entity.php?id=$entity_id'>
                                        <i data-feather=\"edit\"></i>
                                        </a>
                  
                                        
                                        " ."</td></tr>";
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
