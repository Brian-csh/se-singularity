<?php
$active = "Customize Navbar";
include "includes/header.php";
include "includes/navbar.php";

// select all the departments for the entity
$sql_navbar = "SELECT navbar FROM entity WHERE id='$entity_id'";
$result = $conn->query($sql_navbar);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $row = $result->fetch_assoc();
        $navbar_json = $row['navbar'];
        $navbar = json_decode($navbar_json);
    }
}
//todo if navbar is empty
?>

<html>
<div id="layoutSidenav_content">
    <div class="container-fluid pt-5 px-4">
        <?php if (isset($navbar) && isset($navbar['admin'])): ?>
            <div class="card">
                <div class="card-body">
                    <table id="datatablesSimple" style="display: none">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>URL</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Label</th>
                                <th>URL</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                                if (isset($navbar)) {
                                    $admin_items = $navbar['admin'];
                                    foreach ($admin_items as $item) {
                                        $label = $item['label'];
                                        $url = $item['url'];
                                        echo "<tr><td>$label</td><td>$url</td></tr>";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        <?php if (isset($navbar) && isset($navbar['rm'])): ?>
            <!-- for resource manager -->
            <div class="card">
                <div class="card-body">
                    <table id="datatablesSimple" style="display: none">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>URL</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Label</th>
                                <th>URL</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                                if (isset($navbar)) {
                                    $rm_items = $navbar['rm'];
                                    foreach ($rm_items as $item) {
                                        $label = $item['label'];
                                        $url = $item['url'];
                                        echo "<tr><td>$label</td><td>$url</td></tr>";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        <?php if (isset($navbar) && isset($navbar['user'])): ?>
            <!-- for user -->
            <div class="card">
                <div class="card-body">
                    <table id="datatablesSimple" style="display: none">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>URL</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Label</th>
                                <th>URL</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                                if (isset($navbar)) {
                                    $user_items = $navbar['user'];
                                    foreach ($user_items as $item) {
                                        $label = $item['label'];
                                        $url = $item['url'];
                                        echo "<tr><td>$label</td><td>$url</td></tr>";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        <!-- Individual scripts -->
        <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"
        />
        <link rel="stylesheet" href="css/multiselect.css" />
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <script src="includes/multiselect_search_class.js"></script>

        <?php include "includes/footer.php"; ?>
    </div>
</div>
</html>