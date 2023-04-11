<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/db/connect.php";
session_start();
$session_info = $_SESSION;
// if ($session_info['admin']['role'] != '1') {
//     header("Location: /signin.php");
//     exit();
// }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?=$active?> - Singularity EAM</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>

</head>

<body class="nav-fixed">
<nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-black border-bottom border-dark" id="sidenavAccordion">
    <button class="btn btn-icon btn-transparent-dark order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle" onclick="document.body.classList.toggle('sidenav-toggled');localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sidenav-toggled'));
"><i data-feather="menu"></i></button>
    <!-- Navbar Brand-->
    <a class="navbar-brand pe-3 ps-3 text-white" href="/index.php"><!-- <img src="assets/img/logo.svg" height="43px"> --> Singularity EAM</a>

    <!-- Navbar Items-->
    <ul class="navbar-nav align-items-center ms-auto">
        <!-- User Dropdown-->
        <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
            <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage" role="button" aria-expanded="false" onclick="toggleDropdownMenu()">
                <img class="img-fluid" src="/assets/img/demo/user-placeholder.svg" />
            </a>
            <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownUserImage">
                <h6 class="dropdown-header d-flex align-items-center">
                    <img class="dropdown-user-img" src="/assets/img/illustrations/profiles/profile-2.png" />
                    <div class="dropdown-user-details">
                        <div class="dropdown-user-details-name"><?=$session_info?></div>
                        <div class="dropdown-user-details-email">Administrator</div>
                    </div>
                </h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#!">
                    <div class="dropdown-item-icon"><i data-feather="user-plus"></i></div>
                    Add Moderator
                </a>
                <a class="dropdown-item" href="/logout.php">
                    <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                    Log ud
                </a>
            </div>
            <ul class="dropdown-menu animated--fade-in-up" id="userDropdownMenu" aria-labelledby="navbarDropdownUserImage">
                <li><a class="dropdown-item" href="/settings.php">Settings</a></li>
                <li>
                    <!-- Feishu Binding -->
                    <!-- TODO: Disappear this item after binding with 飞书 -->
                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#feishuBindModal" style="cursor : pointer;">
                        Bind to 飞书<img class="img-fluid" width = "16" height= "16" alt="image description" src="/assets/img/feishu_logo.png" />
                    </a>
                </li>
                <li><a class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#logoutModal" style="cursor : pointer;">Logout</a></li>
            </ul>
            <script>
            function toggleDropdownMenu() {
                var dropdownMenu = document.getElementById("userDropdownMenu");
                if (dropdownMenu.style.display === "block") {
                    dropdownMenu.style.display = "none";
                } else {
                dropdownMenu.style.display = "block";
                }
            }
            </script>
        </li>
    </ul>
</nav>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalTitle">Log out</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Are you sure you want to log out?</div>
            <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button><a href="/logout.php" class="btn btn-danger" >Log out</a></div>
        </div>
    </div>
</div>

<!-- Feishu Bind Modal -->
<div class="modal fade" id="feishuBindModal" tabindex="-1" role="dialog" aria-labelledby="feishuBindTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feishuBindTitle">Feishu Binding</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Bind this account to a Feishu account?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                    Cancel
                </button>
                <form id="feishu-bind" action="signin.php" method="post">
                    <button type="submit" name="feishu-bind-click" class="btn btn-primary" style = "background:green; border:none">
                        Bind
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
