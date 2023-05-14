<?php

// Logging functions
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

function insert_log_asset($conn,$row,$user_id,$type_id,$time = null)
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
    } else if ( $type_id ==9){ // move user to user, $row from pending_requests

        //asset_id could be more than once
        // $asset_id = $row['asset'];
        // $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];

        // $user_id = $row['initiator'];
        // $user_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$user_id'"))['name'];

        // $participant_id = $row['participant'];
        // $participant_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$participant_id'"))['name'];

        $text = "Asset ". $asset_name." requested 'move' from " . $user_name . " to " . $participant_name;
    } else if ( $type_id ==10){
        $text = "Asset ". $row['name']." registered (move) approved by" . $user_name;
    } else if ( $type_id ==11){
        $text = "Asset ". $row['name']." registered (repair) from " . $user_name;
    } else if ( $type_id ==12){
        $text = "Asset ". $row['name']." registered (repair) approved by " . $user_name;
    } else if ( $type_id ==13){
        $text = "Asset ". $row['name']." deleted by " . $user_name;
    }
    /* TODO: delete cases where user initiates*/

    $sql = "INSERT INTO log (date, text,log_type, subject,`By`) VALUES
    ('$time_now','$text','$type_id','$asset_id','$user_id')";

    if ($conn->query($sql)){
        return "Record inserted successfully.";
    } else {
        return "ERROR: Could not able to execute $sql. " . $conn->error;
    }
}

function insert_log_asset_user($conn,$initiator,$participant = null,$asset_id,$request_type,$time) // TODO: finish this
{
    $text = '';
    switch ($request_type){
        case 7 :  // asset_request_use
            $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
            $user_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$initiator'"))['name'];
            $text= "Asset ". $asset_name." requested (use) from " . $user_name;
            break;
        case 8 :
            break;
        case 9 : // asset_request_move

            $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];

            $user_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$initiator'"))['name'];

            $participant_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$participant'"))['name'];

            $text= "Asset ". $asset_name." was requested (move) from " . $user_name . " to " . $participant_name;
            break;
        case 10:
            break;
        default :
            break;
    }

    $sql = "INSERT INTO log (date, text, log_type, subject,`By`) VALUES
            ('$time','$text','$request_type','$asset_id','$initiator')";

    if ($conn->query($sql)){
        return "Record inserted successfully.";
    } else {
        return "ERROR: Could not able to execute $sql. " . $conn->error;
    }
}


/**
 * Inserts a log entry indicating that an entity was synced to Feishu by an initiator.
 * The log type of an entity Feishu sync is 50.
 *
 * @param {object} $conn - The database connection object.
 * @param {number} $entity_id - The ID of the entity that was synced to Feishu.
 * @param {number} $initiator_id - The ID of the user who initiated the sync.
 * @return {string} A message indicating whether the record was inserted successfully or an error occurred.
 *
 * @example
 * insert_log_feishu_sync($conn, 123, 456);
 *
 * @throws {Error} If the database query fails.
 */
function insert_log_feishu_sync($conn, $entity_id, $initiator_id)
{
    $time_now = time();
    $initiator_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$initiator_id'"))['name'];
    $entity_name = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];
    $text = "Synced entity " . $entity_name. " to Feishu by " . $initiator_name;
    $type_id = 50;
    $sql = "INSERT INTO log (date, text, log_type,`By`) VALUES
            ('$time_now','$text','$type_id','$initiator_id')";
    if ($conn->query($sql)){
        return "Record inserted successfully.";
    } else {
        return "ERROR: Could not able to execute $sql. " . $conn->error;
    }
}


/**
 * Inserts a log entry indicating that a new user joined Singularity through Feishu.
 *
 * @param {object} $conn - The database connection object.
 * @param {object} $row - The row from the `user` table containing information about the new user.
 * @return {string} A message indicating whether the record was inserted successfully or an error occurred.
 *
 * @example
 * $row = array('name' => 'John Doe', 'id' => 123, 'entity' => 456, 'role' => 4);
 * insert_log_new_feishu_user($conn, $row);
 *
 * @throws {Error} If the database query fails.
 */
function insert_log_new_feishu_user($conn, $row)
{
    /*  BEGIN INSERT LOG */
    $time_now = time();
    $username = $row['name'];
    $user_id = $row['id'];
    //Fetch Entity, Role, Department
    $entity_id = $row['entity'];
    $role_id = $row['role'];
    $type_id = 51;

    $entity = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];
    $role = mysqli_fetch_array($conn->query("SELECT role FROM role WHERE id = '$role_id'"))['role'];
    $text = $username." joined Singularity through Feishu! They are a ".$role." in ".$entity;
    $sql = "INSERT INTO log (date, text,log_type, subject) VALUES 
    ('$time_now','$text','$type_id','$user_id')";
    if( $conn->query($sql)){
        return "Record inserted successfully.";
    } else{
        return "ERROR: Could not able to execute $sql. " . $conn->error;
    }
}

// Request functions
function make_request($conn,$initiator,$participant = null,$asset_ids,$request_type){
    $time = time();
    $results = [];
    switch ($request_type){
        case 1: // request use
            // only can request IDLE assets
            foreach($asset_ids as $asset_id){
                //fetch status of asset                
                $asset_status = mysqli_fetch_array($conn->query("SELECT status FROM asset WHERE id = '$asset_id'"))['status'];
                $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
                if($asset_status == 1){ // IN IDLE
                    $sql = "INSERT INTO pending_requests (initiator, participant, asset, type, request_time) VALUES 
                            ('$initiator',null,'$asset_id','$request_type','$time')";
                    array_push($results,[$asset_name,$conn->query($sql)]);
                    //Make log
                    insert_log_asset_user($conn,$initiator,$participant,$asset_id,7,$time);

                    $sql = "UPDATE asset SET status =6 WHERE id = '$asset_id'";
                    $conn->query($sql);
                } else { // NOT IN IDLE
                    array_push($results,[$asset_name,false]);
                }
            }

            break;
        case 2: // request return == idle

            break; 
        case 3: // request repair  == move
            
            break;
        case 4: // request move (user to user, participant is new user(only one), asset_ids can be list)
            //only can request IN USE assets and user is initiator
            foreach($asset_ids as $asset_id){
                //fetch user
                $user = mysqli_fetch_array($conn->query("SELECT user FROM asset WHERE id = '$asset_id'"))['user'];
                $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
                $status_id = mysqli_fetch_array($conn->query("SELECT status FROM asset WHERE id = '$asset_id'"))['status'];
                if($user == $initiator && $status_id == 2){ // user is initiator, and status is IN USE
                    $sql = "INSERT INTO pending_requests (initiator,participant,asset,type,request_time) VALUES
                            ('$initiator','$participant','$asset_id','$request_type','$time')";
                    array_push($results,[$asset_name,$conn->query($sql)]);
                    //make log
                    insert_log_asset_user($conn,$initiator,$participant,$asset_id,9,$time);

                    $sql = "UPDATE asset SET status = 9 WHERE id = '$asset_id'";
                    $conn->query($sql);
                } else { // user is not initiator
                    array_push($results,[$asset_name,false]);
                }
            }
            break;
        default :
            echo "";
        }
    return $results;
}
?>
