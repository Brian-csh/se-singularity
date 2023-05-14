<?php

/* -------------------------------- Logging functions -------------------------------------------*/
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

// log for editting asset
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

// log for requesting asset (user)
function insert_log_asset_user($conn,$initiator,$participant = null,$asset_id,$request_type,$time) // TODO: finish this
{
    $text = '';
    switch ($request_type){
        case 7 :  // asset_request_use
            $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
            $user_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$initiator'"))['name'];
            $text= "Asset ". $asset_name."was requested (use) from " . $user_name;
            break;
        case 9 : // asset_request_move

            $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];

            $user_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$initiator'"))['name'];

            $participant_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$participant'"))['name'];

            $text= "Asset ". $asset_name." was requested (move) from " . $user_name . " to " . $participant_name;
            break;
        case 10: // asset_approve_move
            break;
        case 11: // asset_request_return
            $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];

            $user_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$initiator'"))['name'];

            $text= "Asset ". $asset_name."was requested (return) from " . $user_name;
            break;
        case 13: // asset_reqeust_repair
            $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];

            $user_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$initiator'"))['name'];

            $text= "Asset ". $asset_name."was requested (repair) from " . $user_name;
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

// log for handling asset (manager)
function insert_log_handle_request($conn, $manager_id,$request_id,$asset_id,$request_type,$time){
    switch($request_type){
        case 8 : // approve request use
            // fetch asset name
            $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
            // fetch user name
            $manager_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$user_id'"))['name'];
            $text = "Request use of asset ". $asset_name." approved by " . $manager_name;
            break;
        case 10: // arppove request move
            break;
        case 12: // approve request return
            break;
        case 14:// approve request repair
            break;
        default:
            break;
    }
    $sql = "INSERT INTO log(date,text,log_type,subject,`By`) VALUES
            ('$time','$text','$request_type','$asset_id','$manager_id')";
    // if ($conn->query($sql)){
    //     return "Record inserted successfully.";
    // } else {
    //     return "ERROR: Could not able to execute $sql. " . $conn->error;
    // }
}

