<?php
include "includes/db/connect.php";
//return the name of the entity corresponding to @param int $id
function getEntityName($id, $conn)
{
    if ($id === -1)
        return "N/A";
    $sql_entity = "SELECT name FROM entity WHERE id = '$id'";
    $row = mysqli_fetch_array($conn->query($sql_entity));
    if (isset($row['name'])) {
        return $row['name'];
    } else {
        return "N/A";
    }
}

//faciliate clearance of assets and requests when a user is moved to another department
function departmentClearance($user_id, $conn) {
    $sql_asset = "UPDATE asset SET status=1, user=NULL WHERE user=$user_id"; //set all assets to idel
    $sql_request = "UPDATE pending_requests SET result=3 WHERE initiator=$user_id"; //cancell all request
    if (!$conn->query($sql_asset)) {
        header("Location: edit_user.php?id=$user_id&error=2");
        exit;
    }
    if (!$conn->query($sql_request)) {
        header("Location: edit_user.php?id=$user_id&error=2");
        exit;
    }
}

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
            $entity_id = isset($current_user_data['entity']) ? $current_user_data['entity'] : -1; //-1 if no entity
            $entity_name = getEntityName($entity_id, $conn);
            $department_id = isset($current_user_data['department']) ? $current_user_data['department'] : -1; //-1 if no department
            $entity_super = $current_user_data['entity_super'];
            $current_role = $current_user_data['role'];
            $locked = $current_user_data['locked'];
        }
    }
} else {
    header('Location: users.php');
}

$edit_status = -1;
if (isset($_GET['error'])) {
    $edit_status = $_GET['error'];
} elseif (isset($_GET['success'])) {
    $edit_status = 0;
}

//handle post requests to update user account details
if (isset($_POST['submit_changes'])) {
    $new_department_id = $_POST['department'];
    $new_role_id = $_POST['role'];
    $new_locked = isset($_POST['lock_account']) ? 1 : 0;

    if ($new_department_id != $department_id) { //moved to another department
        departmentClearance($user_id, $conn);
    }

    //validations for roles
    if ($new_role_id == 2)
        $new_department_id = -1;
    elseif ($new_role_id > 2 && $new_department_id == -1) {
        header("Location: edit_user.php?id=$user_id&error=1");
        exit;
    }

    $sql = "UPDATE user SET department=NULLIF($new_department_id, -1), role='$new_role_id', locked='$new_locked'";
    if (isset($_POST['password']) and $_POST['password'] !== "") { //if password is updated
        $new_password = $_POST['password'];
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql .= ", password='$new_hashed_password'";
    }

    $sql .= " WHERE id='$user_id'";

    if ($conn->query($sql)) { //update successful
        header("Location: edit_user.php?id=$user_id&success");
    } else { //update failed
        header("Location: edit_user.php?id=$user_id&error=2");
        exit;
    }

}

$active = 'Edit User';
include "includes/header.php";
$session_info = $_SESSION['user'];
$editor_role = $session_info['role'];
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
        <!-- Main page content-->
        <div class="container-xl px-4 mt-4 py-3">
            <!-- Account page navigation-->
            <div class="row">
                <div class="col-xl-12">
                    <!-- Account details card-->
                    <div class="card mb-4">
                        <div class="card-header">Account Details</div>
                        <?php
                                if ($edit_status == 0) {
                                    echo '<div class="alert alert-success" role="alert">Update Successful!</div>';                                                     
                                } elseif ($edit_status == 1) {
                                    echo '<div class="alert alert-danger" role="alert">Department cannot be empty</div>'; 
                                } elseif ($edit_status == 2) {
                                    echo '<div class="alert alert-danger" role="alert">Update failed</div>'; 
                                }
                        ?>
                        <div class="card-body">
                            <?php echo  "<p style=\"color: gray;\">Date Joined: " . $last_modified . "</p>" ?>
                            <form method="post" action="edit_user.php?id=<?=$user_id?>">
                                <!-- Form Row -->
                                <div class="row gx-3 mb-3">
                                    <!-- Form Group (name) -->
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputName">Name</label>
                                        <input disabled class="form-control" required id="inputName" type="text" value="<?php echo $name ?>" name="name">
                                    </div>
                                    <!-- Form Group (entity) -->
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputEntity">Entity</label>
                                        <input disabled class="form-control" required id="inputEntity" type="text" value="<?php echo $entity_name ?>" name="entity">
                                    </div>
                                </div>
                                <!-- Form Row -->
                                <div class="row gx-3 mb-3">
                                    <!-- Form Group (department, role)-->
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputDepartment">Department</label>
                                        <select class="form-control" id="inputDepartment" name="department" <?php echo ($editor_role < $current_role && $editor_role < 3) ? "" : "disabled"?>>
                                            <option value="-1">-</option>
                                            <?php
                                                $results = $conn->query("SELECT id, name FROM department WHERE entity='$entity_id'");
                                                echo $entity_id;
                                                while ($row = $results->fetch_assoc()) {
                                                    unset($id, $name);
                                                    $id = $row['id'];
                                                    $name = $row['name'];
                                                    echo '<option value="' . $id . '">' . $name . '</option>';
                                                }
                                            ?>
                                        </select>
                                        <script>
                                            var selectDepartment = document.getElementById('inputDepartment');
                                            selectDepartment.value = <?=$department_id?>;
                                        </script>
                                    </div>                                    
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputRole">Role</label>
                                        <select <?php echo ($editor_role < $current_role && $editor_role < 3) ? "" : "disabled"?> class="form-control" id="inputRole" name="role">
                                            <?php 
                                                if ($editor_role == 1) 
                                                    echo '<option value="1"' . (($current_role == 1) ? "selected" : "null") . '>superadmin</option>'; 
                                            ?>
                                            <option value="2" <?php echo ($current_role == 2) ? "selected" : "null" ?>>admin</option>
                                            <option value="3" <?php echo ($current_role == 3) ? "selected" : "null" ?>>resource manager</option>
                                            <option value="4" <?php echo ($current_role == 4) ? "selected" : "null" ?>>user</option>
                                        </select>

                                    </div>
                                </div>
                                <!-- Form Row -->
                                <div class="row gx-3 mb-4">
                                    <!-- Form Group -->
                                    <!-- password-->
                                    <div class="col-md-6">
                                        <label class="small mb-1" for="inputPassword">New Password</label>
                                        <input class="form-control" id="inputPassword" type="password" name="password" <?php echo ($editor_role < $current_role && $editor_role < 3) ? "" : "disabled"?>>
                                        <label class="small mb-1" for="inputPassword">*if the field is empty, password is unchanged.</label>
                                    </div>
                                </div>
                                <!-- Form Row -->
                                <?php   
                                    if ($editor_role < $current_role)                                  
                                        echo '<div class="form-check form-switch">
                                            <input class="form-check-input" id="inputLockAccount" type="checkbox" name="lock_account" ' . (($locked) ? "checked" : "") . ' />
                                            <label class="form-check-label" for="flexSwitchCheckChecked">Lock Account</label>
                                        </div>';
                                ?>

                                <input type="hidden" name="id" value="<?php echo $user_id ?>">
                                <!-- Save changes button-->
                                <button class="btn btn-success float-end mx-1" type="submit" name="submit_changes" <?php echo ($editor_role < $current_role && $editor_role < 3) ? "" : "disabled"?>>Update</button>
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