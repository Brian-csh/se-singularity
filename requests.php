<?php
// $sql = "SELECT * FROM asset ORDER BY id DESC";
$active = "Assets";

include "includes/header.php";
include "includes/navbar.php";

if (isset($_GET['departmentid'])) {
    $departmentid = $_GET['departmentid'];
} else {
    $departmentid = -1;
}
$department_id = $_SESSION['user']['department'] ? $_SESSION['user']['department'] : -1;
$user_id = $_SESSION['user']['id'];
$user_role_id = $_SESSION['user']['role'];
$entity_id = $_SESSION['user']['entity'];
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
                                <th>Initiator</th>
                                <th>Participant</th>
                                <th>Asset</th>
                                <th>Request Type</th>
                                <th>Result</th>
                                <th>Request_time</th>
                                <th>Review_time</th>
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

<!-- Modals -->
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
            var table = $('#myTable').DataTable({
                ordering: true,
                searching: true,
                processing: true,
                serverSide: true,
                searchDelay: 500,
                ajax: {
                    url: "includes/scripts/datatables_requests.php",
                    data: function(d) {
                        d.department_id = <?=  $department_id ?>;
                        d.user_id = <?= $_SESSION['user']['id'] ?>;
                        d.user_role_id = <?= $_SESSION['user']['role'] ?>;
                        d.entity_id = <?= $_SESSION['user']['entity']?>;
                    }
                },
                columns: [{
                        "data": "id"
                    },
                    {
                        "data": "initiator"
                    },
                    {
                        "data": "participant"
                    },
                    {
                        "data": "asset"
                    },
                    {
                        "data": "type"
                    },
                    {
                        "data": "result"
                    },
                    {
                        "data": "request_time"
                    },
                    {
                        "data": "review_time"
                    }
                ],
                select: {
                    style: 'multi'
                },
                buttons: [
                    {
                        text : 'Approve',
                        action: function(e, dt, node, config) {
                            var selectedRows = dt.rows({
                                selected: true
                            }).data().toArray();
                            var requestIds = selectedRows.map(function(row) {
                                return row.id;
                            });
                            // console.log($_SESSION);
                            $.ajax({
                                url: "includes/scripts/approve_requests.php",
                                method: "POST",
                                data: {
                                    requestIds: requestIds,
                                    user_id : <?=$user_id?>
                                },
                                success: function(response) {
                                    console.log(response);
                                            // Perform any additional actions on success
                                            var data = JSON.parse(response);
                                            for (var i = 0; i<data.result.length; i++){
                                                console.log(data.result[i]);
                                                if(data.result[i][1] === false){ // fail
                                                    // fetch asset name
                                                    alert("Request " + data.result[i][0] + " is not available for approve. You can only approve pending requests.");
                                                } else { // Succeess
                                                    alert("Request " + data.result[i][0] + " approved successfully!.")
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