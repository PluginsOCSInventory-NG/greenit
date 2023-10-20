<?php

require_once(__DIR__ . "/../../config/view.class.php");

class FilteredStatsView extends View
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

    public function ShowFilteredSearch(): void
    {
        global $l;
        global $protectedPost;

        echo "<h4>" . $l->g(102600) . "</h4>";

        $form_name = "filteredSearch";
        $table_name = $form_name;
        $tab_options_filtered_search = $protectedPost;
        $tab_options_filtered_search['form_name'] = $form_name;
        $tab_options_filtered_search['table_name'] = $table_name;

        $list_fields_filtered_search = array(
            $l->g(23) => 'NAME',
            $l->g(190) . ' ' . strtoupper($l->g(1425)) => 'TAG',
            $l->g(25) => 'OS_NAME',
        );

        $list_col_cant_del_filtered_search = $list_fields_filtered_search;
        $default_fields_filtered_search = $list_fields_filtered_search;

        $tab_options_filtered_search['LIEN_LBL'][$l->g(23)] = 'index.php?function=ms_greenit_dashboard&cat=filteredstats&' . strtolower(str_replace(" ", "_", $l->g(23))) . '=';
        $tab_options_filtered_search['LIEN_CHAMP'][$l->g(23)] = 'NAME';

        echo "<hr>";

        echo close_form();
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
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->RETURN != false ? $this->calculation->ConsumptionFormat($this->yesterdayData->totalConsumption, "kW/h", $this->config->GetConsumptionRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102601) . "</p>
                </div>
                <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->RETURN != false ? $this->calculation->TimeFormat($this->yesterdayData->totalUptime, $this->config->GetUptimeFormat()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102603) . "</p>
                </div>
                <div class='col-md-4'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->RETURN != false ? $this->calculation->CostFormat($this->yesterdayData->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102605) . "</p>
                </div>
                
            </div>
            <br>
            <div class='row'>
                <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->RETURN != false ? $this->calculation->ConsumptionFormat($this->yesterdayData->consumptionAverage, "kW/h", $this->config->GetConsumptionRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102602) . "</p>
                </div>
                <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->RETURN != false ? $this->calculation->TimeFormat($this->yesterdayData->uptimeAverage, $this->config->GetUptimeFormat()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102604) . "</p>
                </div>
                <div class='col-md-4'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->RETURN != false ? $this->calculation->CostFormat($this->yesterdayData->totalConsumption / $this->yesterdayData->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
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
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->collectData) && $this->collectData->RETURN != false ? $this->calculation->CostFormat($this->collectData->{"2023-10-18"}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102702) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
                <div class='col-md-6'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->compareData) && $this->compareData->RETURN != false ? $this->calculation->CostFormat($this->compareData->{"2023-10-18"}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102702) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>                
            </div>
            <br>
            <div class='row'>
                <div class='col-md-6' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->collectData) && $this->collectData->RETURN != false ? $this->calculation->CostFormat($this->collectData->{"2023-10-18"}->totalConsumption / $this->collectData->{"2023-10-18"}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102704) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
                <div class='col-md-6'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->compareData) && $this->compareData->RETURN != false ? $this->calculation->CostFormat($this->compareData->{"2023-10-18"}->totalConsumption / $this->compareData->{"2023-10-18"}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102704) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
            </div>
        ";
        echo $table;

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
        $this->diagram->createCanvas("histogram_collect_period", "6", "225");
        $this->diagram->createVerticalBarChart("histogram_collect_period", "", $labels, $datasets);

        $labels = [$l->g(102702) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706)];
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $data = array();
        $data["CONSUMPTION"] = str_replace(" " . "kW/h", "", (isset($this->compareData) && $this->compareData->RETURN != false ? $this->calculation->ConsumptionFormat($this->compareData->{"2023-10-18"}->totalConsumption, "kW/h", $this->config->GetConsumptionRound()) : "0"));
        $data["COST"] = str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->compareData) && $this->compareData->RETURN != false ? $this->calculation->CostFormat($this->compareData->{"2023-10-18"}->totalConsumption, "W/h", $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"));
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