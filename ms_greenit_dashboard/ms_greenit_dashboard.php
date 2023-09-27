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

if(!isset($protectedPost['onglet'])){
    $protectedPost['onglet'] = 1;
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
require_once('data/title.php');
if($protectedGet['cat'] == 'globalstats')
{
    require_once('data/globalStats.php');
}
else if($protectedGet['cat'] == 'computertypestats')
{
    require_once('data/computerTypeSearch.php');
    require_once('data/computerTypeStats.php');
}
else if($protectedGet['cat'] == 'individualstats')
{
    require_once('data/individualSearch.php');
    if(isset($protectedGet[strtolower(str_replace(" ", "_",$l->g(35)))])) require_once('data/individualStats.php');
}

// Start display page
require_once("components/greenitMenu.php");

echo "<div class='col-md-10'>";

require_once("components/title.php");

if($protectedGet['cat'] == 'globalstats')
{
    require_once("components/globalStats/yesterdayStats.php");
    require_once("components/globalStats/costStats.php");    
}
else if ($protectedGet['cat'] == 'computertypestats')
{
    if(isset($protectedPost[strtolower(str_replace(" ", "_",$l->g(25)))]))
    {
        require_once("components/computerTypeStats/yesterdayStats.php");
        require_once("components/computerTypeStats/costStats.php");
    }
    require_once("components/computerTypeStats/computerTypeSearch.php");
}
else if ($protectedGet['cat'] == 'individualstats')
{
    if(isset($protectedGet[strtolower(str_replace(" ", "_",$l->g(35)))]))
    {
        require_once("components/individualStats/yesterdayStats.php");
        require_once("components/individualStats/costStats.php");
    }
    require_once("components/individualStats/individualSearch.php");
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