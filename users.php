<?php
// $sql = "SELECT * FROM user ORDER BY id DESC";
$active = "Users";

include "includes/header.php";
include "includes/navbar.php";
//TODO:
//set department id for cases when admin access users page by manage-user -> department id should be the id of department not the department id of admin

//TODO : change the column for rm, (entity -> possesing assets)


switch($role_id){
    case 1:
        // -1 -> access through navbar
        // GET -> access through manage-user
        $department_id = isset($_GET['departmentid']) ? $_GET['departmentid'] : -1;
        break;
    case 2:
        if(isset($_GET['departmentid'])) {
            $department_id = $_GET['departmentid'];
        }
        break;
    case 3:
        break;
    case 4:
        break;
    default:
        if(isset($_GET['departmentid'])) {
            $department_id = $_GET['departmentid'];
        }
        break;
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
                            <?php if($role_id <=2){ ?>
                            <a href="new_user.php?departmentid=<?= $department_id ?>" class="btn btn-primary btn-xs float-end">+ Add new user</a>
                            <?php }?>
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
                            <?php if($role_id <=2){ ?>
                                <th>Edit User</th>
                            <?php }?>
                        </tr>
                        </thead>
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
                searchDelay: 500,
                ajax: {
                    url: "includes/scripts/datatables_users.php",
                    data: function(d) {
                        d.roleid = <?= $role_id ?>;
                        d.entityid = <?= $entity_id ?>;
                        d.departmentid = <?= $department_id ?>;
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
                    }
                    <?php if($role_id <=2){ ?>
                    ,{
                        "data": "actions"
                    }
                    <?php }?>
                ],
                // select: {
                //     style: 'multi'
                // },
            });
        });
    </script>
    <?php
    include "includes/footer.php";
    ?>