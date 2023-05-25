<?php

/**
 * Sets an asset to be RETIRED (4) and sets user to NULL.
 * @param {object} $row - The database row object corresponding to the item.
 * @return {void}
 *
 * @example
 * handle_expired_item($row);
 */
function handle_expired_item($row){
    include("includes/db/connect.php");
    $asset_id = $row["id"];
    $sql = "UPDATE asset SET status = 4, user = NULL WHERE id = $asset_id";
    $result = $conn->query($sql);
    if(!$result) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

/**
 * Calculates the current price of an item.
 * Pc = P0 * (1 - floor(deltaY)/totalY)
 * Where deltaY is the number of years between the current date and the create date, rounded down
 * and totalY is the number of years between the create date and the expire date, rounded down.
 * @param {object} $row - The database row object corresponding to the item.
 * @param {number} $date - The target date for calculation in unix time.
 * @return {number} The current price of the item.
 *
 * @example
 * calculate_price($row);
 */
function calculate_price($row, $date) {
    $original_price = $row["price"];
    $expire_date = $row["expire"]; // convert to Unix time
    $create_date = $row["date_created"];

    if(isset($expire_date) && $expire_date < time()) {
        $current_price = 0;
        // handle_expired_item($row);
    }
    else if(isset($expire_date)){
        // calculate the number of years between the two dates
        $deltaY = floor(($date - $create_date) / (60 * 60 * 24 * 365));
        $totalY = floor(($expire_date - $create_date) / (60 * 60 * 24 * 365));

        if($totalY == 0)
            return false;

        // calculate the percentage of lifespan remaining
        $remaining_life = max(0, 1 - ($deltaY / $totalY));

        // apply linear depreciation based on the number of years
        $current_price = $original_price * $remaining_life;
        // apply a non-linear depreciation curve to the price
        // $current_price = $original_price * pow($remaining_life, 2);
    }
    else {
        return -1;
    }

    if($current_price <= 0) {
        $current_price = 0;
    }

    return $current_price;
}
?>