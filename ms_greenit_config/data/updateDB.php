<?php

$insertQuery = "
    UPDATE greenit_config 
    SET 
    COLLECT_INFO_PERIOD='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80801)))]."',
    COMPARE_INFO_PERIOD='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80802)))]."',
    CONSUMPTION_ROUND='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80803)))]."',
    COST_ROUND='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80804)))]."',
    COST_UNIT='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80805)))]."',
    KILOWATT_COST='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80806)))]."',
    UPTIME_FORMAT='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80807)))]."'
    WHERE ID='1';
";
mysql2_query_secure($insertQuery, $_SESSION['OCS']["writeServer"]);
msg_success($l->g(80808));

?>