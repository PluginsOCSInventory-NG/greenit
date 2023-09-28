<?php

$date = new DateTime("NOW");
$date->modify('-1 day');

$pastDate = new DateTime("NOW");
$pastDate->modify('-1 day');
$pastDate->modify("-".$config->COLLECT_INFO_PERIOD." days");

$compareDate = new DateTime("NOW");
$compareDate->modify('-1 day');
$compareDate->modify("-".$config->COMPARE_INFO_PERIOD." days");

$yesterdayQuery = "SELECT DATA FROM greenit_stats WHERE DATE='".$date->format("Y-m-d")."'";
$yesterdayDataResult = mysql2_query_secure($yesterdayQuery, $_SESSION['OCS']["readServer"]);

$limitedQuery = "SELECT DATE,DATA FROM greenit_stats WHERE DATE BETWEEN '".$pastDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."'";
$limitedDataResult = mysql2_query_secure($limitedQuery, $_SESSION['OCS']["readServer"]);

$compareQuery = "SELECT DATE,DATA FROM greenit_stats WHERE DATE BETWEEN '".$compareDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."'";
$compareDataResult = mysql2_query_secure($compareQuery, $_SESSION['OCS']["readServer"]);

$yesterdayData = array();
while ($row = mysqli_fetch_object($yesterdayDataResult)) {
    $yesterdayData[] = json_decode($row->DATA);
}

$limitedData = array();
while ($row = mysqli_fetch_object($limitedDataResult)) {
    $limitedData[$row->DATE] = json_decode($row->DATA);
}

$compareData = array();
while ($row = mysqli_fetch_object($compareDataResult)) {
    $compareData[$row->DATE] = json_decode($row->DATA);
}

if($yesterdayData[0]->totalConsumption == null || $yesterdayData[0]->totalUptime == null) $yesterdayData = null;
if(count($limitedData) == 0) $limitedData = null;
if(count($compareData) == 0) $compareData = null;
// Average of Consumption
$sumConsumptionInPeriode = 0;

if (isset($limitedData)) {
    foreach($limitedData as $key => $value)
    {
        $sumConsumptionInPeriode += $value->totalConsumption;
    }
}

$sumConsumptionCompare = 0;

if (isset($compareData)) {
    foreach($compareData as $key => $value)
    {
        $sumConsumptionCompare += $value->totalConsumption;
    }
}

// we need the number of devices in the greenit table to calculate the average consumption (over a specific period if needed)
$nbDevicesInPeriod = "SELECT COUNT(DISTINCT HARDWARE_ID) AS nbDevices FROM greenit WHERE DATE BETWEEN '".$pastDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."'";
$nbDevicesInPeriodResult = mysql2_query_secure($nbDevicesInPeriod, $_SESSION['OCS']["readServer"]);
$numberDeviceInPeriod = mysqli_fetch_object($nbDevicesInPeriodResult)->nbDevices;

$nbDevicesCompare = "SELECT COUNT(DISTINCT HARDWARE_ID) AS nbDevices FROM greenit WHERE DATE BETWEEN '".$compareDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."'";
$nbDevicesCompareResult = mysql2_query_secure($nbDevicesCompare, $_SESSION['OCS']["readServer"]);
$numberDeviceCompare = mysqli_fetch_object($nbDevicesCompareResult)->nbDevices;

?>