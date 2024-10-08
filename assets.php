<?php
// $sql = "SELECT * FROM asset ORDER BY id DESC";
$active = "Assets";

include "includes/header.php";
include "includes/navbar.php";

$class_entity_id = $session_info['entity'];
$user_id_filter = -1;
if (isset($_GET['userid'])) {
    $user_id_filter = intval($_GET['userid']);

    echo "
    <script>
    var active_elements = document.querySelectorAll('a[href*=\"assets.php\"]');

    for (var i = 0; i < active_elements.length; i++) {
    var element = active_elements[i];
    element.classList.remove('active');
    }

    var active_elements = document.querySelectorAll('a[href*=\"assets.php?userid\"]');
    let link = active_elements[0];

    link.classList.toggle('active');

    </script>
    ";
}

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
        $sql_add_class = "INSERT INTO asset_class (name, parent, class_type, entity) 
        VALUES ('$name', '$parent', '$class_type', '$class_entity_id')";
    } else {
        $sql_add_class = "INSERT INTO asset_class (name, parent, class_type, entity) 
        VALUES ('$name', NULL, '$class_type', '$class_entity_id')";
    }
    if ($conn->query($sql_add_class)) {
        // TODO: create a popup for success
    } else {
        echo "Error.";
    }
}


// get all asset classes in this entity
$sql = "SELECT * FROM asset_class WHERE entity = '$class_entity_id'";
$result = mysqli_query($conn, $sql);
$asset_classes = array();
while($row = mysqli_fetch_assoc($result)) {
    $asset_obj = array(
        "value" => intval($row['id']),
        "label" => $row['name'],
        "selected" => false
    );
    $asset_classes[] = $asset_obj;
}
$asset_classes = json_encode($asset_classes);

$hidden_name = "class_parent";

