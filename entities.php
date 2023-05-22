<?php
$active = 'Entities';
include "includes/header.php";
include "includes/navbar.php";
// ONLY ACCESSABLE BY SUPER ADMIN
$sql = "SELECT * FROM entity ORDER BY id DESC";
$popupCondition ="undefined";
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

if (isset($_POST['add_entity'])) {
    $name = $_POST['entity_name'];
    $time = time();
    $sql_add_entity = "INSERT INTO entity (name, date_created) 
        VALUES ('$name','$time')";
    // $popupCondition = $conn->query($sql_add_entity) ? "success" : "failure";
    if($conn->query($sql_add_entity)) {
        //TODO : popup success / failure
        $popupCondition = "success";
    } else {
        $popupCondition = "failure";
    }
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
  /* POP-up for new entity */
  .popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  }

  .popup-content {
    text-align: center;
  }
</style>

<!-- <script>
  function showSuccessPopup() {
    document.getElementById("success-popup").style.display = "block";
  }

  // Show failure popup
  function showFailurePopup() {
    document.getElementById("failure-popup").style.display = "block";
  }

  // Hide all popups
  function hideAllPopups() {
    document.getElementById("success-popup").style.display = "none";
    document.getElementById("failure-popup").style.display = "none";
  }

  // Call the appropriate function based on the assigned condition after the page loads
  window.addEventListener("load", function() {
    var popupCondition = "<?php // echo $popupCondition; ?>";
    if (popupCondition === "success") {
      showSuccessPopup();
    } else if(popupCOnidtion === "failure") {
      showFailurePopup();
    } else {
      hideAllPopups();
    }
  });
</script> -->

<!-- Popup for success -->
<!-- <div id="success-popup" class="popup">
  <div class="popup-content">
    <h3>Success</h3>
    <p>Your entity has been added successfully.</p>
  </div>
</div> -->

<!-- Popup for failure -->
<!-- <div id="failure-popup" class="popup">
  <div class="popup-content">
    <h3>Failure</h3>
    <p>Sorry, an error occurred while adding the entity.</p>
  </div>
</div> -->

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
                            <button type="button" class="btn btn-primary btn-xs float-end" data-bs-toggle="modal" data-bs-target="#addEntityModal">+ Add Entity</button>
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
                                $entityid = $row['id'];
                                $name = $row['name'];

                                echo "<tr data-id='$entityid' ><td>$entityid</td><td><a class='text-primary' href='entity.php?id=$entityid'>" . $name . "</a></td>
                                        <td>" . "
                                        <a title=\"User Info\" class=\"btn btn-datatable btn-icon btn-transparent-light\" href='entity.php?id=$entityid'>
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

    <!-- MODAL -->
    <!-- ADD entity Modal -->
    <div class="modal fade" id="addEntityModal" tabindex="-1" role="dialog" aria-labelledby="classAddLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Entity</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="entities.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="entityName">Entity Name *</label>
                            <input class="form-control" id="entityName" type="text" name="entity_name" placeholder="Please input new entity name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" type="submit" name="add_entity">Submit</button>
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
