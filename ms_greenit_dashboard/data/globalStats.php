<?php

//////////////////////////////
// Get yesterday date
$date = new DateTime("NOW");
$date->modify('-1 day');
//////////////////////////////

//////////////////////////////
// Get collect date
$collectDate = new DateTime("NOW");
$collectDate->modify("-" . $config->COLLECT_INFO_PERIOD . " days");
//////////////////////////////

//////////////////////////////
// Get compare date
$compareDate = new DateTime("NOW");
$compareDate->modify("-" . $config->COMPARE_INFO_PERIOD . " days");
//////////////////////////////

//////////////////////////////
// Create data array
$yesterdayData = array();
$collectData = array();
$compareData = array();
//////////////////////////////

//////////////////////////////
// Get yesterday consumption of GreenIT parc
$yesterdayQuery = "
    SELECT 
    DATA 
    FROM greenit_stats 
    WHERE 
    DATE='" . $date->format("Y-m-d") . "'
";
$yesterdayDataResult = mysql2_query_secure($yesterdayQuery, $_SESSION['OCS']["readServer"]);
//////////////////////////////

//////////////////////////////
// Get collect consumption of GreenIT parc
$collectQuery = "
    SELECT 
    DATE, 
    DATA 
    FROM greenit_stats 
    WHERE 
    DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $date->format("Y-m-d") . "'
";
$collectDataResult = mysql2_query_secure($collectQuery, $_SESSION['OCS']["readServer"]);
//////////////////////////////

//////////////////////////////
// Get compare consumption of GreenIT parc
$compareQuery = "
    SELECT 
    DATE, 
    DATA 
    FROM greenit_stats 
    WHERE 
    DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $date->format("Y-m-d") . "'
";
$compareDataResult = mysql2_query_secure($compareQuery, $_SESSION['OCS']["readServer"]);
//////////////////////////////

//////////////////////////////
// Format yesterday data
$yesterdayData = array();
while ($row = mysqli_fetch_object($yesterdayDataResult)) {
    $yesterdayData["GLOBAL"] = json_decode($row->DATA);
}
//////////////////////////////

//////////////////////////////
// Format collect data
$collectData = array();
while ($row = mysqli_fetch_object($collectDataResult)) {
    $collectData[$row->DATE] = json_decode($row->DATA);
}
//////////////////////////////

//////////////////////////////
// Format compare data
$compareData = array();
while ($row = mysqli_fetch_object($compareDataResult)) {
    $compareData[$row->DATE] = json_decode($row->DATA);
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

//////////////////////////////
// Get number of device of the collect period
$nbDevicesCollect = "
    SELECT 
    COUNT(DISTINCT HARDWARE_ID) AS nbDevices 
    FROM greenit 
    WHERE 
    DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $date->format("Y-m-d") . "'
";
$nbDevicesCollectResult = mysql2_query_secure($nbDevicesCollect, $_SESSION['OCS']["readServer"]);
$numberDeviceCollect = mysqli_fetch_object($nbDevicesCollectResult)->nbDevices;
if ($numberDeviceCollect == 0)
    $numberDeviceCollect = 1;
//////////////////////////////

//////////////////////////////
// Get number fo devices of the compare period
$nbDevicesCompare = "
    SELECT 
    COUNT(DISTINCT HARDWARE_ID) AS nbDevices 
    FROM greenit 
    WHERE 
    DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $date->format("Y-m-d") . "'
";
$nbDevicesCompareResult = mysql2_query_secure($nbDevicesCompare, $_SESSION['OCS']["readServer"]);
$numberDeviceCompare = mysqli_fetch_object($nbDevicesCompareResult)->nbDevices;
if ($numberDeviceCompare == 0)
    $numberDeviceCompare = 1;
//////////////////////////////

?>