echo "<script> 
        var asset_classes = JSON.parse('". $asset_classes ."'); 
        var hidden_name = JSON.parse('". json_encode($hidden_name) ."'); 
    </script>";

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
                            <?php if($role_id ==3){?>
                                <a href="add_asset_by_rm.php" class="btn btn-secondary btn-xs float-end ms-2">+ Add Asset</a> 
                            <?php }?>
                            <?php if($role_id == 2 || $role_id == 3){?>
                                <button type="button" class="btn btn-primary btn-xs float-end" data-bs-toggle="modal" data-bs-target="#addClassModal">+ Add Class</button>
                                <button type="button" class="btn btn-warning btn-xs float-end ms-2 me-2" data-bs-toggle="modal" data-bs-target="#importModal">Import</button>
                                <a href="includes/scripts/export_assets.php" class="btn btn-success btn-xs float-end">Export</a>
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
                    <table id="myTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Parent</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>User</th>
                                <th>Department</th>
                                <th>Expiration Date</th>
                                <th>Position</th>
                                <th>Status</th>
                                <?php if($role_id < 4){?>
                                    <th>Edit Asset</th>
                                <?php } else { ?>
                                    <!-- <th>Image</th> -->
                                <?php }?>
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
    
    <!-- MODAL -->
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

                        <div class="mt-1">
                            <label for="classAddName">Parent Class<label>
                            <!-- uses multiselect_search.js for selection -->
                            <input type="hidden" name="<?php echo $hidden_name ?>" id="<?php echo $hidden_name ?>" value="">
                        </div>
                        <div>
                            <select class="form-control" id="multiple-select-search"></select>
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

    <!-- User Move Modal -->
    <?php if($role_id == 4){?>
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
                                <?php
                                    $results = $conn->query("SELECT id, name,department,role FROM user WHERE department = '$department_id' and role = '4' and id != '$user_id' LIMIT 1000");
                                    while ($row = $results->fetch_assoc() ) {
                                        unset($id, $name);
                                        $id = $row['id'];
                                        $name = $row['name'];
                                        $departmentid = $row['department'];
                                        $department = $conn->query("SELECT name FROM department WHERE id = '$departmentid'")->fetch_object()->name;
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
    <?php }?>
    <?php if($role_id == 3){?>
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
                                <!-- <option value="">Select an department</option> -->
                                <?php
                                if(!function_exists('getAllSubdepartmentIds')) require "includes/get_subdepartments.php";
                                $subdepartmentIds = getAllSubdepartmentIds($department_id, $conn);
                                $subdepartmentIds = implode(',', $subdepartmentIds);
                                $results = $conn->query("SELECT id, name FROM department WHERE id in ($subdepartmentIds) and id != '$department_id'");
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
    <?php }?>

    <!-- Import Asset Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="includes/scripts/import_assets.php" enctype="multipart/form-data" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Import Assets</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="importFile">Select File</label>
                            <input class="form-control" type="file" id="importFile" name="csvFile" accept=".csv">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" id="confirmButton" value="Upload CSV" name="submit">Submit</button>
                    </div>
                </div>
            </form>
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

    <!-- Choices JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <link rel="stylesheet" href="css/multiselect.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="includes/multiselect_search_class.js"></script>

    <!-- Toast -->
    <link rel="stylesheet" type="text/css" href="css/toastify.css">
    <script src="js/toastify.js"></script>

    
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <!-- DataTables Select JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
    <!-- Styles for DataTables buttons -->

    <style>
        button.dt-button.use-button { 
            /* only for user */
            color: white !important;
            background: green !important;
            border-radius: 20px !important;
        }
        button.dt-button.return-button {
            /* only for manager and user */
            color: white !important;
            background: red !important;
            border-radius: 20px !important;
        }
    </style>
    <!-- DataTables Buttons JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                ordering: false,
                searching: true,
                processing: true,
                serverSide: true,
                searchDelay: 500,
                ajax: {
                    url: "includes/scripts/datatables_assets.php",
                    data: function(d) {
                        d.userid = <?= $user_id_filter ?>;
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
                        "data": "department"
                    },
                    {
                        "data": "expire"
                    },
                    {
                        "data": "position"
                    },
                    {
                        "data": "status"
                    }
                    <?php if($role_id < 4){?>
                    ,{
                        "data": "actions"
                    }
                    <?php }?>
                ],
                <?php if($role_id > 2){?>
                select: {
                    style: 'multi'
                },
                <?php }?>
                buttons: [
                    <?php if($role_id == 4){?>
                    { 
                        text: "Use",
                        className: 'use-button',
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
                                                    Toastify({ text: "Asset " + data.result[i][0] + " is not available for USE. You can only make requests for assets that are idle.", duration: 3000, backgroundColor: "red", position: "center", gravity: "top" }).showToast();
                                                } else { // Succeess
                                                    Toastify({ text: "Asset " + data.result[i][0] + " request (USE) made successfully!.", duration: 3000, backgroundColor: "green", position: "center", gravity: "top" }).showToast();
                                                }
                                            }
                                            dt.ajax.reload(); // Refresh the DataTables
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    alert("Error: " + thrownError);
                                }
                            });
                        }
                    }, <?php }?>
                    <?php if($role_id == 4 || $role_id ==3 ) {?>
                    {
                        text: <?php if($role_id == 4) { ?> 'Return', <?php } else if($role_id ==3) { ?> 'Retire', <?php }?>
                        className: 'return-button',
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
                                success: function(response){ // user handle request success
                                    console.log(response);
                                            // Perform any additional actions on success
                                            var data = JSON.parse(response);
                                            for (var i = 0; i<data.result.length; i++){
                                                console.log(data.result[i]);
                                                if(data.result[i][1] === false){ // fail
                                                    // fetch asset name
                                                    <?php if($role_id == 4) { ?> 
                                                        Toastify({ text: 'Asset "' + data.result[i][0] + '" is not available for RETURN. You can only return assets that are in your possession.', duration: 3000, backgroundColor: "red", position: "center", gravity: "top" }).showToast();
                                                    <?php } else { ?> 
                                                        Toastify({ text: 'Asset "' + data.result[i][0] + '" is not available for RETIRE. You can only retire assets that are IDLE.', duration: 3000, backgroundColor: "red", position: "center", gravity: "top" }).showToast();
                                                    <?php }?>
                                                } else { // Succeess
                                                    <?php if($role_id == 4) { ?> 
                                                        Toastify({ text: 'Asset "' + data.result[i][0] + '" request (RETURN) made successfully!', duration: 3000, backgroundColor: "green", position: "center", gravity: "top" }).showToast();
                                                    <?php } else { ?> 
                                                        Toastify({ text: 'Asset "' + data.result[i][0] + '" RETIRED!', duration: 3000, backgroundColor: "green", position: "center", gravity: "top" }).showToast();
                                                    <?php }?>
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
                                            role_id: <?= $role_id ?>,
                                            user_id: <?=$user_id?>
                                        }, // TODO : handle requests
                                        success: function(response) {
                                            console.log(response);
                                            // Perform any additional actions on success
                                            var data = JSON.parse(response);

                                            for (var i = 0; i<data.result.length; i++){
                                                console.log(data.result[i]);
                                                if(data.result[i][1] === false){ // fail
                                                    // fetch asset name
                                                    Toastify({ text: "Asset " + data.result[i][0] + " is not available for MOVE. You can only move assets that are IDLE.", duration: 3000, backgroundColor: "red", position: "center", gravity: "top" }).showToast();
                                                } else { // Succeess
                                                    Toastify({ text: "Asset " + data.result[i][0] + " moved!", duration: 3000, backgroundColor: "green", position: "center", gravity: "top" }).showToast();
                                                }
                                            }
                                            dt.ajax.reload(); // Refresh the DataTables
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.error(textStatus, errorThrown);
                                            dt.ajax.reload(); // Refresh the DataTables
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
                                                    Toastify({ text: "Asset " + data.result[i][0] + " is not available for MOVE. You can only move assets that are in your possession.", duration: 3000, backgroundColor: "red", position: "center", gravity: "top" }).showToast();
                                                } else { // Succeess
                                                    Toastify({ text: "Asset " + data.result[i][0] + " request (MOVE) made successfully!", duration: 3000, backgroundColor: "green", position: "center", gravity: "top" }).showToast();
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
                    }, <?php }?>
                    <?php if($role_id == 4){?>
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
                                                    Toastify({ text: "Asset " + data.result[i][0] + " is not available for REPAIR. You can only make requests for assets that are in your possession.", duration: 3000, backgroundColor: "red", position: "center", gravity: "top" }).showToast();
                                                } else { // Succeess
                                                    Toastify({ text: "Asset " + data.result[i][0] + " request (REPAIR) made successfully!.", duration: 3000, backgroundColor: "green", position: "center", gravity: "top" }).showToast();
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
                ]
                <?php if($role_id > 2){?>
                    ,
                    dom: 'Bfrtip' // Add this line to display buttons
                <?php }?>
            });
        });
    </script>

    <?php
        if (isset($_GET['error'])) {
            echo '<script>Toastify({ text: "Import error at row '.$_GET['error'].'", duration: 3000, backgroundColor: "red", position: "center", gravity: "top" }).showToast();</script>';
        }
    ?>
    
    <!-- For Request Modal -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->

    <?php
    include "includes/footer.php";
    ?>
