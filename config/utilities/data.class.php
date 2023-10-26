<?php

/**
 * Used to get data from database
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
class Data
{
    /**
     * Constructor of the class which define everything the view need to work
     */
    function __construct()
    {
    }

    /**
     * Get the title data from a formated request from the database
     * 
     * @param string $query Define the query to execute in the database
     * 
     * @return object Return an object with the formated data from database or false if the database canno't be reached
     */
    public function GetTitleData(string $query): object
    {
        $data = new stdClass();
        $dataResult = mysql2_query_secure($query, $_SESSION["OCS"]["readServer"]);
        if ($dataResult != false && isset($dataResult->num_rows) && $dataResult->num_rows != 0) {
            $data->return = true;
            while ($row = mysqli_fetch_object($dataResult)) {
                $data->idCount = $row->idCount;
            }
        } else {
            $data->return = false;
        }
        return $data;
    }

    /**
     * Get the GreenIT data from a formated request from the database
     * 
     * @param string $query Define the query to execute in the database
     * 
     * @return object Return an object with the formated data from database or false if the database canno't be reached
     */
    public function GetGreenITData(string $query): object
    {
        $data = new stdClass();
        $dataResult = mysql2_query_secure($query, $_SESSION["OCS"]["readServer"]);
        if ($dataResult != false && isset($dataResult->num_rows) && $dataResult->num_rows != 0) {
            while ($row = mysqli_fetch_object($dataResult)) {
                $data = json_decode($row->DATA);
            }
            $data->return = true;
        } else
            $data->return = false;
        return $data;
    }

    /**
     * Get the filtered GreenIT data from a formated request from the database
     * 
     * @param string $query Define the query to execute in the database
     * @param bool $multiple Set if the retrieved data is on one day or more
     * 
     * @return object Return an object with the formated data from database or false if the database canno't be reached
     */
    public function GetFilteredGreenITData(string $query, object $eletricityPrices, bool $multiple): object
    {
        $data = new stdClass();
        $config = new Config();
        $dataResult = mysql2_query_secure($query, $_SESSION["OCS"]["readServer"]);
        if ($dataResult != false && isset($dataResult->num_rows) && $dataResult->num_rows != 0) {
            $formatedConsumptions = array();
            if ($multiple == false) {
                while ($row = mysqli_fetch_object($dataResult)) {
                    $data->totalMachines = $row->totalMachines;
                    $data->totalConsumption = $row->totalConsumption;
                    $data->totalUptime = $row->totalUptime;
                    $data->consumptionAverage = round($row->totalConsumption / $row->totalMachines, 6);
                    $data->uptimeAverage = round($row->totalUptime / $row->totalMachines, 6);
                    $formatedConsumptions[$row->DATE] = floatval($row->totalConsumption);
                    $totalCost = 0;
                    if (is_defined($config->GetAPIKey())) {
                        foreach ($formatedConsumptions as $FCDate => $FCValue) {
                            $Date = new Datetime($FCDate);
                            foreach ($eletricityPrices as $KWCDate => $KWCValue) {
                                if ($KWCDate != "return") {
                                    if ($Date->format("Y-m-01") > $KWCDate) {
                                        while ($Date->format("Y-m-01") != $KWCDate) {
                                            $Date->modify("- 1 month");
                                        }
                                        break;
                                    }
                                }
                            }
                            $totalCost += round(($formatedConsumptions[$FCDate] / 1000) * ($eletricityPrices->{$Date->format("Y-m-01")} / 100), $config->GetCostRound());
                        }
                    }
                    $data->totalCost = floatval($totalCost);
                }
            } else {
                $data->totalMachines = 0;
                $data->totalConsumption = 0;
                $data->totalUptime = 0;
                $data->consumptionAverage = 0;
                $data->uptimeAverage = 0;
                while ($row = mysqli_fetch_object($dataResult)) {
                    $data->totalMachines = $row->totalMachines;
                    $data->totalConsumption += $row->totalConsumption;
                    $data->totalUptime += $row->totalUptime;
                    $formatedConsumptions[$row->DATE] = floatval($row->totalConsumption);
                    $totalCost = 0;
                    if (is_defined($config->GetAPIKey())) {
                        foreach ($formatedConsumptions as $FCDate => $FCValue) {
                            $Date = new Datetime($FCDate);
                            foreach ($eletricityPrices as $KWCDate => $KWCValue) {
                                if ($KWCDate != "return") {
                                    if ($Date->format("Y-m-01") > $KWCDate) {
                                        while ($Date->format("Y-m-01") != $KWCDate) {
                                            $Date->modify("- 1 month");
                                        }
                                        break;
                                    }
                                }
                            }
                            $totalCost += round(($formatedConsumptions[$FCDate] / 1000) * ($eletricityPrices->{$Date->format("Y-m-01")} / 100), $config->GetCostRound());
                        }
                    }
                    $data->totalCost = floatval($totalCost);
                }
            }
            $data->return = true;
        } else
            $data->return = false;
        return $data;
    }

    /**
     * Get the computer types from OCS-Inventory database
     * 
     * @return object Return an object with the formated data from database or false if the database canno't be reached
     */
    public function GetComputerTypes(): object
    {
        $data = new stdClass();
        $query = "
            SELECT 
            (
                CASE
                
                WHEN (
                    bios.type LIKE '%Desktop%' OR 
                    bios.type LIKE '%Elitedesk%' OR 
                    bios.type LIKE '%Mini Tower%' OR
                    bios.type LIKE '%ProLient%' OR
                    bios.type LIKE '%Precision%' OR
                    bios.type LIKE '%All in One%'
                )
                THEN 'Desktop'
        
                WHEN (
                    bios.type LIKE '%LapTop%' OR 
                    bios.type LIKE '%Portable%' OR
                    bios.type LIKE '%Notebook%'
                )
                THEN 'LapTop'
                
                WHEN (
                    bios.type <> 'Desktop' OR
                    bios.type <> 'LapTop'
                )
                THEN 'Other'
                
                ELSE bios.type
                
                END
            ) AS COMPUTER_TYPE
            FROM greenit
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID
            GROUP BY COMPUTER_TYPE
        ";
        $dataResult = mysql2_query_secure($query, $_SESSION["OCS"]["readServer"]);
        if ($dataResult != false && isset($dataResult->num_rows) && $dataResult->num_rows != 0) {
            $data->ComputerTypes = array();
            $data->return = true;
            while ($row = mysqli_fetch_object($dataResult)) {
                array_push($data->ComputerTypes, $row->COMPUTER_TYPE);
            }
        } else
            $data->return = false;
        return $data;
    }

    /**
     * Get the manufacturers from OCS-Inventory database
     * 
     * @param string $query Define the query to execute in the database
     * 
     * @return object Return an object with the formated data from database or false if the database canno't be reached
     */
    public function GetManufacturers(string $query): object
    {

        $data = new stdClass();
        $dataResult = mysql2_query_secure($query, $_SESSION["OCS"]["readServer"]);
        if ($dataResult != false && isset($dataResult->num_rows) && $dataResult->num_rows != 0) {
            $data->Manufacturers = array();
            $data->return = true;
            while ($row = mysqli_fetch_object($dataResult)) {
                array_push($data->Manufacturers, $row->MANUFACTURER);
            }
        } else
            $data->return = false;
        return $data;
    }

    /**
     * Get the electricity prices from the GreenIT API
     * 
     * @return object Return an object with the formated data from database or false if the database canno't be reached
     */
    public function GetElectricityPrices(): object
    {
        $data = new stdClass();
        $config = new Config();
        $url = 'http://172.18.25.171:8080/data/periods/';
        $query = curl_init($url);
        curl_setopt(
            $query,
            CURLOPT_RETURNTRANSFER,
            true
        );
        if (is_defined($config->GetAPIKey()))
            curl_setopt(
                $query,
                CURLOPT_HTTPHEADER,
                array(
                    'Authorization: Token ' . $config->GetAPIKey()
                )
            );
        $response = curl_exec($query);
        $response = json_decode($response);
        curl_close($query);

        if (curl_getinfo($query, CURLINFO_HTTP_CODE) == 200) {
            foreach ($response as $element) {
                foreach ($element->{"groups"} as $group) {
                    if ($group->{"name"} == $config->GetConsumptionType()) {
                        $data->{$element->{"period"}} = $group->{"electricity_price"};
                    }
                }
            }
            $data->return = true;
        } else
            $data->return = false;
        return $data;
    }
}

?>