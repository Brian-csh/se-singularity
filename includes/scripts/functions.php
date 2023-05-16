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
    $department_id = $row['department']? $row['department'] : -1;

    $entity = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];
    $role = mysqli_fetch_array($conn->query("SELECT role FROM role WHERE id = '$role_id'"))['role'];
    $department = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$department_id'"))['name'];

    if( $type_id == 1){
        if($role_id == 1)
            $text = $username." logged in! Super Admin of Singularity";
        else if($role_id == 2)
            $text = $username." logged in! Admin of ".$entity;
        else if($role_id == 3)
            $text = $username." logged in! Resource Manager of ".$department." in ".$entity;
        else
            $text = $username." logged in! User of ".$department." in ".$entity;

    } else if ($type_id == 2){
        $text = $username." logged in through feishu! ".$role." of ".$department." in ".$entity;
    } else if ($type_id == 3){
        $text = $username." bound account to feishu! ".$role." of ".$department." in ".$entity;
    }
    $sql = "INSERT INTO log (date, text,log_type, `By`,department) VALUES 
    ('$time_now','$text','$type_id','$user_id','$department_id')";
    if( $conn->query($sql)){
        return "Record inserted successfully.";
    } else{
        return "ERROR: Could not able to execute $sql. " . $conn->error;
    }
}

// log for editting asset - other cases should be deleted
function insert_log_edit_asset($conn,$row,$user_id,$type_id,$time = null)
{
    $time_now = time();
    $asset_id = $row['id'];
    $text = '';
    $user_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$user_id'"))['name'];
    $department_id = $row['department']? $row['department'] : -1; // row(asset)


    $text = "Asset ". $row['name']." info changed by " . $user_name;

    $sql = "INSERT INTO log (date, text,log_type, subject,`By`,department) VALUES
    ('$time_now','$text','$type_id','$asset_id','$user_id','$department_id')";

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
    $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];
    $user_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$initiator'"))['name'];
    $department_id = mysqli_fetch_array($conn->query("SELECT department FROM user WHERE id = '$initiator'"))['department'];
    switch ($request_type){
        case 7 :  // asset_request_use
            $text= "Asset ". $asset_name." was requested (use) from " . $user_name;
            break;
        case 9 : // asset_request_move
            $participant_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$participant'"))['name'];
            $text= "Asset ". $asset_name." was requested (move) from " . $user_name . " to " . $participant_name;
            break;
        case 11: // asset_request_return
            $text= "Asset ". $asset_name." was requested (return) from " . $user_name;
            break;
        case 13: // asset_reqeust_repair
            $text= "Asset ". $asset_name." was requested (repair) from " . $user_name;
            break;
        default :
            break;
    }

    $sql = "INSERT INTO log (date, text, log_type, subject,`By`,department) VALUES
            ('$time','$text','$request_type','$asset_id','$initiator','$department_id')";

    if ($conn->query($sql)){
        return "Record inserted successfully.";
    } else {
        return "ERROR: Could not able to execute $sql. " . $conn->error;
    }
}


