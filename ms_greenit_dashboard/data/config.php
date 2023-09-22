<?php

$configQuery = "SELECT COLLECT_INFO_PERIOD, CONSUMPTION_ROUND, COST_ROUND, COST_UNIT, KILOWATT_COST, UPTIME_FORMAT FROM greenit_config WHERE ID='1'";
$configResult = mysql2_query_secure($configQuery, $_SESSION['OCS']["readServer"]);

$config = array();
while ($row = mysqli_fetch_object($configResult)) {
    $config = $row;
}

?>