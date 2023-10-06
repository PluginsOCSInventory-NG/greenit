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
foreach ($yesterdayData as $group => $value) {
    $data .= '"' . str_replace(" " . $config->COST_UNIT, "", (isset($yesterdayData[$group]->totalConsumption) && $yesterdayData[$group]->totalConsumption != NULL ? $calculation->CostFormat($yesterdayData[$group]->totalConsumption, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : 0)) . '"';
    if (next($yesterdayData) == true)
        $data .= ", ";
}

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => '"' . $l->g(102707) . ' (' . $config->COST_UNIT . ')"',
    "data" => "[" . $data . "]",
    "backgroundColor" => "[" . $backgroundColor . "]"
);

$diagram->createCanvas("yesterday_cost_diagram", "4", "275");
$diagram->createRoundChart("yesterday_cost_diagram", "doughnut", $l->g(102701) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
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
if (is_defined($sumConsumptionCollect)) {
    foreach ($sumConsumptionCollect as $group => $value) {
        $data .= '"' . str_replace(" " . $config->COST_UNIT, "", (isset($value) && $value != NULL ? $calculation->CostFormat($value, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : "0")) . '"';
        if (next($sumConsumptionCollect) == true)
            $data .= ", ";
    }
} else
    $data = '"0","0"';

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => '"' . $l->g(102707) . ' (' . $config->COST_UNIT . ')"',
    "data" => "[" . $data . "]",
    "backgroundColor" => "[" . $backgroundColor . "]"
);

$diagram->createCanvas("collect_cost_diagram", "4", "275");
$diagram->createRoundChart("collect_cost_diagram", "doughnut", $l->g(102702) . ' ' . $config->COLLECT_INFO_PERIOD . ' ' . $l->g(102705) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
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
if (is_defined($sumConsumptionCollect)) {
    foreach ($sumConsumptionCompare as $group => $value) {
        $data .= '"' . str_replace(" " . $config->COST_UNIT, "", (isset($value) && $value != NULL ? $calculation->CostFormat($value, "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : "0")) . '"';

        if (next($sumConsumptionCompare) == true)
            $data .= ", ";
    }
} else
    $data = '"0","0"';

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => '"' . $l->g(102707) . ' (' . $config->COST_UNIT . ')"',
    "data" => "[" . $data . "]",
    "backgroundColor" => "[" . $backgroundColor . "]"
);

$diagram->createCanvas("compare_cost_diagram", "4", "275");
$diagram->createRoundChart("compare_cost_diagram", "doughnut", $l->g(102702) . ' ' . $config->COMPARE_INFO_PERIOD . ' ' . $l->g(102705) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

echo close_form();

?>