<?php
include "includes/db/connect.php";
session_start();
$session_info = $_SESSION;

$active = 'Create User';

$error = -1;
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

//always exist
$department_id = isset($_GET['departmentid']) ? $_GET['departmentid'] : -1;
$role_id_ = $session_info['user']['role'];

if (isset($_POST['submit_changes'])) {
    $name = $_POST['name'];
    $date_created = time();
    $role_id = $_POST['role'];
    $department_id = $_POST['department_id'];
    
    $entity_head = 0; 
    if($role_id == 1){ // superadmin
        $entity_id_ = 'null';
        $department_id_ = 'null';
    } else if ($role_id == 2){
        $entity_id_ = $_POST['entity'];
        $department_id_ = 'null';
        $entity_head = 1;
    } else{
        $entity_id_ = $_POST['entity'];
        $department_id_ = $_POST['department'];
    }

    $password = $_POST['password'];
    $reenter_password = $_POST['reenter_password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $password_confirmed = true;

    if (strcmp($password, $reenter_password) != 0) {
        $password_confirmed = false;
    }
    if ($password_confirmed) {
        $sql = "INSERT INTO user (date_created, name, password, entity, department, entity_super, role) 
        VALUES ('$date_created', '$name', '$hashed_password', $entity_id_,$department_id_, '$entity_head', '$role_id')";
    var_dump($sql);
        if ($conn->query($sql)) {
            if($department_id == -1){
                header('Location: users.php');
            } else {
                header('Location: users.php?departmentid='.$department_id);
            }
        } else {
            header('Location: new_user.php?departmentid='.$department_id.'&error=2');
        }
    } else {
        header('Location: new_user.php?departmentid='.$department_id.'&error=1');
    }
}
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
    <nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-black border-bottom border-dark" id="sidenavAccordion">
        <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle" onclick="document.body.classList.toggle('sidenav-toggled');localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sidenav-toggled'));
"><i data-feather="menu"></i></button>
        <!-- Navbar Brand-->
        <a class="navbar-brand pe-3 ps-4 ps-lg-2 text-primary" href="index.php">Singularity EAM</a>

        <!-- Navbar Items-->
        <ul class="navbar-nav align-items-center ms-auto">

            <!-- User Dropdown-->
            <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
                <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" role="button" data-bs-toggle="modal" data-bs-target="#logoutModal"><img class="img-fluid" src="assets/img/demo/user-placeholder.svg" /></a>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                    <h6 class="dropdown-header d-flex align-items-center">
                        <img class="dropdown-user-img" src="assets/img/illustrations/profiles/profile-2.png" />
                    </h6>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalTitle">Log out</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to log out?</div>
                <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button><a href="logout.php" class="btn btn-danger">Log out</a></div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

<div id="layoutSidenav_content">
    <main>
        <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
            <div class="container-fluid px-4">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="briefcase"></i></div>
                                User
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container-xl px-4 mt-4 py-3">
            <!-- Account page navigation-->
            <div class="row">
                <div class="col-xl-12">
                    <!-- Account details card-->
                    <div class="card mb-4">
                        <div class="card-header">Account Details</div>
                        <div class="card-body">
                            <?php
                                if ($error == 1) echo  '<div class="alert alert-danger" role="alert">Re-entered password does not match password.</div>'
                            ?>
                            <form method="post" action="new_user.php">
                                <input type="hidden" name="department_id" value="<?=$_GET['departmentid']?>">
                                <!-- Form Row-->
                                <div class="row gx-3 mb-3">
                                    <!-- Form Group (name)-->
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputName">Name</label>
                                        <input required class="form-control" id="inputName" type="text" value="" name="name" placeholder="Enter a Username">
                                    </div>
                                    <!-- Form Group (entity)-->
                                    <?php if($role_id_ == 1){ ?>
                                        <!-- SUPER ADMIN -->
                                        <?php if($department_id == -1) { // entity undetermined ?>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputEntity">Entity</label>
                                                <select class="form-control" required id="inputEntity" name="entity" onchange = "updatedepartments_sa()">
                                                    <option value="">Select a Entity</option>
                                                </select>
                                            </div>
                                        <?php } else {  // entity dertermined?>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputEntity">Entity</label>
                                                <select class="form-control" required id="inputEntity" name="entity">
                                                    <?php
                                                        $entity_id = mysqli_fetch_array($conn->query("SELECT entity FROM department WHERE id = '$department_id'"))['entity']; 
                                                        $entity_name = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];
                                                        echo '<option value="' . $entity_id. '">' . $entity_name . '</option>';
                                                    ?>
                                                </select>
                                            </div>
                                        <?php }?>
                                    <?php } else {?>
                                        <!-- ADMIN -->
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputEntity">Entity</label>
                                            <select class="form-control" required id="inputEntity" name="entity">
                                                <?php
                                                    $entity_id = $session_info['user']['entity']; 
                                                    $entity_name = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];
                                                    echo '<option value="' . $entity_id. '">' . $entity_name . '</option>';
                                                ?>
                                            </select>
                                        </div>
                                    <?php }?>
                                </div>
                                <!-- Form Row-->
                                <div class="row gx-3 mb-3">
                                    <!-- Form Group (department, role)--> 
                                        <?php if($department_id==-1){?>
                                            <!-- ACCESS THROUGH NAVBAR -->
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputDepartment">Department</label>
                                                <select class="form-control" id="inputDepartment" name="department" required>
                                                    <option value="">Select a Department</option>
                                                </select>
                                            </div>
                                        <?php } else {?>
                                            <!-- ACCESS THROUGH DEPARTMENT PAGE -->
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputDepartment">Department</label>
                                                <select class="form-control" id="inputDepartment" name="department">
                                                    <?php $department_name = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$department_id'"))['name'];?>
                                                        <option value=<?= $department_id?>> <?=$department_name ?></option>
                                                </select>
                                            </div>
                                        <?php }?>
                                    <!-- SELECT ROLE -->
                                    <?php if($department_id == -1){?>
                                        <!-- ACCESS THROUGH NAVBAR -->
                                        <?php if($role_id_ == 1) {?>
                                            <!-- SUPERADMIN -->
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputRole">Role</label>
                                                <select class="form-control" id="inputRole" name="role" onchange = "updateentities()">
                                                    <option value="4">user</option>
                                                    <option value="3">resource manager</option>  
                                                    <option value="2">admin</option>  
                                                    <option value="1" selected>superadmin</option>
                                                </select>
                                            </div>
                                            <?php }else{?>
                                            <!-- ADMIN -->
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputRole">Role</label>
                                                <select class="form-control" id="inputRole" name="role" onchange = "updatedepartments_admin()">
                                                    <option value="4">user</option>
                                                    <option value="3">resource manager</option>  
                                                    <option value="2">admin</option>
                                                </select>
                                            </div>
                                        <?php }?>
                                    <?php } else {?>
                                        <!-- ACCESS THROUGH DEPARTMENT PAGE -->
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputRole">Role</label>
                                            <select class="form-control" id="inputRole" name="role">
                                                <option value="4">user</option>
                                                <option value="3">resource manager</option>
                                            </select>
                                        </div>
                                    <?php }?>
                                </div>

                                <!-- Form Row -->
                                <div class="row gx-3 mb-4">
                                    <!-- Form Group -->
                                    <!-- password-->

                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputPassword">Password</label>
                                        <input class="form-control" id="inputPassword" type="password" value="" name="password" placeholder="Enter Password">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputReenterPassword">Re-enter Password</label>
                                        <input class="form-control" id="inputReenterPassword" type="password" value="" name="reenter_password" placeholder="Confirm Password">
                                    </div>
                                </div>

                                <!-- Save changes button-->
                                <button class="btn btn-success float-end mx-1" type="submit" name="submit_changes">Create new user</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>

