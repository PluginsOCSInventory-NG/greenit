<?php

$date = new DateTime("NOW");
$date->modify('-1 day');

$pastDate = new DateTime("NOW");
$pastDate->modify('-1 day');
$pastDate->modify("-".$config->COLLECT_INFO_PERIOD." days");

$compareDate = new DateTime("NOW");
$compareDate->modify('-1 day');
$compareDate->modify("-".$config->COMPARE_INFO_PERIOD." days");

$yesterdayQuery = "SELECT SUM(greenit.CONSUMPTION) AS totalConsumption, SUM(greenit.UPTIME) AS totalUptime FROM greenit INNER JOIN hardware WHERE greenit.DATE='".$date->format("Y-m-d")."' AND hardware.OSNAME='".$protectedPost[strtolower(str_replace(" ", "_",$l->g(25)))]."' AND greenit.HARDWARE_ID=hardware.ID";
$yesterdayDataResult = mysql2_query_secure($yesterdayQuery, $_SESSION['OCS']["readServer"]);

$limitedQuery = "SELECT greenit.DATE, SUM(greenit.CONSUMPTION) AS totalConsumption, SUM(greenit.UPTIME) AS totalUptime FROM greenit INNER JOIN hardware WHERE greenit.DATE BETWEEN '".$pastDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."' AND hardware.OSNAME='".$protectedPost[strtolower(str_replace(" ", "_",$l->g(25)))]."' AND greenit.HARDWARE_ID=hardware.ID GROUP BY greenit.DATE";
$limitedDataResult = mysql2_query_secure($limitedQuery, $_SESSION['OCS']["readServer"]);

$compareQuery = "SELECT greenit.DATE, SUM(greenit.CONSUMPTION) AS totalConsumption, SUM(greenit.UPTIME) AS totalUptime FROM greenit INNER JOIN hardware WHERE greenit.DATE BETWEEN '".$compareDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."' AND hardware.OSNAME='".$protectedPost[strtolower(str_replace(" ", "_",$l->g(25)))]."' AND greenit.HARDWARE_ID=hardware.ID GROUP BY greenit.DATE";
$compareDataResult = mysql2_query_secure($compareQuery, $_SESSION['OCS']["readServer"]);

$yesterdayData = array();
while ($row = mysqli_fetch_object($yesterdayDataResult)) {
    $yesterdayData[] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}

$limitedData = array();
while ($row = mysqli_fetch_object($limitedDataResult)) {
    $limitedData[$row->DATE] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}

$compareData = array();
while ($row = mysqli_fetch_object($compareDataResult)) {
    $compareData[$row->DATE] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}

if(count($yesterdayData) == 0) $yesterdayData = null;
if(count($limitedData) == 0) $limitedData = null;
if(count($compareData) == 0) $compareData = null;

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
$nbDevicesQuery = "SELECT COUNT(DISTINCT HARDWARE_ID) AS nbDevices FROM greenit";
$nbDevicesResult = mysql2_query_secure($nbDevicesQuery, $_SESSION['OCS']["readServer"]);
$numberDevice = mysqli_fetch_object($nbDevicesResult)->nbDevices;

$nbDevicesInPeriod = "SELECT COUNT(DISTINCT HARDWARE_ID) AS nbDevices FROM greenit WHERE DATE BETWEEN '".$pastDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."'";
$nbDevicesInPeriodResult = mysql2_query_secure($nbDevicesInPeriod, $_SESSION['OCS']["readServer"]);
$numberDeviceInPeriode = mysqli_fetch_object($nbDevicesInPeriodResult)->nbDevices;


?>