<?php

$date = new DateTime("NOW");
$date->modify('-1 day');

$pastDate = new DateTime("NOW");
$pastDate->modify('-1 day');
$pastDate->modify("-".$config->COLLECT_INFO_PERIOD." days");

$compareDate = new DateTime("NOW");
$compareDate->modify('-1 day');
$compareDate->modify("-".$config->COMPARE_INFO_PERIOD." days");

$osGroupQuery = "SELECT OSNAME FROM hardware WHERE OSNAME LIKE '%Windows%' AND DEVICEID<>'_SYSTEMGROUP_' AND DEVICEID<>'_DOWNLOADGROUP_' GROUP BY OSNAME ORDER BY OSNAME";
$osGroupDataResult = mysql2_query_secure($osGroupQuery, $_SESSION['OCS']["readServer"]);

$osGroupData = array();
while ($row = mysqli_fetch_object($osGroupDataResult)) {
    $osGroupData[] = $row->OSNAME;
}

$limitedData = array();
foreach($osGroupData as $key => $osGroup)
{
    $limitedQuery = "SELECT hardware.OSNAME AS osName, greenit.DATE, SUM(greenit.CONSUMPTION) AS totalConsumption, SUM(greenit.UPTIME) AS totalUptime FROM greenit INNER JOIN hardware WHERE greenit.DATE BETWEEN '".$pastDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."' AND hardware.OSNAME='".$osGroup."' AND greenit.HARDWARE_ID=hardware.ID GROUP BY greenit.DATE";
    $limitedDataResult = mysql2_query_secure($limitedQuery, $_SESSION['OCS']["readServer"]);

    while ($row = mysqli_fetch_object($limitedDataResult)) {
        $limitedData[$row->osName][$row->DATE] = (object) array(
            "totalConsumption" => $row->totalConsumption,
            "totalUptime" => $row->totalUptime,
        );
    }
}

$compareData = array();
foreach($osGroupData as $key => $osGroup)
{
    $compareQuery = "SELECT hardware.OSNAME AS osName, greenit.DATE, SUM(greenit.CONSUMPTION) AS totalConsumption, SUM(greenit.UPTIME) AS totalUptime FROM greenit INNER JOIN hardware WHERE greenit.DATE BETWEEN '".$compareDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."' AND hardware.OSNAME='".$osGroup."' AND greenit.HARDWARE_ID=hardware.ID GROUP BY greenit.DATE";
    $compareDataResult = mysql2_query_secure($compareQuery, $_SESSION['OCS']["readServer"]);

    while ($row = mysqli_fetch_object($compareDataResult)) {
        $compareData[$row->osName][$row->DATE] = (object) array(
            "totalConsumption" => $row->totalConsumption,
            "totalUptime" => $row->totalUptime,
        );
    }
}

if(count($limitedData) == 0) $limitedData = null;
if(count($compareData) == 0) $compareData = null;

$sumConsumptionLimited = array();

foreach($limitedData as $group => $date)
{
    $sumConsumptionLimited[$group] = 0;
    foreach($date as $value)
    {
        $sumConsumptionLimited[$group] += $value->totalConsumption;
    }
}

$sumUptimeLimited = array();

foreach($limitedData as $group => $date)
{
    $sumUptimeLimited[$group] = 0;
    foreach($date as $value)
    {
        $sumUptimeLimited[$group] += $value->totalUptime;
    }
}

$sumConsumptionCompare = array();

foreach($compareData as $group => $date)
{
    $sumConsumptionCompare[$group] = 0;
    foreach($date as $value)
    {
        $sumConsumptionCompare[$group] += $value->totalConsumption;
    }
}

$sumUptimeCompare = array();

foreach($compareData as $group => $date)
{
    $sumUptimeCompare[$group] = 0;
    foreach($date as $value)
    {
        $sumUptimeCompare[$group] += $value->totalUptime;
    }
}

?>