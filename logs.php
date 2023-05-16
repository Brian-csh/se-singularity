<?php
$active = "Logs";

include "includes/header.php";
include "includes/navbar.php";
//TODO : show different log for different user
// superadmin : show all the logs, department, entity

// rm : logs in the department and sub-departemnt, department
// log
$sql = "SELECT * FROM log ORDER BY id DESC";

if($role_id == 3){ // resource manager 
    require "includes/get_subdepartments.php";
    $subdepartmentids = getAllSubdepartmentIds($department_id, $conn);
    $sql = "SELECT * FROM log WHERE department IN (".implode(',', $subdepartmentids).") ORDER BY id DESC";
}
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
                            <th>Department</th>
                            <?php if($role_id == 1){?>
                                <th>Entity</th>
                            <?php }?>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Date</th>
                            <th>Log</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>By</th>
                            <th>Department</th>
                            <?php if($role_id == 1){?>
                                <th>Entity</th>
                            <?php }?>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                //Fetch By - always user
                                $by_id = $row["By"];
                                $by_whom = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$by_id'"))['name'];
                                
                                if($role_id == 2){
                                    $entity = mysqli_fetch_array($conn->query("SELECT entity FROM user WHERE id = '$by_id'"))['entity'];
                                    if($entity != $entity_id)continue;
                                }
                                $date = gmdate("Y.m.d \ | H:i:s", $row["date"]+28800);
                                $log_id = $row["id"];
                                $text = $row["text"];

                                //Fetch Log Type
                                $type_id = $row["log_type"];
                                $type = mysqli_fetch_array($conn->query("SELECT type FROM log_type WHERE id = '$type_id'"))['type'];

                                //Fetch Subject
                                $subject_id = $row["subject"];
                                $subject = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$subject_id'"))['name'];
                                


                                //Fetch Depatment
                                $department_id_ = $row["department"];
                                //? if department is -1, then its `By` is admin or superadmin
                                $department = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$department_id_'"))['name'];
                                                                
                                if($role_id == 1){
                                    //? if department is -1, then its `By` is admin or superadmin
                                    $entity_id_ = mysqli_fetch_array($conn->query("SELECT entity FROM department WHERE id = '$department_id_'"))['entity'];
                                    $entity_name = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id_'"))['name'];

                                echo "<tr data-id='$log_id' >
                                <td class='text-primary'>$date</td>
                                <td class='text-white'>$text</td>
                                <td class='text-white'>$type</td>
                                <td class='text-white'>$subject</td>
                                <td class='text-white'>$by_whom</td>
                                <td class='text-white'>$department</td>
                                <td class='text-white'>$entity_name</td>
                                </tr>";
                                } else {
                                    echo "<tr data-id='$log_id' >
                                    <td class='text-primary'>$date</td>
                                    <td class='text-white'>$text</td>
                                    <td class='text-white'>$type</td>
                                    <td class='text-white'>$subject</td>
                                    <td class='text-white'>$by_whom</td>
                                    <td class='text-white'>$department</td>
                                    </tr>";
                                }
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
