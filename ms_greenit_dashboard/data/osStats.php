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
// Get yesterday consumption of all Windows clients
$yesterdayClientQuery = "
    SELECT 
    SUM(greenit.CONSUMPTION) AS totalConsumption, 
    SUM(greenit.UPTIME) AS totalUptime 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    WHERE greenit.DATE='" . $Date->format("Y-m-d") . "' 
    AND hardware.OSNAME LIKE '%Windows%' 
    AND hardware.OSNAME NOT IN (SELECT hardware.OSNAME FROM hardware WHERE hardware.OSNAME LIKE '%Windows Server%') 
";
$yesterdayClientDataResult = mysql2_query_secure($yesterdayClientQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($yesterdayClientDataResult)) {
    $yesterdayData["CLIENTS"] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}
//////////////////////////////

//////////////////////////////
// Get yesterday consumption of all Windows servers
$yesterdayServerQuery = "
    SELECT 
    SUM(greenit.CONSUMPTION) AS totalConsumption, 
    SUM(greenit.UPTIME) AS totalUptime 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    WHERE greenit.DATE='" . $Date->format("Y-m-d") . "' 
    AND hardware.OSNAME LIKE '%Windows Server%' 
";
$yesterdayServerDataResult = mysql2_query_secure($yesterdayServerQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($yesterdayServerDataResult)) {
    $yesterdayData["SERVERS"] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}
//////////////////////////////

//////////////////////////////
// Get collect consumption of all Windows clients
$collectClientQuery = "
    SELECT 
    greenit.DATE, 
    SUM(greenit.CONSUMPTION) AS totalConsumption, 
    SUM(greenit.UPTIME) AS totalUptime 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    WHERE greenit.DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "' 
    AND hardware.OSNAME LIKE '%Windows%' 
    AND hardware.OSNAME NOT IN (SELECT hardware.OSNAME FROM hardware WHERE hardware.OSNAME LIKE '%Windows Server%') 
    GROUP BY greenit.DATE
";
$collectClientDataResult = mysql2_query_secure($collectClientQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($collectClientDataResult)) {
    $collectData["CLIENTS"][$row->DATE] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}
//////////////////////////////

//////////////////////////////
// Get collect consumption of all Windows servers
$collectServerQuery = "
    SELECT 
    greenit.DATE, 
    SUM(greenit.CONSUMPTION) AS totalConsumption, 
    SUM(greenit.UPTIME) AS totalUptime 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    WHERE greenit.DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "' 
    AND hardware.OSNAME LIKE '%Windows Server%' 
    GROUP BY greenit.DATE
";
$collectServerDataResult = mysql2_query_secure($collectServerQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($collectServerDataResult)) {
    $collectData["SERVERS"][$row->DATE] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}
//////////////////////////////

//////////////////////////////
// Get compare consumption of all Windows clients
$compareClientQuery = "
    SELECT 
    greenit.DATE, 
    SUM(greenit.CONSUMPTION) AS totalConsumption, 
    SUM(greenit.UPTIME) AS totalUptime 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    WHERE greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "' 
    AND hardware.OSNAME LIKE '%Windows%' 
    AND hardware.OSNAME NOT IN (SELECT hardware.OSNAME FROM hardware WHERE hardware.OSNAME LIKE '%Windows Server%') 
    GROUP BY greenit.DATE
";
$compareClientDataResult = mysql2_query_secure($compareClientQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($compareClientDataResult)) {
    $compareData["CLIENTS"][$row->DATE] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}
//////////////////////////////

//////////////////////////////
// Get compare consumption of all Windows servers
$compareServerQuery = "
    SELECT 
    greenit.DATE, 
    SUM(greenit.CONSUMPTION) AS totalConsumption, 
    SUM(greenit.UPTIME) AS totalUptime 
    FROM greenit 
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    WHERE greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "' 
    AND hardware.OSNAME LIKE '%Windows Server%' 
    GROUP BY greenit.DATE
";
$compareServerDataResult = mysql2_query_secure($compareServerQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($compareServerDataResult)) {
    $compareData["SERVERS"][$row->DATE] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}
//////////////////////////////

//////////////////////////////
// Sum of all data per period Collect
$sumConsumptionCollect = array();

if (isset($collectData)) {
    foreach ($collectData as $group => $date) {
        $sumConsumptionCollect[$group] = 0;
        foreach ($date as $value) {
            $sumConsumptionCollect[$group] += $value->totalConsumption;
        }
    }
}
//////////////////////////////

//////////////////////////////
// Sum of all data per period Compare
$sumConsumptionCompare = array();

if (isset($compareData)) {
    foreach ($compareData as $group => $date) {
        $sumConsumptionCompare[$group] = 0;
        foreach ($date as $value) {
            $sumConsumptionCompare[$group] += $value->totalConsumption;
        }
    }
}
//////////////////////////////

?>