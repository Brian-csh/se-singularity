<?php
function insert_log($conn,$row,$username)
{                  
    /*  BEGIN INSERT LOG */
    $time_now = time();
    //Fetch Log Type
    $type_id = 1;
    $type = mysqli_fetch_array($conn->query("SELECT type FROM log_type WHERE id = '$type_id'"))['type'];

    //Fetch Entity, Role, Department
    $entity_id = $row['entity'];
    $role_id = $row['role'];
    $department_id = $row['department'];

    $entity = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];
    $role = mysqli_fetch_array($conn->query("SELECT role FROM role WHERE id = '$role_id'"))['role'];
    $department = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$department_id'"))['name'];

    $text = $username." logged in! ".$role." of ".$department." in ".$entity;

    $sql = "INSERT INTO log (date, text,log_type) VALUES 
    ('$time_now','$text','$type')";
    if( $conn->query($sql)){
        echo "Record inserted successfully.";
    } else{
        echo "ERROR: Could not able to execute $sql. " . $conn->error;
    }
}
?>