<?php
$active = "Logs";

include "includes/header.php";
include "includes/navbar.php";

//TODO : show different log for different user
// superadmin : show all the logs
// admin : logs in the entity
// rm : logs in the department and sub-departemnt
// user : logs in the department
// log
$sql = "SELECT * FROM log ORDER BY id DESC";
//TODO:
// if($role_id == 3) $departments = getAllSubdepartentIds($department_id, $conn);
?>

<div id="layoutSidenav_content">
    <main>
        <header class="page-header page-header-compact page-header-light border-bottom bg-black mb-4">
            <div class="container-fluid px-4">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3">
                            <h1 class="page-header-title text-white">
                                <div class="page-header-icon text-white"><i data-feather="align-left"></i></div>
                                <?=$active?>
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
                    <div id="tablePreloader">
                        <p class="text-white p-3">Loading...</p >
                    </div>
                    <table id="datatablesSimple" style="display: none">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Log</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>By</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Date</th>
                            <th>Log</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>By</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $date = gmdate("Y.m.d \ | H:i:s", $row["date"]+28800);
                                $log_id = $row["id"];
                                $text = $row["text"];
                                //Fetch Log Type
                                $type_id = $row["log_type"];
                                $type = mysqli_fetch_array($conn->query("SELECT type FROM log_type WHERE id = '$type_id'"))['type'];

                                //Fetch Subject
                                $subject_id = $row["subject"];
                                if($type_id>=1 && $type_id <=3){
                                    $subject = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$subject_id'"))['name'];
                                    // SUPER ADMIN shows all the logs 
                                    if($role_id==2){ // ADMIN
                                        $entity = mysqli_fetch_array($conn->query("SELECT entity FROM user WHERE id = '$subject_id'"))['entity'];
                                        if($entity != $entity_id) continue;
                                    } else if ($role_id==3){ // RESOURCE MANAGER
                                        $department = mysqli_fetch_array($conn->query("SELECT department FROM user WHERE id = '$subject_id'"))['department'];
                                        //TODO: if department is not in sub-departments, continue;
                                    } else { // user
                                        $department = mysqli_fetch_array($conn->query("SELECT department FROM user WHERE id = '$subject_id'"))['department'];
                                        if($department != $department_id) continue;
                                    }
                                } else {
                                    $subject = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$subject_id'"))['name'];
                                    $department = mysqli_fetch_array($conn->query("SELECT department FROM asset WHERE id = '$subject_id'"))['department'];
                                    // if($department != $department_id&& $department_id!=-1) continue;
                                }

                                //Fetch By - always user
                                $by_id = $row["By"];
                                $by_whom = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$by_id'"))['name'];

                                echo "<tr data-id='$log_id' >
                                <td class='text-primary'>$date</td>
                                <td class='text-white'>$text</td>
                                <td class='text-white'>$type</td>
                                <td class='text-white'>$subject</td>
                                <td class='text-white'>$by_whom</td>
                                </tr>";
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
