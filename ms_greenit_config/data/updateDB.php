<?php

$insertQuery = "
    UPDATE greenit_config 
    SET 
    COLLECT_INFO_PERIOD='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80716)))]."',
    CONSUMPTION_ROUND='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80717)))]."',
    COST_ROUND='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80718)))]."',
    COST_UNIT='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80719)))]."',
    KILOWATT_COST='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80720)))]."',
    UPTIME_FORMAT='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80721)))]."'
    WHERE ID='1';
";
mysql2_query_secure($insertQuery, $_SESSION['OCS']["writeServer"]);
msg_success($l->g(80723));

?>