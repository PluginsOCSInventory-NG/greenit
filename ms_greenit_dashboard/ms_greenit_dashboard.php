<?php
//====================================================================================
// OCS INVENTORY REPORTS
// Copyleft Antoine ROBIN (erwan(at)ocsinventory-ng(pt)org)
// Web: http://www.ocsinventory-ng.org
//
// This code is open source and may be copied and modified as long as the source
// code is always made freely available.
// Please refer to the General Public Licence http://www.gnu.org/ or Licence.txt
//====================================================================================

if (AJAX) {
    parse_str($protectedPost['ocs']['0'], $params);
    $protectedPost += $params;
    ob_start();
}

require_once("class/calculation.class.php");
require_once('class/chart.class.php');

$calculation = new Calculation;
$chart = new Chart;

if(!isset($protectedGet['cat']))
{
    $protectedGet['cat'] = 'globalstats';
}

// Config recovery
require_once('data/config.php');

// Data recovery
if($protectedGet['cat'] == 'globalstats')
{
    require_once('data/globalStats.php');
}
else if($protectedGet['cat'] == 'individualstats' && isset($protectedGet[strtolower(str_replace(" ", "_",$l->g(35)))]))
{
    require_once('data/individualStats.php');
}

// Start display page
require_once("components/greenitMenu.php");

echo "<div class='col-md-10'>";

printEnTete($l->g(80900));
echo "<hr>";

if(!isset($protectedPost['onglet'])){
    $protectedPost['onglet'] = 1;
}

if($protectedGet['cat'] == 'globalstats')
{
    require_once("components/globalStats/yesterdayStats.php");
    require_once("components/globalStats/costStats.php");    
}
else if ($protectedGet['cat'] == 'individualstats')
{
    require_once("components/individualStats/individualSearch.php");
    if(isset($protectedGet[strtolower(str_replace(" ", "_",$l->g(35)))]))
    {
        require_once("components/individualStats/yesterdayStats.php");
        require_once("components/individualStats/costStats.php");
    }
}

echo "</div>";

if (AJAX) {
    ob_end_clean();
    tab_req(
        $list_fields_individual_search,
        $default_fields_individual_search,
        $list_col_cant_del_individual_search,
        $sql_individual_search['SQL'],
        $tab_options_individual_search
    );
}

?>