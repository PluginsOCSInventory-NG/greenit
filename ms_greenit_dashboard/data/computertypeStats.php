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
$computersType = array();
$yesterdayData = array();
$collectData = array();
$compareData = array();
//////////////////////////////

//////////////////////////////
// Get compter type
$computersTypeQuery = "
    SELECT 
    (
        CASE
        
        WHEN (
            bios.type LIKE '%Desktop%' OR 
            bios.type LIKE '%Elitedesk%' OR 
            bios.type LIKE '%Mini Tower%' OR
            bios.type LIKE '%ProLient%' OR
            bios.type LIKE '%Precision%' OR
            bios.type LIKE '%All in One%'
        )
        THEN 'Desktop'

        WHEN (
            bios.type LIKE '%LapTop%' OR 
            bios.type LIKE '%Portable%' OR
            bios.type LIKE '%Notebook%'
        )
        THEN 'LapTop'
        
        WHEN (
            bios.type <> 'Desktop' OR
            bios.type <> 'LapTop'
        )
        THEN 'Other'
        
        ELSE bios.type
        
        END
    ) AS COMPUTER_TYPE
    FROM greenit
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID
    GROUP BY COMPUTER_TYPE
";
$computersTypeDataResult = mysql2_query_secure($computersTypeQuery, $_SESSION['OCS']["readServer"]);

while ($row = mysqli_fetch_object($computersTypeDataResult)) {
    array_push($computersType, $row->COMPUTER_TYPE);
}
//////////////////////////////

//////////////////////////////
// Get yesterday data of all computer type
$yesterdayComputerTypeQuery = "
    SELECT 
    (
        CASE
        
        WHEN (
            bios.type LIKE '%Desktop%' OR 
            bios.type LIKE '%Elitedesk%' OR 
            bios.type LIKE '%Mini Tower%' OR
            bios.type LIKE '%ProLient%' OR
            bios.type LIKE '%Precision%' OR
            bios.type LIKE '%All in One%'
        )
        THEN 'Desktop'

        WHEN (
            bios.type LIKE '%LapTop%' OR 
            bios.type LIKE '%Portable%' OR
            bios.type LIKE '%Notebook%'
        )
        THEN 'LapTop'
        
        WHEN (
            bios.type <> 'Desktop' OR
            bios.type <> 'LapTop'
        )
        THEN 'Other'
        
        ELSE bios.type
        
        END
    ) AS COMPUTER_TYPE, 
    SUM(greenit.CONSUMPTION) AS totalConsumption,
    SUM(greenit.UPTIME) AS totalUptime
    FROM greenit
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID
    WHERE greenit.DATE = '" . $Date->format("Y-m-d") . "'
    GROUP BY COMPUTER_TYPE
";
$yesterdayComputerTypeDataResult = mysql2_query_secure($yesterdayComputerTypeQuery, $_SESSION['OCS']['readServer']);
$yesterdayData = array();
while ($row = mysqli_fetch_object($yesterdayComputerTypeDataResult)) {
    $yesterdayData[$row->COMPUTER_TYPE] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}
//////////////////////////////

//////////////////////////////
// Get collect data of filtered GreenIT parc
$collectComputerTypeQuery = "
    SELECT 
    (
        CASE
        
        WHEN (
            bios.type LIKE '%Desktop%' OR 
            bios.type LIKE '%Elitedesk%' OR 
            bios.type LIKE '%Mini Tower%' OR
            bios.type LIKE '%ProLient%' OR
            bios.type LIKE '%Precision%' OR
            bios.type LIKE '%All in One%'
        )
        THEN 'Desktop'

        WHEN (
            bios.type LIKE '%LapTop%' OR 
            bios.type LIKE '%Portable%' OR
            bios.type LIKE '%Notebook%'
        )
        THEN 'LapTop'
        
        WHEN (
            bios.type <> 'Desktop' OR
            bios.type <> 'LapTop'
        )
        THEN 'Other'
        
        ELSE bios.type
        
        END
    ) AS COMPUTER_TYPE, 
    greenit.DATE, 
    SUM(greenit.CONSUMPTION) AS totalConsumption,
    SUM(greenit.UPTIME) AS totalUptime
    FROM greenit
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID
    WHERE greenit.DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    GROUP BY COMPUTER_TYPE, greenit.DATE
";
$collectComputerTypeDataResult = mysql2_query_secure($collectComputerTypeQuery, $_SESSION['OCS']['readServer']);

$collectData = array();
while ($row = mysqli_fetch_object($collectComputerTypeDataResult)) {
    $collectData[$row->COMPUTER_TYPE][$row->DATE] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}

$sumConsumptionCollect = array();

if (isset($collectData)) {
    foreach ($collectData as $group => $date) {
        $sumConsumptionCollect[$group] = 0;
        foreach ($date as $value) {
            $sumConsumptionCollect[$group] += $value->totalConsumption;
        }
    }
}

