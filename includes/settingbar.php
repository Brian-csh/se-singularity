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
                    <div class="sidenav-menu-heading">General</div>
                    <!-- <a class="nav-link" href="/account_settings.php">
                        <div class="nav-link-icon"><i data-feather="settings"></i></div>
                        Account Settings
                    </a> -->

                    <a class="nav-link" href="/notification_settings.php">
                        <div class="nav-link-icon"><i data-feather="bell"></i></div>
                        Notifications
                    </a>
                    <!-- <a class="nav-link" href="/interface_settings.php">
                        <div class="nav-link-icon"><i data-feather="grid"></i></div>
                        Interface
                    </a> -->
                    <!-- <div class="sidenav-menu-heading">Advanced</div>
                    <a class="nav-link" href="/login_and_security_settings.php">
                        <div class="nav-link-icon"><i data-feather="lock"></i></div>
                        Login & Security
                    </a> -->
                    
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
        if (page === "settings.php" || page === '') {
            document.getElementsByClassName("nav-link")[0].classList.add("active");
        } else if ((new URL(document.location)).searchParams.get('only_coaches')) {
            document.querySelectorAll("a[href='/" + page +"?only_coaches=true'")[0].classList.add("active");
        } else if ((new URL(document.location)).searchParams.get('salto_fail')) {
            document.querySelectorAll("a[href='/" + page +"?salto_fail=true'")[0].classList.add("active");
        } else {
            document.querySelectorAll("a[href='/" + page +"']")[0].classList.add("active");
        }
    </script>
