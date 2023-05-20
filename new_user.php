<?php
include "includes/db/connect.php";
session_start();
$session_info = $_SESSION;

$active = 'Create User';
$errors = "";

//TODO :
// superadmin : can select entity
// admin1 : entity is already set as the entity the admin belongs to 
// admin2 : department is also set if admin access here thourugh department page
if (isset($_POST['submit_changes'])) {

    $name = $_POST['name'];
    $date_created = time();
    $role_id = $_POST['role'];
    $entity_id = $_POST['entity'];
    $department_id = $_POST['department'];
    if (isset($_POST['entity_head'])) { //is entity super
        $entity_head = $_POST['entity_head'];
    } else { //not entity super
        $entity_head = 0;
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
        VALUES ('$date_created', '$name', '$hashed_password', '$entity_id', '$department_id', '$entity_head', '$role_id')";
        if ($conn->query($sql)) {
            header('Location: users.php');
        } else {
            header('Location: new_user.php?insert_error');
        }
    } else {
        $errors .= "Re-entered password does not match with password. ";
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
                                if ($errors != "") echo  '<div class="alert alert-danger" role="alert">
                                ' . $errors . '</div>'
                            ?>
                            <form method="post" action="new_user.php">
                                <!-- Form Row-->
                                <div class="row gx-3 mb-3">
                                    <!-- Form Group (name)-->
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputName">Name</label>
                                        <input required class="form-control" id="inputName" type="text" value="" name="name" placeholder="Enter a Username">
                                    </div>
                                    <!-- Form Group (entity)-->
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputEntity">Entity</label>
                                        <select class="form-control" required id="inputEntity" name="entity" onchange="updateDepartments()">
                                            <option value="">Select an Entity</option>

                                            <?php
                                            $results = $conn->query("SELECT id, name FROM entity");
                                            while ($row = $results->fetch_assoc()) {
                                                unset($id, $name);
                                                $id = $row['id'];
                                                $name = $row['name'];
                                                echo '<option value="' . $id . '">' . $name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                                <!-- Form Row        -->
                                <div class="row gx-3 mb-3">
                                    <!-- Form Group (department, role)--> 
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputDepartment">Department</label>
                                        <select class="form-control" id="inputDepartment" name="department" required>
                                            <option value="">Select an Entity</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputRole">Role</label>
                                        <select class="form-control" id="inputRole" name="role">
                                            <option value="1" selected>superadmin</option>
                                            <option value="2">admin</option>
                                            <option value="3">resource manager</option>
                                            <option value="4">user</option>
                                        </select>

                                    </div>
                                </div>
                                <!-- Form Row -->
                                <div class="row gx-3 mb-4">
                                    <!-- Form Group -->
                                    <!-- entity super, checkbox-->

                                    <div class="col-md-4">
                                        <label class="small mb-1" for="inputEntityHead">Entity Head</label>
                                        <input id="inputEntityHead" type="checkbox" name="entity_head" value="1">
                                    </div>
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

    <script>
        function updateDepartments() {
            let entityId = $('#inputEntity').val();

            if (entityId === "") {
                $('#inputDepartment').empty();
                $('#inputDepartment').append($('<option>', {
                    value: "",
                    text: "Select an Entity"
                }));
                return;
            }

            $.ajax({
                url: 'includes/scripts/ajax.php',
                method: 'POST',
                data: {
                    request: 'get_departments',
                    entity_id: entityId
                },
                dataType: 'json',
                success: function populateDepartments(departments) {
                    var departmentSelect = $('#inputDepartment');
                    departmentSelect.empty();

                    // Add the default "Select a Department" option
                    departmentSelect.append($('<option>', {
                        value: "",
                        text: "Select a Department"
                    }));

                    // Create a map to store parent departments and their subdepartments
                    var departmentMap = {};

                    // Separate parent and subdepartments
                    departments.forEach(function (department) {
                        departmentMap[department.id] = {
                            name: department.name,
                            subdepartments: []
                        };
                        if (department.parent !== null) {
                            if (!departmentMap[department.parent]) {
                                departmentMap[department.parent] = {
                                    subdepartments: []
                                };
                            }
                            departmentMap[department.parent].subdepartments.push(department);
                        }
                    });

                    // Recursive function to add departments and their subdepartments
                    function addDepartmentsToSelect(departmentId, prefix) {
                        var department = departmentMap[departmentId];
                        // Add department
                        departmentSelect.append($('<option>', {
                            value: departmentId,
                            text: prefix + department.name
                        }));

                        // Add subdepartments with additional indentation
                        department.subdepartments.forEach(function (subdepartment) {
                            addDepartmentsToSelect(subdepartment.id, prefix + "— "); // Indentation using an em dash (—)
                        });
                    }

                    // Add top-level departments (those with no parent)
                    departments.filter(function (department) {
                        return department.parent === null;
                    }).forEach(function (topLevelDepartment) {
                        addDepartmentsToSelect(topLevelDepartment.id, "");
                    });
                },
            });

        }
    </script>

</div>

</html>