$nbDevicesCollectQuery = "
    SELECT 
    COUNT(DISTINCT greenit.HARDWARE_ID) AS COUNT,
    (
        CASE
        
        WHEN (
            bios.type LIKE '%Desktop%' OR 
            bios.type LIKE '%Elitedesk%' OR 
            bios.type LIKE '%Mini Tower%' OR
            bios.type LIKE '%ProLient%' OR
            bios.type LIKE '%Precision%' OR
            bios.type LIKE '%All in One%'
        )
        THEN 'Desktop'

        WHEN (
            bios.type LIKE '%LapTop%' OR 
            bios.type LIKE '%Portable%' OR
            bios.type LIKE '%Notebook%'
        )
        THEN 'LapTop'
        
        WHEN (
            bios.type <> 'Desktop' OR
            bios.type <> 'LapTop'
        )
        THEN 'Other'
        
        ELSE bios.type
        
        END
    ) AS COMPUTER_TYPE 
    FROM greenit 
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID
    WHERE 
    DATE BETWEEN '" . $collectDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    GROUP BY COMPUTER_TYPE
";
$nbDevicesCollectResult = mysql2_query_secure($nbDevicesCollectQuery, $_SESSION['OCS']["readServer"]);
$nbDevicesCollect = array();
while ($row = mysqli_fetch_object($nbDevicesCollectResult)) {
    $nbDevicesCollect[$row->COMPUTER_TYPE] = $row->COUNT;
}
foreach ($nbDevicesCollect as $group => $value) {
    if ($value == 0)
        $value = 1;
}
//////////////////////////////

//////////////////////////////
// Get compare data of filtered GreenIT parc
$compareComputerTypeQuery = "
    SELECT 
    (
        CASE
        
        WHEN (
            bios.type LIKE '%Desktop%' OR 
            bios.type LIKE '%Elitedesk%' OR 
            bios.type LIKE '%Mini Tower%' OR
            bios.type LIKE '%ProLient%' OR
            bios.type LIKE '%Precision%' OR
            bios.type LIKE '%All in One%'
        )
        THEN 'Desktop'

        WHEN (
            bios.type LIKE '%LapTop%' OR 
            bios.type LIKE '%Portable%' OR
            bios.type LIKE '%Notebook%'
        )
        THEN 'LapTop'
        
        WHEN (
            bios.type <> 'Desktop' OR
            bios.type <> 'LapTop'
        )
        THEN 'Other'
        
        ELSE bios.type
        
        END
    ) AS COMPUTER_TYPE, 
    greenit.DATE, 
    SUM(greenit.CONSUMPTION) AS totalConsumption,
    SUM(greenit.UPTIME) AS totalUptime
    FROM greenit
    INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID
    WHERE greenit.DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    GROUP BY COMPUTER_TYPE, greenit.DATE
";
$compareComputerTypeDataResult = mysql2_query_secure($compareComputerTypeQuery, $_SESSION['OCS']['readServer']);

$compareData = array();
while ($row = mysqli_fetch_object($compareComputerTypeDataResult)) {
    $compareData[$row->COMPUTER_TYPE][$row->DATE] = (object) array(
        "totalConsumption" => $row->totalConsumption,
        "totalUptime" => $row->totalUptime,
    );
}

$sumConsumptionCompare = array();

if (isset($compareData)) {
    foreach ($compareData as $group => $date) {
        $sumConsumptionCompare[$group] = 0;
        foreach ($date as $value) {
            $sumConsumptionCompare[$group] += $value->totalConsumption;
        }
    }
}

$nbDevicesCompareQuery = "
    SELECT 
    COUNT(DISTINCT greenit.HARDWARE_ID) AS COUNT,
    (
        CASE
        
        WHEN (
            bios.type LIKE '%Desktop%' OR 
            bios.type LIKE '%Elitedesk%' OR 
            bios.type LIKE '%Mini Tower%' OR
            bios.type LIKE '%ProLient%' OR
            bios.type LIKE '%Precision%' OR
            bios.type LIKE '%All in One%'
        )
        THEN 'Desktop'

        WHEN (
            bios.type LIKE '%LapTop%' OR 
            bios.type LIKE '%Portable%' OR
            bios.type LIKE '%Notebook%'
        )
        THEN 'LapTop'
        
        WHEN (
            bios.type <> 'Desktop' OR
            bios.type <> 'LapTop'
        )
        THEN 'Other'
        
        ELSE bios.type
        
        END
    ) AS COMPUTER_TYPE 
    FROM greenit 
    INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID
    WHERE 
    DATE BETWEEN '" . $compareDate->format("Y-m-d") . "' AND '" . $Date->format("Y-m-d") . "'
    GROUP BY COMPUTER_TYPE
";
$nbDevicesCompareResult = mysql2_query_secure($nbDevicesCompareQuery, $_SESSION['OCS']["readServer"]);
$nbDevicesCompare = array();
while ($row = mysqli_fetch_object($nbDevicesCompareResult)) {
    $nbDevicesCompare[$row->COMPUTER_TYPE] = $row->COUNT;
}
foreach ($nbDevicesCompare as $group => $value) {
    if ($value == 0)
        $value = 1;
}
//////////////////////////////