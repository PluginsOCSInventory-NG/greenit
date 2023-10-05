<?php

$form_name = "costStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102700) . "</h4>";

//////////////////////////////
// Show cost per period D-1
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
        "type" => "'bar'",
    ),
    "manufacturerCost" => array(
        "backgroundColor" => $backgroundColor[1],
        "data" => "[
                " . $data["COST"] . "
                ]",
        "label" => "'" . $l->g(102707) . " (" . $config->COST_UNIT . ")'",
        "type" => "'bar'",
    )
);

$diagram->createCanvas("yesterday_cost_diagram", "4", "700");
$diagram->createBarChart("yesterday_cost_diagram", "bar", $l->g(102701) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

//////////////////////////////
// Show cost per period Collect
$labels = array();

$backgroundColor = $diagram->generateColorList(2, true);

$label = "";
$data = array();
$data["CONSUMPTION"] = "";
$data["COST"] = "";
foreach ($manufacturers as $manufacturer) {
    if (isset($collectData[$manufacturer])) {
        $label .= "'" . $manufacturer . "'";
        $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", (isset($sumConsumptionCollect[$manufacturer]) && $sumConsumptionCollect[$manufacturer] != NULL ? $calculation->ConsumptionFormat($sumConsumptionCollect[$manufacturer], "kW/h", $config->CONSUMPTION_ROUND) : '0')) . "'";
        $data["COST"] .= "'" . str_replace(" " . $config->COST_UNIT, "", (isset($sumConsumptionCollect[$manufacturer]) && $sumConsumptionCollect[$manufacturer] != NULL ? $calculation->CostFormat($sumConsumptionCollect[$manufacturer], "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0')) . "'";

        if (next($collectData) == true) {
            $label .= ", ";
            $data["CONSUMPTION"] .= ", ";
            $data["COST"] .= ", ";
        }
    }
}
reset($collectData);
$labels = [$label];

$datasets = array(
    "manufacturerConsumption" => array(
        "backgroundColor" => $backgroundColor[0],
        "data" => "[
            " . $data["CONSUMPTION"] . "
            ]",
        "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
        "type" => "'bar'",
    ),
    "manufacturerCost" => array(
        "backgroundColor" => $backgroundColor[1],
        "data" => "[
                " . $data["COST"] . "
                ]",
        "label" => "'" . $l->g(102707) . " (" . $config->COST_UNIT . ")'",
        "type" => "'bar'",
    )
);

$diagram->createCanvas("collect_cost_diagram", "4", "700");
$diagram->createBarChart("collect_cost_diagram", "bar", $l->g(102702) . ' ' . $config->COLLECT_INFO_PERIOD . ' ' . $l->g(102705) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

//////////////////////////////
// Show cost per period Compare
$labels = array();

$backgroundColor = $diagram->generateColorList(2, true);

$label = "";
$data = array();
$data["CONSUMPTION"] = "";
$data["COST"] = "";
foreach ($manufacturers as $manufacturer) {
    if (isset($compareData[$manufacturer])) {
        $label .= "'" . $manufacturer . "'";
        $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", (isset($sumConsumptionCompare[$manufacturer]) && $sumConsumptionCompare[$manufacturer] != NULL ? $calculation->ConsumptionFormat($sumConsumptionCompare[$manufacturer], "kW/h", $config->CONSUMPTION_ROUND) : '0')) . "'";
        $data["COST"] .= "'" . str_replace(" " . $config->COST_UNIT, "", (isset($sumConsumptionCompare[$manufacturer]) && $sumConsumptionCompare[$manufacturer] != NULL ? $calculation->CostFormat($sumConsumptionCompare[$manufacturer], "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0')) . "'";

        if (next($compareData) == true) {
            $label .= ", ";
            $data["CONSUMPTION"] .= ", ";
            $data["COST"] .= ", ";
        }
    }
}
reset($compareData);
$labels = [$label];

$datasets = array(
    "manufacturerConsumption" => array(
        "backgroundColor" => $backgroundColor[0],
        "data" => "[
            " . $data["CONSUMPTION"] . "
            ]",
        "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
        "type" => "'bar'",
    ),
    "manufacturerCost" => array(
        "backgroundColor" => $backgroundColor[1],
        "data" => "[
                " . $data["COST"] . "
                ]",
        "label" => "'" . $l->g(102707) . " (" . $config->COST_UNIT . ")'",
        "type" => "'bar'",
    )
);

$diagram->createCanvas("compare_cost_diagram", "4", "700");
$diagram->createBarChart("compare_cost_diagram", "bar", $l->g(102702) . ' ' . $config->COMPARE_INFO_PERIOD . ' ' . $l->g(102705) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

echo close_form();

?>