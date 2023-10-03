<?php

$form_name = "yesterdayStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102600) . "</h4>";

//////////////////////////////
// Show total consumption
$labels = array();

$backgroundColor = $diagram->generateColorList(2, true);

$label = "";
$data = array();
$data["CONSUMPTION"] = "";
$data["COST"] = "";
foreach ($manufacturers as $manufacturer) {
    if (isset($yesterdayData[$manufacturer])) {
        $label .= "'" . $manufacturer . "'";
        if (isset($yesterdayData[$manufacturer]->totalConsumption)) {
            $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", $calculation->ConsumptionFormat(floatval($yesterdayData[$manufacturer]->totalConsumption), "kW/h", $config->CONSUMPTION_ROUND)) . "'";
            $data["COST"] .= "'" . str_replace(" " . $config->COST_UNIT, "", $calculation->CostFormat(floatval($yesterdayData[$manufacturer]->totalConsumption), "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND)) . "'";
        } else {
            $data["CONSUMPTION"] .= 0;
            $data["COST"] .= 0;
        }
        if (next($yesterdayData) == true) {
            $label .= ", ";
            $data["CONSUMPTION"] .= ", ";
            $data["COST"] .= ", ";
        }
    }
}
reset($yesterdayData);
$labels = [$label];

$datasets = array(
    "manufacturerConsumption" => array(
        "backgroundColor" => $backgroundColor[0],
        "data" => "[
            " . $data["CONSUMPTION"] . "
            ]",
        "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
        "type" => "'horizontalBar'",
    ),
    "manufacturerCost" => array(
        "backgroundColor" => $backgroundColor[1],
        "data" => "[
                " . $data["COST"] . "
                ]",
        "label" => "'" . $l->g(102707) . " (" . $config->COST_UNIT . ")'",
        "type" => "'horizontalBar'",
    )
);

$diagram->createCanvas("histogram_total_consumption", "12", "200");
$diagram->createBarChart("histogram_total_consumption", "horizontalBar", "", $labels, $datasets);
//////////////////////////////

echo '<hr>';
echo close_form();

?>