<?php

//////////////////////////////
// If reset button clicked, reset session variables
if (isset($protectedPost['RESET'])) {
    unset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))]);
    unset($protectedPost['OS']);
    unset($protectedPost['GROUP']);
    unset($protectedPost['TAG']);
    unset($protectedPost['ASSET']);
    unset($_SESSION['GREENIT']['FILTER']['OS']);
    unset($_SESSION['GREENIT']['FILTER']['GROUP']);
    unset($_SESSION['GREENIT']['FILTER']['TAG']);
    unset($_SESSION['GREENIT']['FILTER']['ASSET']);
}
//////////////////////////////

//////////////////////////////
// If formular submited, reset cache
if (isset($protectedPost['SUBMIT_FORM'])) {
    unset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))]);
    $tab_options['CACHE'] = 'RESET';
}
//////////////////////////////

//////////////////////////////
// Reset filter if default value was post
if (isset($protectedPost["OS"]) && $protectedPost['OS'] == "0")
    unset($_SESSION['GREENIT']['FILTER']['OS']);
if (isset($protectedPost["GROUP"]) && $protectedPost['GROUP'] == "0")
    unset($_SESSION['GREENIT']['FILTER']['GROUP']);
if (isset($protectedPost["TAG"]) && $protectedPost['TAG'] == "0")
    unset($_SESSION['GREENIT']['FILTER']['TAG']);
if (isset($protectedPost["ASSET"]) && $protectedPost['ASSET'] == "0")
    unset($_SESSION['GREENIT']['FILTER']['ASSET']);
//////////////////////////////

//////////////////////////////
// Define filter session variables
if (is_defined($protectedPost['OS']) && $protectedPost['OS'] != "0")
    $_SESSION['GREENIT']['FILTER']['OS'] = $protectedPost['OS'];
if (is_defined($protectedPost['GROUP']) && $protectedPost['GROUP'] != "0")
    $_SESSION['GREENIT']['FILTER']['GROUP'] = $protectedPost['GROUP'];
if (is_defined($protectedPost['TAG']) && $protectedPost['TAG'] != "0")
    $_SESSION['GREENIT']['FILTER']['TAG'] = $protectedPost['TAG'];
if (is_defined($protectedPost['ASSET']) && $protectedPost['ASSET'] != "0")
    $_SESSION['GREENIT']['FILTER']['ASSET'] = $protectedPost['ASSET'];
//////////////////////////////

//////////////////////////////
// Get filtered computers
$computerQuery['SQL'] = '
        SELECT DISTINCT 
        hardware.NAME as NAME 
        FROM hardware 
        INNER JOIN accountinfo ON hardware.ID = accountinfo.hardware_id 
        INNER JOIN greenit ON hardware.ID = greenit.HARDWARE_ID 
        LEFT JOIN groups_cache ON hardware.ID = groups_cache.HARDWARE_ID 
    ';

if (
    is_defined($_SESSION['GREENIT']['FILTER']['OS']) ||
    is_defined($_SESSION['GREENIT']['FILTER']['GROUP']) ||
    is_defined($_SESSION['GREENIT']['FILTER']['TAG']) ||
    is_defined($_SESSION['GREENIT']['FILTER']['ASSET'])
) {
    $computerQuery['WHERE'] = [];
    $computerQuery['SQL'] .= ' WHERE';

    if (is_defined($_SESSION['GREENIT']['FILTER']['OS']))
        array_push($computerQuery['WHERE'], ' hardware.OSNAME="' . $_SESSION['GREENIT']['FILTER']['OS'] . '" AND');
    if (is_defined($_SESSION['GREENIT']['FILTER']['GROUP']))
        array_push($computerQuery['WHERE'], ' GROUP_ID="' . $_SESSION['GREENIT']['FILTER']['GROUP'] . '" AND');
    if (is_defined($_SESSION['GREENIT']['FILTER']['TAG']))
        array_push($computerQuery['WHERE'], ' accountinfo.TAG="' . $_SESSION['GREENIT']['FILTER']['TAG'] . '" AND');
    if (is_defined($_SESSION['GREENIT']['FILTER']['ASSET']))
        array_push($computerQuery['WHERE'], ' hardware.CATEGORY_ID="' . $_SESSION['GREENIT']['FILTER']['ASSET'] . '" AND');
    array_push($computerQuery['WHERE'], ' 1');
    foreach ($computerQuery['WHERE'] as $args) {
        $computerQuery['SQL'] .= $args;
    }
}
$computerDataResult = mysql2_query_secure($computerQuery['SQL'], $_SESSION['OCS']["readServer"]);
$computerData = array();
while ($row = mysqli_fetch_object($computerDataResult)) {
    array_push($computerData, $row->NAME);
}
$computers = "";
foreach ($computerData as $computer) {
    $computers .= $computer;
    if (next($computerData))
        $computers .= ",";
}
//////////////////////////////

