<?php

if (isset($protectedPost['RESET']))
{
    unset($protectedPost[strtolower(str_replace(" ", "_",$l->g(25)))]);
    unset($protectedPost['GROUP']);
    unset($protectedPost['TAG']);
    unset($protectedPost['ASSET']);
    unset($_SESSION['GREENIT']['SEARCH']['OS']);
}

if (isset($protectedPost['SUBMIT_FORM'])) $tab_options['CACHE'] = 'RESET';

if(is_defined($protectedPost[strtolower(str_replace(" ", "_",$l->g(25)))]) && $protectedPost[strtolower(str_replace(" ", "_",$l->g(25)))] != "0") $_SESSION['GREENIT']['SEARCH']['OS'] = $protectedPost[strtolower(str_replace(" ", "_",$l->g(25)))];

$query = "SELECT OSNAME FROM hardware WHERE OSNAME LIKE '%Windows%' AND DEVICEID<>'_SYSTEMGROUP_' AND DEVICEID<>'_DOWNLOADGROUP_' GROUP BY OSNAME ORDER BY OSNAME";
$result = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
$os = [
    0 => "-----",
];
while($item = mysqli_fetch_array($result)){
    $os[$item['OSNAME']] = $item['OSNAME'];
}

?>