<?php

class Calculation
{
    function ConsumptionFormat(float $consumptionInWattPerHour, string $format, int $round = 1): string
    {
        switch ($format) {
            case "W/h":
                return round($consumptionInWattPerHour, $round) . " W/h";
            case "kW/h":
                $ConsumptionInKiloWattPerHour = $consumptionInWattPerHour / 1000;
                return round($ConsumptionInKiloWattPerHour, $round) . " kW/h";
            default:
                return $this->LogMessage("ERROR", "Wrong format.");
        }
    }

    function CostFormat(float $consumption, string $format, float $kilowattCost, string $unit, int $round = 1): string
    {
        switch ($format) {
            case "W/h":
                return round(($consumption / 1000) * $kilowattCost, $round) . " " . $unit;
            case "kW/h":
                return round($consumption * $kilowattCost, $round) . " " . $unit;
            default:
                return $this->LogMessage("ERROR", "Wrong format.");
        }
    }

    function TimeFormat(float $timeInSeconds, string $format): string
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
                return $this->LogMessage("ERROR", "Wrong format.");
        }
    }

    private function LogMessage(string $type, string $message): ?string
    {
        $logType = ["INFO", "WARNING", "ERROR"];

        foreach ($logType as $value) {
            if ($type == $value) {
                return "[" . $value . "] " . $message . "\n";
            }
        }
        return false;
    }

}

?>