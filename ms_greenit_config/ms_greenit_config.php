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

require_once("views/config.class.php");

if (!isset($protectedGet["cat"]))
    $protectedGet["cat"] = "config";

if ($protectedGet["cat"]) {
    switch ($protectedGet["cat"]) {
        case "config":
            $view = new ConfigView();
            break;
        default:
            msg_error("Error 404");
            break;
    }
}

if (isset($view)) {
    echo "
        <div class='col-md-1'></div>
        <div class='col-md-10'>
    ";
    $view->ShowTitle();
    $view->ShowInterfaceSettings();
    $view->ShowAPIConfiguration();
    $view->ShowSubmit();
    echo "
        </div>
        <div class='col-md-1'></div>
    ";
}

if (AJAX) {
    ob_end_clean();
}

?>