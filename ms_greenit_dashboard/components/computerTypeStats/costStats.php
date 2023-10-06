<?php

$form_name = "costStats";
echo open_form($form_name, '', '', 'form-horizontal');

echo "<h4>" . $l->g(102700) . "</h4>";

//////////////////////////////
// Show average cost number data
$table = '';

$table .= '
<div class="row">
';
foreach ($computersType as $computerType) {
    $table .= '
    <div class="col-md-4" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($collectData) ? $calculation->CostFormat($sumConsumptionCollect[$computerType] / $nbDevicesCollect[$computerType], "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102708) . " " . $computerType . " " . $l->g(102709) . " " . " " . $l->g(102710) . " " . $config->COLLECT_INFO_PERIOD . " " . $l->g(102705) . '</p>
    </div>
    ';
}
$table .= '
</div>
<br>
<div class="row">
';
foreach ($computersType as $computerType) {
    $table .= '
    <div class="col-md-4" style="border: 1px solid #ddd; padding: 5px;">
        <p style="font-size: 32px; font-weight:bold;">' . (isset($collectData) ? $calculation->CostFormat($sumConsumptionCompare[$computerType] / $nbDevicesCompare[$computerType], "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0') . '</p>
        <p style="color:#333; font-size: 15px;">' . $l->g(102708) . " " . $computerType . " " . $l->g(102709) . " " . " " . $l->g(102710) . " " . $config->COMPARE_INFO_PERIOD . " " . $l->g(102705) . '</p>
    </div>
    ';
}
$table .= '
</div>
';
echo $table;
//////////////////////////////

//////////////////////////////
// Show cost per period D-1
$labels = array();

$backgroundColor = $diagram->generateColorList(2, true);

$label = "";
$data = array();
$data["CONSUMPTION"] = "";
$data["COST"] = "";
foreach ($computersType as $computerType) {
    if (isset($yesterdayData[$computerType])) {
        $label .= "'" . $computerType . "'";
        if (isset($yesterdayData[$computerType]->totalConsumption)) {
            $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", $calculation->ConsumptionFormat(floatval($yesterdayData[$computerType]->totalConsumption), "kW/h", $config->CONSUMPTION_ROUND)) . "'";
            $data["COST"] .= "'" . str_replace(" " . $config->COST_UNIT, "", $calculation->CostFormat(floatval($yesterdayData[$computerType]->totalConsumption), "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND)) . "'";
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
    "computerTypeConsumption" => array(
        "backgroundColor" => $backgroundColor[0],
        "data" => "[
            " . $data["CONSUMPTION"] . "
            ]",
        "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
        "type" => "'bar'",
    ),
    "computerTypeCost" => array(
        "backgroundColor" => $backgroundColor[1],
        "data" => "[
                " . $data["COST"] . "
                ]",
        "label" => "'" . $l->g(102707) . " (" . $config->COST_UNIT . ")'",
        "type" => "'bar'",
    )
);

$diagram->createCanvas("yesterday_cost_diagram", "4", "425");
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
foreach ($computersType as $computerType) {
    if (isset($collectData[$computerType])) {
        $label .= "'" . $computerType . "'";
        $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", (isset($sumConsumptionCollect[$computerType]) && $sumConsumptionCollect[$computerType] != NULL ? $calculation->ConsumptionFormat($sumConsumptionCollect[$computerType], "kW/h", $config->CONSUMPTION_ROUND) : '0')) . "'";
        $data["COST"] .= "'" . str_replace(" " . $config->COST_UNIT, "", (isset($sumConsumptionCollect[$computerType]) && $sumConsumptionCollect[$computerType] != NULL ? $calculation->CostFormat($sumConsumptionCollect[$computerType], "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0')) . "'";

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
    "computerTypeConsumption" => array(
        "backgroundColor" => $backgroundColor[0],
        "data" => "[
            " . $data["CONSUMPTION"] . "
            ]",
        "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
        "type" => "'bar'",
    ),
    "computerTypeCost" => array(
        "backgroundColor" => $backgroundColor[1],
        "data" => "[
                " . $data["COST"] . "
                ]",
        "label" => "'" . $l->g(102707) . " (" . $config->COST_UNIT . ")'",
        "type" => "'bar'",
    )
);

$diagram->createCanvas("collect_cost_diagram", "4", "425");
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
foreach ($computersType as $computerType) {
    if (isset($compareData[$computerType])) {
        $label .= "'" . $computerType . "'";
        $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", (isset($sumConsumptionCompare[$computerType]) && $sumConsumptionCompare[$computerType] != NULL ? $calculation->ConsumptionFormat($sumConsumptionCompare[$computerType], "kW/h", $config->CONSUMPTION_ROUND) : '0')) . "'";
        $data["COST"] .= "'" . str_replace(" " . $config->COST_UNIT, "", (isset($sumConsumptionCompare[$computerType]) && $sumConsumptionCompare[$computerType] != NULL ? $calculation->CostFormat($sumConsumptionCompare[$computerType], "W/h", $config->KILOWATT_COST, $config->COST_UNIT, $config->COST_ROUND) : '0')) . "'";

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
    "computerTypeConsumption" => array(
        "backgroundColor" => $backgroundColor[0],
        "data" => "[
            " . $data["CONSUMPTION"] . "
            ]",
        "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
        "type" => "'bar'",
    ),
    "computerTypeCost" => array(
        "backgroundColor" => $backgroundColor[1],
        "data" => "[
                " . $data["COST"] . "
                ]",
        "label" => "'" . $l->g(102707) . " (" . $config->COST_UNIT . ")'",
        "type" => "'bar'",
    )
);

$diagram->createCanvas("compare_cost_diagram", "4", "425");
$diagram->createBarChart("compare_cost_diagram", "bar", $l->g(102702) . ' ' . $config->COMPARE_INFO_PERIOD . ' ' . $l->g(102705) . ' (' . $config->COST_UNIT . ')', $labels, $datasets);
//////////////////////////////

echo close_form();

?>