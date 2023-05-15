<?php
$active = "Dashboard";

//header("Location: users.php"); // TODO: uncommment when the dashboard will be ready

include "includes/header.php";
include "includes/navbar.php";
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
}

if (isset($_GET['role'])) {
    $user_role = $_GET['role'];
}

if (isset($_GET['entityid'])) {
    $entity_id = $_GET['entityid'];
}
if (isset($_GET['departmentid'])) {
    $department_id = $_GET['departmentid'];
}

?>

<div id="layoutSidenav_content">
    <main>
        <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
            <div class="container-xl px-4">
                <div class="page-header-content pt-4">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mt-4">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="activity"></i></div>
                                Dashboard
                            </h1>
                            <div class="page-header-subtitle">Welcome to Singularity EAM</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main page content-->
    </main>

    <!-- Individual scripts -->
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>

    <?php include "includes/footer.php"; ?>
</div>
