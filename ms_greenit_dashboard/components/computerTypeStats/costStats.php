<?php

$form_name = "costStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102700) . "</h4>";

//////////////////////////////
// Show cost per period D-1
$labels = array();
$nbLabels = 2;
$labels = [
    "'" . $l->g(102606) . "'",
    "'" . $l->g(102607) . "'"
];

$data = "";
$string = "";
foreach ($yesterdayData as $group => $value) {
    if (isset($yesterdayData[$group]->totalConsumption))
        $string .= '"' . str_replace(" " . $config->COST_UNIT, "", $calculation->CostFormat($yesterdayData[$group]->totalConsumption, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND)) . '"';
    else
        $string .= "0";
    if (next($yesterdayData) == true)
        $string .= ", ";
}
$data = $string;

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => '"' . $l->g(102707) . ' (' . $config->COST_UNIT . ')"',
    "data" => "[" . $data . "]",
    "backgroundColor" => "[" . $backgroundColor . "]"
);

$diagram->createCanvas("yesterday_cost_diagram", "4", "250");
$diagram->createDoughnutChart("yesterday_cost_diagram", $l->g(102701) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

//////////////////////////////
// Show cost per period Collect
$labels = array();
$nbLabels = 2;
$labels = [
    "'" . $l->g(102606) . "'",
    "'" . $l->g(102607) . "'"
];

$data = "";
$string = "";
foreach ($sumConsumptionCollect as $group => $value) {
    $string .= '"' . str_replace(" " . $config->COST_UNIT, "", $calculation->CostFormat($value, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND)) . '"';

    if (next($sumConsumptionCollect) == true)
        $string .= ", ";
}
$data = $string;

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => '"' . $l->g(102707) . ' (' . $config->COST_UNIT . ')"',
    "data" => "[" . $data . "]",
    "backgroundColor" => "[" . $backgroundColor . "]"
);

$diagram->createCanvas("collect_cost_diagram", "4", "250");
$diagram->createDoughnutChart("collect_cost_diagram", $l->g(102702) . ' ' . $config->COLLECT_INFO_PERIOD . ' ' . $l->g(102705) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

//////////////////////////////
// Show cost per period Compare
$labels = array();
$nbLabels = 2;
$labels = [
    "'" . $l->g(102606) . "'",
    "'" . $l->g(102607) . "'"
];

$data = "";
$string = "";
foreach ($sumConsumptionCompare as $group => $value) {
    $string .= '"' . str_replace(" " . $config->COST_UNIT, "", $calculation->CostFormat($value, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND)) . '"';

    if (next($sumConsumptionCompare) == true)
        $string .= ", ";
}
$data = $string;

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => '"' . $l->g(102707) . ' (' . $config->COST_UNIT . ')"',
    "data" => "[" . $data . "]",
    "backgroundColor" => "[" . $backgroundColor . "]"
);

$diagram->createCanvas("compare_cost_diagram", "4", "250");
$diagram->createDoughnutChart("compare_cost_diagram", $l->g(102702) . ' ' . $config->COMPARE_INFO_PERIOD . ' ' . $l->g(102705) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

echo close_form();

?>