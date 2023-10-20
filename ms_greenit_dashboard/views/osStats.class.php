<?php

require_once(__DIR__ . "/../../config/view.class.php");

class OSStatsView extends View
{
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

        $this->yesterdayData = new stdClass();
        $this->collectData = new stdClass();
        $this->compareData = new stdClass();

        $this->yesterdayData->CLIENTS = $this->data->GetGreenITData("
            SELECT 
            DATA 
            FROM greenit_stats 
            WHERE 
            DATE = '" . $this->config->GetYesterdayDate() . "'
        ", false);
        $this->yesterdayData->SERVERS = $this->data->GetGreenITData("
            SELECT 
            DATA 
            FROM greenit_stats 
            WHERE 
            DATE = '" . $this->config->GetYesterdayDate() . "'
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

        $table = "
            <div class='row'>
                <div class='col-md-6' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData->CLIENTS) && $this->yesterdayData->CLIENTS->RETURN != false ? $this->calculation->TimeFormat($this->yesterdayData->CLIENTS->totalUptime, $this->config->GetUptimeFormat()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102709) . "</p>
                </div>
                <div class='col-md-6'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData->SERVERS) && $this->yesterdayData->SERVERS->RETURN != false ? $this->calculation->TimeFormat($this->yesterdayData->SERVERS->totalUptime, $this->config->GetUptimeFormat()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102710) . "</p>
                </div>
            </div>
        ";
        echo $table;

        echo "<hr>";

        echo "
            <div class='row'>
                <div class='col-md-2'></div>
        ";
        $labels = [$l->g(102708)];
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $data = array();
        $data["CLIENTS"] = str_replace(" kW/h", "", (isset($this->yesterdayData->CLIENTS) && $this->yesterdayData->CLIENTS->RETURN != false ? $this->calculation->ConsumptionFormat(floatval($this->yesterdayData->CLIENTS->totalConsumption), "kW/h", $this->config->GetConsumptionRound()) : "0"));
        $data["SERVERS"] = str_replace(" kW/h", "", (isset($this->yesterdayData->SERVERS) && $this->yesterdayData->SERVERS->RETURN != false ? $this->calculation->ConsumptionFormat(floatval($this->yesterdayData->SERVERS->totalConsumption), "kW/h", $this->config->GetConsumptionRound()) : "0"));
        $datasets = array(
            "clientConsumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[
                    '" . $data["CLIENTS"] . "'
                    ]",
                "label" => "'" . str_replace("'", "\\'", $l->g(102802)) . "'",
            ),
            "serverConsumption" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[
                    '" . $data["SERVERS"] . "'
                    ]",
                "label" => "'" . str_replace("'", "\\'", $l->g(102803)) . "'",
            ),
        );
        $this->diagram->createCanvas("histogram_yesterday_total_consumption", "8", "150");
        $this->diagram->createHorizontalBarChart("histogram_yesterday_total_consumption", "", $labels, $datasets);
        echo "
                <div class='col-md-2'></div>
            </div>
        ";

        echo "<hr>";

        $labels = array();
        $labels = [
            $l->g(102804),
            $l->g(102805)
        ];
        $backgroundColor = $this->diagram->GenerateColorList(2, false);
        $data = array();
        $data["CLIENTS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->yesterdayData->CLIENTS) && $this->yesterdayData->CLIENTS->RETURN != false ? $this->calculation->CostFormat($this->yesterdayData->CLIENTS->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
        $data["SERVERS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->yesterdayData->SERVERS) && $this->yesterdayData->SERVERS->RETURN != false ? $this->calculation->CostFormat($this->yesterdayData->SERVERS->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
        $datasets = array(
            "label" => '"' . $l->g(102701) . ' (' . $this->config->GetCostUnit() . ')"',
            "data" => "["
                . $data["CLIENTS"] . ","
                . $data["SERVERS"] .
                "]",
            "backgroundColor" => "[" . $backgroundColor . "]"
        );

        $this->diagram->createCanvas("yesterday_cost_diagram", "4", "300");
        $this->diagram->CreateDoughnutChart("yesterday_cost_diagram", $l->g(102701) . ' (' . $this->config->GetCostUnit() . ')', $labels, $datasets);

        $labels = array();
        $labels = [
            $l->g(102804),
            $l->g(102805)
        ];
        $backgroundColor = $this->diagram->GenerateColorList(2, false);
        $data = array();
        $data["CLIENTS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->yesterdayData->CLIENTS) && $this->yesterdayData->CLIENTS->RETURN != false ? $this->calculation->CostFormat($this->yesterdayData->CLIENTS->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
        $data["SERVERS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->yesterdayData->SERVERS) && $this->yesterdayData->SERVERS->RETURN != false ? $this->calculation->CostFormat($this->yesterdayData->SERVERS->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
        $datasets = array(
            "label" => "'" . $l->g(102702) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . " (" . $this->config->GetCostUnit() . ")'",
            "data" => "["
                . $data["CLIENTS"] . ","
                . $data["SERVERS"] .
                "]",
            "backgroundColor" => "[" . $backgroundColor . "]"
        );

        $this->diagram->createCanvas("collect_cost_diagram", "4", "300");
        $this->diagram->CreateDoughnutChart("collect_cost_diagram", $l->g(102702) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . " (" . $this->config->GetCostUnit() . ")", $labels, $datasets);

        $labels = array();
        $labels = [
            $l->g(102804),
            $l->g(102805)
        ];
        $backgroundColor = $this->diagram->GenerateColorList(2, false);
        $data = array();
        $data["CLIENTS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->yesterdayData->CLIENTS) && $this->yesterdayData->CLIENTS->RETURN != false ? $this->calculation->CostFormat($this->yesterdayData->CLIENTS->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
        $data["SERVERS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->yesterdayData->SERVERS) && $this->yesterdayData->SERVERS->RETURN != false ? $this->calculation->CostFormat($this->yesterdayData->SERVERS->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
        $datasets = array(
            "label" => "'" . $l->g(102702) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . " (" . $this->config->GetCostUnit() . ")'",
            "data" => "["
                . $data["CLIENTS"] . ","
                . $data["SERVERS"] .
                "]",
            "backgroundColor" => "[" . $backgroundColor . "]"
        );

        $this->diagram->createCanvas("compare_cost_diagram", "4", "300");
        $this->diagram->CreateDoughnutChart("compare_cost_diagram", $l->g(102702) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . " (" . $this->config->GetCostUnit() . ")", $labels, $datasets);
    }
}

?>