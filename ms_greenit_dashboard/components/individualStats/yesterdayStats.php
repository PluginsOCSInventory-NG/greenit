<?php

$form_name = "yesterdayStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>".$l->g(80905)."</h4>";

$table = '
<div class="row">
    <div class="col-md-6" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->ConsumptionFormat($yesterdayData[0]->totalConsumption, "kW/h", $config->CONSUMPTION_ROUND) : '0') . '</p>
        <p style="color: #333; font-size: 15px;">'.$l->g(80906).'</p>
    </div>
    <div class="col-md-6" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->TimeFormat($yesterdayData[0]->totalUptime, $config->UPTIME_FORMAT) : '0') . '</p>
        <p style="color: #333; font-size: 15px;">'.$l->g(80908).'</p>
    </div>
</div>
';

echo $table;
echo "<hr>";
echo close_form();

?>