<?php if($department_id==-1 && $role_id_ == 2){?>
<script>
        function updatedepartments_admin() {
            let role = $('#inputRole').val();
            let entityId = $('#inputEntity').val();
            console.log("role : "+role);
        
            if (role === "2") {
                $('#inputDepartment').empty();
                $('#inputDepartment').append($('<option>', {
                    value: null,
                    text: "--"
                }));
                return;
            }

            $.ajax({
                url: 'includes/scripts/ajax.php',
                method: 'POST',
                data: {
                    request: 'set_departments_admin',
                    role: role,
                    entity_id : entityId
                },
                dataType: 'json',
                success: function (departments) {
                    var departmentSelect = $('#inputDepartment');

                    departmentSelect.empty(); // Clear existing options

                    // Add default "Select a Department" option
                    departmentSelect.append($('<option>', {
                        value: '',
                        text: 'Select a Department'
                    }));

                    // Add departments to the select element
                    departments.forEach(function (department) {
                        departmentSelect.append($('<option>', {
                            value: department.id,
                            text: department.name + ' (' + department.parent+ ')'
                        }));
                    });
                }
            });
        }
    </script>
    <script>
        window.onload = (event) => {
            updatedepartments_admin();
        };
    </script>
<?php }?>

<?php if($department_id == -1 && $role_id_ == 1){?>
<script>
        function updateentities() {
            let role = $('#inputRole').val(); // can't be 1
            let entityId = $('#inputEntity').val();
            console.log("role : "+role);
            console.log("entityId : "+entityId);
        
            if (role === "1") {
                $('#inputDepartment').empty();
                $('#inputEntity').empty();

                $('#inputDepartment').append($('<option>', {
                    value: null,
                    text: "--"
                }));

                $('#inputEntity').append($('<option>', {
                    value: null,
                    text:"--"
                }));
                return;
            }
            if (role === "2") {
                $('#inputDepartment').empty();
                $('#inputDepartment').append($('<option>', {
                    value: null,
                    text: "--"
                }));
            }

            $.ajax({
                url: 'includes/scripts/ajax.php',
                method: 'POST',
                data: {
                    request: 'set_entities',
                    entity_id: entityId,
                    role: role
                },
                dataType: 'json',
                success: function (entities) {
                    var entitySelect = $('#inputEntity');

                    entitySelect.empty(); // Clear existing options

                    // Add default "Select a Department" option
                    entitySelect.append($('<option>', {
                        value: '',
                        text: 'Select a Department'
                    }));

                    // Add departments to the select element
                    entities.forEach(function (entity) {
                        entitySelect.append($('<option>', {
                            value: entity.id,
                            text: entity.name
                        }));
                    });
                }
            });
        }
        function updatedepartments_sa(){
            let role = $('#inputRole').val();
            let entityId = $('#inputEntity').val();
            console.log("role : "+role);
        
            if (role === "2") {
                $('#inputDepartment').empty();
                $('#inputDepartment').append($('<option>', {
                    value: null,
                    text: "--"
                }));
                return;
            }

            $.ajax({
                url: 'includes/scripts/ajax.php',
                method: 'POST',
                data: {
                    request: 'set_departments_admin',
                    role: role,
                    entity_id : entityId
                },
                dataType: 'json',
                success: function (departments) {
                    var departmentSelect = $('#inputDepartment');

                    departmentSelect.empty(); // Clear existing options

                    // Add default "Select a Department" option
                    departmentSelect.append($('<option>', {
                        value: '',
                        text: 'Select a Department'
                    }));

                    // Add departments to the select element
                    departments.forEach(function (department) {
                        departmentSelect.append($('<option>', {
                            value: department.id,
                            text: department.name + ' (' + department.parent+ ')'
                        }));
                    });
                }
            });
        }
    </script>
    <script>
        window.onload = (event) => {
            updateentities();
        };
    </script>

<?php }?>

</div>

</html>
