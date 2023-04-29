<?php
// $sql = "SELECT * FROM user ORDER BY id DESC";
$active = "Users";

include "includes/header.php";
include "includes/navbar.php";

if (isset($_GET['departmentid'])) {
    $departmentid = $_GET['departmentid'];
} else {
    $departmentid = -1;
}
?>

<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">

<!-- DataTables Select CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.4/css/select.dataTables.min.css" />

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" />


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
                            <a href="new_user.php" class="btn btn-primary btn-xs float-end">+ Add</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container-fluid px-4">
            <div class="card">
                <div class="card-body">
                    <table id="usersTable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date registered</th>
                            <th>Name</th>
                            <th>Entity</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php


                        // $result = $conn->query($sql);

                        // if ($result->num_rows > 0) {
                        //     while ($row = $result->fetch_assoc()) {
                        //         $date = gmdate("Y.m.d \ | H:i:s", $row['date_created']);

                        //         $user_id = $row['id'];
                        //         $name = $row['name'];
                        //         $entity_super = $row['entity_super'];

                        //         // Fetch entity name
                        //         $entity_id = $row['entity'];
                        //         $entity_name = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];

                        //         // Fetch department name
                        //         $department_id = $row['department'];
                        //         $department_name = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$department_id'"))['name'];

                        //         // Fetch role name
                        //         $role_id = $row['role'];
                        //         $role_name = mysqli_fetch_array($conn->query("SELECT role FROM role WHERE id = '$role_id'"))['role'];



                        //         if ($entity_super == '1') {
                        //             $entity = '<span class="badge bg-warning text-white">' . $entity_name . '</span>';
                        //         } else {
                        //             $entity = '<span class="badge bg-primary text-white">' . $entity_name . '</span>';
                        //         }
                        //         $role = '<span class="">' . $role_name .'</span>';



                        //         echo "<tr data-id='$user_id' ><td>$user_id</td><td>$date</td><td><a class='text-primary' href='edit_user.php?id=$user_id'>" . $name . "</a></td><td>$entity</td>
                        //     <td>$department_name</td><td>$role</td><td>" . "
                        //                 <a title=\"User Info\" class=\"btn btn-datatable btn-icon btn-transparent-light\" href=\"edit_user.php?id=".$row['id']."\">
                        //                 <i data-feather=\"edit\"></i>
                        //                 </a>
                  
                                        
                        //                 " ."</td></tr>";
                        //     }
                        // }


                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>




    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>

    <!-- DataTables Select JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>

    <!-- DataTables Buttons JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                ordering: false,
                searching: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "includes/scripts/datatables_users.php",
                    data: function(d) {
                        d.departmentid = <?= $departmentid ?>;
                    }
                },
                columns: [{
                        "data": "id"
                    },
                    {
                        "data": "date_registered"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "entity"
                    },
                    {
                        "data": "department"
                    },
                    {
                        "data": "role"
                    },
                    {
                        "data": "actions"
                    }
                ],
                select: {
                    style: 'multi'
                },
            });
        });
    </script>
    <?php
    include "includes/footer.php";
    ?>