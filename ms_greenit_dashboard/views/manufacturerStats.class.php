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
 * Manufacturer stats view
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
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
     * Constructor of the view which define everything the view need to work
     */
    function __construct()
    {
        $this->logMessage = new LogMessage();
        $this->config = new Config();
        $this->calculation = new Calculation();
        $this->diagram = new Diagram();
        $this->data = new Data();

        $this->manufacturers = new stdClass();

        $this->yesterdayData = new stdClass();
        $this->collectData = new stdClass();
        $this->compareData = new stdClass();

        $this->manufacturers->Yesterday = $this->data->GetManufacturers("
            SELECT 
            bios.SMANUFACTURER AS MANUFACTURER, 
            SUM(greenit.CONSUMPTION) AS totalConsumption
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
            INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
            WHERE
            greenit.DATE='" . $this->config->GetYesterdayDate() . "'
            GROUP BY MANUFACTURER
            ORDER BY totalConsumption DESC 
            LIMIT 5
        ");
        $this->manufacturers->Collect = $this->data->GetManufacturers("
            SELECT 
            bios.SMANUFACTURER AS MANUFACTURER, 
            SUM(greenit.CONSUMPTION) AS totalConsumption
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
            INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
            WHERE
            greenit.DATE BETWEEN '" . $this->config->GetCollectDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            GROUP BY MANUFACTURER
            ORDER BY totalConsumption DESC 
            LIMIT 5
        ");
        $this->manufacturers->Compare = $this->data->GetManufacturers("
            SELECT 
            bios.SMANUFACTURER AS MANUFACTURER, 
            SUM(greenit.CONSUMPTION) AS totalConsumption
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID 
            INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
            WHERE
            greenit.DATE BETWEEN '" . $this->config->GetCompareDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            GROUP BY MANUFACTURER
            ORDER BY totalConsumption DESC 
            LIMIT 5
        ");

        if ($this->manufacturers->Yesterday->return != false) {
            foreach ($this->manufacturers->Yesterday->Manufacturers as $count => $manufacturer) {
                $this->yesterdayData->{$manufacturer} = $this->data->GetGreenITData("
                    SELECT 
                    DATA 
                    FROM greenit_stats 
                    WHERE 
                    TYPE = 'MANUFACTURERSSTATS_" . strtoupper(str_replace(" ", "_", $manufacturer)) . "' 
                    AND DATE='" . $this->config->GetYesterdayDate() . "'
                ");
                $this->collectData->{$manufacturer} = $this->data->GetGreenITData("
                    SELECT 
                    DATA 
                    FROM greenit_stats 
                    WHERE 
                    TYPE = 'MANUFACTURERS_COLLECT_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $manufacturer)) . "' 
                    AND DATE='0000-00-00'
                ");
                $this->compareData->{$manufacturer} = $this->data->GetGreenITData("
                    SELECT 
                    DATA 
                    FROM greenit_stats 
                    WHERE 
                    TYPE = 'MANUFACTURERS_COMPARE_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $manufacturer)) . "' 
                    AND DATE='0000-00-00'
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

        if ($this->manufacturers->Collect->return != false) {
            $table = "
                <div class='row'>
                    <div class='col-md-1'></div>
            ";
            foreach ($this->manufacturers->Collect->Manufacturers as $count => $manufacturer) {
                if (next($this->manufacturers->Collect->Manufacturers)) {
                    $table .= "
                        <div class='col-md-2' style='border-right: 1px solid #ddd;'>
                    ";
                } else {
                    $table .= "
                        <div class='col-md-2'>
                    ";
                }
                $table .= "
                        <p style='font-size: 32px; font-weight:bold;'>" . (isset($this->collectData->{$manufacturer}) && $this->collectData->{$manufacturer}->return != false ? $this->calculation->CostFormat($this->collectData->{$manufacturer}->costAverage, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                        <p style='color:#333; font-size: 15px;'>" . $l->g(102703) . " " . $manufacturer . " " . $l->g(102705) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . "</p>
                    </div>
                ";
            }
            $table .= "
                    <div class='col-md-1'></div>
                </div>
                <br>
            ";
        }
        if ($this->manufacturers->Compare->return != false) {
            $table .= "
                <div class='row'>
                    <div class='col-md-1'></div>
            ";
            foreach ($this->manufacturers->Compare->Manufacturers as $count => $manufacturer) {
                if (next($this->manufacturers->Compare->Manufacturers)) {
                    $table .= "
                        <div class='col-md-2' style='border-right: 1px solid #ddd;'>
                    ";
                } else {
                    $table .= "
                        <div class='col-md-2'>
                    ";
                }
                $table .= "
                        <p style='font-size: 32px; font-weight:bold;'>" . (isset($this->compareData->{$manufacturer}) && $this->compareData->{$manufacturer}->return != false ? $this->calculation->CostFormat($this->compareData->{$manufacturer}->costAverage, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                        <p style='color:#333; font-size: 15px;'>" . $l->g(102703) . " " . $manufacturer . " " . $l->g(102705) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . "</p>
                    </div>
                ";
            }
            $table .= "
                    <div class='col-md-1'></div>
                </div>
            ";

            echo $table;

            echo "<hr>";
        }

        $labels = array();
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $data = array(
            "CONSUMPTION" => "",
            "COST" => ""
        );
        if ($this->manufacturers->Yesterday->return != false) {
            foreach ($this->manufacturers->Yesterday->Manufacturers as $count => $manufacturer) {
                array_push($labels, $manufacturer);
                $data["CONSUMPTION"] .= str_replace(" " . "kW/h", "", (isset($this->yesterdayData->{$manufacturer}) && $this->yesterdayData->{$manufacturer}->return != false ? $this->calculation->ConsumptionFormat($this->yesterdayData->{$manufacturer}->totalConsumption, $this->config->GetConsumptionRound()) : "0"));
                $data["COST"] .= str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->yesterdayData->{$manufacturer}) && $this->yesterdayData->{$manufacturer}->return != false ? $this->calculation->CostFormat($this->yesterdayData->{$manufacturer}->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"));
                if (next($this->manufacturers->Yesterday->Manufacturers)) {
                    $data["CONSUMPTION"] .= ", ";
                    $data["COST"] .= ", ";
                }
            }
        }
        $datasets = array(
            "manufacturerConsumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[
                    " . $data["CONSUMPTION"] . "
                    ]",
                "label" => "'" . $l->g(102800) . " (" . "kW/h" . ")'",
            ),
            "manufacturerCost" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[
                        " . $data["COST"] . "
                        ]",
                "label" => "'" . $l->g(102801) . " (" . $this->config->GetCostUnit() . ")'",
            )
        );
        $this->diagram->createCanvas("yesterday_cost_diagram", "4", "500");
        $this->diagram->createHorizontalBarChart("yesterday_cost_diagram", $l->g(102701) . " " . $l->g(102711) . ' (' . $this->config->GetCostUnit() . ')', $labels, $datasets);

        $labels = array();
        $data = array(
            "CONSUMPTION" => "",
            "COST" => ""
        );
        if ($this->manufacturers->Collect->return != false) {
            foreach ($this->manufacturers->Collect->Manufacturers as $count => $manufacturer) {
                array_push($labels, $manufacturer);
                $data["CONSUMPTION"] .= str_replace(" " . "kW/h", "", (isset($this->collectData->{$manufacturer}) && $this->collectData->{$manufacturer}->return != false ? $this->calculation->ConsumptionFormat($this->collectData->{$manufacturer}->totalConsumption, $this->config->GetConsumptionRound()) : "0"));
                $data["COST"] .= str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->collectData->{$manufacturer}) && $this->collectData->{$manufacturer}->return != false ? $this->calculation->CostFormat($this->collectData->{$manufacturer}->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"));
                if (next($this->manufacturers->Collect->Manufacturers)) {
                    $data["CONSUMPTION"] .= ", ";
                    $data["COST"] .= ", ";
                }
            }
        }
        $datasets = array(
            "manufacturerConsumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[
                    " . $data["CONSUMPTION"] . "
                    ]",
                "label" => "'" . $l->g(102800) . " (" . "kW/h" . ")'",
            ),
            "manufacturerCost" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[
                        " . $data["COST"] . "
                        ]",
                "label" => "'" . $l->g(102801) . " (" . $this->config->GetCostUnit() . ")'",
            )
        );
        $this->diagram->createCanvas("collect_cost_diagram", "4", "500");
        $this->diagram->createHorizontalBarChart("collect_cost_diagram", $l->g(102701) . " " . $l->g(102711) . ' (' . $this->config->GetCostUnit() . ')', $labels, $datasets);

        $labels = array();
        $data = array(
            "CONSUMPTION" => "",
            "COST" => ""
        );
        if ($this->manufacturers->Compare->return != false) {
            foreach ($this->manufacturers->Compare->Manufacturers as $count => $manufacturer) {
                array_push($labels, $manufacturer);
                $data["CONSUMPTION"] .= str_replace(" " . "kW/h", "", (isset($this->compareData->{$manufacturer}) && $this->compareData->{$manufacturer}->return != false ? $this->calculation->ConsumptionFormat($this->compareData->{$manufacturer}->totalConsumption, $this->config->GetConsumptionRound()) : "0"));
                $data["COST"] .= str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->compareData->{$manufacturer}) && $this->compareData->{$manufacturer}->return != false ? $this->calculation->CostFormat($this->compareData->{$manufacturer}->totalCost, $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"));
                if (next($this->manufacturers->Compare->Manufacturers)) {
                    $data["CONSUMPTION"] .= ", ";
                    $data["COST"] .= ", ";
                }
            }
        }
        $datasets = array(
            "manufacturerConsumption" => array(
                "backgroundColor" => $backgroundColor[0],
                "data" => "[
                    " . $data["CONSUMPTION"] . "
                    ]",
                "label" => "'" . $l->g(102800) . " (" . "kW/h" . ")'",
            ),
            "manufacturerCost" => array(
                "backgroundColor" => $backgroundColor[1],
                "data" => "[
                        " . $data["COST"] . "
                        ]",
                "label" => "'" . $l->g(102801) . " (" . $this->config->GetCostUnit() . ")'",
            )
        );
        $this->diagram->createCanvas("compare_cost_diagram", "4", "500");
        $this->diagram->createHorizontalBarChart("compare_cost_diagram", $l->g(102701) . " " . $l->g(102711) . ' (' . $this->config->GetCostUnit() . ')', $labels, $datasets);
    }
}

?>