<?php

$selectQuery = "SELECT COLLECT_INFO_PERIOD, CONSUMPTION_ROUND, COST_ROUND, COST_UNIT, KILOWATT_COST, UPTIME_FORMAT FROM greenit_config WHERE ID='1'";
$selectResult = mysql2_query_secure($selectQuery, $_SESSION['OCS']["readServer"]);

$config = array();
while ($row = mysqli_fetch_object($selectResult)) {
    $config = $row;
}

?>