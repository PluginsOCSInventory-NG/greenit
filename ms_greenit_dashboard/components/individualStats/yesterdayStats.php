<?php

$form_name = "yesterdayStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<br><h4>".$l->g(80905)."</h4>";

$table =
'<div>
    <table id="tab_stats" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; text-align:center; margin:auto; width:100%; margin-top:20px; background:#fff; border: 1px solid #ddd; table-layout: fixed;" >
        <tr>
            <td style="border-right: 1px solid #ddd; padding: 5px;"><span style="font-size:32px; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->ConsumptionFormat($yesterdayData[0]->totalConsumption, "kW/h", $config->CONSUMPTION_ROUND) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80906).'</span></td>
            <td style="border-right: 1px solid #ddd;"><span style="font-size:32px; font-weight:bold;">' . (isset($yesterdayData) ? $calculation->TimeFormat($yesterdayData[0]->totalUptime, $config->UPTIME_FORMAT) : '0') . '</span> </p><span style="color:#333; font-size:13pt;">'.$l->g(80908).'</span></td>
        </tr>
    </table>
</div>';

echo $table;
echo "<hr>";
echo close_form();

?>