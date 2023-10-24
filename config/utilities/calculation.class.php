<?php

/**
 * Use to calculate and format a consumption, a time or a cost
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
class Calculation
{
    /**
     * Constructor of the class which define everything the view need to work
     */
    function __construct()
    {
    }

    /**
     * Format a consumption in Watt per hour and round if it's needed
     * 
     * @param float $consumptionInWattPerHour Define the consumption in Watt per hour
     * @param int $round Define a number which will round the consumption default: 1
     * 
     * @return string Return a string with formated consumption in kiloWatt per hour
     */
    public function ConsumptionFormat(float $consumptionInWattPerHour, int $round = 1): string
    {
        return round($consumptionInWattPerHour / 1000, $round) . " kW/h";
    }

    /**
     * Format a time in seconds in another format
     * 
     * @param float $timeInSeconds Define the time in seconds
     * @param string $format Define the format with "s", "m-s" or "h-m-s"
     * 
     * @return string|false Return a string with formated time or false if the format is not correct
     */
    function TimeFormat(float $timeInSeconds, string $format): string|false
    {
        switch ($format) {
            case "s":
                return round($timeInSeconds) . " s";
            case "m-s":
                $timeInMinutes = $timeInSeconds / 60;
                $timeInSeconds = ($timeInMinutes - intval($timeInMinutes)) * 60;
                return round(intval($timeInMinutes)) . " m " . round($timeInSeconds) . " s";
            case "h-m-s":
                $timeInHours = $timeInSeconds / 3600;
                $timeInMinutes = ($timeInHours - intval($timeInHours)) * 60;
                $timeInSeconds = ($timeInMinutes - intval($timeInMinutes)) * 60;
                return round(intval($timeInHours)) . " h " . round(intval($timeInMinutes)) . " m " . round($timeInSeconds) . " s";
            default:
                return false;
        }
    }

    /**
     * Calcul a cost with a consumption and a kiloWatt price
     * 
     * @param float $formatedConsumption Define the formated consumption
     * @param float $kilowattCost Define kiloWatt price for 1 kW/h
     * @param int $round Define a number which will round the cost default: 1
     * 
     * @return string Return a string with calculated cost
     */
    function CostFormat(float $formatedConsumption, float $kilowattCost, string $costUnit, int $round = 1): string
    {
        return round(($formatedConsumption / 1000) * $kilowattCost, $round) . " " . $costUnit;
    }
}

?>