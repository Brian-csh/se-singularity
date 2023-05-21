<?php

require "../db/connect.php";

// TODO : superadmin!
if($_POST['request'] == 'set_departments'){
    $role = $_POST['role'];
    $entity_id = $_POST['entity_id'];
        $results = $conn->query("SELECT id, parent, name FROM department WHERE entity = '$entity_id'");
        $departments = [];
        $entity_name = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entity_id'"))['name'];
        while($row = $results->fetch_assoc()){
            $row2 = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$row[parent]'"));
            if(isset($row2['name'])) $parent_name = $row2['name'];
            else $parent_name = $entity_name;
            $departments[] = [
                'id' => $row['id'],
                'parent' => $parent_name,
                'name' => $row['name'],
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($departments);
}