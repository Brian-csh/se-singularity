<?php
// $sql = "SELECT * FROM asset ORDER BY id DESC";
$active = "Requests";

include "includes/header.php";
include "includes/navbar.php";
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
                                <th>Request for</th>
                                <th>Result</th>
                                <th>Requested time</th>
                                <th>Reviewed time</th>
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


    <!-- Styles for DataTables buttons -->
    <style>
        button.dt-button.approve-button {
            color: white !important;
            background: green !important;
            border-radius: 20px !important;
        }
        button.dt-button.reject-button {
            color: white !important;
            background: red !important;
            border-radius: 20px !important;
        }
    </style>

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
                        d.userid = <?= $user_id ?>;
                        d.roleid = <?= $role_id ?>;
                        d.entityid = <?= $entity_id?>;
                        d.departmentid = <?=  $department_id ?>;
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
                <?php if($role_id == 3){?>
                select: {
                    style: 'multi'
                },
                <?php }?>
                buttons: [
                    <?php if($role_id == 3){?>
                    {
                        text : 'Approve',
                        className: 'approve-button',
                        action: function(e, dt, node, config) {
                            var selectedRows = dt.rows({
                                selected: true
                            }).data().toArray();
                            var requestIds = selectedRows.map(function(row) {
                                return row.id;
                            });
                            // console.log($_SESSION);
                            $.ajax({
                                url: "includes/scripts/handle_requests.php",
                                method: "POST",
                                data: {
                                    requestIds: requestIds,
                                    user_id : <?=$user_id?>,
                                    handle_type : 1
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
                    },
                    {
                        text : 'Reject',
                        className : 'reject-button',
                        action: function(e, dt, node, config) {
                            var selectedRows = dt.rows({
                                selected: true
                            }).data().toArray();
                            var requestIds = selectedRows.map(function(row) {
                                return row.id;
                            });
                            // console.log($_SESSION);
                            $.ajax({
                                url: "includes/scripts/handle_requests.php",
                                method: "POST",
                                data: {
                                    requestIds: requestIds,
                                    user_id : <?=$user_id?>,
                                    handle_type : 2
                                },
                                success: function(response) {
                                    console.log(response);
                                            // Perform any additional actions on success
                                            var data = JSON.parse(response);
                                            for (var i = 0; i<data.result.length; i++){
                                                console.log(data.result[i]);
                                                if(data.result[i][1] === false){ // fail
                                                    // fetch asset name
                                                    alert("Request " + data.result[i][0] + " is not available for reject. You can only reject pending requests.");
                                                } else { // Succeess
                                                    alert("Request " + data.result[i][0] + " rejected successfully!.")
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
                    <?php }?>
                ],
                <?php if($role_id == 3){?>
                    dom: 'Bfrtip' // Add this line to display buttons
                <?php }?>

            });
        });
    </script>
    
    <!-- For Request Modal -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->

    <?php
    include "includes/footer.php";
    ?>