<?php

require "../db/connect.php";

function exportAssets($conn)
{
    // Prepare the SQL query to select all assets
    $query = "SELECT * FROM asset";

    // Execute the query
    $result = $conn->query($query);

    // Fetch all rows from the result set
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // Close the database connection
    $conn->close();

    // Define the filename for the exported data
    $filename = "assets.csv";

    // Set the appropriate headers for download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Write the column headers to the output stream
    $headers = array_keys($rows[0]);
    fputcsv($output, $headers);

    // Write each row to the output stream
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }

    // Close the output stream
    fclose($output);
}

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Call the function to export the assets and initiate the download
exportAssets($conn);

?>
