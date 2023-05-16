<?php

require "db/connect.php";

/**
 * Gets all subdepartments in an array, given a department ID.
 * The array will include the department itself.
 * Assuming you have established a MySQL database connection
 *
 * @param {number} $departmentId - The ID of the requested department
 * @param {object} $mysqli - The database connection object.
 * @return {array} An array of department IDs.
 *
 * @example
 * getAllSubdepartmentIds(4, $conn);
 */
function getAllSubdepartmentIds($departmentId, $mysqli)
{
    $query = "SELECT id FROM department WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $departmentId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if department exists
    if ($result->num_rows === 0) {
        echo "Department not found.";
        return;
    }

    // Retrieve subdepartment IDs
    $subdepartmentIds = [];
    $query = "SELECT id FROM department WHERE parent = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $departmentId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Loop through subdepartments
    while ($row = $result->fetch_assoc()) {
        $subdepartmentId = $row['id'];

        // Recursively call the function to get subdepartment IDs
        $subdepartmentIds[] = $subdepartmentId;
        $subdepartmentIds = array_merge($subdepartmentIds, getAllSubdepartmentIds($subdepartmentId, $mysqli));
    }
    
    array_push($subdepartmentIds, $departmentId);

    return $subdepartmentIds;
}