//////////////////////////////
// Get filter table values
$sql_filtered_search['SQL'] = '
    SELECT DISTINCT 
    hardware.NAME as NAME,
    hardware.OSNAME AS OS_NAME,
    accountinfo.TAG AS TAG,
    groups_cache.GROUP_ID AS GROUP_ID,
    hardware.CATEGORY_ID AS CATEGORY_ID
    FROM hardware
    INNER JOIN accountinfo ON hardware.ID = accountinfo.hardware_id
    INNER JOIN greenit ON hardware.ID = greenit.HARDWARE_ID
    LEFT JOIN groups_cache ON hardware.ID = groups_cache.HARDWARE_ID
';

if (
    is_defined($_SESSION['GREENIT']['FILTER']['OS']) ||
    is_defined($_SESSION['GREENIT']['FILTER']['GROUP']) ||
    is_defined($_SESSION['GREENIT']['FILTER']['TAG']) ||
    is_defined($_SESSION['GREENIT']['FILTER']['ASSET'])
) {
    $sql_filtered_search['WHERE'] = [];
    $sql_filtered_search['SQL'] .= ' WHERE';

    if (is_defined($_SESSION['GREENIT']['FILTER']['OS']))
        array_push($sql_filtered_search['WHERE'], ' hardware.OSNAME="' . $_SESSION['GREENIT']['FILTER']['OS'] . '" AND');
    if (is_defined($_SESSION['GREENIT']['FILTER']['GROUP']))
        array_push($sql_filtered_search['WHERE'], ' GROUP_ID="' . $_SESSION['GREENIT']['FILTER']['GROUP'] . '" AND');
    if (is_defined($_SESSION['GREENIT']['FILTER']['TAG']))
        array_push($sql_filtered_search['WHERE'], ' accountinfo.TAG="' . $_SESSION['GREENIT']['FILTER']['TAG'] . '" AND');
    if (is_defined($_SESSION['GREENIT']['FILTER']['ASSET']))
        array_push($sql_filtered_search['WHERE'], ' hardware.CATEGORY_ID="' . $_SESSION['GREENIT']['FILTER']['ASSET'] . '" AND');
    array_push($sql_filtered_search['WHERE'], ' 1');
    foreach ($sql_filtered_search['WHERE'] as $args) {
        $sql_filtered_search['SQL'] .= $args;
    }
}

$sql_filtered_search['SQL'] .= ' GROUP BY NAME';
//////////////////////////////

//////////////////////////////
// OS filter
$query = "SELECT OSNAME FROM hardware WHERE OSNAME LIKE '%Windows%' AND DEVICEID<>'_SYSTEMGROUP_' AND DEVICEID<>'_DOWNLOADGROUP_' GROUP BY OSNAME ORDER BY OSNAME";
$result = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
$os = [
    0 => "-----",
];
while ($item = mysqli_fetch_array($result)) {
    $os[$item['OSNAME']] = $item['OSNAME'];
}
//////////////////////////////

//////////////////////////////
// GROUP filter
$query = "SELECT NAME, ID FROM hardware WHERE DEVICEID = '_SYSTEMGROUP_' GROUP BY NAME ORDER BY NAME";
$result = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
$group = [
    0 => "-----",
];
while ($item = mysqli_fetch_array($result)) {
    $group[$item['ID']] = $item['NAME'];
}
//////////////////////////////

//////////////////////////////
// TAG filter
$query = "SELECT TAG FROM accountinfo";
$result = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
$tag = [
    0 => "-----",
];
while ($item = mysqli_fetch_array($result)) {
    $tag[$item['TAG']] = $item['TAG'];
}
//////////////////////////////

//////////////////////////////
// ASSET filter
$query = "SELECT CATEGORY_NAME, ID FROM assets_categories GROUP BY CATEGORY_NAME ORDER BY CATEGORY_NAME";
$result = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
$asset = [
    0 => "-----",
];
while ($item = mysqli_fetch_array($result)) {
    $asset[$item['ID']] = $item['CATEGORY_NAME'];
}
//////////////////////////////

?>