<?php


?>
<div>
    <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
        <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownNotificationImage" role="button"
            aria-expanded="false" onclick="toggleNotificationDropdown()">
            <img class="img-fluid" src="/assets/img/demo/bell.svg" />
        </a>
        <ul class="dropdown-menu animated--fade-in-up" id="userDropdownMenu" aria-labelledby="navbarDropdownUserImage">
                <li><a class="dropdown-item dropdown-settings-header" href="/settings.php">Settings</a></li>
                <li>
                    <!-- Feishu Binding -->
                    <?php if (!$feishu_binded): ?>
                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#feishuBindModal">
                                Bind to 飞书<img class="img-fluid" width = "16" height= "16" alt="image description" src="/assets/img/feishu_logo.png" />
                            </a>
                    <?php else: ?>
                            <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#feishuUnbindModal">
                                Unbind from 飞书<img class="img-fluid" width = "16" height= "16" alt="image description" src="/assets/img/feishu_logo.png" />
                            </a>
                    <?php endif; ?>
                </li>
                <li><a class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#logoutModal" style="cursor : pointer;">Logout</a></li>
            </ul>
    </li>
    <script>
        function toggleNotificationDropdown() {
            var dropdownMenu = document.getElementById("notificationDropdownMenu");
            if (dropdownMenu.style.display === "block") {
                dropdownMenu.style.display = "none";
            } else {
                dropdownMenu.style.display = "block";
            }
        }
    </script>

</div>