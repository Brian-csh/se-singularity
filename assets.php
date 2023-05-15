<?php
// $sql = "SELECT * FROM asset ORDER BY id DESC";
$active = "Assets";

include "includes/header.php";
include "includes/navbar.php";

if (isset($_POST['add_class'])) {
    $name = $_POST['class_name'];
    if ($_POST['class_type'] == "ItemAsset") {
        $class_type = 0;
    } else if ($_POST['class_type'] == "ValueAsset") {
        $class_type = 1;
    } else {
        $class_type = -1;
    }
    if (isset($_POST['class_parent']) && $_POST['class_parent']) {
        $parent = $_POST['class_parent'];
        $sql_add_class = "INSERT INTO asset_class (name, parent, class_type) 
        VALUES ('$name', '$parent', '$class_type')";
    } else {
        $sql_add_class = "INSERT INTO asset_class (name, parent, class_type) 
        VALUES ('$name', NULL, '$class_type')";
    }
    if ($conn->query($sql_add_class)) {
        // TODO: create a popup for success
    } else {
        echo "Error.";
    }
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
                                <div class="page-header-icon text-white"><i data-feather="home"></i></div>
                                <?= $active ?>
                            </h1>
                            <a href="add_asset.php" class="btn btn-secondary btn-xs float-end ms-2">+ Add Asset</a> 
                            <button type="button" class="btn btn-primary btn-xs float-end" data-bs-toggle="modal" data-bs-target="#addClassModal">+ Add Class</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
        <div class="container-fluid px-4">
            <div class="card">
                <div class="card-body">
                    <table id="myTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Parent</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>User</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Class Modal -->
    <div class="modal fade" id="addClassModal" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Asset Class</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="assets.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="classAddName">Class Name *</label>
                            <input class="form-control" id="classAddName" type="text" name="class_name" placeholder="Furniture" required>
                        </div>

                        <div class="mb-3">
                            <label for="classAddType">Class Type *</label><br>
                            <input type="radio" id="classItemType" name="class_type" value="ItemAsset" checked>
                            <label for="classItemType">Item Asset</label>
                            <input type="radio" id="classValueType" name="class_type" value="ValueAsset">
                            <label for="classValueType">Amount Asset</label><br>
                        </div>

                        <div class="mb-3">
                            <label for="classAddName">Parent Class<label>
                                    <select class="form-control ms-2" id="inputParentClass" name="class_parent">
                                        <option value="">Select a Parent Class</option>
                                        <?php
                                        $results = $conn->query("SELECT id, name FROM asset_class");
                                        while ($row = $results->fetch_assoc()) {
                                            if ($row['name']) {
                                                unset($id, $parent);
                                                $id = $row['id'];
                                                $parent = $row['name'];
                                                echo '<option value="' . $id . '">' . $parent . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="add_class">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <!-- User Move Modal -->
    <div class="modal fade" id="chooseUserModal" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Move to Another User</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- User -->
                        <div class ="mb-3">
                            <label for="destinationUser">Destination User Name</label>
                            <select class="form-control" id="destinationUser">
                                <!-- <option value=""> N/A </option> -->
                                <?php
                                    $results = $conn->query("SELECT id, name,department,role FROM user WHERE entity = '$entity_id' and role = '4' and id != '$user_id'");
                                    while ($row = $results->fetch_assoc() ) {
                                        unset($id, $name);
                                        $id = $row['id'];
                                        $name = $row['name'];
                                        $departmentid = $row['department'];
                                        $department = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$departmentid'"))['name'];
                                        echo '<option value="' . $id . '">' . $name ." - ". $department.'</option>';
                                    }
                                    ?>
                                </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" id="confirmButton">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manager move Modal -->
        <div class="modal fade" id="chooseDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Move to Another Department</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="destinationDepartment">Destination Department Name</label>
                            <select class="form-control" id="destinationDepartment">
                                <option value="">N/A</option>
                                <?php
                                $results = $conn->query("SELECT id, name, entity FROM department WHERE entity = $entity_id");
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
                        <button class="btn btn-success" type="submit" id="confirmButton">Submit</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- handleRequestModal -->
        <!-- <div class="modal fade" id="handleRequestModal" tabindex="-1" role="dialog" aria-labelledby="BasicInfoEditLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            </div>
        </div> -->

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

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
            $('#myTable').DataTable({
                ordering: true,
                searching: true,
                processing: true,
                serverSide: true,
                searchDelay: 500,
                ajax: {
                    url: "includes/scripts/datatables_assets.php",
                    data: function(d) {
                        d.userid = <?= $user_id ?>;
                        d.roleid = <?= $role_id ?>;
                        d.entityid = <?= $entity_id ?>;
                        d.departmentid = <?= $department_id ?>;
                    }
                },
                columns: [{
                        "data": "id"
                    },
                    {
                        "data": "parent"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "class"
                    },
                    {
                        "data": "user"
                    },
                    {
                        "data": "price"
                    },
                    {
                        "data": "description"
                    },
                    {
                        "data": "position"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "actions"
                    }
                ],
                select: {
                    style: 'multi'
                },
                buttons: [
                    {
                        text: <?php if($role_id == 4) { ?> 'Return', <?php } else { ?> 'Retire', <?php }?>
                        action: function(e, dt, node, config) {
                            var selectedRows = dt.rows({
                                selected: true
                            }).data().toArray();
                            var assetIds = selectedRows.map(function(row) {
                                return row.id;
                            });

                            // Perform AJAX request
                            $.ajax({
                                url: <?php if($role_id == 4) { ?> 'includes/scripts/return_assets.php',
                                        <?php } else { ?> "includes/scripts/retire_assets.php", <?php }?>
                                method: "POST",
                                data: {
                                    assets: assetIds,
                                    user_id: <?= $user_id?>
                                },
                                <?php if ($role_id !=4 ) { ?>
                                success: function(response) { // manager handle request success
                                    console.log(response);
                                    // Perform any additional actions on success
                                    dt.ajax.reload(); // Refresh the DataTables
                                },
                                <?php } else {?>
                                success: function(response){ // user handle request success
                                    console.log(response);
                                            // Perform any additional actions on success
                                            var data = JSON.parse(response);

                                            for (var i = 0; i<data.result.length; i++){
                                                console.log(data.result[i]);
                                                if(data.result[i][1] === false){ // fail
                                                    // fetch asset name
                                                    alert("Asset " + data.result[i][0] + " is not available for RETURN. You can only return assets that are in your possession.");
                                                } else { // Succeess
                                                    alert("Asset " + data.result[i][0] + " request (RETURN) made successfully!.")
                                                }
                                            }
                                            dt.ajax.reload(); // Refresh the DataTables
                                }, 
                                <?php }?>
                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.error(textStatus, errorThrown);
                                }
                            });
                        }
                    },
                    {
                        text: 'Move',
                        action: function(e, dt, node, config) {
                            var selectedRows = dt.rows({
                                selected: true
                            }).data().toArray();
                            var assetIds = selectedRows.map(function(row) {
                                return row.id;
                            });
                            if(<?= $role_id ?> != 4){ // manager move
                                $('#chooseDepartmentModal').modal('show');

                                $('#chooseDepartmentModal').on('click', '#confirmButton', function () {
                                    var departmentId = $('#destinationDepartment').val()
                                    // Perform AJAX request
                                    $.ajax({
                                        url: "includes/scripts/move_assets.php",
                                        method: "POST",
                                        data: {
                                            assets: assetIds,
                                            destination: departmentId,
                                            role_id: <?= $_SESSION['user']['role'] ?>
                                        }, // TODO : handle requests
                                        success: function(response) {
                                            console.log(response);
                                            // Perform any additional actions on success
                                            dt.ajax.reload(); // Refresh the DataTables
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.error(textStatus, errorThrown);
                                        }
                                    });

                                    $('#chooseDepartmentModal').modal('hide');
                                });
                            } else { // user move
                                $('#chooseUserModal').modal('show');

                                $('#chooseUserModal').on('click', '#confirmButton', function () {
                                    var userId = $('#destinationUser').val()
                                    // Perform AJAX request
                                    $.ajax({
                                        url: "includes/scripts/move_assets.php",
                                        method: "POST",
                                        data: {
                                            assets: assetIds,
                                            destination: userId,
                                            role_id: <?=$role_id?>,
                                            user_id: <?=$user_id?>
                                        },
                                        success: function(response) {
                                            console.log(response);
                                            // Perform any additional actions on success
                                            var data = JSON.parse(response);

                                            for (var i = 0; i<data.result.length; i++){
                                                console.log(data.result[i]);
                                                if(data.result[i][1] === false){ // fail
                                                    // fetch asset name
                                                    alert("Asset " + data.result[i][0] + " is not available for MOVE. You can only move assets that are in your possession.");
                                                } else { // Succeess
                                                    alert("Asset " + data.result[i][0] + " request (MOVE) made successfully!")
                                                }
                                            }
                                            dt.ajax.reload(); // Refresh the DataTables
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.error(textStatus, errorThrown);
                                        }
                                    });

                                    $('#chooseUserModal').modal('hide');
                                });
                            }
                        }
                    }
                    <?php if($role_id == 4){?>
                    , { 
                        text: "Use",
                        action: function(e, dt, node, config) {
                            var selectedRows = dt.rows({
                                selected: true
                            }).data().toArray();
                            var assetIds = selectedRows.map(function(row) {
                                return row.id;
                            });

                            // Perform AJAX request
                            $.ajax({
                                url: "includes/scripts/request_assets.php",
                                method: "POST",
                                data: {
                                    assets: assetIds,
                                    user_id: <?=$user_id?>
                                },
                                success: function(response) {
                                    console.log(response);
                                            // Perform any additional actions on success
                                            var data = JSON.parse(response);

                                            for (var i = 0; i<data.result.length; i++){
                                                console.log(data.result[i]);
                                                if(data.result[i][1] === false){ // fail
                                                    // fetch asset name
                                                    alert("Asset " + data.result[i][0] + " is not available for USE. You can only make requests for assets that are idle.");
                                                } else { // Succeess
                                                    alert("Asset " + data.result[i][0] + " request (USE) made successfully!.")
                                                }
                                            }
                                            dt.ajax.reload(); // Refresh the DataTables
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.error(textStatus, errorThrown);
                                }
                            });
                        }
                    },
                    { // TODO : handle requests
                        text:"Repair", 
                        action: function(e, dt, node, config) {
                            var selectedRows = dt.rows({
                                selected: true
                            }).data().toArray();
                            var assetIds = selectedRows.map(function(row) {
                                return row.id;
                            });
                            // Perform AJAX request
                            $.ajax({
                                url: "includes/scripts/repair_assets.php",
                                method: "POST",
                                data: {
                                    assets: assetIds,
                                    user_id: <?=$user_id?>
                                },
                                success: function(response) {
                                    console.log(response);
                                            // Perform any additional actions on success
                                            var data = JSON.parse(response);

                                            for (var i = 0; i<data.result.length; i++){
                                                console.log(data.result[i]);
                                                if(data.result[i][1] === false){ // fail
                                                    // fetch asset name
                                                    alert("Asset " + data.result[i][0] + " is not available for REPAIR. You can only make requests for assets that are in your possession.");
                                                } else { // Succeess
                                                    alert("Asset " + data.result[i][0] + " request (REPAIR) made successfully!.")
                                                }
                                            }
                                            dt.ajax.reload(); // Refresh the DataTables
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    console.error(textStatus, errorThrown);
                                }
                            });
                        }
                    }
                    <?php } ?>
                ],
                dom: 'Bfrtip' // Add this line to display buttons
            });
        });
    </script>
    
    <!-- For Request Modal -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->

    <?php
    include "includes/footer.php";
    ?>