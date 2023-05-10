<?php
require "../db/connect.php";

// Get asset IDs from POST data
$assetIds = isset($_POST['assets']) ? $_POST['assets'] : [];

// Retire assets 
if (!empty($assetIds)) {
    $ids = implode(',', $assetIds);
    //set user to null for the asset and notify user 
    $sql = "UPDATE asset SET status = 4, user = null WHERE id IN ($ids)";
    $result = $conn->query($sql);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Assets retired successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error retiring assets.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No assets selected.']);
}
// Close the database connection
$conn->close();
?>
