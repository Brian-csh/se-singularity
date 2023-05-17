<?php

include "includes/db/connect.php";
include "includes/header.php";
include "includes/navbar.php";

if($role_id == 1){
    //TODO: access department page from entities.php
} else if ($role_id == 2){
    // access from entity.php
    $department_id = isset($_GET['departmentid']) ? $_GET['departmentid'] : -1;
} else if ($role_id == 3){// resource manager
    if(isset($_GET['departmentid'])){ // access after editting department name
        $department_id = $_GET['departmentid'];
    } else { // access through navbar
        $department_id = $department_id;
    }
} else { //user
    //do nothing. user can't see this page
}

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

echo "<script>document.title = '" . $department_name . "';</script>";


if (isset($_POST['edit_details'])) {
    //update the name and parent
    $departmentid = $_POST['department_id_'];
    $updated_department_name = $_POST['department_name'];
    $updated_department_parent = $_POST['parent'];

    if ($updated_department_parent === "") {
        $sql = "UPDATE department SET name = '$updated_department_name', parent = NULL WHERE id = '$departmentid'";
    } else {
        $sql = "UPDATE department SET name = '$updated_department_name', parent = '$updated_department_parent' WHERE id = '$departmentid'";
    }
    if ($conn->query($sql)) {
        echo "<script>window.location.href = 'department.php?departmentid=" . $departmentid . "&name=" . $updated_department_name . "'</script>";
    } else {
        echo "<script>window.location.href = 'department.php?departmentid=" . $departmentid . "&name=" . $updated_department_name . "&insert_error'</script>";
    }
}

if (isset($_POST['define_tag'])) {
    // Retrieve the selected checkboxes
    $selectedOptions = $_POST['checkboxOptions'];
    $departmentid_ = $_POST['department_id'];

    $template = json_encode($selectedOptions);
    $sql = "UPDATE department SET template = '$template' WHERE id = '$departmentid_'";

    if ($conn->query($sql)) {
        echo "<script>window.location.href = 'department.php?departmentid=" . $departmentid_ . "'</script>";
    } else {
        echo "<script>window.location.href = 'department.php?departmentid=" . $departmentid_ ."&insert_error'</script>";
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
                                <button type="button" class="btn btn-primary btn-xs float-end" data-bs-toggle="modal" data-bs-target="#defineAssetTags">Define Asset Tag</a>
                            <button type="button" class="btn btn-primary btn-xs float-end" data-bs-toggle="modal" id="manageUsers">Manage Users</a>
                            <button type="button" class="btn btn-primary btn-xs float-end" data-bs-toggle="modal" data-bs-target="#editDepartmentModal" style="margin-right: 10px">Edit</a>
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

                                        echo "<tr data-id='$id'><td>$id</td><td><a href='/department.php?departmentid=$id'>$name</a></td></tr>";
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



    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
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
                    <input type="hidden" name="department_id_" value="<?php echo $department_id ?>">
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="edit_details">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Define Asset Tag Modal -->
    <div class="modal fade" id="defineAssetTags" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Define Asset Tags</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="department.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <h6>Current template</h6>
                            <?php
                                //obtain the existing template
                                $sql = "SELECT * FROM department WHERE id = '$department_id' LIMIT 1";
                                $result = $conn->query($sql);
                                if ($result) {
                                    if (mysqli_num_rows($result) > 0) {
                                        $row = $result->fetch_assoc();
                                        $template_string = isset($row['template']) ? $row['template'] : "";
                                    }
                                } else {
                                    exit("No department found with that ID.");
                                }
                                $template = json_decode($template_string);
                                $template_values = is_array($template) ? $template : []; // Ensure $template is an array
                                $template_value_string = implode(", ", $template_values);
                                if (!empty($template_value_string)) {
                                    $template_value_string = ", ".$template_value_string;
                                }
                                echo "<p>id, name, class" . $template_value_string . "<br></p>";

                            ?>
                            <h6>Select contents to be included</h6>
                            <?php
                                $checkboxOptions = array(
                                    'description',
                                    'entity',
                                    'department',
                                    'position',
                                    'expire',
                                    'serial number',
                                    'brand',
                                    'model'
                                );
                                // Generate checkboxes dynamically
                                foreach ($checkboxOptions as $option) {
                                    echo '<label>';
                                    echo '<input type="checkbox" name="checkboxOptions[]" value="' . $option . '"> ' . $option;
                                    echo '</label><br>';
                                }
                            ?>
                        </div>
                    </div>
                    <input type="hidden" name="department_id" value="<?=$department_id?>">
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="define_tag">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    var btn = document.getElementById('manageUsers');
    btn.addEventListener('click', function() {
        document.location.href = '/users.php?departmentid=<?= $department_id ?>';
    });
    </script>

    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <?php 
        echo "<script>
            // Get the element by its href attribute
            var element = document.querySelector('a[href=\"/entity.php\"]');
            // Toggle the \"active\" class
            element.classList.toggle('active');
        </script>"
    ?>
    <?php
    include "includes/footer.php";
    ?>
