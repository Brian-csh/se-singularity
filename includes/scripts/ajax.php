<?php

require "/var/www/html/control.open-spaceapp.com/includes/db/connect.php";
require "/var/www/html/control.open-spaceapp.com/includes/classes/SaltoSocket.php";

$time_now = time();
$saltoSocket = new SaltoSocket();

// Function to add the user to the salto
if ($_POST['request_type'] == "salto_add") {
    // Get user id from the post array
    $user_id = $_POST['id'];

    // Get all user data from the database
    $get_user_data = "SELECT * FROM users WHERE id = '$user_id'";
    $user_data = mysqli_fetch_array($conn->query($get_user_data));


    // Send the SALTO Update websocket message
    try {
        $result = $saltoSocket->add_user($user_data['id'], $user_data['group_id'], $user_data['expiration_date'],
            $user_data['phone'], $user_data['first_name'], $user_data['last_name'], $user_data['birthday'],
            $user_data['email'], $user_data['gender'],$user_data['city'], $user_data['zipcode']);
        echo "Success";

        // logging
        $english = "Sent Salto Update request: <b>" . $result;
        $danish = "Sendt anmodning om Salto-opdatering: <b>" . $result;
        $sql = "INSERT into logs (date, danish, english) VALUES ('', '$danish', '$english')";
        $result = $conn -> query($sql);
    } catch (RuntimeException $e) {
        echo "Error: {$e->getMessage()}" . PHP_EOL;
    }

}

// Function to remove the user from the salto
if ($_POST['request_type'] == "salto_remove") {
    // Get user id from the post array
    $user_id = $_POST['id'];

    // Get users phone from the database
    $get_user_data = "SELECT phone FROM users WHERE id = '$user_id'";
    $user_data = mysqli_fetch_array($conn->query($get_user_data));


    // Send the SALTO Remove websocket message
    try {
        $result = $saltoSocket->delete_user($user_data['phone']);
        echo "Success";
        $english = "Sent Salto Suspend request: <b>" . $result;
        $danish = "Sendt Salto Suspend anmodning: <b>" . $result;
        $sql = "INSERT into logs (date, danish, english) VALUES ('$time_now', '$danish', '$english')";
        $result = $conn->query($sql);
    } catch (RuntimeException $e) {
        echo "Error: {$e->getMessage()}" . PHP_EOL;
    }
}

// Salto fail in user.php
if ($_POST['request_type'] == "salto_fail") {
    $user_id = $_POST['id'];
    if ($_POST['checked'] == 'false') {
        $conn -> query("UPDATE users SET status = '1' WHERE id = '$user_id'");
        print_r("Fail removed");
    } else if ($_POST['checked'] == 'true') {
        $conn -> query("UPDATE users SET status = '0' WHERE id = '$user_id'");
        print_r("Marked as fail");
    }
}