/* ------------------------- Request functions-------------------------------------*/
function make_request($conn,$initiator,$participant = null,$asset_ids,$request_type){
    $time = time();
    $results = [];
    switch ($request_type){
        case 1: // request use
            // only can request IDLE assets
            foreach($asset_ids as $asset_id){
                //fetch status of asset                
                $asset_status = mysqli_fetch_array($conn->query("SELECT status FROM asset WHERE id = '$asset_id'"))['status'];
                $department_id = mysqli_fetch_array($conn->query("SELECT department FROM asset WHERE id = '$asset_id'"))['department'];
                $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
                if($asset_status == 1){ // IN IDLE
                    $sql = "INSERT INTO pending_requests (initiator, participant, asset, type, request_time,department) VALUES 
                            ('$initiator',null,'$asset_id','$request_type','$time','$department_id')";
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
        case 2: // request return
            //only can request IN USE assets and user is initiator
            foreach($asset_ids as $asset_id){
                //fetch user
                $user_id = mysqli_fetch_array($conn->query("SELECT user FROM asset WHERE id = '$asset_id'"))['user'];
                $department_id = mysqli_fetch_array($conn->query("SELECT department FROM asset WHERE id = '$asset_id'"))['department'];
                $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
                $status_id = mysqli_fetch_array($conn->query("SELECT status FROM asset WHERE id = '$asset_id'"))['status'];
                if($user_id == $initiator && $status_id == 2){ // user is initiator, and status is IN USE
                    $sql = "INSERT INTO pending_requests (initiator,participant,asset,type,request_time,department) VALUES
                            ('$initiator',null,'$asset_id','$request_type','$time','$department_id')";
                    array_push($results,[$asset_name,$conn->query($sql)]);
                    //make log
                    insert_log_asset_user($conn,$initiator,$participant,$asset_id,11,$time);

                    $sql = "UPDATE asset SET status = 7 WHERE id = '$asset_id'";
                    $conn->query($sql);
                } else { // user is not initiator
                    array_push($results,[$asset_name,false]);
                }
            }

            break; 
        case 3: // request repair
            //only can request IN USE assets and user is initiator
            foreach($asset_ids as $asset_id){
                //fetch data
                $user_id = mysqli_fetch_array($conn->query("SELECT user FROM asset WHERE id = '$asset_id'"))['user'];
                $department_id = mysqli_fetch_array($conn->query("SELECT department FROM asset WHERE id = '$asset_id'"))['department'];
                $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
                $status_id = mysqli_fetch_array($conn->query("SELECT status FROM asset WHERE id = '$asset_id'"))['status'];
                if($user_id == $initiator && $status_id == 2){ // user is initiator, and status is IN USE
                    $sql = "INSERT INTO pending_requests (initiator,participant,asset,type,request_time,department) VALUES
                            ('$initiator',null,'$asset_id','$request_type','$time','$department_id')";
                    array_push($results,[$asset_name,$conn->query($sql)]);
                    //make log
                    insert_log_asset_user($conn,$initiator,$participant,$asset_id,13,$time);

                    $sql = "UPDATE asset SET status = 8 WHERE id = '$asset_id'";
                    $conn->query($sql);
                } else { // user is not initiator
                    array_push($results,[$asset_name,false]);
                }
            }
            break;
        case 4: // request move (user to user, participant is new user(only one), asset_ids can be list)
            //only can request IN USE assets and user is initiator
            foreach($asset_ids as $asset_id){
                //fetch user
                $user_id = mysqli_fetch_array($conn->query("SELECT user FROM asset WHERE id = '$asset_id'"))['user'];
                $department_id = mysqli_fetch_array($conn->query("SELECT department FROM asset WHERE id = '$asset_id'"))['department'];
                $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
                $status_id = mysqli_fetch_array($conn->query("SELECT status FROM asset WHERE id = '$asset_id'"))['status'];
                if($user == $initiator && $status_id == 2){ // user is initiator, and status is IN USE
                    $sql = "INSERT INTO pending_requests (initiator,participant,asset,type,request_time,department) VALUES
                            ('$initiator','$participant','$asset_id','$request_type','$time','$department_id')";
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

//TODO : 树的遍历 - iteration
function traverse_department($conn, $departmentid){
    
    return $sql;
}

// HANDLE REQUESTS
function approve_request($conn, $manager_id,$requestIds){
    $time = time();
    $results = [];
    foreach($requestIds as $request_id){
        //fetch request type
        $request_type = mysqli_fetch_array($conn->query("SELECT type FROM pending_requests WHERE id = '$request_id'"))['type'];
        //fetch initiator
        $initiator = mysqli_fetch_array($conn->query("SELECT initiator FROM pending_requests WHERE id = '$request_id'"))['initiator'];
        //fetch asset_id
        $asset_id = mysqli_fetch_array($conn->query("SELECT asset FROM pending_requests WHERE id = '$request_id'"))['asset'];
        switch ($request_type){
            case 1://request use
                // check if request is valid
                $request_status = mysqli_fetch_array($conn->query("SELECT result FROM pending_requests WHERE id = '$request_id'"))['result'];
                if($request_status != 0){
                    array_push($results,[$request_id,false]);
                    break;
                }
                //set user as user_id and asset status to IN uSE
                $sql = "UPDATE asset SET user = '$initiator',status = 2 WHERE id = '$asset_id'";
                $conn->query($sql);
                // leave log
                insert_log_handle_request($conn,$manager_id,$request_id,$aset_id,8);
                // set request as done in pending request and record review time
                $sql = "UPDATE pending_requests SET result = 1, review_time = $time WHERE id = '$request_id'";

                array_push($results,[$request_id,$conn->query($sql)]);
                break;
            case 2:// request return

                break;
            case 3:// request repair
                break;
            case 4:// request move
                break;
            default:
                break;
        }
    }
    return $results;
}
?>