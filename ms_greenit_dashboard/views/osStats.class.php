<?php
//====================================================================================
// OCS INVENTORY REPORTS
// Copyleft Antoine ROBIN 2023
// Web: http://www.ocsinventory-ng.org
//
// This code is open source and may be copied and modified as long as the source
// code is always made freely available.
// Please refer to the General Public Licence http://www.gnu.org/ or Licence.txt
//====================================================================================

require_once(__DIR__ . "/../../config/view.class.php");

/**
 * OS stats view
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
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
     * Constructor of the view which define everything the view need to work
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

        $this->yesterdayData->clients = $this->data->GetGreenITData("
            SELECT 
            DATA 
            FROM greenit_stats 
            WHERE 
            TYPE = 'OSSTATS_CLIENTS' 
            AND DATE='" . $this->config->GetYesterdayDate() . "'
        ");
        $this->yesterdayData->servers = $this->data->GetGreenITData("
            SELECT 
            DATA 
            FROM greenit_stats 
            WHERE 
            TYPE = 'OSSTATS_SERVERS' 
            AND DATE='" . $this->config->GetYesterdayDate() . "'
        ");
        $this->collectData->clients = $this->data->GetGreenITData("
            SELECT 
            DATE, 
            DATA 
            FROM greenit_stats 
            WHERE 
            TYPE = 'OS_COLLECT_TOTAL_STATS_CLIENTS' 
            AND DATE = '0000-00-00'
        ");
        $this->collectData->servers = $this->data->GetGreenITData("
            SELECT 
            DATE, 
            DATA 
            FROM greenit_stats 
            WHERE 
            TYPE = 'OS_COLLECT_TOTAL_STATS_SERVERS' 
            AND DATE = '0000-00-00'
        ");
        $this->compareData->clients = $this->data->GetGreenITData("
            SELECT 
            DATE, 
            DATA 
            FROM greenit_stats 
            WHERE 
            TYPE = 'OS_COMPARE_TOTAL_STATS_CLIENTS' 
            AND DATE = '0000-00-00'
        ");
        $this->compareData->servers = $this->data->GetGreenITData("
            SELECT 
            DATE, 
            DATA 
            FROM greenit_stats 
            WHERE 
            TYPE = 'OS_COMPARE_TOTAL_STATS_SERVERS' 
            AND DATE = '0000-00-00'
        ");
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
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData->clients) && $this->yesterdayData->clients->return != false ? $this->calculation->TimeFormat($this->yesterdayData->clients->totalUptime, $this->config->GetUptimeFormat()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102709) . "</p>
                </div>
                <div class='col-md-6'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData->servers) && $this->yesterdayData->servers->return != false ? $this->calculation->TimeFormat($this->yesterdayData->servers->totalUptime, $this->config->GetUptimeFormat()) : "0") . "</p>
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
        $labels = [
            $l->g(102708)
        ];
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $data = array();
        $data["CLIENTS"] = str_replace(" kW/h", "", (isset($this->yesterdayData->clients) && $this->yesterdayData->clients->return != false ? $this->calculation->ConsumptionFormat(floatval($this->yesterdayData->clients->totalConsumption), $this->config->GetConsumptionRound()) : "0"));
        $data["SERVERS"] = str_replace(" kW/h", "", (isset($this->yesterdayData->servers) && $this->yesterdayData->servers->return != false ? $this->calculation->ConsumptionFormat(floatval($this->yesterdayData->servers->totalConsumption), $this->config->GetConsumptionRound()) : "0"));
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

        $labels = [
            $l->g(102804),
            $l->g(102805)
        ];
        $backgroundColor = $this->diagram->GenerateColorList(2, false);
        $data["CLIENTS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->yesterdayData->clients) && $this->yesterdayData->clients->return != false ? $this->calculation->CostFormat($this->yesterdayData->clients->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
        $data["SERVERS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->yesterdayData->servers) && $this->yesterdayData->servers->return != false ? $this->calculation->CostFormat($this->yesterdayData->servers->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
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

        $labels = [
            $l->g(102804),
            $l->g(102805)
        ];
        $data["CLIENTS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->collectData->clients) && $this->collectData->clients->return != false ? $this->calculation->CostFormat($this->collectData->clients->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
        $data["SERVERS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->collectData->servers) && $this->collectData->servers->return != false ? $this->calculation->CostFormat($this->collectData->servers->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
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

        $labels = [
            $l->g(102804),
            $l->g(102805)
        ];
        $data["CLIENTS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->compareData->clients) && $this->compareData->clients->return != false ? $this->calculation->CostFormat($this->compareData->clients->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
        $data["SERVERS"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->compareData->servers) && $this->compareData->servers->return != false ? $this->calculation->CostFormat($this->compareData->servers->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : 0));
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