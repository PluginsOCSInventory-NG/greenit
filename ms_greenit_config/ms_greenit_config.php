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
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80716)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80717)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80718)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80719)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80720)))]) &&
    isset($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80721)))])
)
{
    $insertQuery = "
        UPDATE greenit_config 
        SET 
        COLLECT_INFO_PERIOD='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80716)))]."',
        CONSUMPTION_ROUND='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80717)))]."',
        COST_ROUND='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80718)))]."',
        COST_UNIT='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80719)))]."',
        KILOWATT_COST='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80720)))]."',
        UPTIME_FORMAT='".$protectedPost[strtoupper(str_replace(" ", "_",$l->g(80721)))]."'
        WHERE ID='1';
    ";
    mysql2_query_secure($insertQuery, $_SESSION['OCS']["writeServer"]);
    msg_success($l->g(80723));
}
else
{
    $selectQuery = "SELECT COLLECT_INFO_PERIOD, CONSUMPTION_ROUND, COST_ROUND, COST_UNIT, KILOWATT_COST, UPTIME_FORMAT FROM greenit_config WHERE ID='1'";
    $selectResult = mysql2_query_secure($selectQuery, $_SESSION['OCS']["readServer"]);

    $config = array();
    while ($row = mysqli_fetch_object($selectResult)) {
        $config = $row;
    }
}

// Start display page
printEnTete($l->g(80715));
echo "<div class='col-md-10 col-xs-offset-0 col-md-offset-1'>";
echo "<hr>";

if(!isset($protectedPost['onglet'])){
    $protectedPost['onglet'] = 1;
}

$form_name = "configuration";
echo open_form($form_name, '', '', 'form-horizontal');

$uptimeFormat = [
    "0" => "-----",
    "s" => "s",
    "m-s" => "m-s",
    "h-m-s" => "h-m-s",
];

echo '
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(80716))).'">'.$l->g(80716).'</label>
        <div class="col-sm-10">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80716))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80716))).'" class="form-control" type="number" min="1" max="365" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80716)))] ?? $config->COLLECT_INFO_PERIOD).'"\>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(80717))).'">'.$l->g(80717).'</label>
        <div class="col-sm-10">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80717))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80717))).'" class="form-control" type="number" min="1" max="10" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80717)))] ?? $config->CONSUMPTION_ROUND).'"\>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(80718))).'">'.$l->g(80718).'</label>
        <div class="col-sm-10">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80718))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80718))).'" class="form-control" type="number" min="1" max="10" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80718)))] ?? $config->COST_ROUND).'"\>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(80719))).'">'.$l->g(80719).'</label>
        <div class="col-sm-10">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80719))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80719))).'" class="form-control" type="text" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80719)))] ?? $config->COST_UNIT).'"\>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="'.strtoupper(str_replace(" ", "_",$l->g(80720))).'">'.$l->g(80720).'</label>
        <div class="col-sm-10">
            <input name="'.strtoupper(str_replace(" ", "_",$l->g(80720))).'" id="'.strtoupper(str_replace(" ", "_",$l->g(80720))).'" class="form-control" type="number" min="0" step="0.000001" value="'.($protectedPost[strtoupper(str_replace(" ", "_",$l->g(80720)))] ?? $config->KILOWATT_COST).'"\>
        </div>
    </div>
';
formGroup('select', strtoupper(str_replace(" ", "_",$l->g(80721))), $l->g(80721), '', '', $protectedPost[strtoupper(str_replace(" ", "_",$l->g(80721)))] ?? $config->UPTIME_FORMAT, '', $uptimeFormat, $uptimeFormat, '');

echo '<input type="submit" class="btn btn-success" value="'.$l->g(80722).'" name="SUBMIT_FORM">';

echo close_form();

echo "</div>";
?>