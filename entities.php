<?php
$sql = "SELECT * FROM entity ORDER BY id DESC";
$active = "Entities";

include "includes/header.php";
include "includes/navbar.php";

$sync_result = 0;
// Handle sync success/error messages
if(isset($_GET['sync_success'])) {
    $sync_result = 1;
    $added_amount = $_GET['sync_success'];
}

if(isset($_GET['sync_error'])) {
    $sync_result = 2;
    $error = $_GET['sync_error'];
}
?>

<style>
  .alert {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
  }
</style>

<?php if ($sync_result == 1 && $added_amount > 0):?>
    <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Sync success!</strong> <?php echo $added_amount?> user(s) added.
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
<?php elseif ($sync_result == 1 && $added_amount <= 0):?>
    <div class='alert alert-primary alert-dismissible fade show' role='alert'>
        <strong>No change</strong> There were no new users to add.
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
<?php elseif ($sync_result == 2):?>
    <div class='alert alert-danger alert-dismissible fade show' role='alert'>
        <strong>Sync error!</strong> $error
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
<?php endif;?>

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
                            <a href="new_entity.php" class="btn btn-primary btn-xs float-end">+ Add entity</a>
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


                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $entity_id = $row['id'];
                                $name = $row['name'];

                                echo "<tr data-id='$entity_id' ><td>$entity_id</td><td><a class='text-primary' href='entity.php?id=$entity_id'>" . $name . "</a></td>
                                        <td>" . "
                                        <a title=\"User Info\" class=\"btn btn-datatable btn-icon btn-transparent-light\" href='entity.php?id=$entity_id'>
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

    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/simple-datatables@4.0.8.js" crossorigin="anonymous"></script>
    <script src="js/datatables/datatables-simple-demo.js"></script>
    <?php
    include "includes/footer.php";
    ?>
