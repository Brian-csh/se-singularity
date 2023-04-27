<?php
function insert_log_login($conn,$row,$type_id)
{                  
    /*  BEGIN INSERT LOG */
    $time_now = time();
    $username = $row['name'];
    $user_id = $row['id'];
    //Fetch Entity, Role, Department
    $entity_id = $row['entity'];
    $role_id = $row['role'];
    $department_id = $row['department'];

    $entity = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];
    $role = mysqli_fetch_array($conn->query("SELECT role FROM role WHERE id = '$role_id'"))['role'];
    $department = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$department_id'"))['name'];

    if( $type_id == 1){
        $text = $username." logged in! ".$role." of ".$department." in ".$entity;
    } else if ($type_id == 2){
        $text = $username." logged in through feishu! ".$role." of ".$department." in ".$entity;
    } else if ($type_id == 3){
        $text = $username." bound account to feishu! ".$role." of ".$department." in ".$entity;
    }
    $sql = "INSERT INTO log (date, text,log_type, subject) VALUES 
    ('$time_now','$text','$type_id','$user_id')";
    if( $conn->query($sql)){
        return "Record inserted successfully.";
    } else{
        return "ERROR: Could not able to execute $sql. " . $conn->error;
    }
}

function insert_log_asset($conn, $row,$user_id,$type_id)
{
    /*  BEGIN INSERT LOG */
    $time_now = time();
    $asset_id = $row['id'];
    $text = '';
    $user_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$user_id'"))['name'];

    if( $type_id ==4){
    } else if ( $type_id ==5){
        $text = "Asset ". $row['name']." created by " . $user_name;
    } else if ( $type_id ==6){
        $text = "Asset ". $row['name']." info changed by " . $user_name;
    } else if ( $type_id ==7){
        $text = "Asset ". $row['name']." registered (use) from " . $user_name;
    } else if ( $type_id ==8){
        $text = "Asset ". $row['name']." register(use) approved by " . $user_name;
    } else if ( $type_id ==9){
        $text = "Asset ". $row['name']." registered (move) from " . $user_name;
    } else if ( $type_id ==10){
        $text = "Asset ". $row['name']." registered (move) approved by" . $user_name;
    } else if ( $type_id ==11){
        $text = "Asset ". $row['name']." registered (repair) from " . $user_name;
    } else if ( $type_id ==12){
        $text = "Asset ". $row['name']." registered (repair) approved by " . $user_name;
    } else if ( $type_id ==13){
        $text = "Asset ". $row['name']." deleted by " . $user_name;
    }
    /* TODO: finish other cases*/

    $sql = "INSERT INTO log (date, text,log_type, subject,`By`) VALUES
    ('$time_now','$text','$type_id','$asset_id','$user_id')";

    if ($conn->query($sql)){
        return "Record inserted successfully.";
    } else {
        return "ERROR: Could not able to execute $sql. " . $conn->error;
    }
}
?>
