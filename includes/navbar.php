<?php

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
                    <!-- Sidenav Menu Heading (Core)-->
                    <div class="sidenav-menu-heading">Main</div>
                    <a class="nav-link" href="/index.php">
                        <div class="nav-link-icon"><i data-feather="activity"></i></div>
                        Dashboard
                    </a>
                    <!-- Sidenav Heading (Users)-->
                    <div class="sidenav-menu-heading">Data</div>
                    <a class="nav-link" href="/users.php">
                        <div class="nav-link-icon"><i data-feather="user"></i></div>
                        Users
                    </a>
                    <!-- Sidenav Heading (Entities)-->
                    <a class="nav-link" href="/entities.php">
                        <div class="nav-link-icon"><i data-feather="home"></i></div>
                        Entities
                    </a>
                    <!-- Sidenav Heading (Entities)-->
                    <a class="nav-link" href="/logs.php">
                        <div class="nav-link-icon"><i data-feather="list"></i></div>
                        Logs
                    </a>
                    <!-- Sidenav Heading (Assets)-->
                    <a class="nav-link" href="/assets.php">
                        <div class="nav-link-icon"><i data-feather="package"></i></div>
                        Assets
                    </a>
                </div>
            </div>
            <!-- Sidenav Footer-->
            <div class="sidenav-footer">
                <div class="sidenav-footer-content">
                    <div class="sidenav-footer-subtitle">Logget ind som:</div>
                    <div class="sidenav-footer-title"><?= /** @var array $session_info */
                        $session_info['admin']['username']?></div>
                </div>
            </div>
        </nav>
    </div>
    <script>
        let path = window.location.pathname;
        let page = path.split("/").pop();
        if (page === "index.php" || page === '') {
            document.getElementsByClassName("nav-link")[0].classList.add("active");
        } else if ((new URL(document.location)).searchParams.get('only_coaches')) {
            document.querySelectorAll("a[href='/" + page +"?only_coaches=true'")[0].classList.add("active");
        } else if ((new URL(document.location)).searchParams.get('salto_fail')) {
            document.querySelectorAll("a[href='/" + page +"?salto_fail=true'")[0].classList.add("active");
        } else {
        document.querySelectorAll("a[href='/" + page +"']")[0].classList.add("active");
        }
    </script>