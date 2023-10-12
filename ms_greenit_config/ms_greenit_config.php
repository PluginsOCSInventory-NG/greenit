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

if (
    isset($protectedPost['SUBMIT_FORM']) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102002)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102003)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102004)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102005)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102006)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102007)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102009)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102012)))])
) {
    // Data insert
    require_once("data/updateDB.php");
    require_once("data/config.php");
    require_once("data/API.php");
} else if (isset($protectedPost['TEST_API'])) {
    require_once("data/config.php");
    require_once("data/testAPI.php");
} else {
    // Config recovery
    require_once("data/config.php");
}



// Start display page
echo '
<div class="col-md-1"></div>
<div class="col-md-10">
';

require_once("components/title.php");

require("components/formular.php");

echo '
</div>
<div class="col-md-1"></div>
';

?>