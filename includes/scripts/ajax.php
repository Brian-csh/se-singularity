<?php

require "../db/connect.php";

// if ($_POST['request'] == 'get_departments') {
//     $entity_id = $_POST['entity_id'];

//     $results = $conn->query("SELECT id, parent, name FROM department WHERE entity = " . $entity_id);
//     $departments = [];

//     while ($row = $results->fetch_assoc()) {
//         $departments[] = [
//             'id' => $row['id'],
//             'parent' => $row['parent'],
//             'name' => $row['name'],
//         ];
//     }

//     header('Content-Type: application/json');
//     echo json_encode($departments);
// }

if ($_POST['request'] == 'get_departments') {
    $department_id = $_POST['department_id'];

    $results = $conn->query("SELECT id, parent, name FROM department WHERE entity = " . $entity_id);
    $departments = [];

    while ($row = $results->fetch_assoc()) {
        $departments[] = [
            'id' => $row['id'],
            'parent' => $row['parent'],
            'name' => $row['name'],
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($departments);
}
