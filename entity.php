<?php

include "includes/header.php";
include "includes/navbar.php";
$initiator_id = $user_id; 

//ONLY SUPERADMIN and ADMIN CAN ACCESS THIS PAGE
if($role_id == 1){
    // TODO: access entity page from entities.php
    if (isset($_GET['id'])) {
        $entity_id = $_GET['id'];
    }
} else if ($role_id == 2){ // no department_id
    // TODO: access entity page from navbar -> no get request
}

echo "<script>document.title = '" . 'Entity #'. $entity_id . ' - Singularity EAM'."';</script>";

// Fetch entity values
$sql = "SELECT * FROM entity WHERE id = '$entity_id' LIMIT 1";
$result = $conn->query($sql);
if ($result -> num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entity_name = $row['name'];
    }
} else {
    exit("No entity found with that ID.");
}

// Delete attribute function
if (isset($_GET['delete_attribute_id']) and $_GET['delete_attribute_id'] != "") {
    $attribute_deletion_id = $_GET['delete_attribute_id'];

    // Get data about this attribute
    $sql = "SELECT * FROM asset_attribute WHERE id = '$attribute_deletion_id' LIMIT 1";
    $result = $conn->query($sql);
    if ($result -> num_rows > 0) {
        while ($row_delete = $result->fetch_assoc()) {
            $attribute_entity_id = $row_delete['entity_id'];

            // Delete the attribute
            $sql = "DELETE FROM asset_attribute WHERE id = '$attribute_deletion_id' LIMIT 1";
            $conn->query($sql);
            echo "<script type='text/javascript'>alert('Attribute deleted successfully.');</script>";
        }
    }
}

// Create attribute
if (isset($_POST['create_asset_attribute'])) {
    // Get the input data for the new attribute
    $attribute_name = $_POST['attribute_name'];

    // Insert the new attribute into the database
    $sql = "INSERT INTO asset_attribute (entity_id, custom_attribute) VALUES ('$entity_id', '$attribute_name')";
    $result = $conn->query($sql);

    if ($result) {
        echo "<script type='text/javascript'>alert('Attribute created successfully.');</script>";
    } else {
        echo "<script type='text/javascript'>alert('Error creating attribute: ". $conn->error ."');</script>";
    }
}


?>

<div id="layoutSidenav_content">
    <main>
        <header class="page-header pt-10 page-header-dark bg-gradient-primary-to-secondary pb-5">
            <div class="container-xl px-4">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="home"></i></div>
                                <?php echo $entity_name ?>
                            </h1>
                            <div class="page-header-subtitle">
                                <?php
                                $sql_entity = "SELECT * FROM entity WHERE id = '$entity_id' LIMIT 1";
                                $result = $conn->query($sql_entity);

                                if ($result) {
                                    if (mysqli_num_rows($result) > 0) {
                                        $entity_data = mysqli_fetch_assoc($result);
                                        $date_created = gmdate("Y.m.d \ | H:i:s", $entity_data['date_created']);
                                    } else {
                                        $date_created = "N/A";
                                    }
                                }

                                echo "Date Created: $date_created<br>";

                                $sql_user = "SELECT * FROM user WHERE (id = '$entity_id' AND entity_super = 1) LIMIT 1";
                                $result = $conn->query($sql_user);


                                if ($result) {
                                    if (mysqli_num_rows($result) > 0) {
                                        $user_data = mysqli_fetch_assoc($result);
                                        echo "Entity Head: $user_data <br>";
                                    }
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
                                Departments
                            </h1>
                            <a <?php echo "href=\"new_department.php?entity_id=$entity_id\""?> class="btn btn-primary btn-xs float-end ms-2">+ Add Department</a>
                            <a href="#" class="btn btn-secondary btn-xs float-end" data-bs-toggle="modal" data-bs-target="#addAttrModal">+ Add Asset Attribute</a>
                            <?php if($entity_id == $_SESSION['user']['entity']) {
                                echo "<a href='includes/entity_sync.php?entity_id=$entity_id&initiator=$initiator_id' class='btn btn-primary btn-xs float-end me-2'>
                                        Sync Feishu
                                    </a>";
                            } ?>
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
                                <th>Parent</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Parent</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php

                            // select all the departments for the entity
                            $sql_department = "SELECT * FROM department WHERE entity = '$entity_id' ORDER BY id DESC";
                            $result = $conn->query($sql_department);

                            if ($result) {
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $departmentid = $row['id'];
                                        $department_name = $row['name'];
                                        $department_parent = $row['parent'];

                                        $sql_parent_name = "SELECT name FROM department WHERE id = '$department_parent' LIMIT 1";
                                        $parent_name_result = $conn->query($sql_parent_name);
                                        $parent_assoc = $parent_name_result->fetch_assoc();
                                        $parent_name = (isset($parent_assoc['name'])) ? $parent_assoc['name'] : "N/A";

                                        echo "<tr data-id='$departmentid' ><td>$departmentid</td>".
                                                "<td><a class='text-primary' href='/department.php?departmentid=$departmentid'>$department_name</a></td><td>$parent_name</td></tr>";
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="container p-3 pt-5">
            <h1>
                Custom Attributes:
            </h1>
            <p class="text-white ">
                <?php
                $sql = "SELECT * FROM asset_attribute WHERE entity_id = '$entity_id'";
                $result = $conn->query($sql);

                if ($result) {
                    if (mysqli_num_rows($result) > 0) {
                        echo "<ul class='text-white'>";
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<li>" . $row['custom_attribute'] . " | <a class='text-danger' href='entity.php?id=" . $entity_id . "&delete_attribute_id=" . $row['id'] . "'>Delete</a></li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "No custom attributes";
                    }
                }
                ?>
            </p>
        </div>

    </main>


    <!-- Add Attribute Modal -->
    <div class="modal fade" id="addAttrModal" tabindex="-1" role="dialog" aria-labelledby="attrAddLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Asset Attribute</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="entity.php?id=<?=$entity_id?>" method="post" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="classAddName">Attribute Name *</label>
                            <input class="form-control" id="classAddName" type="text" name="attribute_name" placeholder="Size" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="create_asset_attribute">Submit</button>
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
    <script>
        // Get the element by its href attribute
        var element = document.querySelector('a[href="/entities.php"]');
        // Toggle the "active" class
        element.classList.toggle('active');
    </script>
    <?php
    include "includes/footer.php";
    ?>