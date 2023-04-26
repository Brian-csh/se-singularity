<?php
$sql = "SELECT * FROM asset ORDER BY id DESC";
$active = "Assets";

include "includes/header.php";
include "includes/navbar.php";

/*
Parent: class
Item Asset: 0
Value Asset: 1
*/

if (isset($_POST['add_class'])) {

    $name = $_POST['class_name'];
    if($_POST['class_type'] == "ItemAsset") {
        $class_type = 0;
    }
    else if($_POST['class_type'] == "ValueAsset") {
        $class_type = 1;
    }
    else {
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
        echo "Pain in my assholes.";
    }
}

?>

<div id="layoutSidenav_content">
    <main>
        <header class="page-header page-header-compact page-header-light border-bottom bg-black mb-4">
            <div class="container-fluid px-4">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3 d-inline w-100">
                            <h1 class="page-header-title text-white d-inline">
                                <div class="page-header-icon text-white"><i data-feather="home"></i></div>
                                <?=$active?>
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
                    <div id="tablePreloader">
                        <p class="text-white p-3">Loading...</p>
                    </div>
                    <table id="datatablesSimple" style="display: none">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Parent</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>User</th>
                            <th>Price</th>
                            <!-- <th>Description</th> -->
                            <th>Position</th>
                            <th>Expiration Date</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                        <th>ID</th>
                            <th>Parent</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>User</th>
                            <th>Price</th>
                            <!-- <th>Description</th> -->
                            <th>Position</th>
                            <th>Expiration Date</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php


                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if($row['name']=='NULL') continue;
                                $asset_id = $row['id'];
                                $asset_name = $row['name'];

                                //Fetch Parent 
                                //Who is the parent of asset?
                                $asset_parent = $row['parent'];

                                //Fetch Class
                                $asset_class_id = $row['class'];
                                $asset_class = mysqli_fetch_array($conn->query("SELECT name FROM asset_class WHERE id = '$asset_class_id'"))['name'];
                                
                                //Fetch User
                                $asset_user_id = $row['user'];
                                $asset_user = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$asset_user_id'"))['name'];

                                $asset_price = $row['price'];
                                $asset_description = $row['description'];
                                $asset_position = $row['position'];
                                $asset_expire = date("Y-m-d", strtotime($row['expire']));

                                echo "<tr data-id='$asset_id' ><td>$asset_id</td><td>$asset_parent</td><td><a class='text-primary' href='/asset.php?id=$asset_id&name=$asset_name'>" . $asset_name . "</a></td>
                                        <td>$asset_class</td><td>$asset_user</td><td>$asset_price</td><td>$asset_position</td><td>$asset_expire</td><td>" . "
                                        <a title=\"Edit asset\" class=\"btn btn-datatable btn-icon btn-transparent-light\" href=\"edit_asset.php?id=$asset_id&name=$asset_name"."\">
                                        <i data-feather=\"edit\"></i>
                                        </a>
                                        
                                        " ."</td></tr>";
                            }
                        }


                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- TODO: Hide this from non Project Manager roles. -->

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

    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>

    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <?php
    include "includes/footer.php";
    ?> 
