<?php

$insertQuery = "
    UPDATE greenit_config 
    SET 
    COLLECT_INFO_PERIOD='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(102001)))]."',
    COMPARE_INFO_PERIOD='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(102002)))]."',
    CONSUMPTION_ROUND='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(102003)))]."',
    COST_ROUND='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(102004)))]."',
    COST_UNIT='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(102005)))]."',
    KILOWATT_COST='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(102006)))]."',
    UPTIME_FORMAT='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(102007)))]."'
    WHERE ID='1';
";
mysql2_query_secure($insertQuery, $_SESSION['OCS']["writeServer"]);
msg_success($l->g(101000));

?>