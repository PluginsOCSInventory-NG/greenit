<?php

$greenitMachineQuery = "SELECT COUNT(DISTINCT HARDWARE_ID) AS ID_COUNT FROM greenit";
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