<?php

$form_name = "costStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102700) . "</h4>";

//////////////////////////////
// Show average cost number data
$table = '';

$table .= '
<div class="row">
<div class="col-md-1"></div>
';
foreach ($compareManufacturers as $manufacturer) {
    $table .= '
    <div class="col-md-2" style="border: 1px solid #ddd; padding: 5px;">
    <p style="font-size: 32px; font-weight:bold;">' . (isset($compareData[$manufacturer]->totalConsumption) ? $calculation->CostFormat($compareData[$manufacturer]->totalConsumption / $nbDevicesCompare[$manufacturer], "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
    <p style="color:#333; font-size: 15px;">' . $l->g(102708) . " " . $manufacturer . " " . $l->g(102709) . " " . $config->COMPARE_INFO_PERIOD . " " . $l->g(102705) . '</p>
    </div>
    ';
}
$table .= '
<div class="col-md-1"></div>
</div>
';
echo $table;
//////////////////////////////

echo "<br>";

//////////////////////////////
// Show cost per period D-1
$labels = array();

$backgroundColor = $diagram->generateColorList(2, true);

$label = "";
$data = array();
$data["CONSUMPTION"] = "";
$data["COST"] = "";
foreach ($yesterdayManufacturers as $manufacturer) {
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

$diagram->createCanvas("yesterday_cost_diagram", "4", "550");
$diagram->createBarChart("yesterday_cost_diagram", "horizontalBar", $l->g(102701) . " " . $l->g(102711) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

//////////////////////////////
// Show cost per period Collect
$labels = array();

$backgroundColor = $diagram->generateColorList(2, true);

$label = "";
$data = array();
$data["CONSUMPTION"] = "";
$data["COST"] = "";
foreach ($collectManufacturers as $manufacturer) {
    if (isset($collectData[$manufacturer])) {
        $label .= "'" . $manufacturer . "'";
        if (isset($collectData[$manufacturer]->totalConsumption)) {
            $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", $calculation->ConsumptionFormat(floatval($collectData[$manufacturer]->totalConsumption), "kW/h", $config->CONSUMPTION_ROUND)) . "'";
            $data["COST"] .= "'" . str_replace(" " . $config->COST_UNIT, "", $calculation->CostFormat(floatval($collectData[$manufacturer]->totalConsumption), "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND)) . "'";
        } else {
            $data["CONSUMPTION"] .= 0;
            $data["COST"] .= 0;
        }
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

$diagram->createCanvas("collect_cost_diagram", "4", "550");
$diagram->createBarChart("collect_cost_diagram", "horizontalBar", $l->g(102702) . ' ' . $config->COLLECT_INFO_PERIOD . ' ' . $l->g(102705) . ' ' . $l->g(102711) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

//////////////////////////////
// Show cost per period Compare
$labels = array();

$backgroundColor = $diagram->generateColorList(2, true);

$label = "";
$data = array();
$data["CONSUMPTION"] = "";
$data["COST"] = "";
foreach ($compareManufacturers as $manufacturer) {
    if (isset($compareData[$manufacturer])) {
        $label .= "'" . $manufacturer . "'";
        if (isset($compareData[$manufacturer]->totalConsumption)) {
            $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", $calculation->ConsumptionFormat(floatval($compareData[$manufacturer]->totalConsumption), "kW/h", $config->CONSUMPTION_ROUND)) . "'";
            $data["COST"] .= "'" . str_replace(" " . $config->COST_UNIT, "", $calculation->CostFormat(floatval($compareData[$manufacturer]->totalConsumption), "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND)) . "'";
        } else {
            $data["CONSUMPTION"] .= 0;
            $data["COST"] .= 0;
        }
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

$diagram->createCanvas("compare_cost_diagram", "4", "550");
$diagram->createBarChart("compare_cost_diagram", "horizontalBar", $l->g(102702) . ' ' . $config->COMPARE_INFO_PERIOD . ' ' . $l->g(102705) . ' ' . $l->g(102711) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

echo close_form();

?>