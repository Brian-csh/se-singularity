<?php
session_start();

$session_info = $_SESSION;
if (isset($session_info['admin'])) header("Location: users.php");

require 'includes/db/connect.php';
include 'functions.php';
$errors = "";
$username = "";
$password = "";

// Handle feishu login form submit
if (isset($_POST['feishu-login_click'])) {
    $mode = "signin";
    include 'feishu_redirect.php';
}

// Handle feishu failed logins
if(isset($_GET['signin'])) {
    $signin_status = $_GET['signin'];
    if ($signin_status == "403") {
        $errors = "Unable to login with Feishu. User does not exist.";
    }
}

// Handle feishu bind
if (isset($_POST['feishu-bind-click'])) {
    $mode = "bind";
    include 'feishu_redirect.php';
}

// Handle normal login form submit
if (isset($_POST['normal-login_click'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === "" || $password === "") {
        $errors = "Please fill in both fields";
    } else {
        $query = "SELECT * FROM user WHERE name = '$username'";
        $result = $conn->query($query);
        $row = $result -> fetch_array(MYSQLI_ASSOC);
        
        if ($row) {
            if (isset($row['locked']) && $row['locked']) {
                $errors = "Account is locked";
            } else if (password_verify($password, $row['password'])) {
                // TODO: add role based session parameters
                $_SESSION['user']['id'] = $row['id'];
                $_SESSION['user']['name'] = $row['name'];
                $_SESSION['user']['role'] = $row['role'];
                $_SESSION['user']['feishu_id'] = $row['feishu_id'];
                $_SESSION['user']['entity'] = $row['entity'];
                $_SESSION['user']['department'] = $row['department'];

                // Insert log
                insert_log($conn,$row,$username,1);

                header("Location: index.php");
            } else $errors = "Wrong password";
        } else $errors = "Wrong username or password";

        // Free result set
        $result->free_result();
        $conn->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Log in - Dashboard</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.png" />
    <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
<div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
        <main>
            <div class="container-xl px-4">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-header justify-content-center"><h3 class="fw-bolder my-4">Log in</h3></div>
                            <div class="card-body">
                                <?php
                                if ($errors != "") echo  '<div class="alert alert-danger" role="alert">
                                ' . $errors . '</div>'
                                ?>
                                <!-- Begin Login form container -->
                                    <!-- BEGIN normal login form -->
                                        <form id="normal-login" action="signin.php" method="post">
                                            <!-- Form Group (username)-->
                                            <div class="mb-3">
                                                <label class="small mb-1 text-light" for="inputUsername-normal">Username</label>
                                                <input class="form-control" id="inputUsername-normal" type="text" placeholder="Enter username" name="username" value="<?=$username?>"/>
                                            </div>
                                            <!-- Form Group (password)-->
                                            <div class="mb-3">
                                                <label class="small mb-1 text-light" for="inputPassword-normal">Password</label>
                                                <input class="form-control" id="inputPassword-normal" type="password" placeholder="Enter password" name="password" />
                                            </div>
                                            <!-- Form Group (login box)-->
                                            <div class="d-flex align-items-center justify-content-center mt-3 mb-0">
                                                <button type="submit" name="normal-login_click" class="btn btn-lg btn-primary" >Log in</button>
                                            </div>
                                        </form>
                                    <!-- END normal login form -->
                                    <hr class="mt-5" style="height: 3px; color: white;">
                                    <!-- BEGIN feishu login form -->
                                    <div>
                                        <form id="feishu-login" action="signin.php" method="post">
                                            <!-- Form Group (login box)-->
                                            <div class="d-flex align-items-center justify-content-center">
                                                <button type="submit" name="feishu-login_click" class="btn btn-lg btn-primary text-light" >Login with 飞书</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!--END feishu login form -->
                                </div>
                                <!-- End Login form container -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div id="layoutAuthentication_footer">
        <footer class="footer-admin mt-auto footer-dark">
            <div class="container-xl px-4">
                <div class="row">
                    <div class="col-md-6 small">Copyright &copy; Singularity EAM <?=date("Y");?></div>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
</body>
</html>
