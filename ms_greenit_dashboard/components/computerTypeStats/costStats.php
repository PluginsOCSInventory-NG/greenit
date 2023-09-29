<?php

$form_name = "costStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>".$l->g(103000)."</h4>";

// ****************************************** Total cost for yesterday doughnut ******************************************/
$labels = array();
$nbLabels = 0;
$string = "";
foreach($sumCostYesterday as $group => $value)
{
    $string .= '"'.$group.'"';
    $nbLabels++;
    if (next($sumCostYesterday)==true) $string .= ", ";
}
$labels = [$string];

$data = "";
$string = "";
foreach($sumCostYesterday as $group => $value)
{
    $string .= '"'.str_replace(" "."€", "", floatval($calculation->CostFormat(floatval($value), "W/h", floatval($config->KILOWATT_COST), $config->COST_UNIT, intval($config->COST_ROUND)))).'"';

    if (next($sumCostYesterday)==true) $string .= ", ";
}
$data = $string;

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => '"'.$l->g(103001).' ('.$config->COST_UNIT.')"',
    "data" => "[".$data."]",
    "backgroundColor" => "[".$backgroundColor."]",
);

$diagram->createCanvas($l->g(103001)." ($config->COST_UNIT)", "6", "400");
$diagram->createDoughnutChart($l->g(103001)." ($config->COST_UNIT)", $l->g(103001)." ($config->COST_UNIT)", $labels, $datasets);

// ****************************************** Total cost for last compare doughnut ******************************************/
$labels = array();
$nbLabels = 0;
$string = "";
foreach($sumCostCompare as $group => $value)
{
    $string .= '"'.$group.'"';
    $nbLabels++;
    if (next($sumCostCompare)==true) $string .= ", ";
}
$labels = [$string];

$data = "";
$string = "";
foreach($sumCostCompare as $group => $value)
{
    $string .= '"'.str_replace(" "."€", "", floatval($calculation->CostFormat(floatval($value), "W/h", floatval($config->KILOWATT_COST), $config->COST_UNIT, intval($config->COST_ROUND)))).'"';

    if (next($sumCostCompare)==true) $string .= ", ";
}
$data = $string;

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => '"'.$l->g(103002).' '.$config->COMPARE_INFO_PERIOD.' '.$l->g(102705).' ('.$config->COST_UNIT.')"',
    "data" => "[".$data."]",
    "backgroundColor" => "[".$backgroundColor."]",
);

$diagram->createCanvas($l->g(103002)." ".$config->COMPARE_INFO_PERIOD." ".$l->g(102705)." (".$config->COST_UNIT.")", "6", "400");
$diagram->createDoughnutChart($l->g(103002)." ".$config->COMPARE_INFO_PERIOD." ".$l->g(102705)." (".$config->COST_UNIT.")", $l->g(103002)." ".$config->COMPARE_INFO_PERIOD." ".$l->g(102705)." (".$config->COST_UNIT.")", $labels, $datasets);

echo close_form();

?>