<?php

$form_name = "costStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>".$l->g(80910)."</h4>";

$table = '
<div class="row">
    <div class="col-md-3" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size:2vw; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->CostFormat($yesterdayData[0]->totalConsumption, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size:1vw;">'.$l->g(80911).'</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size:2vw; font-weight:bold;">' . (isset($limitedData) ? $calculation->CostFormat($sumConsumptionInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size:1vw;">'.$l->g(80912). " ".$config->COLLECT_INFO_PERIOD." ".$l->g(80915).'</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size:2vw; font-weight:bold;">' . (isset($data) ? $calculation->CostFormat($sumConsumption/$numberDevice, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size:1vw;">'.$l->g(80913).'</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size:2vw; font-weight:bold;">' . (isset($limitedData) ? $calculation->CostFormat($sumConsumptionInPeriode/$numberDeviceInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size:1vw;">'.$l->g(80914)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(80915).'</p>
    </div>
</div>
';

echo $table;

echo "<br>";

$labels = ["'".$l->g(80912)." ".$config->COMPARE_INFO_PERIOD." ".$l->g(80915)."', '".$l->g(80912)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(80915)."'"];

$labelsSettings = array(
    "consumption" => array(
        "backgroundColor" => "'#1941A5'",
        "data" => "[
            '".str_replace(" "."kW/h", "", $calculation->ConsumptionFormat($sumConsumptionCompare, "kW/h", $config->CONSUMPTION_ROUND))."',
            '".str_replace(" "."kW/h", "", $calculation->ConsumptionFormat($sumConsumptionInPeriode, "kW/h", $config->CONSUMPTION_ROUND))."'
            ]",
        "label" => "'".$l->g(80916)." ("."kW/h".")'",
        "type" => "'bar'"
    ),
    "cost" => array(
        "backgroundColor" => "'#AFD8F8'",
        "data" => "[
            '".str_replace(" ".$config->COST_UNIT, "", $calculation->CostFormat($sumConsumptionCompare, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND))."',
            '".str_replace(" ".$config->COST_UNIT, "", $calculation->CostFormat($sumConsumptionInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND))."'
            ]",
        "label" => "'".$l->g(80917)." (".$config->COST_UNIT.")'",
        "type" => "'bar'"
    )
);

$chart->createCanvas("histogram");
$chart->createChart("histogram", "", $labels, $labelsSettings);

echo close_form();

?>