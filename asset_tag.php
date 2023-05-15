<?php
    include "includes/db/connect.php";
    $id = $_GET['id'];

    //fetch the asset entry
    $sql = "SELECT * FROM asset WHERE id = $id LIMIT 1";
    $result = $conn->query($sql);

    if (!$result) {
        exit ("No asset found with that ID.");
    }

    $row = $result->fetch_assoc();

    //obtain the basic information of the asset
    $name = $row['name'];
    if (isset($row['class'])) {
        $class_id = $row['class'];
        $class = mysqli_fetch_array($conn->query("SELECT name FROM asset_class WHERE id = '$class_id'"))['name'];
    } else {
        $class = "N/A";
    }
    $qr_url = "https://singularity-eam-singularity.app.secoder.net/asset_info.php?id=" . $row['id'];

    //custom template
    $department_id = $row['department'];
    $sql_dept = "SELECT * FROM department WHERE id = $department_id LIMIT 1";
    $result_dept = $conn->query($sql_dept);
    $row_dept = $result_dept->fetch_assoc();
    if (isset($row_dept["template"]) && !empty($row_dept["template"])) {
        $template = json_decode($row_dept["template"]);
    } else {
        $template = [];
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Asset Tag Card</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
        }
        .asset-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            width: 300px;
            padding: 20px;
            margin: 10px;
            text-align: center;
        }
        .asset-card h2 {
            margin-bottom: 10px;
            font-size: 24px;
            color: #333;
        }
        .asset-card p {
            font-size: 18px;
            color: #777;
            margin-bottom: 10px;
        }
        .asset-id {
            font-weight: bold;
            color: #555;
        }
        .asset-category {
            font-style: italic;
            color: #888;
        }
        .card-footer {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 10px;
        }
        .print-button {
            padding: 10px 20px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            background-color: #008CBA; /* Blue */
            color: white;
            cursor: pointer;
        }
        .print-button:hover {
            background-color: #007B9A; /* Darker blue */
        }
        /* Styles for printing */
        @media print {
            body {
                height: auto;
                background-color: #fff;
            }
            .asset-card {
                box-shadow: none;
                border: 1px solid #333;
                page-break-inside: avoid; /* Avoid page breaks inside card */
            }
            .print-button {
                display: none; /* Hide print button when printing */
            }
        }
    </style>
</head>
<body>
    <div class="asset-card">
        <?php
            if (in_array("entity", $template)) {
                $entity_id = $row_dept['entity'];
                $sql_entity = "SELECT * from entity WHERE id='$entity_id' LIMIT 1";
                $result_entity = $conn->query($sql_entity);
                $row_entity = $result_entity->fetch_assoc();
                $entity = $row_entity["name"];
                echo '<h2 class="asset-description"><u>' . $entity . '</u></h2>';
            }
            if (in_array("department", $template)) {
                echo '<p class="asset-description">' . $row_dept['name'] . '<br></p>';
            }
        ?>
        <h2 class="asset-name"><?=$name?></h2>
        <p class="asset-id">Asset ID: <?=$id?></p>
        <p class="asset-category">Category: <?=$class?></p>
        <?php
            if (in_array("description", $template)) {
                $description = (isset($row['description']) && !empty($row['description'])) ? $row['description'] : "N/A";
                echo '<p class="asset-description">Description: ' . $description . '<br></p>';
            }
            if (in_array("position", $template)) {
                $position = (isset($row['position']) && !empty($row['position'])) ? $row['position'] : "N/A";
                echo '<p class="asset-description">Position: ' . $position . '<br></p>';
            }
            if (in_array("expire", $template)) {
                $expire = (isset($row['expire']) && !empty($row['expire'])) ? $row['expire'] : "N/A";
                echo '<p class="asset-description">Expire: ' . $expire . '<br></p>';
            }
            if (in_array("serial number", $template)) {
                $serialnumber = (isset($row['serial number']) && !empty($row['serial number'])) ? $row['serial number'] : "N/A";
                echo '<p class="asset-description">Serial Number: ' . $serialnumber . '<br></p>';
            }
            if (in_array("brand", $template)) {
                $brand = (isset($row['brand']) && !empty($row['brand'])) ? $row['brand'] : "N/A";
                echo '<p class="asset-description">Brand: ' . $brand . '<br></p>';
            }
            if (in_array("model", $template)) {
                $model = (isset($row['model']) && !empty($row['model'])) ? $row['model'] : "N/A";
                echo '<p class="asset-description">Model: ' . $model . '<br></p>';
            }
        ?>
        <div class="card-footer">
            <div id="qrcode"></div>
            <button class="print-button" onclick="window.print()">Print</button>
        </div>
    </div>
    <script src="js/qrcode.min.js"></script>
    <script>
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "<?=$qr_url?>",
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>