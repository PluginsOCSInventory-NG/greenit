<?php

$form_name = "consumptionStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>".$l->g(102708)."</h4>";

$labels = array();
$string = "";
foreach($sumConsumptionLimited as $group => $value)
{
    $string .= "'".$group."'";
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

$datasets = array(
    "label" => "'".$l->g(102801)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (kW/h)'",
    "data" => "[".$data."]",
    "backgroundColor" => "[
        'rgb(255, 99, 132)',
        'rgb(54, 162, 235)',
        'rgb(255, 205, 86)'
    ]",
);

$diagram->createCanvas("test", "6", "400");
$diagram->createDoughnutChart("test", $l->g(102801)." ".$config->COLLECT_INFO_PERIOD." ".$l->g(102705)." (kW/h)", $labels, $datasets);

echo close_form();

?>