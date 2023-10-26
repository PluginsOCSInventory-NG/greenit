<?php

require_once(__DIR__ . "/../../config/view.class.php");

/**
 * ComputerType stats view
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
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
     * Constructor of the view which define everything the view need to work
     */
    function __construct()
    {
        $this->logMessage = new LogMessage();
        $this->config = new Config();
        $this->calculation = new Calculation();
        $this->diagram = new Diagram();
        $this->data = new Data();

        $this->computerTypes = $this->data->GetComputerTypes();

        $this->yesterdayData = new stdClass();

        if ($this->computerTypes->return != false) {
            foreach ($this->computerTypes->ComputerTypes as $count => $computerType) {
                $this->yesterdayData->{$computerType} = $this->data->GetGreenITData("
                    SELECT 
                    DATA 
                    FROM greenit_stats 
                    WHERE 
                    TYPE = 'COMPUTERTYPESSTATS_" . strtoupper(str_replace(" ", "_", $computerType)) . "' 
                    AND DATE='" . $this->config->GetYesterdayDate() . "'
                ");
            }

            $this->collectData = new stdClass();

            foreach ($this->computerTypes->ComputerTypes as $count => $computerType) {
                $this->collectData->{$computerType} = $this->data->GetGreenITData("
                    SELECT 
                    DATA 
                    FROM greenit_stats 
                    WHERE 
                    TYPE = 'COMPUTERTYPES_COLLECT_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $computerType)) . "' 
                    AND DATE = '0000-00-00'
                ");
            }

            $this->compareData = new stdClass();

            foreach ($this->computerTypes->ComputerTypes as $count => $computerType) {
                $this->compareData->{$computerType} = $this->data->GetGreenITData("
                    SELECT 
                    DATA 
                    FROM greenit_stats 
                    WHERE 
                    TYPE = 'COMPUTERTYPES_COMPARE_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $computerType)) . "' 
                    AND DATE = '0000-00-00'
                ");
            }
        }
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

        if ($this->computerTypes->return != false) {
            $table = "
                <div class='row'>
            ";
            foreach ($this->computerTypes->ComputerTypes as $count => $computerType) {
                if (next($this->computerTypes->ComputerTypes)) {
                    $table .= "
                        <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                    ";
                } else {
                    $table .= "
                        <div class='col-md-4'>
                    ";
                }
                $table .= "
                        <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->collectData->{$computerType}) && $this->collectData->{$computerType}->return != false ? $this->calculation->CostFormat($this->collectData->{$computerType}->totalCost / $this->collectData->{$computerType}->totalMachines, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                        <p style='color:#333; font-size: 15px;'>" . $l->g(102703) . " " . $computerType . " " . $l->g(102705) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . "</p>
                    </div>
                ";
            }
            reset($this->computerTypes);
            $table .= "
                </div>
                <br>
                <div class='row'>
            ";
            foreach ($this->computerTypes->ComputerTypes as $count => $computerType) {
                if (next($this->computerTypes->ComputerTypes)) {
                    $table .= "
                        <div class='col-md-4' style='border-right: 1px solid #ddd;'>
                    ";
                } else {
                    $table .= "
                   <div class='col-md-4'>
                ";
                }
                $table .= "
                        <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->compareData->{$computerType}) && $this->compareData->{$computerType}->return != false ? $this->calculation->CostFormat($this->compareData->{$computerType}->totalCost / $this->collectData->{$computerType}->totalMachines, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                        <p style='color:#333; font-size: 15px;'>" . $l->g(102703) . " " . $computerType . " " . $l->g(102705) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . "</p>
                    </div>
                ";
            }

            $table .= "
                </div>
            ";
            echo $table;

            echo "<hr>";
        }

        $labels = array();
        $data = array(
            "CONSUMPTION" => "",
            "COST" => ""
        );
        if ($this->computerTypes->return != false) {
            foreach ($this->computerTypes->ComputerTypes as $count => $computerType) {
                array_push($labels, $computerType);
                $data["CONSUMPTION"] .= str_replace(" " . "kW/h", "", (isset($this->yesterdayData->{$computerType}) && $this->yesterdayData->{$computerType}->return != false ? $this->calculation->ConsumptionFormat($this->yesterdayData->{$computerType}->totalConsumption, $this->config->GetConsumptionRound()) : "0"));
                $data["COST"] .= str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->yesterdayData->{$computerType}) && $this->yesterdayData->{$computerType}->return != false ? $this->calculation->CostFormat($this->yesterdayData->{$computerType}->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"));
                if (next($this->computerTypes->ComputerTypes)) {
                    $data["CONSUMPTION"] .= ", ";
                    $data["COST"] .= ", ";
                }

            }
        }
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
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
        $this->diagram->createCanvas("histogram_yesterday_period", "4", "550");
        $this->diagram->createVerticalBarChart("histogram_yesterday_period", "", $labels, $datasets);

        $labels = array();
        $data = array(
            "CONSUMPTION" => "",
            "COST" => ""
        );
        if ($this->computerTypes->return != false) {
            foreach ($this->computerTypes->ComputerTypes as $count => $computerType) {
                array_push($labels, $computerType);
                $data["CONSUMPTION"] .= str_replace(" " . "kW/h", "", (isset($this->collectData->{$computerType}) && $this->collectData->{$computerType}->return != false ? $this->calculation->ConsumptionFormat($this->collectData->{$computerType}->totalConsumption, $this->config->GetConsumptionRound()) : "0"));
                $data["COST"] .= str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->collectData->{$computerType}) && $this->collectData->{$computerType}->return != false ? $this->calculation->CostFormat($this->collectData->{$computerType}->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"));
                if (next($this->computerTypes->ComputerTypes)) {
                    $data["CONSUMPTION"] .= ", ";
                    $data["COST"] .= ", ";
                }
            }
        }
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
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
        $this->diagram->createCanvas("histogram_collect_period", "4", "550");
        $this->diagram->createVerticalBarChart("histogram_collect_period", "", $labels, $datasets);

        $labels = array();
        $data = array(
            "CONSUMPTION" => "",
            "COST" => ""
        );
        if ($this->computerTypes->return != false) {
            foreach ($this->computerTypes->ComputerTypes as $count => $computerType) {
                array_push($labels, $computerType);
                $data["CONSUMPTION"] .= str_replace(" " . "kW/h", "", (isset($this->compareData->{$computerType}) && $this->compareData->{$computerType}->return != false ? $this->calculation->ConsumptionFormat($this->compareData->{$computerType}->totalConsumption, $this->config->GetConsumptionRound()) : "0"));
                $data["COST"] .= str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->compareData->{$computerType}) && $this->compareData->{$computerType}->return != false ? $this->calculation->CostFormat($this->compareData->{$computerType}->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"));
                if (next($this->computerTypes->ComputerTypes)) {
                    $data["CONSUMPTION"] .= ", ";
                    $data["COST"] .= ", ";
                }
            }
        }
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
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
        $this->diagram->createCanvas("histogram_compare_period", "4", "550");
        $this->diagram->createVerticalBarChart("histogram_compare_period", "", $labels, $datasets);
    }
}

?>