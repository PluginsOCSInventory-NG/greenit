<?php

$form_name = "yesterdayStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102600) . "</h4>";

//////////////////////////////
// Show consumption and uptime
$table = '
<div class="row">
    <div class="col-md-6" style="border: 1px solid #ddd;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->TimeFormat($yesterdayData["CLIENTS"]->totalUptime, $config->UPTIME_FORMAT) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102603) . " " . $l->g(102605) . " " . $l->g(102606) . '</p>
    </div>
    <div class="col-md-6" style="border: 1px solid #ddd;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->TimeFormat($yesterdayData["SERVERS"]->totalUptime, $config->UPTIME_FORMAT) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102603) . " " . $l->g(102605) . " " . $l->g(102607) . '</p>
    </div>
</div>
';
echo $table;
//////////////////////////////

//////////////////////////////
// Show total consumption
$labels = ["'" . $l->g(102601) . "'"];

$backgroundColor = $diagram->generateColorList(2, true);

$datasets = array(
    "clientConsumption" => array(
        "backgroundColor" => $backgroundColor[0],
        "data" => "[
            '" . str_replace(" " . "kW/h", "", $calculation->ConsumptionFormat(floatval($yesterdayData["CLIENTS"]->totalConsumption), "kW/h", $config->CONSUMPTION_ROUND)) . "'
            ]",
        "label" => "'" . $l->g(102601) . " " . $l->g(102605) . " " . $l->g(102606) . " (" . "kW/h" . ")'",
        "type" => "'bar'"
    ),
    "serverConsumption" => array(
        "backgroundColor" => $backgroundColor[1],
        "data" => "[
            '" . str_replace(" " . "kW/h", "", $calculation->ConsumptionFormat(floatval($yesterdayData["SERVERS"]->totalConsumption), "kW/h", $config->CONSUMPTION_ROUND)) . "'
            ]",
        "label" => "'" . $l->g(102601) . " " . $l->g(102605) . " " . $l->g(102607) . " (" . "kW/h" . ")'",
        "type" => "'bar'"
    ),
);
echo '
<div class="row">
<div class="col-md-3"></div>
';
$diagram->createCanvas("histogram_total_consumption", "6", "200");
echo '
<div class="col-md-3"></div>
</div>
';
$diagram->createBarChart("histogram_total_consumption", "", $labels, $datasets);
//////////////////////////////

echo '<hr>';
echo close_form();

?>