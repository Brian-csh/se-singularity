<?php
$user_id = $_SESSION['user']['id'];
$user_name = $_SESSION['user']['name'];
$role_id = $_SESSION['user']['role'];
$entity_id = $_SESSION['user']['entity'] ? $_SESSION['user']['entity'] : -1;
$department_id = $_SESSION['user']['department'] ? $_SESSION['user']['department'] : -1;
?>

<style>
    .disabled {
        color: #545252 !important;
    }

    #saltoFail {
        background: rgb(var(--bs-primary-rgb));
        margin-left: 0.5rem;
    }
</style>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sidenav shadow-right sidenav-dark">
            <div class="sidenav-menu">
                <div class="nav accordion" id="accordionSidenav">
                    <!-- SupeAdmin -->
                    <?php if($role_id == 1) { ?>
                        <!-- Sidenav Menu Heading (Core)-->
                        <div class="sidenav-menu-heading">Main</div>
                        <a class="nav-link" href="/index.php">
                            <div class="nav-link-icon"><i data-feather="activity"></i></div>
                            Dashboard
                        </a> 
                        <div class="sidenav-menu-heading">Data</div>
                        <!-- Sidenav Heading (Entities)-->
                        <a class="nav-link" href="/entities.php">
                            <div class="nav-link-icon"><i data-feather="home"></i></div>
                            Entities
                        </a>   
                        <!-- Sidenav Heading (Users)-->
                        <a class="nav-link" href="/users.php">
                            <div class="nav-link-icon"><i data-feather="user"></i></div>
                            Users
                        </a>          
                    <?php } else if($role_id == 2){?>
                    <!-- Admin -->
                        <div class="sidenav-menu-heading">Main</div>
                        <a class="nav-link" href="/index.php">
                            <div class="nav-link-icon"><i data-feather="activity"></i></div>
                            Dashboard
                        </a>
                        <!-- Sidenav Heading (Entities)-->
                        <div class="sidenav-menu-heading">Data</div>
                        <a class="nav-link" href= <?="/entity.php"?>>
                            <div class="nav-link-icon"><i data-feather="home"></i></div>
                            Departments
                        </a>   
                        <!-- Sidenav Heading (Users)-->
                        <a class="nav-link" href="/users.php">
                            <div class="nav-link-icon"><i data-feather="user"></i></div>
                            Users
                        </a>  
                        <!-- Sidenav Heading (logs)-->
                        <a class="nav-link" href="/logs.php">
                            <div class="nav-link-icon"><i data-feather="log-in"></i></div>
                            Logs
                        </a>
                    <?php } else if($role_id == 3){?>
                        <!-- Sidenav Menu Heading (Core)-->
                        <div class="sidenav-menu-heading">Main</div>
                        <a class="nav-link" href="/index.php">
                            <div class="nav-link-icon"><i data-feather="activity"></i></div>
                            Dashboard
                        </a>
                        <!-- Sidenav Heading (Department and sub-departments)-->
                        <div class="sidenav-menu-heading">Data</div>
                        <a class="nav-link" href="/department.php">
                            <div class="nav-link-icon"><i data-feather="home"></i></div>
                            Department
                        </a>
                        <!-- Sidenav Heading (Users)-->
                        <a class="nav-link" href="/users.php">
                            <div class="nav-link-icon"><i data-feather="user"></i></div>
                            Users
                        </a>
                        <!-- Sidenav Heading (logs)-->
                        <a class="nav-link" href="/logs.php">
                            <div class="nav-link-icon"><i data-feather="log-in"></i></div>
                            Logs
                        </a>
                        <!-- Sidenav Heading (Assets)-->
                        <a class="nav-link" href="/assets.php">
                            <div class="nav-link-icon"><i data-feather="package"></i></div>
                            Assets
                        </a>
                        <!-- Sidenav Heading (Requests)-->
                        <a class="nav-link" href= "/requests.php">
                            <div class="nav-link-icon"><i data-feather="git-pull-request"></i></div>
                            Requests
                        </a>
                    <?php } else {  ?>
                        <!-- Sidenav Menu Heading (Core)-->
                        <div class="sidenav-menu-heading">Main</div>
                        <a class="nav-link" href="/index.php">
                            <div class="nav-link-icon"><i data-feather="activity"></i></div>
                            Dashboard
                        </a>
                        <!-- Sidenav Heading (Assets)-->
                        <div class="sidenav-menu-heading">Data</div>
                        <a class="nav-link" href="/assets.php">
                            <div class="nav-link-icon"><i data-feather="package"></i></div>
                            Assets
                        </a>
                        <!-- Sidenav Heading (Requests)-->
                        <a class="nav-link" href="/requests.php">
                            <div class="nav-link-icon"><i data-feather="git-pull-request"></i></div>
                            Requests
                        </a>
                    <?php } ?>
                </div>
            </div>
            <!-- Sidenav Footer-->
            <div class="sidenav-footer">
                <div class="sidenav-footer-content">
                    <div class="sidenav-footer-subtitle">Logged in as:</div>
                    <div class="sidenav-footer-title"><?= /** @var array $session_info */
                        $session_info['name']?></div>
                </div>
            </div>
        </nav>
    </div>
    <script>
        let path = window.location.pathname;
        let page = path.split("/").pop();

        if (page === "index.php" || page === '') {
            document.getElementsByClassName("nav-link")[0].classList.add("active");
        } else {
            let links = document.querySelectorAll("a[href='/" + page +"']");

            console.log(links)
            // Toggle the class only if it's in the
            if (links.length > 0 && !links[0].classList.contains("dropdown-settings-header")) {
                links[0].classList.add("active");
            }

        }
    </script>
