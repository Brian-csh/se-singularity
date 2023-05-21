<?php
$active = "Customize Navbar";
include "includes/header.php";
include "includes/navbar.php";

//

// select all the departments for the entity
$sql_navbar = "SELECT urls FROM entity WHERE id='$entity_id'";
$result = $conn->query($sql_navbar);

// Declare an empty navbar array
$urls = array();

$row = $result->fetch_assoc();
$urls_json = $row['urls'];

if ($urls_json != null) {
    $urls = json_decode($urls_json);
}


// Get the roles from database to an array like $role[1] = 'admin', etc.
$sql_roles = "SELECT * FROM role";
$result = $conn->query($sql_roles);
$roles = array();
while ($row = $result->fetch_assoc()) {
    $roles[$row['id']] = $row['role'];
}

// Add Link
if (isset($_POST['add_url'])) {
    $url_name = $_POST['url_name'];
    $url_link = $_POST['url_link'];
    $url_role = $_POST['url_role'];

    if (!is_object($urls)) {
        $urls = new stdClass();
    }

    $new_object = new stdClass();
    $new_object->name = $url_name;
    $new_object->url = $url_link;

    if (!isset($urls->$url_role)) {
        $urls->$url_role = array();
    }

    array_push($urls->$url_role, $new_object);

    // Encode the array back
    $urls_json = json_encode($urls);

    // Update the database
    $sql_update = "UPDATE entity SET urls='$urls_json' WHERE id='$entity_id'";
    $conn->query($sql_update);

}

// Remove all links
if (isset($_GET['delete_all'])) {
    // Empty the array
    $urls = array();

    // Encode the array back
    $urls_json = json_encode($urls);

    // Update the database
    $sql_update = "UPDATE entity SET urls='$urls_json' WHERE id='$entity_id'";
    $conn->query($sql_update);
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
                                <div class="page-header-icon text-white"><i data-feather="server"></i></div>
                                <?=$active?>
                            </h1>
                                <a href="#" class="btn btn-primary btn-xs float-end ms-2" data-bs-toggle="modal" data-bs-target="#addNewURL">+ Add</a>
                                <a href="#" class="btn btn-danger btn-xs float-end" data-bs-toggle="modal" data-bs-target="#deleteAllModal">Delete All</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="container-fluid pt-5 px-4">
            <!-- For admin -->
            <?php if (!empty($urls)) : ?>
                <div class="card">
                    <div class="card-body">
                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>URL</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Label</th>
                                    <th>URL</th>
                                    <th>Role</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php
                                    foreach($urls as $role => $roleData) {
                                        foreach($roleData as $data) {
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($data->name) . '</td>';
                                            echo '<td><a href="' . htmlspecialchars($data->url) . '">' . htmlspecialchars($data->url) . '</a></td>';
                                            echo '<td>' . $roles[$role] . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>


    <!-- Delete ALl Modal -->
    <div class="modal fade" id="deleteAllModal" tabindex="-1" role="dialog" aria-labelledby="deleteAllModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalTitle">Delete All</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to delete all third-party URLs?</div>
                <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button><a href="customize_navbar.php?delete_all" class="btn btn-danger" >Delete All</a></div>
            </div>
        </div>
    </div>

    <!-- Add New Third Party URL user Modal -->
    <div class="modal fade" id="addNewURL" tabindex="-1" role="dialog" aria-labelledby="addNewURLModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Third Party URL</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="customize_navbar.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="urlName">URL Name *</label>
                            <input class="form-control" id="urlName" type="text" name="url_name" placeholder="Info System" required>
                        </div>

                        <div class="mb-3">
                            <label for="urlLink">URL Link *</label>
                            <input class="form-control" id="urlLink" type="text" name="url_link" placeholder="https://info.tsinghua.edu.cn" required>
                        </div>

                        <div class="mb-3">
                            <label for="urlRole">Role *</label>
                            <select class="form-control" id="urlRole" name="url_role">
                                <option value="2">Admin</option>
                                <option value="3">Resource Manager</option>
                                <option value="4">User</option>

                            </select>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="add_url">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Individual scripts -->
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>

    <?php include "includes/footer.php"; ?>
    </div>