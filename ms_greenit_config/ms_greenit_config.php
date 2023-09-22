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

if(AJAX){
        parse_str($protectedPost['ocs']['0'], $params);
        $protectedPost+=$params;
        ob_start();
        $ajax = true;
}
else{
        $ajax=false;
}

if(
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80801)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80802)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80803)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80804)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80805)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80806)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80807)))])
)
{
    require_once("data/updateDB.php");
}
else
{
    require_once("data/config.php");
}

// Start display page
echo "<div class='col-md-1'></div>";
echo "<div class='col-md-10'>";

printEnTete($l->g(80800));
echo "<hr>";

if(!isset($protectedPost['onglet'])){
    $protectedPost['onglet'] = 1;
}

$form_name = "configuration";
echo open_form($form_name, '', '', 'form-horizontal');

require("components/formular.php");

echo '<input type="submit" class="btn btn-success" value="'.$l->g(103).'" name="SUBMIT_FORM">';

echo close_form();

echo "</div>";
echo "<div class='col-md-1'></div>";

?>