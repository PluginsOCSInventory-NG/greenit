<?php

require_once(__DIR__ . "/../../config/view.class.php");

/**
 * FilteredStats view
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
class FilteredStatsView extends View
{
    /**
     *  List of feilds for the filtered search module
     */
    private array $listFieldsFilteredSearch;

    /**
     *  List of default feilds for the filtered search module
     */
    private array $defaultFieldsFilteredSearch;

    /**
     *  List of column that cannot be deleted for the filtered search module
     */
    private array $listColCantDelFilteredSearch;

    /**
     *  List to generate sql query for the filtered search module
     */
    private array $sqlFilteredSearch;

    /**
     *  List of option for the filtered search module
     */
    private array $tabOptionsFilteredSearch;

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
        global $l;
        global $protectedPost;

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
        ", false);
        $this->collectData = $this->data->GetGreenITData("
            SELECT 
            DATE, 
            DATA 
            FROM greenit_stats 
            WHERE 
            (
                TYPE = 'GLOBALSTATS' 
                OR TYPE = 'GLOBAL_COLLECT_TOTAL_STATS' 
            )
            AND 
            (
                DATE BETWEEN '" . $this->config->GetCollectDate() . "' AND '" . $this->config->GetYesterdayDate() . "' 
                OR DATE = '0000-00-00'
            )
            ", true);
        $this->compareData = $this->data->GetGreenITData("
            SELECT 
            DATE, 
            DATA 
            FROM greenit_stats 
            WHERE 
            (
                TYPE = 'GLOBALSTATS' 
                OR TYPE = 'GLOBAL_COMPARE_TOTAL_STATS' 
            )
            AND 
            (
                DATE BETWEEN '" . $this->config->GetCompareDate() . "' AND '" . $this->config->GetYesterdayDate() . "' 
                OR DATE = '0000-00-00'
            )
        ", true);

        //////////////////////////////
        // If reset button clicked, reset session variables
        if (isset($protectedPost['RESET'])) {
            if (isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))]))
                unset($protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))]);
            if (isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))]))
                unset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))]);
            unset($protectedPost['OS']);
            unset($protectedPost['GROUP']);
            unset($protectedPost['TAG']);
            unset($protectedPost['ASSET']);
            unset($_SESSION['GREENIT']['FILTER']['OS']);
            unset($_SESSION['GREENIT']['FILTER']['GROUP']);
            unset($_SESSION['GREENIT']['FILTER']['TAG']);
            unset($_SESSION['GREENIT']['FILTER']['ASSET']);
        }
        //////////////////////////////

        //////////////////////////////
        // If formular submited, reset cache
        if (isset($protectedPost['SUBMIT_FORM'])) {
            if (isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))]))
                unset($protectedGet[strtolower(str_replace(" ", "_", $l->g(23)))]);
            if (isset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))]))
                unset($protectedGet[strtolower(str_replace(" ", "_", $l->g(729)))]);
            $tab_options['CACHE'] = 'RESET';
        }
        //////////////////////////////

        //////////////////////////////
        // Reset filter if default value was post
        if (isset($protectedPost["OS"]) && $protectedPost['OS'] == "0")
            unset($_SESSION['GREENIT']['FILTER']['OS']);
        if (isset($protectedPost["GROUP"]) && $protectedPost['GROUP'] == "0")
            unset($_SESSION['GREENIT']['FILTER']['GROUP']);
        if (isset($protectedPost["TAG"]) && $protectedPost['TAG'] == "0")
            unset($_SESSION['GREENIT']['FILTER']['TAG']);
        if (isset($protectedPost["ASSET"]) && $protectedPost['ASSET'] == "0")
            unset($_SESSION['GREENIT']['FILTER']['ASSET']);
        //////////////////////////////

        //////////////////////////////
        // Define filter session variables
        if (is_defined($protectedPost['OS']) && $protectedPost['OS'] != "0")
            $_SESSION['GREENIT']['FILTER']['OS'] = $protectedPost['OS'];
        if (is_defined($protectedPost['GROUP']) && $protectedPost['GROUP'] != "0")
            $_SESSION['GREENIT']['FILTER']['GROUP'] = $protectedPost['GROUP'];
        if (is_defined($protectedPost['TAG']) && $protectedPost['TAG'] != "0")
            $_SESSION['GREENIT']['FILTER']['TAG'] = $protectedPost['TAG'];
        if (is_defined($protectedPost['ASSET']) && $protectedPost['ASSET'] != "0")
            $_SESSION['GREENIT']['FILTER']['ASSET'] = $protectedPost['ASSET'];
        //////////////////////////////

        //////////////////////////////
        // Get filtered computers
        $computerQuery['SQL'] = '
            SELECT DISTINCT 
            hardware.NAME as NAME 
            FROM hardware 
            INNER JOIN accountinfo ON hardware.ID = accountinfo.hardware_id 
            INNER JOIN greenit ON hardware.ID = greenit.HARDWARE_ID 
            LEFT JOIN groups_cache ON hardware.ID = groups_cache.HARDWARE_ID 
        ';

        if (
            is_defined($_SESSION['GREENIT']['FILTER']['OS']) ||
            is_defined($_SESSION['GREENIT']['FILTER']['GROUP']) ||
            is_defined($_SESSION['GREENIT']['FILTER']['TAG']) ||
            is_defined($_SESSION['GREENIT']['FILTER']['ASSET'])
        ) {
            $computerQuery['WHERE'] = [];
            $computerQuery['SQL'] .= ' WHERE';

            if (is_defined($_SESSION['GREENIT']['FILTER']['OS']))
                array_push($computerQuery['WHERE'], ' hardware.OSNAME="' . $_SESSION['GREENIT']['FILTER']['OS'] . '" AND');
            if (is_defined($_SESSION['GREENIT']['FILTER']['GROUP']))
                array_push($computerQuery['WHERE'], ' GROUP_ID="' . $_SESSION['GREENIT']['FILTER']['GROUP'] . '" AND');
            if (is_defined($_SESSION['GREENIT']['FILTER']['TAG']))
                array_push($computerQuery['WHERE'], ' accountinfo.TAG="' . $_SESSION['GREENIT']['FILTER']['TAG'] . '" AND');
            if (is_defined($_SESSION['GREENIT']['FILTER']['ASSET']))
                array_push($computerQuery['WHERE'], ' hardware.CATEGORY_ID="' . $_SESSION['GREENIT']['FILTER']['ASSET'] . '" AND');
            array_push($computerQuery['WHERE'], ' 1');
            foreach ($computerQuery['WHERE'] as $args) {
                $computerQuery['SQL'] .= $args;
            }
        }
        $computerDataResult = mysql2_query_secure($computerQuery['SQL'], $_SESSION['OCS']["readServer"]);
        $computerData = array();
        while ($row = mysqli_fetch_object($computerDataResult)) {
            array_push($computerData, $row->NAME);
        }
        $computers = "";
        foreach ($computerData as $computer) {
            $computers .= $computer;
            if (next($computerData))
                $computers .= ",";
        }
        //////////////////////////////

        //////////////////////////////
        // Get filter table values
        $sql_filtered_search['SQL'] = '
            SELECT DISTINCT 
            hardware.NAME as NAME,
            hardware.OSNAME AS OS_NAME,
            accountinfo.TAG AS TAG,
            groups_cache.GROUP_ID AS GROUP_ID,
            hardware.CATEGORY_ID AS CATEGORY_ID
            FROM hardware
            INNER JOIN accountinfo ON hardware.ID = accountinfo.hardware_id
            INNER JOIN greenit ON hardware.ID = greenit.HARDWARE_ID
            LEFT JOIN groups_cache ON hardware.ID = groups_cache.HARDWARE_ID
        ';

        if (
            is_defined($_SESSION['GREENIT']['FILTER']['OS']) ||
            is_defined($_SESSION['GREENIT']['FILTER']['GROUP']) ||
            is_defined($_SESSION['GREENIT']['FILTER']['TAG']) ||
            is_defined($_SESSION['GREENIT']['FILTER']['ASSET'])
        ) {
            $sql_filtered_search['WHERE'] = [];
            $sql_filtered_search['SQL'] .= ' WHERE';

            if (is_defined($_SESSION['GREENIT']['FILTER']['OS']))
                array_push($sql_filtered_search['WHERE'], ' hardware.OSNAME="' . $_SESSION['GREENIT']['FILTER']['OS'] . '" AND');
            if (is_defined($_SESSION['GREENIT']['FILTER']['GROUP']))
                array_push($sql_filtered_search['WHERE'], ' GROUP_ID="' . $_SESSION['GREENIT']['FILTER']['GROUP'] . '" AND');
            if (is_defined($_SESSION['GREENIT']['FILTER']['TAG']))
                array_push($sql_filtered_search['WHERE'], ' accountinfo.TAG="' . $_SESSION['GREENIT']['FILTER']['TAG'] . '" AND');
            if (is_defined($_SESSION['GREENIT']['FILTER']['ASSET']))
                array_push($sql_filtered_search['WHERE'], ' hardware.CATEGORY_ID="' . $_SESSION['GREENIT']['FILTER']['ASSET'] . '" AND');
            array_push($sql_filtered_search['WHERE'], ' 1');
            foreach ($sql_filtered_search['WHERE'] as $args) {
                $sql_filtered_search['SQL'] .= $args;
            }
        }

        $sql_filtered_search['SQL'] .= ' GROUP BY NAME';
        //////////////////////////////

        //////////////////////////////
        // OS filter
        $query = "SELECT OSNAME FROM hardware WHERE OSNAME LIKE '%Windows%' AND DEVICEID<>'_SYSTEMGROUP_' AND DEVICEID<>'_DOWNLOADGROUP_' GROUP BY OSNAME ORDER BY OSNAME";
        $result = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
        $os = [
            0 => "-----",
        ];
        while ($item = mysqli_fetch_array($result)) {
            $os[$item['OSNAME']] = $item['OSNAME'];
        }
        //////////////////////////////

        //////////////////////////////
        // GROUP filter
        $query = "SELECT NAME, ID FROM hardware WHERE DEVICEID = '_SYSTEMGROUP_' GROUP BY NAME ORDER BY NAME";
        $result = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
        $group = [
            0 => "-----",
        ];
        while ($item = mysqli_fetch_array($result)) {
            $group[$item['ID']] = $item['NAME'];
        }
        //////////////////////////////

        //////////////////////////////
        // TAG filter
        $query = "SELECT TAG FROM accountinfo";
        $result = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
        $tag = [
            0 => "-----",
        ];
        while ($item = mysqli_fetch_array($result)) {
            $tag[$item['TAG']] = $item['TAG'];
        }
        //////////////////////////////

        //////////////////////////////
        // ASSET filter
        $query = "SELECT CATEGORY_NAME, ID FROM assets_categories GROUP BY CATEGORY_NAME ORDER BY CATEGORY_NAME";
        $result = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
        $asset = [
            0 => "-----",
        ];
        while ($item = mysqli_fetch_array($result)) {
            $asset[$item['ID']] = $item['CATEGORY_NAME'];
        }
        //////////////////////////////
    }

    /**
     * Generate the FilteredSearch HTML code of the view
     * 
     * @return void Return nothing
     */
    public function ShowFilteredSearch(): void
    {
        echo "<h4>" . $l->g(102600) . "</h4>";

        $form_name = "filteredSearch";

        //////////////////////////////
        // Table settings
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
        //////////////////////////////

        echo open_form($form_name, '', '', 'form-horizontal');

        echo "<h4>" . $l->g(102800) . "</h4>";

        echo '
        <div class="form-group">
            <div class="col-sm-12">
        ';

        //////////////////////////////
        // Show generate filtered stats + warning message about filter on
        if (
            is_defined($_SESSION['GREENIT']['FILTER']['OS']) ||
            is_defined($_SESSION['GREENIT']['FILTER']['GROUP']) ||
            is_defined($_SESSION['GREENIT']['FILTER']['TAG']) ||
            is_defined($_SESSION['GREENIT']['FILTER']['ASSET'])
        ) {
            msg_warning($l->g(767));
            echo '
                <a 
                    href="index.php?function=ms_greenit_dashboard&cat=filteredstats&' . strtolower(str_replace(" ", "_", $l->g(729))) . '=' . $computers . '" 
                    class="btn btn-success"
                >
                    ' . $l->g(102801) . '
                </a>
            ';
        }

        ajaxtab_entete_fixe($list_fields_filtered_search, $default_fields_filtered_search, $tab_options_filtered_search, $list_col_cant_del_filtered_search);

        echo '
                <button type="button" data-toggle="collapse" data-target="#filter" class="btn">' . $l->g(735) . '</button>
                <div id="filter" class="collapse">
        ';

        //////////////////////////////
        // OS
        echo '
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="OS">' . $l->g(25) . '</label>
                        <div class="col-sm-3">
                            <select name="OS" id="OS" class="form-control">
            ';
        foreach ($os as $key => $name) {
            if (isset($_SESSION['GREENIT']['FILTER']['OS']) && $_SESSION['GREENIT']['FILTER']['OS'] == $key) {
                echo "<option value='" . $key . "' selected>" . $name . "</option>";
            } else {
                echo "<option value='" . $key . "'>" . $name . "</option>";
            }
        }
        echo '
                            </select>
                        </div>
            ';
        //////////////////////////////

        //////////////////////////////
        // GROUP
        echo '
                        <label class="control-label col-sm-2" for="GROUP">' . $l->g(583) . '</label>
                        <div class="col-sm-3">
                            <select name="GROUP" id="GROUP" class="form-control">
            ';
        foreach ($group as $key => $name) {
            if (isset($_SESSION['GREENIT']['FILTER']['GROUP']) && $_SESSION['GREENIT']['FILTER']['GROUP'] == $key) {
                echo "<option value='" . $key . "' selected>" . $name . "</option>";
            } else {
                echo "<option value='" . $key . "'>" . $name . "</option>";
            }
        }
        echo '
                            </select>
                        </div>
                    </div>
            ';
        //////////////////////////////

        //////////////////////////////
        // TAG
        echo '
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="TAG">' . $l->g(1425) . '</label>
                        <div class="col-sm-3">
                            <select name="TAG" id="TAG" class="form-control">
            ';
        foreach ($tag as $key => $name) {
            if (isset($_SESSION['GREENIT']['FILTER']['TAG']) && $_SESSION['GREENIT']['FILTER']['TAG'] == $key) {
                echo "<option value='" . $key . "' selected>" . $name . "</option>";
            } else {
                echo "<option value='" . $key . "'>" . $name . "</option>";
            }
        }
        echo '
                            </select>
                        </div>
            ';
        //////////////////////////////

        //////////////////////////////
        // ASSET CATEGORY
        echo '
                        <label class="control-label col-sm-2" for="ASSET">' . $l->g(2132) . '</label>
                        <div class="col-sm-3">
                            <select name="ASSET" id="ASSET" class="form-control">
            ';
        foreach ($asset as $key => $name) {
            if (isset($_SESSION['GREENIT']['FILTER']['ASSET']) && $_SESSION['GREENIT']['FILTER']['ASSET'] == $key) {
                echo "<option value='" . $key . "' selected>" . $name . "</option>";
            } else {
                echo "<option value='" . $key . "'>" . $name . "</option>";
            }
        }
        echo '
                            </select>
                        </div>
                    </div>
            ';
        //////////////////////////////


        echo '
                <button class="btn btn-success" name="SUBMIT_FORM">' . $l->g(393) . '</button>
                <button class="btn btn-danger" name="RESET">' . $l->g(41) . '</button>
            </div>
        </div>
        ';

        echo close_form();

        echo "<hr>";
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
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->return != false ? $this->calculation->CostFormat($this->yesterdayData->totalConsumption, $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
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
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->yesterdayData) && $this->yesterdayData->return != false ? $this->calculation->CostFormat($this->yesterdayData->totalConsumption / $this->yesterdayData->totalMachines, $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
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
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->collectData) && $this->collectData->return != false ? $this->calculation->CostFormat($this->collectData->{"0000-00-00"}->totalConsumption, $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102702) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
                <div class='col-md-6'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->compareData) && $this->compareData->return != false ? $this->calculation->CostFormat($this->compareData->{"0000-00-00"}->totalConsumption, $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102702) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>                
            </div>
            <br>
            <div class='row'>
                <div class='col-md-6' style='border-right: 1px solid #ddd;'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->collectData) && $this->collectData->return != false ? $this->calculation->CostFormat($this->collectData->{"0000-00-00"}->totalConsumption / $this->collectData->{"0000-00-00"}->totalMachines, $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102704) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
                <div class='col-md-6'>
                    <p style='font-size: 30px; font-weight:bold;'>" . (isset($this->compareData) && $this->compareData->return != false ? $this->calculation->CostFormat($this->compareData->{"0000-00-00"}->totalConsumption / $this->compareData->{"0000-00-00"}->totalMachines, $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0") . "</p>
                    <p style='color:#333; font-size: 15px;'>" . $l->g(102704) . " " . $this->config->GetCompareInfoPeriod() . " " . $l->g(102706) . "</p>
                </div>
            </div>
        ";
        echo $table;

        $labels = [$l->g(102702) . " " . $this->config->GetCollectInfoPeriod() . " " . $l->g(102706)];
        $backgroundColor = $this->diagram->GenerateColorList(2, true);
        $data = array(
            "CONSUMPTION" => str_replace(" " . "kW/h", "", (isset($this->collectData) && $this->collectData->return != false ? $this->calculation->ConsumptionFormat($this->collectData->{"0000-00-00"}->totalConsumption, $this->config->GetConsumptionRound()) : "0")),
            "COST" => str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->collectData) && $this->collectData->return != false ? $this->calculation->CostFormat($this->collectData->{"0000-00-00"}->totalConsumption, $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"))
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
            "CONSUMPTION" => str_replace(" " . "kW/h", "", (isset($this->compareData) && $this->compareData->return != false ? $this->calculation->ConsumptionFormat($this->compareData->{"0000-00-00"}->totalConsumption, $this->config->GetConsumptionRound()) : "0")),
            "COST" => str_replace(" " . $this->config->GetCostUnit(), "", (isset($this->compareData) && $this->compareData->return != false ? $this->calculation->CostFormat($this->compareData->{"0000-00-00"}->totalConsumption, $this->config->GetKiloWattCost(), $this->config->GetCostUnit(), $this->config->GetCostRound()) : "0"))
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