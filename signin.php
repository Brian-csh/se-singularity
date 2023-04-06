<?php
session_start();

$session_info = $_SESSION;
if (isset($session_info['admin'])) header("Location: users.php");

require 'includes/db/connect.php';

$errors = "";
$username = "";
$password = "";

// TODO: find a better and more secure way to store API credentials
// TODO: conditional - local vs deployment for redirect
$feishu_app_id = "cli_a4a8e931cd79900e";
$feishu_app_secret = "7Q1Arabz1qImkNpLOp2D9coj5cXp1ufJ";
$singularity_redirect = "https://singularity-eam-singularity.app.secoder.net/callback.php"; // "http://localhost/singularity-eam/callback.php";
$feishu_redirect = "https://passport.feishu.cn/suite/passport/oauth/authorize?client_id=".$feishu_app_id."&redirect_uri=".$singularity_redirect."&response_type=code&state=";

function redirect($url) {
    header('Location: '.$url);
    die();
}

// Handle normal login form submit
if (isset($_POST['normal-login_click'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $time_now = time();
    $type = "login";
    $text = "test logs";
    // TODO : specify type after log_type table is done
    //$type = ;

    if ($username === "" || $password === "") {
        $errors = "Please fill in both fields";
    } else {
        $query = "SELECT * FROM user WHERE name = '$username'";
        $result = $conn->query($query);
        $row = $result -> fetch_array(MYSQLI_ASSOC);

        if ($row) {
            if (password_verify($password, $row['password'])) {
                // TODO: add role based session parameters
                $_SESSION['admin'] = $row;
                // TODO: add log_type 
                $sql = "INSERT INTO log (date, text, log_type) VALUES 
                ('$time_now','$text','$type')";
                if( $conn->query($sql)){
                    echo "Records inserted successfully.";
                } else{
                    echo "ERROR: Could not able to execute $sql. " . $conn->error;
                }
                header("Location: index.php");
            } else $errors = "Wrong password";
        } else $errors = "Wrong username or password";

        // Free result set
        $result->free_result();
        $conn->close();
    }
}

// Handle feishu login form submit
if (isset($_POST['feishu-login_click'])) {
    redirect($feishu_redirect);
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
                                <!-- begin switch button-->
                                <div class="row">
                                    <label type="text" class="col-6 btn text-center text-light" onclick="showNormalLoginForms()" id="normal-login-label">Log in with Singularity</label>
                                    <label type="text" class="col-6 btn text-center" onclick="showFeishuLoginForms()" id="feishu-login-label">Log in with 飞书</label>
                                </div>
                                <!-- end switch button-->
                                <!-- Begin Login form container -->
                                <div style = "overflow-x: auto; white-space: nowrap; overflow: hidden;">
                                    <!-- normal login form -->
                                    <div style="display: inline-block;  width:100%;">
                                        <form id="normal-login" action="signin.php" method="post" style="height: 300px">
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
                                    </div>
                                    <!-- end normal login form -->
                                    <!-- feishu login form -->
                                    <div style="display: inline-block; width: 100%;" >
                                        <form id="feishu-login" action="signin.php" method="post">
                                            <!-- Form Group (login box)-->
                                            <div class="d-flex align-items-center justify-content-center mt-3 mb-0">

                                                <button type="submit" name="feishu-login_click" class="btn btn-lg text-light" >Scan code or click to login with 飞书</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!--end feishu login form -->
                                </div>
                                <!-- End Login form container -->
                                <!-- JavaScript to show/unshow the login forms -->
                                <script>

                                function showNormalLoginForms() {
                                    document.getElementById("normal-login").style.display = "block";
                                    document.getElementById("feishu-login").style.display = "none";
                                    document.getElementById("normal-login-label").classList.add("text-light");
                                    document.getElementById("feishu-login-label").classList.remove("text-light");
                                    document.getElementById("normal-login").scrollIntoView({behavior: "smooth"});
                                }

                                function showFeishuLoginForms() {
                                    document.getElementById("normal-login").style.display = "none";
                                    document.getElementById("feishu-login").style.display = "block";
                                    document.getElementById("normal-login-label").classList.remove("text-light");
                                    document.getElementById("feishu-login-label").classList.add("text-light");
                                    document.getElementById("feishu-login").scrollIntoView({behavior: "smooth"});
                                }
                                </script>
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
