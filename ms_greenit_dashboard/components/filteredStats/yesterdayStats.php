<?php

$form_name = "yesterdayStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102600) . "</h4>";

//////////////////////////////
// Show yesterday stats
$table = '
<div class="row">
    <div class="col-md-6" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData["FILTERED"]) && $yesterdayData["FILTERED"]->totalConsumption != NULL ? $calculation->ConsumptionFormat($yesterdayData["FILTERED"]->totalConsumption, "kW/h", $config->CONSUMPTION_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102601) . '</p>
    </div>
    <div class="col-md-6" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData["FILTERED"]) && $yesterdayData["FILTERED"]->totalUptime != NULL ? $calculation->TimeFormat($yesterdayData["FILTERED"]->totalUptime, $config->UPTIME_FORMAT) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102603) . '</p>
    </div>
</div>
';

echo $table;
//////////////////////////////

echo "<hr>";

echo close_form();

?>