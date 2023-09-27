<?php

$form_name = "yesterdayStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>".$l->g(102600)."</h4>";

$table = '
<div class="row">
    <div class="col-md-3" style="border: 1px solid #ddd;">
        <p style="font-size: 2vw; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->ConsumptionFormat($yesterdayData[0]->totalConsumption, "kW/h", $config->CONSUMPTION_ROUND) : '0') . '</p>
        <p style="color:#333; font-size:1vw;">'.$l->g(80906).'</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd;">
        <p style="font-size: 2vw; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->ConsumptionFormat($yesterdayData[0]->consumptionAverage, "kW/h", $config->CONSUMPTION_ROUND) : '0') . '</p>
        <p style="color:#333; font-size:1vw;">'.$l->g(80907).'</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd;">
        <p style="font-size: 2vw; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->TimeFormat($yesterdayData[0]->totalUptime, $config->UPTIME_FORMAT) : '0') . '</p>
        <p style="color:#333; font-size:1vw;">'.$l->g(80908).'</p>
    </div>
    <div class="col-md-3" style="border: 1px solid #ddd;">
        <p style="font-size: 2vw; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->TimeFormat($yesterdayData[0]->uptimeAverage, $config->UPTIME_FORMAT) : '0') . '</p>
        <p style="color:#333; font-size:1vw;">'.$l->g(80909).'</p>
    </div>
</div>
';

echo $table;
echo "<hr>";
echo close_form();

?>