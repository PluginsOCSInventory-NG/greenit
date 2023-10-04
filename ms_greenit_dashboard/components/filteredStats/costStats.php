<?php

$form_name = "costStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102700) . "</h4>";

//////////////////////////////
// Show cost number data
$table = '
<div class="row">
    <div class="col-md-4" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData["FILTERED"]) && $yesterdayData["FILTERED"] != NULL ? $calculation->CostFormat($yesterdayData["FILTERED"]->totalConsumption, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102701) . '</p>
    </div>
    <div class="col-md-4" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($collectData) ? $calculation->CostFormat($sumConsumptionCollect, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102702) . " " . $config->COLLECT_INFO_PERIOD . " " . $l->g(102705) . '</p>
    </div>
    <div class="col-md-4" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($collectData) ? $calculation->CostFormat($sumConsumptionCompare, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102702) . " " . $config->COMPARE_INFO_PERIOD . " " . $l->g(102705) . '</p>
    </div>
</div>
';

echo $table;
//////////////////////////////

echo "<br>";

//////////////////////////////
// Show cost of device between collect period diagram
$labels = ["'" . $l->g(102702) . " " . $config->COLLECT_INFO_PERIOD . " " . $l->g(102705) . "'"];

$labelsSettings = array(
    "consumption" => array(
        "backgroundColor" => "'#1941A5'",
        "data" => "[
            '" . str_replace(" " . "kW/h", "", (isset($collectData) ? $calculation->ConsumptionFormat($sumConsumptionCollect, "kW/h", $config->CONSUMPTION_ROUND) : "0")) . "'
            ]",
        "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
        "type" => "'bar'"
    ),
    "cost" => array(
        "backgroundColor" => "'#AFD8F8'",
        "data" => "[
            '" . str_replace(" " . $config->COST_UNIT, "", (isset($collectData) ? $calculation->CostFormat($sumConsumptionCollect, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : "0")) . "'
            ]",
        "label" => "'" . $l->g(102707) . " (" . $config->COST_UNIT . ")'",
        "type" => "'bar'"
    )
);

$diagram->createCanvas("histogram_collect_period", "6", "200");
$diagram->createBarChart("histogram_collect_period", "bar", "", $labels, $labelsSettings);
//////////////////////////////

//////////////////////////////
// Show cost of devices between compare period diagram
$labels = ["'" . $l->g(102702) . " " . $config->COMPARE_INFO_PERIOD . " " . $l->g(102705) . "'"];

$labelsSettings = array(
    "consumption" => array(
        "backgroundColor" => "'#1941A5'",
        "data" => "[
            '" . str_replace(" " . "kW/h", "", (isset($collectData) ? $calculation->ConsumptionFormat($sumConsumptionCompare, "kW/h", $config->CONSUMPTION_ROUND) : "0")) . "'
            ]",
        "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
        "type" => "'bar'"
    ),
    "cost" => array(
        "backgroundColor" => "'#AFD8F8'",
        "data" => "[
            '" . str_replace(" " . $config->COST_UNIT, "", (isset($collectData) ? $calculation->CostFormat($sumConsumptionCompare, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : "0")) . "'
            ]",
        "label" => "'" . $l->g(102707) . " (" . $config->COST_UNIT . ")'",
        "type" => "'bar'"
    )
);

$diagram->createCanvas("histogram_compare_period", "6", "200");
$diagram->createBarChart("histogram_compare_period", "bar", "", $labels, $labelsSettings);
//////////////////////////////

echo close_form();

?>