<?php

$date = new DateTime("NOW");
$date->modify('-1 day');
$pastDate = new DateTime("NOW");
$pastDate->modify('-1 day');
$pastDate->modify("-".$config->COLLECT_INFO_PERIOD." days");

$yesterdayQuery = "SELECT DATA FROM greenit_stats WHERE DATE='".$date->format("Y-m-d")."'";
$yesterdayDataResult = mysql2_query_secure($yesterdayQuery, $_SESSION['OCS']["readServer"]);

$limitedQuery = "SELECT DATE,DATA FROM greenit_stats WHERE DATE BETWEEN '".$pastDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."'";
$limitedDataResult = mysql2_query_secure($limitedQuery, $_SESSION['OCS']["readServer"]);

$dataQuery = "SELECT DATE,DATA FROM greenit_stats";
$dataResult = mysql2_query_secure($dataQuery, $_SESSION['OCS']["readServer"]);

$yesterdayData = array();
while ($row = mysqli_fetch_object($yesterdayDataResult)) {
    $yesterdayData[] = json_decode($row->DATA);
}

$limitedData = array();
while ($row = mysqli_fetch_object($limitedDataResult)) {
    $limitedData[$row->DATE] = json_decode($row->DATA);
}

$data = array();
while ($row = mysqli_fetch_object($dataResult)) {
    $data[$row->DATE] = json_decode($row->DATA);
}

if(count($limitedData) == 0) $limitedData = null;
if(count($yesterdayData) == 0) $yesterdayData = null;
if(count($data) == 0) $data = null;

// we need the number of devices in the greenit table to calculate the average consumption (over a specific period if needed)
$nbDevicesQuery = "SELECT COUNT(DISTINCT HARDWARE_ID) AS nbDevices FROM greenit";
$nbDevicesResult = mysql2_query_secure($nbDevicesQuery, $_SESSION['OCS']["readServer"]);
$numberDevice = mysqli_fetch_object($nbDevicesResult)->nbDevices;

$nbDevicesInPeriod = "SELECT COUNT(DISTINCT HARDWARE_ID) AS nbDevices FROM greenit WHERE DATE BETWEEN '".$pastDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."'";
$nbDevicesInPeriodResult = mysql2_query_secure($nbDevicesInPeriod, $_SESSION['OCS']["readServer"]);
$numberDeviceInPeriode = mysqli_fetch_object($nbDevicesInPeriodResult)->nbDevices;

// Average of Consumption
$sumConsumptionInPeriode = 0;
if (isset($limitedData)) {
    foreach($limitedData as $key => $value)
    {
        $sumConsumptionInPeriode += $value->totalConsumption;
    }
}
$sumConsumption = 0;

if (isset($data)) {
    foreach($data as $key => $value)
    {
        $sumConsumption += $value->totalConsumption;
    }
}

?>