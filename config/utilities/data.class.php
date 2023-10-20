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
     * Get the title data from a formated request from the database
     * 
     * @param string $query Define the query to execute in the database
     * 
     * @return object Return an object with the formated data from database or false if the database canno't be reached
     */
    public function GetTitleData(string $query): object
    {
        $data = new stdClass();
        $dataResult = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
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
     * @param bool $multiple Set if the retrieved data is on one day or more
     * 
     * @return object Return an object with the formated data from database or false if the database canno't be reached
     */
    public function GetGreenITData(string $query, bool $multiple): object
    {
        $data = new stdClass();
        $dataResult = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
        if ($dataResult != false && isset($dataResult->num_rows) && $dataResult->num_rows != 0) {
            while ($row = mysqli_fetch_object($dataResult)) {
                if ($multiple == true)
                    $data->{$row->DATE} = json_decode($row->DATA);
                else
                    $data = json_decode($row->DATA);
                $data->return = true;
            }
        } else
            $data->return = false;
        return $data;
    }

    /**
     * Get the computer types from OCS-Inventory database
     * @param string $query Define the query to execute in the database
     * @param bool $multiple Set if the retrieved data is on one day or more
     * 
     * @return object Return an object with the formated data from database or false if the database canno't be reached
     */
    public function GetComputerTypes(string $query, bool $multiple): object
    {
        $Data = new stdClass();
        $DataResult = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
        if ($DataResult != false && isset($DataResult->num_rows) && $DataResult->num_rows != 0) {
        } else
            $Data->RETURN = false;
        return $Data;
    }

    /**
     * Get the manufacturers from OCS-Inventory database
     * @param string $query Define the query to execute in the database
     * @param bool $multiple Set if the retrieved data is on one day or more
     * 
     * @return object Return an object with the formated data from database or false if the database canno't be reached
     */
    public function GetManufacturers(string $query, bool $multiple): object
    {
        $Data = new stdClass();
        $DataResult = mysql2_query_secure($query, $_SESSION['OCS']["readServer"]);
        if ($DataResult != false && isset($DataResult->num_rows) && $DataResult->num_rows != 0) {
        } else
            $Data->RETURN = false;
        return $Data;
    }
}

?>