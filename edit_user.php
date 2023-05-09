<?php
include "includes/db/connect.php";

//return the name of the entity corresponding to @param int $id
function getEntityName($id, $conn)
{
    $sql_entity = "SELECT name FROM entity WHERE id = '$id'";
    return mysqli_fetch_array($conn->query($sql_entity))['name'];
}

//return the name of the department corresponding to @param int $id
function getDepartmentName($id, $conn)
{
    $sql_department = "SELECT name FROM department WHERE id = '$id'";
    return mysqli_fetch_array($conn->query($sql_department))['name'];
}

session_start();
$session_info = $_SESSION['user'];

$active = 'Edit User';

//set up inital value of the form
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "SELECT * from user WHERE id = '$user_id' limit 1";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $current_user_data = mysqli_fetch_assoc($result);
            $last_modified = date('Y-m-d H:i:s', $current_user_data['date_created']); //convert format
            $name = $current_user_data['name'];
            $entity = getEntityName($current_user_data['entity'], $conn);
            $department = getDepartmentName($current_user_data['department'], $conn);
            $entity_super = $current_user_data['entity_super'];
            $current_role = $current_user_data['role'];
            $locked = $current_user_data['locked'];
        }
    }
} else {
    header('Location: users.php');
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
                            if (isset($_GET["insert_error"])) echo  '<div class="alert alert-danger" role="alert">Failed to update. Re-entered password does not match password.</div>'
                            ?>
                            <?php echo  "<p style=\"color: gray;\">date joined: " . $last_modified . "</p>" ?>
                            <form method="post" action="update_user.php">
                                <!-- Form Row-->
                                <div class="row gx-3 mb-3">
                                    <!-- Form Group (name)-->
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputName">Name</label>
                                        <input disabled class="form-control" required id="inputName" type="text" value="<?php echo $name ?>" name="name">
                                    </div>
                                    <!-- Form Group (entity)-->
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputEntity">Entity</label>
                                        <input disabled class="form-control" required id="inputEntity" type="text" value="<?php echo $entity ?>" name="entity">
                                    </div>

                                </div>
                                <!-- Form Row        -->
                                <div class="row gx-3 mb-3">
                                    <!-- Form Group (department, role)-->
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputDepartment">Department</label>
                                        <input disabled class="form-control" id="inputDepartment" type="text" value="<?php echo $department ?>" name="department">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputRole">Role</label>
                                        <select class="form-control" id="inputRole" name="role" value=<?php echo $current_role ?>>
                                            <option value="1" <?php echo ($current_role == 1) ? "selected" : "null" ?>>superadmin</option>
                                            <option value="2" <?php echo ($current_role == 2) ? "selected" : "null" ?>>admin</option>
                                            <option value="3" <?php echo ($current_role == 3) ? "selected" : "null" ?>>resource manager</option>
                                            <option value="4" <?php echo ($current_role == 4) ? "selected" : "null" ?>>user</option>
                                        </select>

                                    </div>
                                </div>
                                <!-- Form Row -->
                                <?php
                                if ($user_id != $session_info['id'])
                                    echo "<div class=\"row gx-3 mb-4\">
                                        <!-- Form Group -->
                                        <!-- entity super, checkbox-->

                                        <div class=\"col-md-4\">
                                            <label class=\"small mb-1\" for=\"inputLockAccount\">Lock Account</label>
                                            <input class=\"form-check-input\" id=\"inputLockAccount\" type=\"checkbox\" name=\"lock_account\" <?php echo ($locked) ? \"checked\" : \"\" ?>>
                                        </div>
                                    </div>"
                                ?>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" id="inputLockAccount" type="checkbox" name="lock_account" <?php echo ($user_id == $session_info['id']) ? "disabled" : "" ?> <?php echo ($locked) ? "checked" : "" ?> />
                                    <label class="form-check-label" for="flexSwitchCheckChecked">Lock Account</label>
                                </div>
                                <!-- Form Row -->
                                <div class="row gx-3 mb-4">
                                    <!-- Form Group -->
                                    <!-- password-->

                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputPassword">New Password</label>
                                        <input class="form-control" id="inputPassword" type="password" name="password">
                                    </div>
                                </div>

                                <input type="hidden" name="id" value="<?php echo $user_id ?>">
                                <!-- Save changes button-->
                                <button class="btn btn-success float-end mx-1" type="submit" name="submit_changes">Update</button>
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
</div>

</html>