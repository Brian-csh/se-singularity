<?php
session_start();

$session_info = $_SESSION;
if (isset($session_info['admin'])) header("Location: users.php");

require 'includes/db/connect.php';

$errors = "";
$username = "";
$password = "";

if (isset($_POST['login_click'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === "" || $password === "") {
        $errors = "Please fill in both fields";
    } else {
        $query = "SELECT * FROM user WHERE name = '$username'";
        $result = $conn->query($query);

        $row = $result -> fetch_array(MYSQLI_ASSOC);

        if ($row) {
            if (password_verify($password, $row['password'])) {
              header("Location: users.php");
            } else $errors = "Wrong username or password";
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
    <title>Log på - Dashboard</title>
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
                        <!-- Basic login form-->
                        <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-header justify-content-center"><h3 class="fw-bolder my-4">Log in</h3></div>
                            <div class="card-body">
                                <?php
                                if ($errors != "") echo  '<div class="alert alert-danger" role="alert">
                                ' . $errors . '</div>'
                                ?>
                                <!-- Login form-->
                                <form action="signin.php" method="post">
                                    <!-- Form Group (email address)-->
                                    <div class="mb-3">
                                        <label class="small mb-1 text-light" for="inputUsername">Username</label>
                                        <input class="form-control" id="inputUsername" type="text" placeholder="Enter name" name="username" value="<?=$username?>"/>
                                    </div>
                                    <!-- Form Group (password)-->
                                    <div class="mb-3">
                                        <label class="small mb-1 text-light" for="inputPassword">Password</label>
                                        <input class="form-control" id="inputPassword" type="password" placeholder="Enter password" name="password" />
                                    </div>
                                    <!-- Form Group (login box)-->
                                    <div class="d-flex align-items-center justify-content-center mt-4 mb-0">
                                        <button type="submit" name="login_click" class="btn btn-lg btn-primary" >Log på</button>
                                    </div>
                                </form>
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
