<?php

$date = new DateTime("NOW");
$date->modify('-1 day');

$collectDate = new DateTime("NOW");
$collectDate->modify("-" . $config->COLLECT_INFO_PERIOD . " days");

$compareDate = new DateTime("NOW");
$compareDate->modify("-" . $config->COMPARE_INFO_PERIOD . " days");

$manufacturers = array();

$yesterdayData = array();
$collectData = array();
$compareData = array();

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
// Get yesterday consumption of all manufacturer group
foreach ($manufacturers as $manufacturer) {
    $yesterdayManufacturerQuery = "
        SELECT 
        SUM(greenit.CONSUMPTION) AS totalConsumption, 
        SUM(greenit.UPTIME) AS totalUptime 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
        INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
        WHERE bios.SMANUFACTURER='$manufacturer'
        AND greenit.DATE='" . $date->format("Y-m-d") . "'
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

?>