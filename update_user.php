<?php
include "includes/db/connect.php";
//handle post requests
if (isset($_POST['submit_changes'])) {
    $user_id = $_POST['id'];
    $role_id = $_POST['role'];
    $password = $_POST['password'];
    $reenter_password = $_POST['reenter_password'];
    $locked = (isset($_POST['lock_account'])) ? 1 : 0;

    $valid_password = true;

    if (strcmp($password, $reenter_password) != 0) {
        $valid_password = false;
    }

    if ($valid_password) {
        $sql = "UPDATE user SET password = '$password', role = '$role_id', locked = '$locked' WHERE id = '$user_id'";
        if ($conn->query($sql)) {
            header('Location: users.php');
        } else {
            header("Location: edit_user.php?id=$user_id&insert_error");
        }
    } else {
        header("Location: edit_user.php?id=$user_id&insert_error");
    }
}