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
$manufacturers = array();
$yesterdayData = array();
$collectData = array();
$compareData = array();
//////////////////////////////


//////////////////////////////
// Get manufacturer
$manufacturersQuery = "
    SELECT 
    CASE WHEN TRIM(SMANUFACTURER)='' 
    THEN 'Unknown' 
    ELSE SMANUFACTURER 
    END AS MANUFACTURER 
    FROM bios 
    GROUP BY MANUFACTURER;
";
$manufacturersDataResult = mysql2_query_secure($manufacturersQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($manufacturersDataResult)) {
    array_push($manufacturers, $row->MANUFACTURER);
}
//////////////////////////////

//////////////////////////////
// Get yesterday data of all manufacturer group
foreach ($manufacturers as $manufacturer) {
    $yesterdayManufacturerQuery = "
        SELECT 
        SUM(greenit.CONSUMPTION) AS totalConsumption, 
        SUM(greenit.UPTIME) AS totalUptime 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
        INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
        WHERE 
        bios.SMANUFACTURER='$manufacturer'
        AND greenit.DATE='" . $Date->format("Y-m-d") . "'
    ";
    $yesterdayManufacturerDataResult = mysql2_query_secure($yesterdayManufacturerQuery, $_SESSION['OCS']["readServer"]);

    while ($row = mysqli_fetch_object($yesterdayManufacturerDataResult)) {
        if (
            $row->totalConsumption != NULL ||
            $row->totalUptime != NULL
        ) {
            $yesterdayData[$manufacturer] = (object) array(
                "totalConsumption" => $row->totalConsumption,
                "totalUptime" => $row->totalUptime,
            );
        }
    }
}
//////////////////////////////

//////////////////////////////
// Get collect data of all manufacturer group
foreach ($manufacturers as $manufacturer) {
    $collectManufacturerQuery = "
        SELECT 
        greenit.DATE, 
        SUM(greenit.CONSUMPTION) AS totalConsumption, 
        SUM(greenit.UPTIME) AS totalUptime 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
        INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
        WHERE 
        bios.SMANUFACTURER='$manufacturer'
        AND greenit.DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
        GROUP BY greenit.DATE;
    ";
    $collectManufacturerDataResult = mysql2_query_secure($collectManufacturerQuery, $_SESSION['OCS']["readServer"]);

    while ($row = mysqli_fetch_object($collectManufacturerDataResult)) {
        if (
            $row->totalConsumption != NULL ||
            $row->totalUptime != NULL
        ) {
            $collectData[$manufacturer][$row->DATE] = (object) array(
                "totalConsumption" => $row->totalConsumption,
                "totalUptime" => $row->totalUptime,
            );
        }
    }
}

$sumConsumptionCollect = array();

if (isset($collectData)) {
    foreach ($collectData as $group => $date) {
        $sumConsumptionCollect[$group] = 0;
        foreach ($date as $value) {
            $sumConsumptionCollect[$group] += $value->totalConsumption;
        }
    }
}

//////////////////////////////


//////////////////////////////
// Get compare data of all manufacturer group
foreach ($manufacturers as $manufacturer) {
    $compareManufacturerQuery = "
        SELECT 
        greenit.DATE, 
        SUM(greenit.CONSUMPTION) AS totalConsumption, 
        SUM(greenit.UPTIME) AS totalUptime 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
        INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
        WHERE 
        bios.SMANUFACTURER='$manufacturer'
        AND greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
        GROUP BY greenit.DATE
    ";
    $compareManufacturerDataResult = mysql2_query_secure($compareManufacturerQuery, $_SESSION['OCS']["readServer"]);

    while ($row = mysqli_fetch_object($compareManufacturerDataResult)) {
        if (
            $row->totalConsumption != NULL ||
            $row->totalUptime != NULL
        ) {
            $compareData[$manufacturer][$row->DATE] = (object) array(
                "totalConsumption" => $row->totalConsumption,
                "totalUptime" => $row->totalUptime,
            );
        }
    }
}

$sumConsumptionCompare = array();

if (isset($compareData)) {
    foreach ($compareData as $group => $date) {
        $sumConsumptionCompare[$group] = 0;
        foreach ($date as $value) {
            $sumConsumptionCompare[$group] += $value->totalConsumption;
        }
    }
}
//////////////////////////////

?>