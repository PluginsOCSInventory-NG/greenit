<?php

$date = new DateTime("NOW");
$date->modify('-1 day');

$pastDate = new DateTime("NOW");
$pastDate->modify("-" . $config->COLLECT_INFO_PERIOD . " days");

$compareDate = new DateTime("NOW");
$compareDate->modify("-" . $config->COMPARE_INFO_PERIOD . " days");

$yesterdayQuery = "SELECT greenit.CONSUMPTION, greenit.UPTIME FROM greenit INNER JOIN hardware WHERE greenit.DATE='" . $date->format("Y-m-d") . "' AND hardware.NAME='" . $protectedGet[strtolower(str_replace(" ", "_", $l->g(35)))] . "' AND greenit.HARDWARE_ID=hardware.ID";
$yesterdayDataResult = mysql2_query_secure($yesterdayQuery, $_SESSION['OCS']["readServer"]);

$limitedQuery = "SELECT greenit.DATE, greenit.CONSUMPTION, greenit.UPTIME FROM greenit INNER JOIN hardware WHERE greenit.DATE BETWEEN '" . $pastDate->format("Y-m-d") . "' AND '" . $date->format("Y-m-d") . "' AND hardware.NAME='" . $protectedGet[strtolower(str_replace(" ", "_", $l->g(35)))] . "' AND greenit.HARDWARE_ID=hardware.ID";
$limitedDataResult = mysql2_query_secure($limitedQuery, $_SESSION['OCS']["readServer"]);

$compareQuery = "SELECT greenit.DATE, greenit.CONSUMPTION, greenit.UPTIME FROM greenit INNER JOIN hardware WHERE greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $date->format("Y-m-d") . "' AND hardware.NAME='" . $protectedGet[strtolower(str_replace(" ", "_", $l->g(35)))] . "' AND greenit.HARDWARE_ID=hardware.ID";
$compareDataResult = mysql2_query_secure($compareQuery, $_SESSION['OCS']["readServer"]);

$yesterdayData = array();
while ($row = mysqli_fetch_object($yesterdayDataResult)) {
    $yesterdayData[] = (object) array(
        "totalConsumption" => $row->CONSUMPTION,
        "totalUptime" => $row->UPTIME,
    );
}

$limitedData = array();
while ($row = mysqli_fetch_object($limitedDataResult)) {
    $limitedData[$row->DATE] = (object) array(
        "totalConsumption" => $row->CONSUMPTION,
        "totalUptime" => $row->UPTIME,
    );
}

$compareData = array();
while ($row = mysqli_fetch_object($compareDataResult)) {
    $compareData[$row->DATE] = (object) array(
        "totalConsumption" => $row->CONSUMPTION,
        "totalUptime" => $row->UPTIME,
    );
}

if ($yesterdayData[0]->totalConsumption == null || $yesterdayData[0]->totalUptime == null)
    $yesterdayData = null;
if (count($limitedData) == 0)
    $limitedData = null;
if (count($compareData) == 0)
    $compareData = null;

$sumConsumptionInPeriode = 0;

if (isset($limitedData)) {
    foreach ($limitedData as $key => $value) {
        $sumConsumptionInPeriode += $value->totalConsumption;
    }
}

$sumConsumptionCompare = 0;

if (isset($compareData)) {
    foreach ($compareData as $key => $value) {
        $sumConsumptionCompare += $value->totalConsumption;
    }
}

?>