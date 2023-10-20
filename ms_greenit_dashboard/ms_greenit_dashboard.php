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
    parse_str($protectedPost["ocs"]["0"], $params);
    $protectedPost += $params;
    ob_start();
}

require_once("views/globalStats.class.php");
require_once("views/filteredStats.class.php");
require_once("views/osStats.class.php");
require_once("views/computerTypeStats.class.php");
require_once("views/manufacturerStats.class.php");

if (!isset($protectedGet["cat"]))
    $protectedGet["cat"] = "globalstats";

if ($protectedGet["cat"]) {
    switch ($protectedGet["cat"]) {
        case "globalstats":
            $view = new GlobalStatsView();
            break;
        case "filteredstats":
            $view = new FilteredStatsView();
            break;
        case "osstats":
            $view = new OSStatsView();
            break;
        case "computertypestats":
            $view = new ComputerTypeStatsView();
            break;
        case "manufacturerstats":
            $view = new ManufacturerStatsView();
            break;
        default:
            msg_error("Error 404");
            break;
    }
}

if (isset($view)) {
    echo "
        <div class='row'>
            <div class='col-md-2'>
    ";
    $view->ShowTitle();
    $view->ShowMenu();
    echo "
            </div>
            <div class='col-md-10'>
    ";
    if ($protectedGet["cat"] == "filteredstats") {
        $view->ShowFilteredSearch();
        if (isset($protectedGet["computers"]) || isset($protectedGet["computers"])) {
            $view->ShowYesterdayStats();
            $view->ShowComparatorStats();
        }
    } else {
        $view->ShowYesterdayStats();
        $view->ShowComparatorStats();
    }
    echo "
            </div>
        </div>
    ";
}

if (AJAX) {
    ob_end_clean();
    if ($protectedGet["cat"] == "filteredstats") {
        tab_req(
            $list_fields_filtered_search,
            $default_fields_filtered_search,
            $list_col_cant_del_filtered_search,
            $sql_filtered_search['SQL'],
            $tab_options_filtered_search
        );
    }
}

?>