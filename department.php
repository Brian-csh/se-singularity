<?php
if (isset($_GET['id'])) {
    $department_id = $_GET['id'];
}
if (isset($_GET['name'])) {
    $department_name = $_GET['name'];
}

if (isset($_POST['edit_details'])) {
    //update the name and parent
}

$active = $department_name;
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
                                <div class="page-header-icon"><i data-feather="box"></i></div>
                                <?php echo $department_name ?>
                            </h1>
                            <div class="page-header-subtitle">
                                <?php
                                //get parent department
                                $sql = "SELECT * FROM department WHERE id = '$department_id' LIMIT 1";
                                $result = $conn->query($sql);

                                if ($result) {
                                    if (mysqli_num_rows($result) > 0) {
                                        $row = $result->fetch_assoc();
                                        $department_parent = isset($row['parent']) ? $row['parent'] : -1;
                                        $entity_id = $row['entity'];
                                    }
                                } else {
                                    echo "error: cannot find department";
                                }

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
                            <button type="button" class="btn btn-primary btn-xs float-end" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">Edit</a>
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
                                        $department_id = $row['id'];
                                        $department_name = $row['name'];

                                        echo "<tr data-id='$department_id' ><td>$department_id</td><td>$department_name</td></tr>";
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
                    <h5 class="modal-title" id="exampleModalLabel">Add New Asset CLass</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="department.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="categoryUpdateName">Department Name</label>
                            <input class="form-control" id="categoryUpdateName" type="text" name="category_name" <?php echo "placeholder=\"$department_name\"" ?> required>
                            <label for="categoryUpdateParent">Parent Department Name</label>
                            <select class="form-control" id="inputDepartment" name="parent">
                                <option value="">N/A</option>
                                <?php
                                $results = $conn->query("SELECT id, name FROM department where entity='$entity_id'");
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
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="edit_details">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <?php
    include "includes/footer.php";
    ?>