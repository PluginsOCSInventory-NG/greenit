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
// Get coputer type
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
    foreach ($computersType as $computerType)
        $yesterdayData[$computerType] = (object) array(
            "TYPE" => $row->COMPUTER_TYPE,
            "totalConsumption" => $row->totalConsumption,
            "totalUptime" => $row->totalUptime,
        );
}
echo "<pre style='text-align: left;'>";
var_dump($yesterdayData);
echo "</pre>";
//////////////////////////////

?>