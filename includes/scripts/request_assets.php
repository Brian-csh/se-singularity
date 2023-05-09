<?php
require "../db/connect.php";

// Get asset IDs from POST data
$assetIds = isset($_POST['assets']) ? $_POST['assets'] : [];
$destination = isset($_POST['destination']) ? $_POST['destination'] : -1;

// Retire assets
if (empty($assetIds)) {
    echo json_encode(['success' => false, 'message' => 'No assets selected.']);
} else if ($destination == -1) {
    echo json_encode(['success' => false, 'message' => 'Invalid department.']);
} else {
    $ids = implode(',', $assetIds);
    $sql = "UPDATE asset SET department = $destination WHERE id IN ($ids)";
    $result = $conn->query($sql);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Assets retired successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error retiring assets.']);
    }
}

// Close the database connection
$conn->close();
?>
