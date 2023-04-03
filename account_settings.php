<?php
$active = "account_settings";

include "includes/header.php";
include "includes/settingbar.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <div class = "card">
                <div class="card-body">
                <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                    <h4>Account Settings</h4>
                    </div>
                    <div class="card-body">
                    <div class="form-group">
                        <label for="profile_pic">Profile Picture</label>
                        <br>
                        <img src="https://via.placeholder.com/150" id="profile_pic_preview" alt="Profile Picture" width="150" height="150">
                        <br>
                        <label style="font-size: 12px; padding: 10px 10px;" for="profile_pic" class="btn btn-primary mt-2">Upload profile picture</label>
                        <input type="file" id="profile_pic" name="profile_pic" class="form-control-file mt-3" accept = "image/*" style="display:none;">
                    </div>
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" class="form-control" placeholder="Enter your phone number">
                    </div>
                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-control" placeholder="Enter your bio" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" onClick="">Update Profile</button>
                        <button type="reset" class="btn btn-secondary ml-2">Reset</button>
                    </div>
                    </div>
                </div>
                </div>
                </div>
            </div>
        </div> 
    </main>



    <!-- Individual scripts -->
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>

    <?php include "includes/footer.php"; ?>
</div>

