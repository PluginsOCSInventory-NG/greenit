<?php

//////////////////////////////
// Get yesterday date
$Date = new DateTime("NOW");
$Date->modify('-1 day');
//////////////////////////////

//////////////////////////////
// Get collect date
$collectDate = new DateTime("NOW");
$collectDate->modify("-" . $config->COLLECT_INFO_PERIOD - 1 . " days");
//////////////////////////////

//////////////////////////////
// Get compare date
$compareDate = new DateTime("NOW");
$compareDate->modify("-" . $config->COMPARE_INFO_PERIOD - 1 . " days");
//////////////////////////////

//////////////////////////////
// Create data array
$yesterdayManufacturers = array();
$collectManufacturers = array();
$compareManufacturers = array();
$yesterdayData = array();
$collectData = array();
$compareData = array();
//////////////////////////////

//////////////////////////////
// Get yesterday data of all manufacturer group
$yesterdayManufacturersQuery = "
    SELECT 
    bios.SMANUFACTURER AS MANUFACTURER, 
    SUM(greenit.CONSUMPTION) AS totalConsumption
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
    WHERE
    greenit.DATE='" . $Date->format("Y-m-d") . "'
    GROUP BY MANUFACTURER
    ORDER BY totalConsumption DESC 
    LIMIT 5
";
$yesterdayManufacturersDataResult = mysql2_query_secure($yesterdayManufacturersQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($yesterdayManufacturersDataResult)) {
    array_push($yesterdayManufacturers, $row->MANUFACTURER);
}

$yesterdayManufacturerQuery = "
    SELECT 
    bios.SMANUFACTURER AS MANUFACTURER, 
    SUM(greenit.CONSUMPTION) AS totalConsumption, 
    SUM(greenit.UPTIME) AS totalUptime 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
    WHERE 
    greenit.DATE='" . $Date->format("Y-m-d") . "'
    GROUP BY MANUFACTURER
    ORDER BY totalConsumption DESC 
    LIMIT 5
";
$yesterdayManufacturerDataResult = mysql2_query_secure($yesterdayManufacturerQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($yesterdayManufacturerDataResult)) {
    if (
        $row->totalConsumption != NULL ||
        $row->totalUptime != NULL
    ) {
        $yesterdayData[$row->MANUFACTURER] = (object) array(
            "totalConsumption" => $row->totalConsumption,
            "totalUptime" => $row->totalUptime,
        );
    }
}
//////////////////////////////

//////////////////////////////
// Get collect data of all manufacturer group
$collectManufacturersQuery = "
    SELECT 
    bios.SMANUFACTURER AS MANUFACTURER, 
    SUM(greenit.CONSUMPTION) AS totalConsumption
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
    WHERE
    greenit.DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    GROUP BY MANUFACTURER
    ORDER BY totalConsumption DESC 
    LIMIT 5
";
$collectManufacturersDataResult = mysql2_query_secure($collectManufacturersQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($collectManufacturersDataResult)) {
    array_push($collectManufacturers, $row->MANUFACTURER);
}

$collectManufacturerQuery = "
    SELECT 
    bios.SMANUFACTURER AS MANUFACTURER, 
    SUM(greenit.CONSUMPTION) AS totalConsumption, 
    SUM(greenit.UPTIME) AS totalUptime 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
    WHERE 
    greenit.DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    GROUP BY MANUFACTURER
    ORDER BY totalConsumption DESC
    LIMIT 5
";
$collectManufacturerDataResult = mysql2_query_secure($collectManufacturerQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($collectManufacturerDataResult)) {
    if (
        $row->totalConsumption != NULL ||
        $row->totalUptime != NULL
    ) {
        $collectData[$row->MANUFACTURER] = (object) array(
            "totalConsumption" => $row->totalConsumption,
            "totalUptime" => $row->totalUptime,
        );
    }
}
//////////////////////////////


//////////////////////////////
// Get compare data of all manufacturer group
$compareManufacturersQuery = "
    SELECT 
    bios.SMANUFACTURER AS MANUFACTURER, 
    SUM(greenit.CONSUMPTION) AS totalConsumption
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
    WHERE
    greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    GROUP BY MANUFACTURER
    ORDER BY totalConsumption DESC 
    LIMIT 5
";
$compareManufacturersDataResult = mysql2_query_secure($compareManufacturersQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($compareManufacturersDataResult)) {
    array_push($compareManufacturers, $row->MANUFACTURER);
}

$compareManufacturerQuery = "
    SELECT 
    bios.SMANUFACTURER AS MANUFACTURER, 
    SUM(greenit.CONSUMPTION) AS totalConsumption, 
    SUM(greenit.UPTIME) AS totalUptime 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
    WHERE 
    greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    GROUP BY MANUFACTURER
    ORDER BY totalConsumption DESC 
    LIMIT 5
";
$compareManufacturerDataResult = mysql2_query_secure($compareManufacturerQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($compareManufacturerDataResult)) {
    if (
        $row->totalConsumption != NULL ||
        $row->totalUptime != NULL
    ) {
        $compareData[$row->MANUFACTURER] = (object) array(
            "totalConsumption" => $row->totalConsumption,
            "totalUptime" => $row->totalUptime,
        );
    }
}

$nbDevicesCompareQuery = "
    SELECT 
    COUNT(DISTINCT greenit.HARDWARE_ID) AS COUNT,
    bios.SMANUFACTURER AS MANUFACTURER,
    SUM(greenit.CONSUMPTION) AS totalConsumption 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
    WHERE 
    greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    GROUP BY MANUFACTURER
    ORDER BY totalConsumption DESC 
    LIMIT 5
";
$nbDevicesCompareResult = mysql2_query_secure($nbDevicesCompareQuery, $_SESSION['OCS']["readServer"]);
$nbDevicesCompare = array();
while ($row = mysqli_fetch_object($nbDevicesCompareResult)) {
    $nbDevicesCompare[$row->MANUFACTURER] = $row->COUNT;
}
foreach ($nbDevicesCompare as $group => $value) {
    if ($value == 0)
        $value = 1;
}
//////////////////////////////

?>