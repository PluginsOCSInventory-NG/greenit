<?php

$form_name = "costStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<br><h4>".$l->g(80707)."</h4>";

$table =
'<div>
    <table id="tab_stats" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; text-align:center; margin:auto; width:100%; margin-top:20px; background:#fff; border: 1px solid #ddd; table-layout: fixed;" >
        <tr>
            <td style="border-right: 1px solid #ddd; padding: 5px;"><span style="font-size:32px; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->CostFormat($yesterdayData[0]->totalConsumption, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80708).'</span></td>
            <td style="border-right: 1px solid #ddd; padding: 5px;"><span style="font-size:32px; font-weight:bold;">' . (isset($limitedData) ? $calculation->CostFormat($sumConsumptionInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80709). " ".$config->COLLECT_INFO_PERIOD." ".$l->g(80712).'</span></td>
        </tr>
    </table>
</div>';

echo $table;

echo "<br>";

$labels = ["'".$l->g(80703)."', '".$l->g(80709)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(80712)."'"];

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

?>