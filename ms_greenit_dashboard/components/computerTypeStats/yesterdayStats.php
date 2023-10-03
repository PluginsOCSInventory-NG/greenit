<?php

$form_name = "yesterdayStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102600) . "</h4>";

//////////////////////////////
// Show consumption and uptime
$table = '
<div class="row">
    <div class="col-md-6" style="border: 1px solid #ddd;">
        <p style="font-size: 32px; font-weight:bold;">' . ($yesterdayData["CLIENTS"]->totalUptime != NULL ? $calculation->TimeFormat($yesterdayData["CLIENTS"]->totalUptime, $config->UPTIME_FORMAT) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102603) . " " . $l->g(102605) . " " . $l->g(102606) . '</p>
    </div>
    <div class="col-md-6" style="border: 1px solid #ddd;">
        <p style="font-size: 32px; font-weight:bold;">' . ($yesterdayData["SERVERS"]->totalUptime != NULL ? $calculation->TimeFormat($yesterdayData["SERVERS"]->totalUptime, $config->UPTIME_FORMAT) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102603) . " " . $l->g(102605) . " " . $l->g(102607) . '</p>
    </div>
</div>
';
echo $table;
//////////////////////////////

//////////////////////////////
// Show total consumption
$labels = ["'" . $l->g(102601) . " (" . "kW/h" . ")'"];

$backgroundColor = $diagram->generateColorList(2, true);

$data = array();
if (isset($yesterdayData["CLIENTS"]->totalConsumption))
    $data["CLIENTS"] = str_replace(" " . "kW/h", "", $calculation->ConsumptionFormat(floatval($yesterdayData["CLIENTS"]->totalConsumption), "kW/h", $config->CONSUMPTION_ROUND));
else
    $data["CLIENTS"] = 0;

if (isset($yesterdayData["SERVERS"]->totalConsumption))
    $data["SERVERS"] = str_replace(" " . "kW/h", "", $calculation->ConsumptionFormat(floatval($yesterdayData["SERVERS"]->totalConsumption), "kW/h", $config->CONSUMPTION_ROUND));
else
    $data["SERVERS"] = 0;

$datasets = array(
    "clientConsumption" => array(
        "backgroundColor" => $backgroundColor[0],
        "data" => "[
            '" . $data["CLIENTS"] . "'
            ]",
        "label" => "'" . $l->g(102601) . " " . $l->g(102605) . " " . $l->g(102606) . "'",
        "type" => "'horizontalBar'"
    ),
    "serverConsumption" => array(
        "backgroundColor" => $backgroundColor[1],
        "data" => "[
            '" . $data["SERVERS"] . "'
            ]",
        "label" => "'" . $l->g(102601) . " " . $l->g(102605) . " " . $l->g(102607) . "'",
        "type" => "'horizontalBar'"
    ),
);
echo '
<div class="row">
    <div class="col-md-2"></div>
';
$diagram->createCanvas("histogram_total_consumption", "8", "200");
echo '
    <div class="col-md-2"></div>
</div>
';
$diagram->createBarChart("histogram_total_consumption", "horizontalBar", "", $labels, $datasets);
//////////////////////////////

echo '<hr>';
echo close_form();

?>