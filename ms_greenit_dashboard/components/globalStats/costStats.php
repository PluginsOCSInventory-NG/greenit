<?php

$form_name = "costStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>".$l->g(102700)."</h4>";

$table = '
<div class="row">
<<<<<<< HEAD
    <div class="col-md-4" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->CostFormat($yesterdayData[0]->totalConsumption, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">'.$l->g(102701).'</p>
    </div>
    <div class="col-md-4" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($limitedData) ? $calculation->CostFormat($sumConsumptionInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">'.$l->g(102702). " ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705).'</p>
    </div>
    <div class="col-md-4" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($limitedData) ? $calculation->CostFormat($sumConsumptionCompare, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">'.$l->g(102702). " ".$config->COMPARE_INFO_PERIOD." ".$l->g(102705).'</p>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-6" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($data) ? $calculation->CostFormat($sumConsumption/$numberDevice, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">'.$l->g(102703).'</p>
    </div>
    <div class="col-md-6" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($limitedData) ? $calculation->CostFormat($sumConsumptionInPeriode/$numberDeviceInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">'.$l->g(102704)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705).'</p>
=======
    <div class="col-md-3" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight: bold;">' . (isset($yesterdayData) ? $calculation->CostFormat($yesterdayData[0]->totalConsumption, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color: #333; font-size: 15px;">'.$l->g(80911).'</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight: bold;">' . (isset($limitedData) ? $calculation->CostFormat($sumConsumptionInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color: #333; font-size: 15px;">'.$l->g(80912). " ".$config->COLLECT_INFO_PERIOD." ".$l->g(80915).'</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight: bold;">' . (isset($data) ? $calculation->CostFormat($sumConsumption/$numberDevice, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color: #333; font-size: 15px;">'.$l->g(80913).'</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight: bold;">' . (isset($limitedData) ? $calculation->CostFormat($sumConsumptionInPeriode/$numberDeviceInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color: #333; font-size: 15px;">'.$l->g(80914)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(80915).'</p>
>>>>>>> main
    </div>
</div>
';

echo $table;

echo "<br>";

$labels = ["'".$l->g(102702)." ".$config->COMPARE_INFO_PERIOD." ".$l->g(102705)."', '".$l->g(102702)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)."'"];

$labelsSettings = array(
    "consumption" => array(
        "backgroundColor" => "'#1941A5'",
        "data" => "[
            '".str_replace(" "."kW/h", "", $calculation->ConsumptionFormat($sumConsumptionCompare, "kW/h", $config->CONSUMPTION_ROUND))."',
            '".str_replace(" "."kW/h", "", $calculation->ConsumptionFormat($sumConsumptionInPeriode, "kW/h", $config->CONSUMPTION_ROUND))."'
            ]",
        "label" => "'".$l->g(102706)." ("."kW/h".")'",
        "type" => "'bar'"
    ),
    "cost" => array(
        "backgroundColor" => "'#AFD8F8'",
        "data" => "[
            '".str_replace(" ".$config->COST_UNIT, "", $calculation->CostFormat($sumConsumptionCompare, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND))."',
            '".str_replace(" ".$config->COST_UNIT, "", $calculation->CostFormat($sumConsumptionInPeriode, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND))."'
            ]",
        "label" => "'".$l->g(102707)." (".$config->COST_UNIT.")'",
        "type" => "'bar'"
    )
);

$chart->createCanvas("histogram");
$chart->createChart("histogram", "", $labels, $labelsSettings);

echo close_form();

?>