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

if (!isset($protectedPost['onglet'])) {
    $protectedPost['onglet'] = 1;
}

require_once("class/calculation.class.php");
require_once('class/diagram.class.php');

$calculation = new Calculation;
$diagram = new Diagram;

if (!isset($protectedGet['cat'])) {
    $protectedGet['cat'] = 'globalstats';
}

// Config recovery
require_once('data/config.php');

// Data recovery
require_once('data/title.php');
if ($protectedGet['cat'] == 'globalstats') {
    require_once('data/globalStats.php');
} else if ($protectedGet['cat'] == 'filteredstats') {
    require_once('data/filteredSearch.php');
    if (
        isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))]) ||
        isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))])
    )
        require_once('data/filteredStats.php');
} else if ($protectedGet['cat'] == 'osstats') {
    require_once('data/osStats.php');
} else if ($protectedGet['cat'] == 'computertypestats') {
    require_once('data/computertypeStats.php');
} else if ($protectedGet['cat'] == 'manufacturerstats') {
    require_once('data/manufacturerStats.php');
}

// Start display page
require_once("components/greenitMenu.php");

echo "<div class='col-md-10'>";

require_once("components/title.php");

if ($protectedGet['cat'] == 'globalstats') {
    require_once("components/globalStats/yesterdayStats.php");
    require_once("components/globalStats/costStats.php");
} else if ($protectedGet['cat'] == 'filteredstats') {
    if (
        isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))]) ||
        isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))])
    ) {
        require_once("components/filteredStats/yesterdayStats.php");
        require_once("components/filteredStats/costStats.php");
    }
    require_once("components/filteredStats/filteredSearch.php");
} else if ($protectedGet['cat'] == 'osstats') {
    require_once("components/osStats/yesterdayStats.php");
    require_once("components/osStats/costStats.php");
} else if ($protectedGet['cat'] == 'computertypestats') {
    require_once("components/computerTypeStats/costStats.php");
} else if ($protectedGet['cat'] == 'manufacturerstats') {
    require_once("components/manufacturerStats/costStats.php");
}

echo "</div>";

if (AJAX) {
    ob_end_clean();
    tab_req(
        $list_fields_filtered_search,
        $default_fields_filtered_search,
        $list_col_cant_del_filtered_search,
        $sql_filtered_search['SQL'],
        $tab_options_filtered_search
    );
}

?>