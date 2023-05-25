<?php

require "../db/connect.php";

// TODO : superadmin!
if($_POST['request'] == 'set_departments_admin'){
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


if($_POST['request'] == 'set_entities'){
    $role = $_POST['role'];
    $entity_id = $_POST['entity_id'];
    $entities = [];
    $results = $conn->query("SELECT id, name FROM entity");
    while($row = $results->fetch_assoc()){
        $entities[] = [
            'id' => $row['id'],
            'name' => $row['name'],
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($entities);
}

if($_POST['request'] == 'set_departments_sa'){
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