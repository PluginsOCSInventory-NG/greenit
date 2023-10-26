<?php

require_once(__DIR__ . "/../../config/view.class.php");

/**
 * Global stats view
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
class GlobalStatsView extends View
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

        $this->yesterdayData = $this->data->GetGreenITData("
            SELECT 
            DATA 
            FROM greenit_stats 
            WHERE 
            TYPE = 'GLOBALSTATS' 
            AND DATE='" . $this->config->GetYesterdayDate() . "'
        ");
        $this->collectData = $this->data->GetGreenITData("
            SELECT 
            DATA 
            FROM greenit_stats 
            WHERE 
            TYPE = 'GLOBAL_COLLECT_TOTAL_STATS' 
            AND DATE = '0000-00-00'
        ");
        $this->compareData = $this->data->GetGreenITData("
            SELECT 
            DATA 
            FROM greenit_stats 
            WHERE 
            TYPE = 'GLOBAL_COMPARE_TOTAL_STATS' 
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
        global $l;

        echo "<h4>" . $l->g(102600) . "</h4>";

        $table = "
            <div class='row'>
                <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->return != false ? $this->calculation->ConsumptionFormat($this->yesterdayData->totalConsumption, $this->config->GetConsumptionRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102601) . "</p>
                </div>
                <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->return != false ? $this->calculation->TimeFormat($this->yesterdayData->totalUptime, $this->config->GetUptimeFormat()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102603) . "</p>
                </div>
                <div class='col-md-4'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->return != false ? $this->calculation->CostFormat($this->yesterdayData->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102605) . "</p>
                </div>
                
            </div>
            <br>
            <div class='row'>
                <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->return != false ? $this->calculation->ConsumptionFormat($this->yesterdayData->consumptionAverage, $this->config->GetConsumptionRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102602) . "</p>
                </div>
                <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->return != false ? $this->calculation->TimeFormat($this->yesterdayData->uptimeAverage, $this->config->GetUptimeFormat()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102604) . "</p>
                </div>
                <div class='col-md-4'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->return != false ? $this->calculation->CostFormat($this->yesterdayData->totalCost / $this->yesterdayData->totalMachines, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102606) . "</p>
                </div>
            </div>
        ";
        echo $table;

        echo "<hr>";
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
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->collectData) && $this->collectData->return != false ? $this->calculation->CostFormat($this->collectData->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102702) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
                <div class='col-md-6'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->compareData) && $this->compareData->return != false ? $this->calculation->CostFormat($this->compareData->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102702) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>                
            </div>
            <br>
            <div class='row'>
                <div class='col-md-6' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->collectData) && $this->collectData->return != false ? $this->calculation->CostFormat($this->collectData->totalCost / $this->collectData->totalMachines, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102704) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
                <div class='col-md-6'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->compareData) && $this->compareData->return != false ? $this->calculation->CostFormat($this->compareData->totalCost / $this->compareData->totalMachines, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102704) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
            </div>
        ";
        echo $table;

        $labels = [$l->g(102702) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706)];
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $data = array(
            "CONSUMPTION" => str_replace(" " . "kW/h", "", (isset($this->collectData) && $this->collectData->return != false ? $this->calculation->ConsumptionFormat($this->collectData->totalConsumption, $this->config->GetConsumptionRound()) : "0")),
            "COST" => str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->collectData) && $this->collectData->return != false ? $this->calculation->CostFormat($this->collectData->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"))
        );
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
        $this->diagram->createCanvas("histogram_collect_period", "6", "225");
        $this->diagram->createVerticalBarChart("histogram_collect_period", "", $labels, $datasets);

        $labels = [$l->g(102702) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706)];
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $data = array(
            "CONSUMPTION" => str_replace(" " . "kW/h", "", (isset($this->compareData) && $this->compareData->return != false ? $this->calculation->ConsumptionFormat($this->compareData->totalConsumption, $this->config->GetConsumptionRound()) : "0")),
            "COST" => str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->compareData) && $this->compareData->return != false ? $this->calculation->CostFormat($this->compareData->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"))
        );
        $datasets = array(
            "consumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[" . $data["CONSUMPTION"] . "]",
                "label" => "'" . $l->g(102800) . " (" . "kW/h" . ")'",
                "type" => "'bar'"
            ),
            "cost" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[" . $data["COST"] . "]",
                "label" => "'" . $l->g(102801) . " (" . $this->config->GetCostUnit() . ")'",
                "type" => "'bar'"
            )
        );
        $this->diagram->createCanvas("histogram_compare_period", "6", "225");
        $this->diagram->createVerticalBarChart("histogram_compare_period", "", $labels, $datasets);
    }
}

?>