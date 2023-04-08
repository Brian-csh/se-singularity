<?php
if (isset($_GET['id'])) {
    $entity_id = $_GET['id'];
}
if (isset($_GET['name'])) {
    $entity_name = $_GET['name'];
}

$active = $entity_name;
include "includes/header.php";
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
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container-fluid px-4">
            <div class="card">
                <div class="class-header" style='padding:25px'>
                    <?php 
                        echo "<h1>$entity_name</h1>";

                    ?> 
                </div>
                <div class="card-body">
                    <div id="tablePreloader">
                        <p class="text-white p-3">Loading...</p>
                    </div>
                    <table id="datatablesSimple" style="display: none">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Parent</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Parent</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php

                        // select all the departments for the entity
                        $sql_department = "SELECT * FROM department WHERE id = '$entity_id'";
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
