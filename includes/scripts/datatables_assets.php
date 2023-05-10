<?php

require "../db/connect.php";

// Get the DataTables request parameters
$draw = intval($_GET['draw']);
$start = intval($_GET['start']);
$length = intval($_GET['length']);
$user_role = strval($_GET['role_id']);

$departmentid = intval($_GET['departmentid']);
$userid = intval($_GET['userid']);

// Fetch data from your database table
if ($userid != -1)
    $sql = "SELECT * FROM asset WHERE user = $userid";
else if ($departmentid != -1)
    $sql = "SELECT * FROM asset WHERE department = $departmentid";  
else
    $sql = "SELECT * FROM asset WHERE 1=1"; 

if (isset($_GET['search']['value'])) {
    $search_string = $_GET['search']['value'];
    if (!empty($search_string)) {
        $class_condition = "";
        $asset_class_sql = "SELECT * FROM asset_class WHERE name LIKE '%$search_string%'";
        $asset_query_result = $conn->query($asset_class_sql);
        if ($asset_query_result->num_rows > 0) {
            $class_array = array();
            while ($row = $asset_query_result->fetch_assoc()) {
                array_push($class_array, $row['id']);
            }
            $class_condition .= " OR class IN (" . implode(", ", $class_array) . ")";
        }
        $sql .= " AND (name LIKE '%$search_string%' OR description LIKE '%$search_string%'" . $class_condition . ")";

    }
}
$sql .= " LIMIT $start, $length";

$result = $conn->query($sql);

$data = array();
while($row = $result->fetch_assoc()) {

    if(isset($row['status'])){
        $status_id = $row['status'];
        $status = mysqli_fetch_array($conn->query("SELECT status FROM asset_status_class WHERE id = '$status_id'"))['status'];
    } else {
        $status = "N/A";
    }

    if (isset($row['user'])) {
        $user_id = $row['user'];
        $user = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$user_id'"))['name'];
    } else {
        $user = "N/A";
    }

    if (isset($row['parent'])) {
        $parent_id = $row['parent'];
        $parent = mysqli_fetch_array($conn->query("SELECT name FROM asset WHERE id = '$parent_id'"))['name'];
    } else {
        $parent = "N/A";
    }

    if (isset($row['class'])) {
        $class_id = $row['class'];
        $class = mysqli_fetch_array($conn->query("SELECT name FROM asset_class WHERE id = '$class_id'"))['name'];
    } else {
        $class = "N/A";
    }


    //TODO : add link to descpription (modal or sth)
if( $user_role != '4'){
    $data[] = array(
        "id" => $row['id'],
        "parent" => $parent,
        "name" => "<a class='text-primary' href='/asset.php?id=".$row['id']."&name=".$row['name']."'>". $row['name']."</a>",
        "class" => $class,
        "user" => $user,
        "price" => $row['price'],
        "description" => strip_tags(substr($row['description'],0,30)) . "...",
        "position" => $row['position'],
        "expire" => $row['expire'], 
        // add Modal for the requests?
        // "status" => ($status_id >=6 && $status_id <= 10 )? "<button class= 'text-primary handleRequestButton' data-bs-toggle='modal' data-bs-target = '#handleRequestModal'>"."You have pending Request! : ".$status. "</button>" : $status,
        "status" => $status,
        "actions" => "<a title=\"User Info\" class=\"btn btn-datatable\" href=\"edit_asset.php?id=".$row['id']."&name=".$row['name']."\">
        Info
        </a>"
    );
} else { // user_role == 4 (user)
    $data[] = array(
        "id" => $row['id'],
        "parent" => $parent,
        "name" => "<a class='text-primary' href='../../asset_info.php?id=".$row['id']."&name=".$row['name']."'>". $row['name']."</a>",
        "class" => $class,
        "user" => $user,
        "price" => $row['price'],
        "description" => strip_tags(substr($row['description'],0,30)) . "...",
        "position" => $row['position'],
        "expire" => $row['expire'],
        "status" => $status,
        "actions" => "<a title=\"User Info\" class=\"btn btn-datatable\" href=\"request_asset_status.php?id=".$row['id']."&name=".$row['name']."\">
        Info
        </a>"
    );
}
}

// Get the total number of records in the table
$sql = "SELECT COUNT(*) as total FROM asset";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total = $row['total'];

// Prepare the JSON response
$response = array(
    "draw" => $draw,
    "recordsTotal" => $total,
    "recordsFiltered" => $total,
    "data" => $data
);

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>