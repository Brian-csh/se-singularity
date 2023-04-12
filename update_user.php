<?php
include "includes/db/connect.php";

//handle post requests to update user account details
if (isset($_POST['submit_changes'])) {
    $user_id = $_POST['id'];
    $role_id = $_POST['role'];
    $locked = isset($_POST['lock_account']) ? 1 : 0;

    if (isset($_POST['password']) and $_POST['password'] !== "") {
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE user SET password = '$hashed_password', role = '$role_id', locked = '$locked' WHERE id = '$user_id'";
        if ($conn->query($sql)) {
            header('Location: users.php');
        } else {
            header("Location: edit_user.php?id=$user_id&insert_error");
        }
    } else  {
        $sql = "UPDATE user SET role = '$role_id', locked = '$locked' WHERE id = '$user_id'";
        if ($conn->query($sql)) {
            header('Location: users.php');
        } else {
            header("Location: edit_user.php?id=$user_id&insert_error");
        }
    }
}