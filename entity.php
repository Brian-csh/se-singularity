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
        <header class="page-header pt-10 page-header-dark bg-gradient-primary-to-secondary pb-5">
            <div class="container-xl px-4">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="home"></i></div>
                                <?php echo $entity_name ?>
                            </h1>
                            <div class="page-header-subtitle">
                                <?php
                                $sql_entity = "SELECT * FROM entity WHERE id = '$entity_id' LIMIT 1";
                                $result = $conn->query($sql_entity);

                                if ($result) {
                                    if (mysqli_num_rows($result) > 0) {
                                        $entity_data = mysqli_fetch_assoc($result);
                                        $date_created = $entity_data['date_created'];
                                    } else {
                                        $date_created = "N/A";
                                    }
                                }

                                echo "Date Created: $date_created<br>";

                                $sql_user = "SELECT * FROM user WHERE (id = '$entity_id' AND entity_super = 1) LIMIT 1";
                                $result = $conn->query($sql_user);


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
        <header class="page-header page-header-compact page-header-light border-bottom bg-black mb-4">
            <div class="container-fluid px-4">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3 d-inline w-100">
                            <h1 class="page-header-title text-white d-inline">
                                <div class="page-header-icon text-white"><i data-feather="box"></i></div>
                                Departments
                            </h1>
                            <a <?php echo "href=\"new_department.php?entity_id=$entity_id\""?> class="btn btn-primary btn-xs float-end">+ Add</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
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
                            $sql_department = "SELECT * FROM department WHERE entity = '$entity_id' ORDER BY id DESC";
                            $result = $conn->query($sql_department);

                            if ($result) {
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $department_id = $row['id'];
                                        $department_name = $row['name'];
                                        $department_parent = $row['parent'];

                                        $sql_parent_name = "SELECT name FROM department WHERE id = '$department_parent' LIMIT 1";
                                        $parent_name_result = $conn->query($sql_parent_name);
                                        $parent_assoc = $parent_name_result->fetch_assoc();
                                        $parent_name = (isset($parent_assoc['name'])) ? $parent_assoc['name'] : "N/A";

                                        echo "<tr data-id='$department_id' ><td>$department_id</td><td>$department_name</td><td>$parent_name</td></tr>";
                                    }
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