<?php

require_once(__DIR__ . "/../../config/view.class.php");

class ManufacturerStatsView extends View
{
    /**
     * List of manufacturers
     */
    private object $manufacturers;

    /**
     *  List of yesterday data for the current view
     */
    private object $yesterdayData;

    /**
     * List of collect data for the current view
     */
    private object $collectData;

    /**
     * List of compare data for the current view
     */
    private object $compareData;

    /**
     * Constructor of the view which define evrything the view need to work
     */
    function __construct()
    {
        $this->logMessage = new LogMessage();
        $this->config = new Config();
        $this->calculation = new Calculation();
        $this->diagram = new Diagram();
        $this->data = new Data();

        $this->manufacturers = new stdClass();
        $this->manufacturers->{"HP"} = 10;
        $this->manufacturers->{"VMware, Inc"} = 10;
        $this->manufacturers->{"Hewlett-Packard"} = 10;
        $this->manufacturers->{"Dell Inc."} = 10;
        $this->manufacturers->{"Microsoft Corporation"} = 10;

        $this->yesterdayData = $this->data->GetGreenITData("
            SELECT 
            DATA 
            FROM greenit_stats 
            WHERE 
            DATE='" . $this->config->GetYesterdayDate() . "'
        ", false);
        $this->collectData = $this->data->GetGreenITData("
            SELECT 
            DATE, 
            DATA 
            FROM greenit_stats 
            WHERE 
            DATE BETWEEN '" . $this->config->GetCollectDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
        ", true);
        $this->compareData = $this->data->GetGreenITData("
            SELECT 
            DATE, 
            DATA 
            FROM greenit_stats 
            WHERE 
            DATE BETWEEN '" . $this->config->GetCompareDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
        ", true);
    }

    /**
     * Generate the YesterdayStats HTML code of the view
     * 
     * @return void Return nothing
     */
    public function ShowYesterdayStats(): void
    {
    }

    /**
     * Generate the ComparaisonStats HTML code of the view
     * 
     * @return void Return nothing
     */
    public function ShowComparatorStats(): void
    {
        global $l;

        echo "<h4>" . $l->g(102700) . "</h4>";

        $table = '
            <div class="row">
                <div class="col-md-1"></div>
        ';
        foreach ($this->manufacturers as $manufacturer => $count) {
            if (next($this->manufacturers)) {
                $table .= "
                    <div class='col-md-2' style='border-right: 1px solid #ddd;'>
                ";
            } else {
                $table .= "
                    <div class='col-md-2'>
                ";
            }
            $table .= "
                    <p style='font-size: 32px; font-weight:bold;'>" . (isset($this->compareData) && $this->compareData->RETURN != false ? $this->calculation->CostFormat($this->compareData->{$manufacturer}->totalConsumption / $this->compareData->{$manufacturer}->nbDevices, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102703) . " " . $manufacturer . " " . $l->g(102705) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
            ";
        }
        $table .= "
                <div class='col-md-1'></div>
            </div>
        ";
        echo $table;

        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $label = "";
        $data = array();
        $data["CONSUMPTION"] = "";
        $data["COST"] = "";
        foreach ($this->manufacturers as $manufacturer) {
            if (isset($this->yesterdayData->{$manufacturer})) {
                $label .= "'" . $manufacturer . "'";
                if (isset($this->yesterdayData->{$manufacturer}->totalConsumption)) {
                    $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", $this->calculation->ConsumptionFormat($this->yesterdayData->{$manufacturer}->totalConsumption, "kW/h", $this->config->GetConsumptionRound())) . "'";
                    $data["COST"] .= "'" . str_replace(" " . $this->config->COST_UNIT, "", $this->calculation->CostFormat($this->yesterdayData->{$manufacturer}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound())) . "'";
                } else {
                    $data["CONSUMPTION"] .= 0;
                    $data["COST"] .= 0;
                }
                if (next($this->yesterdayData) == true) {
                    $label .= ", ";
                    $data["CONSUMPTION"] .= ", ";
                    $data["COST"] .= ", ";
                }
            }
        }
        reset($this->yesterdayData);
        $labels = [$label];
        $datasets = array(
            "manufacturerConsumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[
                    " . $data["CONSUMPTION"] . "
                    ]",
                "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
            ),
            "manufacturerCost" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[
                        " . $data["COST"] . "
                        ]",
                "label" => "'" . $l->g(102707) . " (" . $this->config->GetCostUnit() . ")'",
            )
        );

        $this->diagram->createCanvas("yesterday_cost_diagram", "4", "550");
        $this->diagram->createHorizontalBarChart("yesterday_cost_diagram", $l->g(102701) . " " . $l->g(102711) . ' (' . $this->config->GetCostUnit() . ')', $labels, $datasets);

        $backgroundColor = $this->diagram->GenerateColorList(2, true);

        $label = "";
        $data = array();
        $data["CONSUMPTION"] = "";
        $data["COST"] = "";
        foreach ($this->manufacturers as $manufacturer) {
            if (isset($this->yesterdayData->{$manufacturer})) {
                $label .= "'" . $manufacturer . "'";
                if (isset($this->yesterdayData->{$manufacturer}->totalConsumption)) {
                    $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", $this->calculation->ConsumptionFormat($this->yesterdayData->{$manufacturer}->totalConsumption, "kW/h", $this->config->GetConsumptionRound())) . "'";
                    $data["COST"] .= "'" . str_replace(" " . $this->config->COST_UNIT, "", $this->calculation->CostFormat($this->yesterdayData->{$manufacturer}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound())) . "'";
                } else {
                    $data["CONSUMPTION"] .= 0;
                    $data["COST"] .= 0;
                }
                if (next($this->yesterdayData) == true) {
                    $label .= ", ";
                    $data["CONSUMPTION"] .= ", ";
                    $data["COST"] .= ", ";
                }
            }
        }
        reset($this->yesterdayData);
        $labels = [$label];
        $datasets = array(
            "manufacturerConsumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[
                    " . $data["CONSUMPTION"] . "
                    ]",
                "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
            ),
            "manufacturerCost" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[
                        " . $data["COST"] . "
                        ]",
                "label" => "'" . $l->g(102707) . " (" . $this->config->GetCostUnit() . ")'",
            )
        );

        $this->diagram->createCanvas("collect_cost_diagram", "4", "550");
        $this->diagram->createHorizontalBarChart("collect_cost_diagram", $l->g(102701) . " " . $l->g(102711) . ' (' . $this->config->GetCostUnit() . ')', $labels, $datasets);

        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $label = "";
        $data = array();
        $data["CONSUMPTION"] = "";
        $data["COST"] = "";
        foreach ($this->manufacturers as $manufacturer) {
            if (isset($this->yesterdayData->{$manufacturer})) {
                $label .= "'" . $manufacturer . "'";
                if (isset($this->yesterdayData->{$manufacturer}->totalConsumption)) {
                    $data["CONSUMPTION"] .= "'" . str_replace(" " . "kW/h", "", $this->calculation->ConsumptionFormat($this->yesterdayData->{$manufacturer}->totalConsumption, "kW/h", $this->config->GetConsumptionRound())) . "'";
                    $data["COST"] .= "'" . str_replace(" " . $this->config->COST_UNIT, "", $this->calculation->CostFormat($this->yesterdayData->{$manufacturer}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound())) . "'";
                } else {
                    $data["CONSUMPTION"] .= 0;
                    $data["COST"] .= 0;
                }
                if (next($this->yesterdayData) == true) {
                    $label .= ", ";
                    $data["CONSUMPTION"] .= ", ";
                    $data["COST"] .= ", ";
                }
            }
        }
        reset($this->yesterdayData);
        $labels = [$label];
        $datasets = array(
            "manufacturerConsumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[
                    " . $data["CONSUMPTION"] . "
                    ]",
                "label" => "'" . $l->g(102706) . " (" . "kW/h" . ")'",
            ),
            "manufacturerCost" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[
                        " . $data["COST"] . "
                        ]",
                "label" => "'" . $l->g(102707) . " (" . $this->config->GetCostUnit() . ")'",
            )
        );

        $this->diagram->createCanvas("compare_cost_diagram", "4", "550");
        $this->diagram->createHorizontalBarChart("compare_cost_diagram", $l->g(102701) . " " . $l->g(102711) . ' (' . $this->config->GetCostUnit() . ')', $labels, $datasets);
    }
}

?>