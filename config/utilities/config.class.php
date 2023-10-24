<?php

/**
 * Used to retrieve config data
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
class Config
{
    /**
     * Data of the collect period
     */
    private int $COLLECT_INFO_PERIOD;

    /**
     * Data of the compare period
     */
    private int $COMPARE_INFO_PERIOD;

    /**
     * Data of the round of consumption
     */
    private int $CONSUMPTION_ROUND;

    /**
     * Data of the uptime format
     */
    private string $UPTIME_FORMAT;

    /**
     * Data of the kilowatt price
     */
    private float $KILOWATT_COST;

    /**
     * Data of the round of cost
     */
    private int $COST_ROUND;

    /**
     * Data of the cost unit
     */
    private string $COST_UNIT;

    /**
     * Data of the API key
     */
    private string $API_KEY;

    /**
     * Data of the consumption type
     */
    private string $CONSUMPTION_TYPE;

    /**
     * Date of D-1 for dynamic queries
     */
    private DateTime $yesterdayDate;

    /**
     * Date of collect period for dynamic queries 
     */
    private DateTime $collectDate;

    /**
     * Date of compare period for dynamic queries
     */
    private DateTime $compareDate;

    /**
     * Constructor of the class which define everything the view need to work
     */
    function __construct()
    {
        $configQuery = "
            SELECT 
            COLLECT_INFO_PERIOD, 
            COMPARE_INFO_PERIOD, 
            CONSUMPTION_ROUND, 
            UPTIME_FORMAT, 
            KILOWATT_COST, 
            COST_ROUND, 
            COST_UNIT,
            API_KEY,
            CONSUMPTION_TYPE 
            FROM greenit_config 
            WHERE 
            ID='1'
        ";
        $configResult = mysql2_query_secure($configQuery, $_SESSION["OCS"]["readServer"]);

        while ($row = mysqli_fetch_object($configResult)) {
            $this->COLLECT_INFO_PERIOD = $row->COLLECT_INFO_PERIOD;
            $this->COMPARE_INFO_PERIOD = $row->COMPARE_INFO_PERIOD;
            $this->CONSUMPTION_ROUND = $row->CONSUMPTION_ROUND;
            $this->UPTIME_FORMAT = $row->UPTIME_FORMAT;
            $this->KILOWATT_COST = $row->KILOWATT_COST;
            $this->COST_ROUND = $row->COST_ROUND;
            $this->COST_UNIT = $row->COST_UNIT;
            $this->API_KEY = $row->API_KEY;
            $this->CONSUMPTION_TYPE = $row->CONSUMPTION_TYPE;
        }

        $this->yesterdayDate = new DateTime("NOW");
        $this->yesterdayDate->modify("-1 day");
        $this->collectDate = new DateTime("NOW");
        $this->collectDate->modify("-" . $this->GetCollectInfoPeriod() - 1 . " days");
        $this->compareDate = new DateTime("NOW");
        $this->compareDate->modify("-" . $this->GetCompareInfoPeriod() - 1 . " days");
    }

    /**
     * Get the collect period data
     * 
     * @return int Return collect period data
     */
    public function GetCollectInfoPeriod(): int
    {
        return $this->COLLECT_INFO_PERIOD;
    }

    /**
     * Get the compare period data
     * 
     * @return int Return compare period data
     */
    public function GetCompareInfoPeriod(): int
    {
        return $this->COMPARE_INFO_PERIOD;
    }

    /**
     * Get the consumption round data
     * 
     * @return int Return consumption round data
     */
    public function GetConsumptionRound(): int
    {
        return $this->CONSUMPTION_ROUND;
    }

    /**
     * Get the uptime format data
     * 
     * @return int Return uptime format data
     */
    public function GetUptimeFormat(): string
    {
        return $this->UPTIME_FORMAT;
    }

    /**
     * Get the kilowatt price data
     * 
     * @return int Return kilowatt price data
     */
    public function GetKiloWattCost(): float
    {
        return $this->KILOWATT_COST;
    }

    /**
     * Get the cost round data
     * 
     * @return int Return cost round data
     */
    public function GetCostRound(): int
    {
        return $this->COST_ROUND;
    }

    /**
     * Get the cost unit data
     * 
     * @return int Return cost unit data
     */
    public function GetCostUnit(): string
    {
        return $this->COST_UNIT;
    }

    /**
     * Get the D-1 date
     * 
     * @return string Return D-1 date formated with "Y-m-d" format
     */
    public function GetYesterdayDate(): string
    {
        return $this->yesterdayDate->format("Y-m-d");
    }

    /**
     * Get the collect date
     * 
     * @return string Return collect date formated with "Y-m-d" format
     */
    public function GetCollectDate(): string
    {
        return $this->collectDate->format("Y-m-d");
    }

    /**
     * Get the compare date
     * 
     * @return string Return compare date formated with "Y-m-d" format
     */
    public function GetCompareDate(): string
    {
        return $this->compareDate->format("Y-m-d");
    }

    /**
     * Get the API key
     * 
     * @return string Return the API key
     */
    public function GetAPIKey(): string
    {
        return $this->API_KEY;
    }

    /**
     * Get the consumption type
     * 
     * @return string Return the consumption type
     */
    public function GetConsumptionType(): string
    {
        return $this->CONSUMPTION_TYPE;
    }
}

?>