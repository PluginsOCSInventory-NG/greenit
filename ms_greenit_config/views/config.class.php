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
 * Config view
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
class ConfigView
{
    /**
     * Using Config Class to get user configuration
     */
    private Config $config;

    /**
     * Using LogMessage Class to send message when error or to inform user
     */
    private LogMessage $logMessage;

    /**
     * List of uptime formats
     */
    private array $uptimeFormats;

    /**
     * List of consumption types
     */
    private array $consumptionTypes;

    /**
     * Constructor of the view which define everything the view need to work
     */
    function __construct()
    {
        global $l;
        global $protectedPost;

        $this->logMessage = new LogMessage();
        $this->config = new Config();

        $this->uptimeFormats = array(
            "0" => "-----",
            "s" => "s",
            "m-s" => "m-s",
            "h-m-s" => "h-m-s",
        );

        $this->consumptionTypes = array(
            "PX_ELE_I_TTES_TRANCHES" => $l->g(102013),
            "PX_ELE_I_IA" => $l->g(102014),
            "PX_ELE_I_IB" => $l->g(102015),
            "PX_ELE_I_IC" => $l->g(102016),
            "PX_ELE_I_ID" => $l->g(102017),
            "PX_ELE_I_IE" => $l->g(102018),
            "PX_ELE_I_IF" => $l->g(102019),
            "PX_ELE_I_IG" => $l->g(102020),
        );

        if (isset($protectedPost['SUBMIT_FORM'])) {
            $insertQuery = "
                UPDATE greenit_config 
                SET 
                COLLECT_INFO_PERIOD='" . $protectedPost[strtoupper(str_replace(" ", "_", $l->g(102002)))] . "',
                COMPARE_INFO_PERIOD='" . $protectedPost[strtoupper(str_replace(" ", "_", $l->g(102003)))] . "',
                CONSUMPTION_ROUND='" . $protectedPost[strtoupper(str_replace(" ", "_", $l->g(102004)))] . "',
                COST_ROUND='" . $protectedPost[strtoupper(str_replace(" ", "_", $l->g(102005)))] . "',
                COST_UNIT='" . $protectedPost[strtoupper(str_replace(" ", "_", $l->g(102006)))] . "',
                UPTIME_FORMAT='" . $protectedPost[strtoupper(str_replace(" ", "_", $l->g(102007)))] . "',
                API_KEY='" . $protectedPost[strtoupper(str_replace(" ", "_", $l->g(102009)))] . "',
                CONSUMPTION_TYPE='" . $protectedPost[strtoupper(str_replace(" ", "_", $l->g(102012)))] . "'
                WHERE ID='1';
            ";
            if (mysql2_query_secure($insertQuery, $_SESSION['OCS']["writeServer"])) {
                $this->config = new Config();
                msg_success($l->g(101000));
            } else
                msg_error($l->g(101001));
        }
        if (isset($protectedPost["TEST_API"])) {

            $url = 'http://172.18.25.171:8080/';
            $query = curl_init($url);
            curl_setopt(
                $query,
                CURLOPT_RETURNTRANSFER,
                true
            );
            $apiKey = $this->config->GetAPIKey();
            if (is_defined($apiKey))
                curl_setopt(
                    $query,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Authorization: Token ' . $this->config->GetAPIKey()
                    )
                );
            $response = curl_exec($query);
            $response = curl_getinfo($query);
            curl_close($query);

            if (curl_getinfo($query, CURLINFO_HTTP_CODE) == 200)
                msg_success($l->g(101002));
            else
                msg_error($l->g(101003));
        }
    }

    /**
     * Generate the Title HTML code of the view
     * 
     * @return void Return nothing
     */
    public function ShowTitle(): void
    {
        global $l;

        printEnTete($l->g(100000));
        echo "<hr>";

        $form_name = "configuration";
        echo open_form($form_name, '', '', 'form-horizontal');
    }

    /**
     * Generate the Interface settings HTML code of the view
     * 
     * @return void Return nothing
     */
    public function ShowInterfaceSettings(): void
    {
        global $l;
        global $protectedPost;

        echo "
            <h4>" . $l->g(102001) . "</h4>

            <div class='form-group'>
                <label class='col-sm-7 text-left' for='" . strtoupper(str_replace(" ", "_", $l->g(102002))) . "'>" . $l->g(102002) . "</label>
                <div class='col-sm-5'>
                    <input name='" . strtoupper(str_replace(" ", "_", $l->g(102002))) . "' id='" . strtoupper(str_replace(" ", "_", $l->g(102002))) . "' class='form-control' type='number' min='1' max='365' value='" . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102002)))] ?? $this->config->GetCollectInfoPeriod()) . "' required />
                </div>
            </div>
            <div class='form-group'>
                <label class='col-sm-7 text-left' for='" . strtoupper(str_replace(" ", "_", $l->g(102003))) . "'>" . $l->g(102003) . "</label>
                <div class='col-sm-5'>
                    <input name='" . strtoupper(str_replace(" ", "_", $l->g(102003))) . "' id='" . strtoupper(str_replace(" ", "_", $l->g(102003))) . "' class='form-control' type='number' min='1' value='" . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102003)))] ?? $this->config->GetCompareInfoPeriod()) . "' required />
                </div>
            </div>
            <div class='form-group'>
                <label class='col-sm-7 text-left' for='" . strtoupper(str_replace(" ", "_", $l->g(102004))) . "'>" . $l->g(102004) . "</label>
                <div class='col-sm-5'>
                    <input name='" . strtoupper(str_replace(" ", "_", $l->g(102004))) . "' id='" . strtoupper(str_replace(" ", "_", $l->g(102004))) . "' class='form-control' type='number' min='1' max='10' value='" . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102004)))] ?? $this->config->GetConsumptionRound()) . "' required />
                </div>
            </div>
            <div class='form-group'>
                <label class='col-sm-7 text-left' for='" . strtoupper(str_replace(" ", "_", $l->g(102005))) . "'>" . $l->g(102005) . "</label>
                <div class='col-sm-5'>
                    <input name='" . strtoupper(str_replace(" ", "_", $l->g(102005))) . "' id='" . strtoupper(str_replace(" ", "_", $l->g(102005))) . "' class='form-control' type='number' min='1' max='10' value='" . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102005)))] ?? $this->config->GetCostRound()) . "' required />
                </div>
            </div>
            <div class='form-group'>
                <label class='col-sm-7 text-left' for='" . strtoupper(str_replace(" ", "_", $l->g(102006))) . "'>" . $l->g(102006) . "</label>
                <div class='col-sm-5'>
                    <input name='" . strtoupper(str_replace(" ", "_", $l->g(102006))) . "' id='" . strtoupper(str_replace(" ", "_", $l->g(102006))) . "' class='form-control' type='text' value='" . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102006)))] ?? $this->config->GetCostUnit()) . "' required />
                </div>
            </div>
            <div class='form-group'>
                <label class='col-sm-7 text-left' for='" . strtoupper(str_replace(" ", "_", $l->g(102007))) . "'>" . $l->g(102007) . "</label>
                <div class='col-sm-5'>
                    <select name='" . strtoupper(str_replace(" ", "_", $l->g(102007))) . "' id='" . strtoupper(str_replace(" ", "_", $l->g(102007))) . "' class='form-control'>
        ";
        if (is_array($this->uptimeFormats)) {
            foreach ($this->uptimeFormats as $option => $value) {
                echo "<option value='" . $option . "' " . ($option == ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102007)))] ?? $this->config->GetUptimeFormat()) ? "selected" : "") . ">" . ($this->uptimeFormats[$option] ? $this->uptimeFormats[$option] : $option) . "</option>";
            }
        }
        echo "
                    </select>
                </div>
            </div>
            <hr>
        ";
    }

    /**
     * Generate the API Configuration HTML code of the view
     * 
     * @return void Return nothing
     */
    public function ShowAPIConfiguration(): void
    {
        global $l;
        global $protectedPost;

        echo "
            <h4>" . $l->g(102008) . "</h4>

            <div class='form-group'>
                <label class='col-sm-7 text-left' for='" . strtoupper(str_replace(" ", "_", $l->g(102009))) . "'>" . $l->g(102009) . "</label>
        ";
        $apiKey = $this->config->GetAPIKey();
        if (is_defined($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102009)))]) || is_defined($apiKey)) {
            echo "
                <div class='col-sm-3'>
                    <input name='" . strtoupper(str_replace(" ", "_", $l->g(102009))) . "' id='" . strtoupper(str_replace(" ", "_", $l->g(102009))) . "' class='form-control' type='text' placeholder='" . $l->g(102011) . "' value='" . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102009)))] ?? $this->config->GetAPIKey()) . "' />
                </div>
                <div class='col-sm-2'>
                    <button class='btn btn-success' name='TEST_API'>" . $l->g(102010) . "</button>
                </div>
            ";
        } else {
            echo "
                <div class='col-sm-5'>
                    <input name='" . strtoupper(str_replace(" ", "_", $l->g(102009))) . "' id='" . strtoupper(str_replace(" ", "_", $l->g(102009))) . "' class='form-control' type='text' placeholder='" . $l->g(102011) . "' value='" . ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102009)))] ?? $this->config->GetAPIKey()) . "' />
                </div>
            ";
        }
        echo "
            </div>
            
            <div class='form-group'>
                <label class='col-sm-7 text-left' for='" . strtoupper(str_replace(" ", "_", $l->g(102012))) . "'>" . $l->g(102012) . "</label>
                <div class='col-sm-5'>
                    <select name='" . strtoupper(str_replace(" ", "_", $l->g(102012))) . "' id='" . strtoupper(str_replace(" ", "_", $l->g(102012))) . "' class='form-control'>
        ";
        if (is_array($this->consumptionTypes)) {
            foreach ($this->consumptionTypes as $option => $value) {
                echo "<option value='" . $option . "' " . ($option == ($protectedPost[strtoupper(str_replace(" ", "_", $l->g(102012)))] ?? $this->config->GetConsumptionType()) ? "selected" : "") . ">" . $this->consumptionTypes[$option] . "</option>";
            }
        }
        echo "
                    </select>
                </div>
            </div>
        ";
    }

    public function ShowSubmit(): void
    {
        global $l;

        echo "
            <button class='btn btn-success' name='SUBMIT_FORM'>" . $l->g(103) . "</button>
        ";
        echo close_form();
    }
}

?>