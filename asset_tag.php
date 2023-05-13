<?php
    $id = $_GET['id'];
    $name = $_GET['name'];
    $class = $_GET['class'];
    // $class = mysqli_fetch_array($conn->query("SELECT name FROM asset_class WHERE id = '$classid' LIMIT 1"))['name']; 
    if (isset($_GET['description'])) {
        $description = $_GET['description'];
    }
    if (isset($_GET['entity'])) {
        $entity = $_GET['entity'];
        // $entity = mysqli_fetch_array($conn->query("SELECT name FROM entity WHERE id = '$entityid' LIMIT 1"))['name']; 
    }
    if (isset($_GET['department'])) {
        $department = $_GET['department'];
        // $department = mysqli_fetch_array($conn->query("SELECT name FROM department WHERE id = '$departmentid' LIMIT 1"))['name']; 
    }
    if (isset($_GET['position'])) {
        $position = $_GET['position'];
    }
    if (isset($_GET['expire'])) {
        $expire = $_GET['expire'];
    }
    if (isset($_GET['serialnumber'])) {
        $serialnumber = $_GET['serialnumber'];
    }
    if (isset($_GET['brand'])) {
        $brand = $_GET['brand'];
    }
    if (isset($_GET['model'])) {
        $model = $_GET['model'];
    }
    if (isset($_GET['user'])) {
        $user = $_GET['user'];
        // $user = mysqli_fetch_array($conn->query("SELECT name FROM user WHERE id = '$userid' LIMIT 1"))['name'];
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
        <h2 class="asset-name"><?=$name?></h2>
        <p class="asset-id">Asset ID: <?=$id?></p>
        <p class="asset-category">Category: <?=$class?></p>
        <p class="asset-description">Description: <?=$description?></p>
        <?php
            if (isset($entity)) {
                echo '<p class="asset-description">Entity: ' . $entity . '</p>';
            }
            if (isset($department)) {
                echo '<p class="asset-description">Department: ' . $department . '</p>';
            }
            if (isset($position)) {
                echo '<p class="asset-description">Position: ' . $position . '</p>';
            }
            if (isset($expire)) {
                echo '<p class="asset-description">Expire: ' . $expire . '</p>';
            }
            if (isset($serialnumber)) {
                echo '<p class="asset-description">Serial Number: ' . $serialnumber . '</p>';
            }
            if (isset($brand)) {
                echo '<p class="asset-description">Brand: ' . $brand . '</p>';
            }
            if (isset($model)) {
                echo '<p class="asset-description">Model: ' . $model . '</p>';
            }
            if (isset($user)) {
                echo '<p class="asset-description">User: ' . $user . '</p>';
            }
        ?>
        <div class="card-footer">
            <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=12345&choe=UTF-8" alt="QR Code" />
            <button class="print-button" onclick="window.print()">Print</button>
        </div>
    </div>
</body>
</html>