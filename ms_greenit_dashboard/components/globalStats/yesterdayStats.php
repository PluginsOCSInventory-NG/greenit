<?php

$form_name = "yesterdayStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102600) . "</h4>";

//////////////////////////////
// Show yesterday stats
$table = '
<div class="row">
    <div class="col-md-3" style="border: 1px solid #ddd;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData["GLOBAL"]) && $yesterdayData["GLOBAL"]->totalConsumption != NULL ? $calculation->ConsumptionFormat($yesterdayData["GLOBAL"]->totalConsumption, "kW/h", $config->CONSUMPTION_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102601) . '</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData["GLOBAL"]) && $yesterdayData["GLOBAL"]->consumptionAverage != NULL ? $calculation->ConsumptionFormat($yesterdayData["GLOBAL"]->consumptionAverage, "kW/h", $config->CONSUMPTION_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102602) . '</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData["GLOBAL"]) && $yesterdayData["GLOBAL"]->totalUptime != NULL ? $calculation->TimeFormat($yesterdayData["GLOBAL"]->totalUptime, $config->UPTIME_FORMAT) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102603) . '</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData["GLOBAL"]) && $yesterdayData["GLOBAL"]->uptimeAverage != NULL ? $calculation->TimeFormat($yesterdayData["GLOBAL"]->uptimeAverage, $config->UPTIME_FORMAT) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102604) . '</p>
    </div>
</div>
';

echo $table;
//////////////////////////////

echo "<hr>";

echo close_form();

?>