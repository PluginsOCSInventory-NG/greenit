<?php

$greenitMachineQuery = "
    SELECT 
    count(DISTINCT hardware.ID)
    FROM hardware
    INNER JOIN accountinfo ON hardware.ID = accountinfo.hardware_id
    INNER JOIN greenit ON hardware.ID = greenit.HARDWARE_ID
    LEFT JOIN groups_cache ON hardware.ID = groups_cache.HARDWARE_ID
";
$greenitMachineResult = mysql2_query_secure($greenitMachineQuery, $_SESSION['OCS']["readServer"]);

$greenitMachineInfos = array();
while ($row = mysqli_fetch_object($greenitMachineResult)) {
    $greenitMachineInfos = $row;
}

$totalMachineQuery = "SELECT DISTINCT COUNT(ID) AS ID_COUNT FROM hardware WHERE DEVICEID <> '_SYSTEMGROUP_'";
$totalMachineResult = mysql2_query_secure($totalMachineQuery, $_SESSION['OCS']["readServer"]);

$totalMachineInfos = array();
while ($row = mysqli_fetch_object($totalMachineResult)) {
    $totalMachineInfos = $row;
}

?>