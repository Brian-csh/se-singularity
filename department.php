<?php

include "includes/db/connect.php";
include "includes/header.php";
include "includes/navbar.php";


// if (isset($_GET['id'])) {
    // $department_id = $_GET['id'];
// } else {
// }

//get the department name given the id from database using mysql
$sql = "SELECT * FROM department WHERE id = '$department_id' LIMIT 1";
$result = $conn->query($sql);
if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $row = $result->fetch_assoc();
        $department_parent = isset($row['parent']) ? $row['parent'] : -1;
        $entity_id = $row['entity'];
        $department_name = $row['name'];
    }
} else {
    exit("No department found with that ID.");
}

if (isset($_POST['edit_details'])) {
    //update the name and parent
    $department_id = $_POST['department_id'];
    $updated_department_name = $_POST['department_name'];
    $updated_department_parent = $_POST['parent'];

    if ($updated_department_parent === "") {
        $sql = "UPDATE department SET name = '$updated_department_name', parent = NULL WHERE id = '$department_id'";
    } else {
        $sql = "UPDATE department SET name = '$updated_department_name', parent = '$updated_department_parent' WHERE id = '$department_id'";
    }
    if ($conn->query($sql)) {
        header('Location: department.php?id=' . $department_id . '&name=' . $updated_department_name);
    } else {
        header('Location: department.php?id=' . $department_id . '&name=' . $updated_department_name . '&insert_error');
    }
}
$active = $department_name;
?>


<div id="layoutSidenav_content">
    <main>
        <header class="page-header pt-10 page-header-dark bg-gradient-primary-to-secondary pb-5">
            <div class="container-xl px-4">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="box"></i></div>
                                <?php echo $department_name ?>
                            </h1>
                            <div class="page-header-subtitle">
                                <?php
                                //get parent department name
                                if ($department_parent > 0) {
                                    $sql_parent_name = "SELECT name FROM department WHERE id = '$department_parent' LIMIT 1";
                                    $parent_name_result = $conn->query($sql_parent_name);
                                    $parent_assoc = $parent_name_result->fetch_assoc();
                                    $parent_name = $parent_assoc['name'];
                                    echo "Parent Department: " . $parent_name;
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
                                Sub-departments
                            </h1>
                            <?php if($role_id <= 2 && $role_id >=1) {?>
                            <button type="button" class="btn btn-primary btn-xs float-end" data-bs-toggle="modal" id="manageUsers">Manage Users</a>
                            <button type="button" class="btn btn-primary btn-xs float-end" data-bs-toggle="modal" data-bs-target="#addDepartmentModal" style="margin-right: 10px">Edit</a>
                            <?php }?>
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

                            // select all the departments for the entity
                            $sql_department = "SELECT * FROM department WHERE parent = '$department_id' ORDER BY id DESC";
                            $result = $conn->query($sql_department);

                            if ($result) {
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $id = $row['id'];
                                        $name = $row['name'];

                                        echo "<tr data-id='$id' ><td>$id</td><td>$name</td></tr>";
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



    <!-- Add Class Modal -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Department</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="department.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="departmentUpdateName">Department Name</label>
                            <input class="form-control" id="departmentUpdateName" type="text" name="department_name" <?php echo "value=\"$department_name\"" ?> required>
                            <label for="departmentUpdateParent">Parent Department Name</label>
                            <select class="form-control" id="inputDepartment" name="parent">
                                <option value="">N/A</option>
                                <?php
                                $results = $conn->query("SELECT id, name FROM department where entity='$entity_id' AND id!='$department_id' AND (parent IS NULL OR parent!='$department_id')"); //parent cannot be itself nor its sub-department
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
                    <input type="hidden" name="department_id" value="<?php echo $department_id ?>">
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="edit_details">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    var btn = document.getElementById('manageUsers');
    btn.addEventListener('click', function() {
      document.location.href = 'users.php?departmentid=<?= $department_id ?>';
    });
    </script>

    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <?php
    include "includes/footer.php";
    ?>
