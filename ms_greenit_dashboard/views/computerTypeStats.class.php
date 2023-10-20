<?php

require_once(__DIR__ . "/../../config/view.class.php");

class ComputerTypeStatsView extends View
{
    /**
     * List of computer types
     */
    private object $computerTypes;

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

        $this->computerTypes = new stdClass();
        $this->computerTypes->{"Desktop"} = 10;
        $this->computerTypes->{"Laptop"} = 10;
        $this->computerTypes->{"Other"} = 10;

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
        ';
        foreach ($this->computerTypes as $computerType => $count) {
            if (next($this->computerTypes)) {
                $table .= "
                   <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                ";
            } else {
                $table .= "
                   <div class='col-md-4'>
                ";
            }
            $table .= "
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->RETURN != false ? $this->calculation->CostFormat($this->yesterdayData->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102703) . " " . $computerType . " " . $l->g(102705) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
            ";
        }
        reset($this->computerTypes);
        $table .= '
            </div>
            <br>
            <div class="row">
        ';
        foreach ($this->computerTypes as $computerType => $count) {
            if (next($this->computerTypes)) {
                $table .= "
                   <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                ";
            } else {
                $table .= "
                   <div class='col-md-4'>
                ";
            }
            $table .= "
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->RETURN != false ? $this->calculation->CostFormat($this->yesterdayData->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102703) . " " . $computerType . " " . $l->g(102705) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
            ";
        }
        $table .= '
            </div>
        ';
        echo $table;

        echo "<hr>";

        $labels = [$l->g(102701)];
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $data = array();
        $data["CONSUMPTION"] = str_replace(" " . "kW/h", "", (isset($this->collectData) && $this->collectData->RETURN != false ? $this->calculation->ConsumptionFormat($this->collectData->{"2023-10-18"}->totalConsumption, "kW/h", $this->config->GetConsumptionRound()) : "0"));
        $data["COST"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->collectData) && $this->collectData->RETURN != false ? $this->calculation->CostFormat($this->collectData->{"2023-10-18"}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"));
        $datasets = array(
            "consumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[" . $data["CONSUMPTION"] . "]",
                "label" => "'" . $l->g(102800) . " (" . "kW/h" . ")'",
            ),
            "cost" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[" . $data["COST"] . "]",
                "label" => "'" . $l->g(102801) . " (" . $this->config->GetCostUnit() . ")'",
            )
        );
        $this->diagram->createCanvas("histogram_yesterday_period", "4", "500");
        $this->diagram->createVerticalBarChart("histogram_yesterday_period", "", $labels, $datasets);

        $labels = [$l->g(102702) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706)];
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $data = array();
        $data["CONSUMPTION"] = str_replace(" " . "kW/h", "", (isset($this->collectData) && $this->collectData->RETURN != false ? $this->calculation->ConsumptionFormat($this->collectData->{"2023-10-18"}->totalConsumption, "kW/h", $this->config->GetConsumptionRound()) : "0"));
        $data["COST"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->collectData) && $this->collectData->RETURN != false ? $this->calculation->CostFormat($this->collectData->{"2023-10-18"}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"));
        $datasets = array(
            "consumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[" . $data["CONSUMPTION"] . "]",
                "label" => "'" . $l->g(102800) . " (" . "kW/h" . ")'",
            ),
            "cost" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[" . $data["COST"] . "]",
                "label" => "'" . $l->g(102801) . " (" . $this->config->GetCostUnit() . ")'",
            )
        );
        $this->diagram->createCanvas("histogram_collect_period", "4", "500");
        $this->diagram->createVerticalBarChart("histogram_collect_period", "", $labels, $datasets);

        $labels = [$l->g(102702) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706)];
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $data = array();
        $data["CONSUMPTION"] = str_replace(" " . "kW/h", "", (isset($this->collectData) && $this->collectData->RETURN != false ? $this->calculation->ConsumptionFormat($this->collectData->{"2023-10-18"}->totalConsumption, "kW/h", $this->config->GetConsumptionRound()) : "0"));
        $data["COST"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->collectData) && $this->collectData->RETURN != false ? $this->calculation->CostFormat($this->collectData->{"2023-10-18"}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"));
        $datasets = array(
            "consumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[" . $data["CONSUMPTION"] . "]",
                "label" => "'" . $l->g(102800) . " (" . "kW/h" . ")'",
            ),
            "cost" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[" . $data["COST"] . "]",
                "label" => "'" . $l->g(102801) . " (" . $this->config->GetCostUnit() . ")'",
            )
        );
        $this->diagram->createCanvas("histogram_compare_period", "4", "500");
        $this->diagram->createVerticalBarChart("histogram_compare_period", "", $labels, $datasets);
    }
}

?>