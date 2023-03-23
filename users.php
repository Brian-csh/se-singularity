<?php
$sql = "SELECT * FROM user ORDER BY id DESC";
$active = "Users";

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
                                <div class="page-header-icon text-white"><i data-feather="user"></i></div>
                                <?=$active?>
                            </h1>
                            <a href="new_user.php" class="btn btn-primary btn-xs float-end">+ Opret ny bruger</a>
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
                            <th>Date registered</th>
                            <th>Name</th>
                            <th>Entity</th>
                            <th>Department</th>
                            <th>Role</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Date registered</th>
                            <th>Name</th>
                            <th>Entity</th>
                            <th>Department</th>
                            <th>Role</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php


                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $date = gmdate("Y.m.d \ | H:i:s", $row['date_created']);

                                $user_id = $row['id'];
                                $name = $row['name'];
                                $entity_super = $row['entity_super'];

                                // Fetch entity name
                                $entity_id = $row['entity'];
                                $entity_name = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];

                                // Fetch department name
                                $department_id = $row['department'];
                                $department_name = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$department_id'"))['name'];

                                // Fetch role name
                                $role_id = $row['role'];
                                $role_name = mysqli_fetch_array($conn->query("SELECT name FROM role WHERE id = '$role_id'"))['name'];



                                if ($entity_super == '1') {
                                    $entity = '<span class="badge bg-primary text-white">' . $entity_name . '</span>';
                                } else {
                                    $entity = '<span class="badge bg-gold text-white">' . $entity_name . '</span>';
                                }
                                $role = '<span class="">' . $role_name .'</span>';



                                echo "<tr data-id='$user_id' ><td>$user_id</td><td>$date</td><td><a class='text-primary' href='user.php?id=$user_id'>" . $name . "</a></td><td>$entity</td>
                            <td>$department_name</td><td>$role</td><td>" . "
                                        <a title=\"User Info\" class=\"btn btn-datatable btn-icon btn-transparent-light\" href=\"user.php?id=".$row['id']."\">
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
