<?php

$form_name = "consumptionStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>".$l->g(102708)."</h4>";

// ****************************************** Total consumption doughnut ******************************************/
$labels = array();
$nbLabels = 0;
$string = "";
foreach($sumConsumptionLimited as $group => $value)
{
    $string .= "'".$group."'";
    $nbLabels++;
    if (next($sumConsumptionLimited)==true) $string .= ", ";
}
$labels = [$string];

$data = "";
$string = "";
foreach($sumConsumptionLimited as $group => $value)
{
    $string .= "'".str_replace(" "."kW/h", "", $calculation->ConsumptionFormat($value, "kW/h", $config->CONSUMPTION_ROUND))."'";

    if (next($sumConsumptionLimited)==true) $string .= ", ";
}
$data = $string;

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => "'".$l->g(102801)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (kW/h)'",
    "data" => "[".$data."]",
    "backgroundColor" => "[".$backgroundColor."]",
);

$diagram->createCanvas($l->g(102801)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (kW/h)", "6", "400");
$diagram->createDoughnutChart($l->g(102801)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (kW/h)", $l->g(102801)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (kW/h)", $labels, $datasets);

// ****************************************** Total uptime doughnut ******************************************/
$labels = array();
$nbLabels = 0;
$string = "";
foreach($sumConsumptionLimited as $group => $value)
{
    $string .= "'".$group."'";
    $nbLabels++;
    if (next($sumConsumptionLimited)==true) $string .= ", ";
}
$labels = [$string];

$data = "";
$string = "";
foreach($sumConsumptionLimited as $group => $value)
{
    $string .= "'".str_replace(" "."kW/h", "", $calculation->ConsumptionFormat($value, "kW/h", $config->CONSUMPTION_ROUND))."'";

    if (next($sumConsumptionLimited)==true) $string .= ", ";
}
$data = $string;

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => "'".$l->g(102801)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (kW/h)'",
    "data" => "[".$data."]",
    "backgroundColor" => "[".$backgroundColor."]"
);

$diagram->createCanvas("test2", "6", "400");
$diagram->createDoughnutChart("test2", $l->g(102801)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (kW/h)", $labels, $datasets);

echo close_form();

?>