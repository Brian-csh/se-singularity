<?php
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
    $expire_date = strtotime($row["expire"]); // convert to Unix time
    $create_date = $row["date_created"];

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

    return $current_price;
}
?>