<?php

$form_name = "uptimeStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>".$l->g(102900)."</h4>";

// ****************************************** Total uptime for last collect doughnut ******************************************/
$labels = array();
$nbLabels = 0;
$string = "";
foreach($sumUptimeYesterday as $group => $value)
{
    $string .= "'".$group."'";
    $nbLabels++;
    if (next($sumUptimeYesterday)==true) $string .= ", ";
}
$labels = [$string];

$data = "";
$string = "";
foreach($sumUptimeYesterday as $group => $value)
{
    $string .= "'".str_replace(" "."s", "", $calculation->TimeFormat($value, "s"))."'";

    if (next($sumUptimeYesterday)==true) $string .= ", ";
}
$data = $string;

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => "'".$l->g(102901)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (s)'",
    "data" => "[".$data."]",
    "backgroundColor" => "[".$backgroundColor."]"
);

$diagram->createCanvas($l->g(102901)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (s)", "6", "400");
$diagram->createDoughnutChart($l->g(102901)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (s)", $l->g(102901)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (s)", $labels, $datasets);

// ****************************************** Total uptime for last compare doughnut ******************************************/
$labels = array();
$nbLabels = 0;
$string = "";
foreach($sumUptimeCompare as $group => $value)
{
    $string .= "'".$group."'";
    $nbLabels++;
    if (next($sumUptimeCompare)==true) $string .= ", ";
}
$labels = [$string];

$data = "";
$string = "";
foreach($sumUptimeCompare as $group => $value)
{
    $string .= "'".str_replace(" "."s", "", $calculation->TimeFormat($value, "s"))."'";

    if (next($sumUptimeCompare)==true) $string .= ", ";
}
$data = $string;

$backgroundColor = $diagram->generateColorList($nbLabels);

$datasets = array(
    "label" => "'".$l->g(102901)." ".$config->COMPARE_INFO_PERIOD." ".$l->g(102705)." (s)'",
    "data" => "[".$data."]",
    "backgroundColor" => "[".$backgroundColor."]"
);

$diagram->createCanvas($l->g(102901)." ".$config->COMPARE_INFO_PERIOD." ".$l->g(102705)." (s)", "6", "400");
$diagram->createDoughnutChart($l->g(102901)." ".$config->COMPARE_INFO_PERIOD." ".$l->g(102705)." (s)", $l->g(102901)." ".$config->COMPARE_INFO_PERIOD." ".$l->g(102705)." (s)", $labels, $datasets);

echo close_form();

?>