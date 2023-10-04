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
$yesterdayData = array();
$collectData = array();
$compareData = array();
//////////////////////////////

//////////////////////////////
// Get yesterday data of filtered GreenIT parc
$yesterdayQuery = "
    SELECT 
    greenit.CONSUMPTION, 
    greenit.UPTIME 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    WHERE 
    greenit.DATE='" . $Date->format("Y-m-d") . "' 
    AND hardware.NAME='" . $protectedGet[strtolower(str_replace(" ", "_", $l->g(35)))] . "' 
";
$yesterdayDataResult = mysql2_query_secure($yesterdayQuery, $_SESSION['OCS']["readServer"]);
//////////////////////////////

//////////////////////////////
// Get collect data of filtered GreenIT parc
$collectQuery = "
    SELECT 
    greenit.DATE, 
    greenit.CONSUMPTION, 
    greenit.UPTIME 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    WHERE 
    greenit.DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "' 
    AND hardware.NAME='" . $protectedGet[strtolower(str_replace(" ", "_", $l->g(35)))] . "' 
";
$collectDataResult = mysql2_query_secure($collectQuery, $_SESSION['OCS']["readServer"]);
//////////////////////////////

//////////////////////////////
// Get compare data of filtered GreenIT parc
$compareQuery = "
    SELECT 
    greenit.DATE, 
    greenit.CONSUMPTION, 
    greenit.UPTIME 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    WHERE 
    greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "' 
    AND hardware.NAME='" . $protectedGet[strtolower(str_replace(" ", "_", $l->g(35)))] . "'  
";
$compareDataResult = mysql2_query_secure($compareQuery, $_SESSION['OCS']["readServer"]);
//////////////////////////////

//////////////////////////////
// Format yesterday data
while ($row = mysqli_fetch_object($yesterdayDataResult)) {
    $yesterdayData["FILTERED"] = (object) array(
        "totalConsumption" => $row->CONSUMPTION,
        "totalUptime" => $row->UPTIME,
    );
}
//////////////////////////////

//////////////////////////////
// Format collect data
while ($row = mysqli_fetch_object($collectDataResult)) {
    $collectData[$row->DATE] = (object) array(
        "totalConsumption" => $row->CONSUMPTION,
        "totalUptime" => $row->UPTIME,
    );
}
//////////////////////////////

//////////////////////////////
// Format compare data
while ($row = mysqli_fetch_object($compareDataResult)) {
    $compareData[$row->DATE] = (object) array(
        "totalConsumption" => $row->CONSUMPTION,
        "totalUptime" => $row->UPTIME,
    );
}
//////////////////////////////

//////////////////////////////
// Average of Consumption of the collect period
$sumConsumptionCollect = 0;
if (isset($collectData)) {
    foreach ($collectData as $key => $value) {
        $sumConsumptionCollect += $value->totalConsumption;
    }
}
//////////////////////////////

//////////////////////////////
// Average of Consumption of the compare period
$sumConsumptionCompare = 0;
if (isset($compareData)) {
    foreach ($compareData as $key => $value) {
        $sumConsumptionCompare += $value->totalConsumption;
    }
}
//////////////////////////////

?>