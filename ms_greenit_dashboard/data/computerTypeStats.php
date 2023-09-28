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

$sumConsumptionLimited = array();

foreach($limitedData as $group => $date)
{
    $sumConsumptionLimited[$group] = 0;
    foreach($date as $value)
    {
        $sumConsumptionLimited[$group] += $value->totalConsumption;
    }
}

if(count($limitedData) == 0) $limitedData = null;

$sumConsumptionInPeriode = 0;

?>