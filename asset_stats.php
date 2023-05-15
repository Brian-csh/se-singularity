<?php
require 'includes/db/connect.php';
include 'includes/get_subdepartments.php';

// get all assets in this department, including in subdepartments
$subdepartmentIds = getAllSubdepartmentIds($department, $conn);
$department_list = implode(',', $subdepartmentIds);

$sql = "SELECT * FROM asset WHERE department IN ($department_list)";

// Prepare and execute the query with the department ID parameter
$stmt = $conn->prepare($sql);
// $stmt->bind_param('i', $department_id); for the recursive query
$stmt->execute();

// Execute the query and get the result set
$asset_results = $stmt->get_result();
// Process the result set
// while ($row = $result->fetch_assoc()) {
//     echo "Asset name: " . $row["name"] . " Asset department: " . $row["department"] . "<br>";
// }

$stmt->close();

// basic information
$num_assets = mysqli_num_rows($asset_results);
$sql = "SELECT * FROM entity WHERE id = '$entity'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$entity_results = $stmt->get_result();
$stmt->close();
$entity_name = $entity_results->fetch_assoc()["name"];

$sql = "SELECT * FROM department WHERE id = '$department'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$department_results = $stmt->get_result();
$stmt->close();
$department_name = $department_results->fetch_assoc()["name"];

// split by asset state (pie chart)
$sql = "SELECT * FROM asset_status_class";
$stmt = $conn->prepare($sql);
$stmt->execute();
$state_results = $stmt->get_result();
$stmt->close();

$states = array();
while ($row = $state_results->fetch_assoc()) {
    $states[] = $row["status"];
}

$asset_status_counts = array();
foreach ($asset_results as $asset) {
    $asset_status = $asset["status"];
    if (array_key_exists($states[$asset_status-1], $asset_status_counts)) {
        $asset_status_counts[$states[$asset_status-1]] += 1;
    } else {
        $asset_status_counts[$states[$asset_status-1]] = 1;
    }
}
$asset_status_counts_json = json_encode($asset_status_counts);
echo "<script> var status_counts = JSON.parse('". $asset_status_counts_json ."'); </script>";


// split by department
$sql = "SELECT * FROM department";
$stmt = $conn->prepare($sql);
$stmt->execute();
$state_results = $stmt->get_result();
$stmt->close();
$departments = array();
while ($row = $state_results->fetch_assoc()) {
    $departments[] = $row["name"];
}

$asset_department_counts = array();
foreach($asset_results as $asset) {
    $asset_department = $asset["department"];
    if (array_key_exists($departments[$asset_department-1], $asset_department_counts)) {
        $asset_department_counts[$departments[$asset_department-1]] += 1;
    } else {
        $asset_department_counts[$departments[$asset_department-1]] = 1;
    }
}
$asset_department_counts_json = json_encode($asset_department_counts);
echo "<script> var department_counts = JSON.parse('". $asset_department_counts_json ."'); </script>";

// total asset value graph
?>

<html>
    <body>
        <div class="m-4">
          <h2>Asset Statistics</h2>
          <div class="container">
            <div class="row mt-3">
              <h6 class="text-white m-1 mb-4">Current Entity: <?php echo $entity_name ?></h6>
            <div>
            <div class="row">
              <h6 class="text-white m-1 mb-4">Current Department: <?php echo $department_name ?></h6>
            <div>
            <div class="row">
              <h6 class="text-white m-1 mb-4">Total asset count: <?php echo $num_assets ?></h6>
            <div>
            <div class="row mt-3">
              <div class="col-lg-6">
                <!-- sub header -->
                <h6 class="text-white m-1 mb-4 ml-1">
                  Status Distribution
                </h6>
                <div class="chart-pie w-20 mb-4"><canvas id="assetStatusPieChart"></canvas></div>
              </div>

              <div class="col-lg-6">
                <h6 class="text-white m-1 mb-4">
                  Department Distribution
                </h6>
                <div class="chart-pie w-20 mb-4"><canvas id="assetDepartmentPieChart"></canvas></div>
              </div>
            </div>
            <div class="row mt-3">
              <h6 class="text-white m-1 mb-4">
                Value Over Time
              </h6>
</div>
          </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/charts/asset_status_pie.js"></script>
        <script src="assets/charts/asset_department_pie.js"></script>
    </body>
</html>