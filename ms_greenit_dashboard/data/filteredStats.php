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
if (isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))])) {
    $yesterdayQuery = "
        SELECT 
        greenit.CONSUMPTION, 
        greenit.UPTIME 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
        WHERE 
        greenit.DATE='" . $Date->format("Y-m-d") . "' 
        AND hardware.NAME='" . $protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))] . "' 
    ";
} else if (isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))])) {
    $computersData = explode(",", $protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))]);
    $yesterdayQuery = "
        SELECT 
        SUM(greenit.CONSUMPTION) AS CONSUMPTION, 
        SUM(greenit.UPTIME) AS UPTIME 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
        WHERE 
        greenit.DATE='" . $Date->format("Y-m-d") . "' 
    ";
    $yesterdayQuery .= "AND (";
    foreach ($computersData as $computerName) {
        $yesterdayQuery .= "hardware.NAME='" . $computerName . "'";
        if (next($computersData))
            $yesterdayQuery .= " OR ";
    }
    reset($computersData);
    $yesterdayQuery .= ")";
}
$yesterdayDataResult = mysql2_query_secure($yesterdayQuery, $_SESSION['OCS']["readServer"]);
while ($row = mysqli_fetch_object($yesterdayDataResult)) {
    $yesterdayData["FILTERED"] = (object) array(
        "totalConsumption" => $row->CONSUMPTION,
        "totalUptime" => $row->UPTIME,
    );
}
//////////////////////////////

//////////////////////////////
// Get collect data of filtered GreenIT parc
if (isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))])) {
    $collectQuery = "
        SELECT 
        greenit.DATE, 
        greenit.CONSUMPTION, 
        greenit.UPTIME 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
        WHERE 
        greenit.DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "' 
        AND hardware.NAME='" . $protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))] . "' 
    ";
} else if (isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))])) {
    $computersData = explode(",", $protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))]);
    $collectQuery = "
        SELECT 
        greenit.DATE, 
        SUM(greenit.CONSUMPTION) AS CONSUMPTION, 
        SUM(greenit.UPTIME) AS UPTIME 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
        WHERE 
        greenit.DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "' 
    ";
    $collectQuery .= "AND (";
    foreach ($computersData as $computerName) {
        $collectQuery .= "hardware.NAME='" . $computerName . "'";
        if (next($computersData))
            $collectQuery .= " OR ";
    }
    reset($computersData);
    $collectQuery .= ")";
}
$collectDataResult = mysql2_query_secure($collectQuery, $_SESSION['OCS']["readServer"]);
while ($row = mysqli_fetch_object($collectDataResult)) {
    $collectData[$row->DATE] = (object) array(
        "totalConsumption" => $row->CONSUMPTION,
        "totalUptime" => $row->UPTIME,
    );
}
//////////////////////////////

//////////////////////////////
// Get compare data of filtered GreenIT parc
if (isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))])) {
    $compareQuery = "
        SELECT 
        greenit.DATE, 
        greenit.CONSUMPTION, 
        greenit.UPTIME 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
        WHERE 
        greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "' 
        AND hardware.NAME='" . $protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))] . "'  
    ";
} else if (isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))])) {
    $computersData = explode(",", $protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))]);
    $compareQuery = "
        SELECT 
        greenit.DATE, 
        SUM(greenit.CONSUMPTION) AS CONSUMPTION, 
        SUM(greenit.UPTIME) AS UPTIME 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
        WHERE 
        greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "' 
    ";
    $compareQuery .= "AND (";
    foreach ($computersData as $computerName) {
        $compareQuery .= "hardware.NAME='" . $computerName . "'";
        if (next($computersData))
            $compareQuery .= " OR ";
    }
    reset($computersData);
    $compareQuery .= ")";
}
$compareDataResult = mysql2_query_secure($compareQuery, $_SESSION['OCS']["readServer"]);
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

//////////////////////////////
// Get number of device of the collect period
if (isset($computersData)) {
    $nbDevicesCollect = "
        SELECT 
        COUNT(DISTINCT HARDWARE_ID) AS nbDevices 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
        WHERE 
        DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    ";
    $nbDevicesCollect .= "AND (";
    foreach ($computersData as $computerName) {
        $nbDevicesCollect .= "hardware.NAME='" . $computerName . "'";
        if (next($computersData))
            $nbDevicesCollect .= " OR ";
    }
    reset($computersData);
    $nbDevicesCollect .= ")";
    $nbDevicesCollectResult = mysql2_query_secure($nbDevicesCollect, $_SESSION['OCS']["readServer"]);
    $numberDeviceCollect = mysqli_fetch_object($nbDevicesCollectResult)->nbDevices;
    if ($numberDeviceCollect == 0)
        $numberDeviceCollect = 1;
}

//////////////////////////////

//////////////////////////////
// Get number fo devices of the compare period
if (isset($computersData)) {
    $nbDevicesCompare = "
        SELECT 
        COUNT(DISTINCT HARDWARE_ID) AS nbDevices 
        FROM greenit 
        INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
        WHERE 
        DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    ";
    $nbDevicesCompare .= "AND (";
    foreach ($computersData as $computerName) {
        $nbDevicesCompare .= "hardware.NAME='" . $computerName . "'";
        if (next($computersData))
            $nbDevicesCompare .= " OR ";
    }
    reset($computersData);
    $nbDevicesCompare .= ")";
    $nbDevicesCompareResult = mysql2_query_secure($nbDevicesCompare, $_SESSION['OCS']["readServer"]);
    $numberDeviceCompare = mysqli_fetch_object($nbDevicesCompareResult)->nbDevices;
    if ($numberDeviceCompare == 0)
        $numberDeviceCompare = 1;
}
//////////////////////////////

?>