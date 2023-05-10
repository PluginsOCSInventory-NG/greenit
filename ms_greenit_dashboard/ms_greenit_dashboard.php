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

require_once("class/calculation.class.php");
require('class/chart.class.php');

$calculation = new Calculation;
$chart = new Chart;

// Config recovery
$configQuery = "SELECT COLLECT_INFO_PERIOD, CONSUMPTION_ROUND, COST_ROUND, COST_UNIT, KILOWATT_COST, UPTIME_FORMAT FROM greenit_config WHERE ID='1'";
$configResult = mysql2_query_secure($configQuery, $_SESSION['OCS']["readServer"]);

$config = array();
while ($row = mysqli_fetch_object($configResult)) {
    $config = $row;
}

// Data recovery
$date = new DateTime("NOW");
$pastDate = new DateTime("NOW");
$pastDate->modify("-".$config->COLLECT_INFO_PERIOD." days");

$todayQuery = "SELECT DATA FROM greenit_stats WHERE DATE='".$date->format("Y-m-d")."'";
$todayDataResult = mysql2_query_secure($todayQuery, $_SESSION['OCS']["readServer"]);

$limitedQuery = "SELECT DATE,DATA FROM greenit_stats WHERE DATE BETWEEN '".$pastDate->format("Y-m-d")."' AND '".$date->format("Y-m-d")."'";
$limitedDataResult = mysql2_query_secure($limitedQuery, $_SESSION['OCS']["readServer"]);

$dataQuery = "SELECT DATE,DATA FROM greenit_stats";
$dataResult = mysql2_query_secure($dataQuery, $_SESSION['OCS']["readServer"]);

$todayData = array();
while ($row = mysqli_fetch_object($todayDataResult)) {
    $todayData[] = json_decode($row->DATA);
}

$limitedData = array();
while ($row = mysqli_fetch_object($limitedDataResult)) {
    $limitedData[$row->DATE] = json_decode($row->DATA);
}

$data = array();
while ($row = mysqli_fetch_object($dataResult)) {
    $data[$row->DATE] = json_decode($row->DATA);
}

if(count($data) == 0) $data = null;
if(count($limitedData) == 0) $limitedData = null;
if(count($todayData) == 0) $todayData = null;

// Average of Consumption
$sumConsumptionInPeriode = 0;
$numberDeviceInPeriode = 0;
foreach($limitedData as $key => $value)
{
    $sumConsumptionInPeriode += $value->totalConsumption;
    $numberDeviceInPeriode++;
}

$sumConsumption = 0;
$numberDevice = 0;
foreach($data as $key => $value)
{
    $sumConsumption += $value->totalConsumption;
    $numberDevice++;
}

// Start display page
printEnTete($l->g(80701));
echo "<div class='col-md-10 col-xs-offset-0 col-md-offset-1'>";
echo "<hr>";

if(!isset($protectedPost['onglet'])){
    $protectedPost['onglet'] = 1;
}

/************************************** todayStats **************************************/

$form_name = "todayStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<br><h4>".$l->g(80702)."</h4>";

$table =
'<div>
    <table id="tab_stats" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; text-align:center; margin:auto; width:100%; margin-top:20px; background:#fff; border: 1px solid #ddd; table-layout: fixed;" >
        <tr>
            <td style="border-right: 1px solid #ddd; padding: 5px;"><span style="font-size:32px; font-weight:bold;">' . (isset($todayData) ? $calculation->ConsumptionFormat($todayData[0]->totalConsumption, "kW/h", $config->CONSUMPTION_ROUND) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80703).'</span></td>
            <td style="border-right: 1px solid #ddd;"><span style="font-size:32px; font-weight:bold;">' . (isset($todayData) ? $calculation->ConsumptionFormat($todayData[0]->consumptionAverage, "kW/h", $config->CONSUMPTION_ROUND) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80704).'</span></td>
            <td style="border-right: 1px solid #ddd;"><span style="font-size:32px; font-weight:bold;">' . (isset($todayData) ? $calculation->TimeFormat($todayData[0]->totalUptime, $config->UPTIME_FORMAT) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80705).'</span></td>
            <td style="border-right: 1px solid #ddd;"><span style="font-size:32px; font-weight:bold;">' . (isset($todayData) ? $calculation->TimeFormat($todayData[0]->uptimeAverage, $config->UPTIME_FORMAT) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80706).'</span></td>                 
        </tr>
    </table>
</div>';

echo $table;
echo "<hr>";
echo close_form();

/************************************** costStats **************************************/

$form_name = "costStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<br><h4>".$l->g(80707)."</h4>";

$table =
'<div>
    <table id="tab_stats" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; text-align:center; margin:auto; width:100%; margin-top:20px; background:#fff; border: 1px solid #ddd; table-layout: fixed;" >
        <tr>
            <td style="border-right: 1px solid #ddd; padding: 5px;"><span style="font-size:32px; font-weight:bold;">' . (isset($todayData) ? $calculation->CostFormat($todayData[0]->totalConsumption, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80708).'</span></td>
            <td style="border-right: 1px solid #ddd; padding: 5px;"><span style="font-size:32px; font-weight:bold;">' . (isset($limitedData) ? $calculation->CostFormat($sumConsumptionInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80709)." ".$l->g(80711)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(80712).'</span></td>
            <td style="border-right: 1px solid #ddd; padding: 5px;"><span style="font-size:32px; font-weight:bold;">' . (isset($data) ? $calculation->CostFormat($sumConsumption/$numberDevice, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80710).'</span></td>
            <td style="border-right: 1px solid #ddd; padding: 5px;"><span style="font-size:32px; font-weight:bold;">' . (isset($limitedData) ? $calculation->CostFormat($sumConsumptionInPeriode/$numberDeviceInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80710)." ".$l->g(80711)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(80712).'</span></td>
        </tr>
    </table>
</div>';

echo $table;

echo "<br>";

$labels = ["'".$l->g(80703)."', '".$l->g(80709)." ".$l->g(80711)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(80712)."'"];

$labelsSettings = array(
    "consumption" => array(
        "backgroundColor" => "'#1941A5'",
        "data" => "['".str_replace(" "."kW/h", "", $calculation->ConsumptionFormat($sumConsumption, "kW/h", $config->CONSUMPTION_ROUND))."', '".str_replace(" "."kW/h", "", $calculation->ConsumptionFormat($sumConsumptionInPeriode, "kW/h", $config->CONSUMPTION_ROUND))."']",
        "label" => "'".$l->g(80713)." ("."kW/h".")'",
        "type" => "'bar'"
    ),
    "cost" => array(
        "backgroundColor" => "'#AFD8F8'",
        "data" => "['".str_replace(" ".$config->COST_UNIT, "", $calculation->CostFormat($sumConsumption, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND))."', '".str_replace(" ".$config->COST_UNIT, "", $calculation->CostFormat($sumConsumptionInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND))."']",
        "label" => "'".$l->g(80714)." (".$config->COST_UNIT.")'",
        "type" => "'bar'"
    )
);

$chart->createCanvas("histogram");
$chart->createChart("histogram", "", $labels, $labelsSettings);

echo close_form();

echo "</div>";
?>