// log for handling asset (manager)
function insert_log_handle_request($conn, $manager_id,$request_id,$asset_id,$request_type,$handle_type,$time){
        // fetch asset name
        $asset_name = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$asset_id'"))['name'];

        // fetch department_id
        $department_id = mysqli_fetch_array($conn->query("SELECT department FROM asset WHERE id = '$asset_id'"))['department'];
        // fetch user name
        $manager_name = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$manager_id'"))['name'];
        switch($request_type){
            case 8 : // approve request use
                if($handle_type == 1){
                    $text = "Request use of asset ". $asset_name." approved by " . $manager_name;
                } else {
                    $text = "Request use of asset ". $asset_name." rejected by " . $manager_name;
                }
                break;
            case 10: // arppove request move
                if($handle_type == 1){
                    $text = "Request move of asset ". $asset_name." approved by " . $manager_name;
                } else {
                    $text = "Request move of asset ". $asset_name." rejected by " . $manager_name;
                }
                break;
            case 12: // approve request return
                if($handle_type == 1){
                    $text = "Request return of asset ". $asset_name." approved by " . $manager_name;
                } else {
                    $text = "Request return of asset ". $asset_name." rejected by " . $manager_name;
                }
                break;
            case 14:// approve request repair
                if($handle_type == 1){
                    $text = "Request repair of asset ". $asset_name." approved by " . $manager_name;
                } else {
                    $text = "Request repair of asset ". $asset_name." rejected by " . $manager_name;
                }
                break;
            default:
                break;
        }
        $sql = "INSERT INTO log(date,text,log_type,subject,`By`,department) VALUES
                ('$time','$text','$request_type','$asset_id','$manager_id','$department_id')";
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



// HANDLE REQUESTS
function handle_request($conn, $manager_id,$requestIds,$handle_type){
    $time = time();
    $results = [];
        foreach($requestIds as $request_id){
            //fetch request type
            $request_type = mysqli_fetch_array($conn->query("SELECT type FROM pending_requests WHERE id = '$request_id'"))['type'];
            //fetch initiator
            $initiator = mysqli_fetch_array($conn->query("SELECT initiator FROM pending_requests WHERE id = '$request_id'"))['initiator'];
            //fetch asset_id
            $asset_id = mysqli_fetch_array($conn->query("SELECT asset FROM pending_requests WHERE id = '$request_id'"))['asset'];
            $request_status = mysqli_fetch_array($conn->query("SELECT result FROM pending_requests WHERE id = '$request_id'"))['result'];
            // check if request is valid
            if($request_status != 0){ 
                array_push($results,[$request_id,false]);
                break;
            }
            switch ($request_type){
                case 1://request use
                    if($handle_type == 1){
                        //set user as user_id and asset status to IN USE
                        $sql = "UPDATE asset SET user = '$initiator',status = 2 WHERE id = '$asset_id'"; $conn->query($sql);
                    } else {
                        // set asset status to IDLE
                        $sql = "UPDATE asset SET status = 1 WHERE id = '$asset_id'"; $conn->query($sql);
                    }
                    // leave log
                    insert_log_handle_request($conn,$manager_id,$request_id,$asset_id,8,$handle_type,$time);
                    break;
                case 2:// request return
                    if($handle_type == 1){
                        //set user as N/A and asset status to IDLE
                        $sql = "UPDATE asset SET user = null, status = 1 WHERE id = '$asset_id'"; $conn->query($sql);
                    } else {
                        // set asset status to IN USE
                        $sql = "UPDATE asset SET status = 2 WHERE id = '$asset_id'"; $conn->query($sql);
                    }
                    // leave log
                    insert_log_handle_request($conn,$manager_id,$request_id,$asset_id,12,$handle_type,$time);
                    break;
                case 3:// request repair
                    if($handle_type == 1){
                        //set user as N/A and asset status to IN MAINTAIN
                        $sql = "UPDATE asset SET user = null,status = 3 WHERE id = '$asset_id'"; $conn->query($sql);
                    } else {
                        //set asset status to IN USE
                        $sql = "UPDATE asset SET status = 2 WHERE id = '$asset_id'"; $conn->query($sql);
                    }
                    // leave log
                    insert_log_handle_request($conn,$manager_id,$request_id,$asset_id,14,$time);
                    break;
                case 4:// request move
                    if($handle_type == 1){
                        //fetch participant
                        $participant_id = mysqli_fetch_array($conn->query("SELECT participant FROM pending_requests WHERE id = '$request_id'"))['participant'];
                        //set user as participant and asset status to IN USE
                        $sql = "UPDATE asset SET user = $participant_id,status = 2 WHERE id = '$asset_id'"; $conn->query($sql);
                    } else {
                        $sql = "UPDATE asset SET status = 2 WHERE id = '$asset_id'"; $conn->query($sql);
                    }
                    // leave log
                    insert_log_handle_request($conn,$manager_id,$request_id,$asset_id,10,$time);
                    break;
                default:
                    break;
            }
            // set request as done in pending request and record review time
            $sql = "UPDATE pending_requests SET result = $handle_type, review_time = $time WHERE id = '$request_id'";
            array_push($results,[$request_id,$conn->query($sql)]);
        }
    return $results;
}

/**
 * Gets all departments in an array, given an entity ID.
 *
 * @param int $entityId - The ID of the requested entity.
 * @param mysqli $mysqli - The database connection object.
 * @return array - An array of department IDs.
 */
function getAllDepartmentIds($entityId, $mysqli) {
    // Prepare the SQL statement
    $sql = "SELECT id FROM department WHERE entity = ?";

    // Prepare the statement
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        // Bind the entity ID parameter
        $stmt->bind_param("i", $entityId);

        // Execute the statement
        $stmt->execute();

        // Bind the result
        $stmt->bind_result($departmentId);

        // Create an array to store the department IDs
        $departmentIds = array();

        // Fetch the results
        while ($stmt->fetch()) {
            // Add the department ID to the array
            $departmentIds[] = $departmentId;
        }

        // Close the statement
        $stmt->close();

        // Return the department IDs
        return $departmentIds;
    } else {
        // Handle the error if the statement preparation fails
        // You can customize this based on your error handling needs
        return null;
    }
}

?>