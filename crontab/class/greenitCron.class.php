<?php
class CronStats
{
    private array $options;
    private datetime $messageTime;
    private array $logType;

    function __construct()
    {
        require_once(__DIR__ . '/../../../../var.php');
        require_once(CONF_MYSQL);
        require_once(ETC_DIR . '/require/function_commun.php');
        require_once(ETC_DIR . '/require/config/include.php');
        require_once(ETC_DIR . '/require/fichierConf.class.php');

        $_SESSION['OCS']["writeServer"] = dbconnect(SERVER_WRITE, COMPTE_BASE, PSWD_BASE, DB_NAME, SSL_KEY, SSL_CERT, CA_CERT, SERVER_PORT);
        $_SESSION['OCS']["readServer"] = dbconnect(SERVER_READ, COMPTE_BASE, PSWD_BASE, DB_NAME, SSL_KEY, SSL_CERT, CA_CERT, SERVER_PORT);

        $shortopts = "h::";
        $shortopts .= "m:";
        $longopts = array(
            "help::",
            "mode:",
        );
        $this->options = getopt($shortopts, $longopts);

        if (isset($this->options["h"]))
            $this->options["help"] = $this->options["h"];
        if (isset($this->options["m"]))
            $this->options["mode"] = $this->options["m"];

        $logType = ["INFO", "WARNING", "ERROR"];
        $this->logType = $logType;
    }

    public function Options()
    {
        if (sizeof($this->options) == 0) {
            echo $this->LogMessage("ERROR", "Mode required to start the script. --help for more information.");
            return;
        }
        if (isset($this->options["help"])) {
            $this->Help();
            return;
        }
        $this->Mode();
    }

    private function Help()
    {
        echo "Usage: php cron_stats.php [OPTION]... [VALUE]..." . "\n";
        echo "Script to push stats data to greenit stats table." . "\n";
        echo "\n";
        echo "-m, --mode    Specify the mode to use ('delta' or 'full')." . "\n";
        echo "-h, --help    To display the help panel." . "\n";
    }

    private function Mode()
    {

        switch ($this->options["mode"]) {
            case "delta":
                $this->DeltaMode();
                break;
            case "full":
                $this->FullMode();
                break;
        }
    }

    private function DeltaMode()
    {
        echo $this->LogMessage("INFO", "Executing delta mode. Please wait during the treatment...");
        echo $this->LogMessage("INFO", "Communinication with database system...");
        $Date = new DateTime("NOW");
        $Date->modify("-1 day");
        $selectQuery = "SELECT CONSUMPTION,UPTIME FROM greenit WHERE DATE = '" . $Date->format("Y-m-d") . "' ORDER BY UPTIME;";

        $consumptionRegex = "/[0-9]+|[0-9]+[.,][0-9]+/";

        $uptimeRegex = "/[0-9]+/";

        echo $this->LogMessage("INFO", "Getting values to insert...");
        if ($query = mysql2_query_secure($selectQuery, $_SESSION['OCS']["readServer"])) {
            foreach ($query as $values) {
                if (!isset($consumptionCount[$Date->format("Y-m-d")]))
                    $consumptionCount[$Date->format("Y-m-d")] = 0;
                if (!isset($uptimeCount[$Date->format("Y-m-d")]))
                    $uptimeCount[$Date->format("Y-m-d")] = 0;

                preg_match($consumptionRegex, $values["CONSUMPTION"], $consumptionMatches);
                preg_match($uptimeRegex, $values["UPTIME"], $uptimeMatches);
                foreach ($consumptionMatches as $match) {
                    if (isset($data[$Date->format("Y-m-d")]["totalConsumption"]))
                        $data[$Date->format("Y-m-d")]["totalConsumption"] += floatval(str_replace(",", ".", $match));
                    else
                        $data[$Date->format("Y-m-d")]["totalConsumption"] = floatval(str_replace(",", ".", $match));
                    $consumptionCount[$Date->format("Y-m-d")]++;
                }
                foreach ($uptimeMatches as $match) {
                    if ($values["CONSUMPTION"] != "VM detected") {
                        if (isset($data[$Date->format("Y-m-d")]["totalUptime"]))
                            $data[$Date->format("Y-m-d")]["totalUptime"] += intval($match);
                        else
                            $data[$Date->format("Y-m-d")]["totalUptime"] = intval($match);
                        $uptimeCount[$Date->format("Y-m-d")]++;
                    }
                }
            }
        }

        if (isset($data)) {
            foreach ($data as $key => $value) {
                $data[$key]["consumptionAverage"] = round($data[$key]["totalConsumption"] / $consumptionCount[$key], 6);
                $data[$key]["uptimeAverage"] = round($data[$key]["totalUptime"] / $uptimeCount[$key], 6);
            }

            echo $this->LogMessage("INFO", "Insert values to database...");
            $deleteQuery = "DELETE FROM greenit_stats WHERE DATE = '%s';";
            $alterQuery = "ALTER TABLE greenit_stats AUTO_INCREMENT 0;";
            $insertQuery = "INSERT INTO greenit_stats (DATE,DATA) VALUES ('%s','%s');";

            foreach ($data as $key => $value)
                mysql2_query_secure(sprintf($deleteQuery, $key), $_SESSION['OCS']["writeServer"]);
            mysql2_query_secure($alterQuery, $_SESSION['OCS']["writeServer"]); foreach ($data as $key => $value)
                mysql2_query_secure(sprintf($insertQuery, $key, json_encode($value)), $_SESSION['OCS']["writeServer"]);
            echo $this->LogMessage("INFO", "Job done");
        } else
            echo $this->LogMessage("ERROR", "No data");
    }

    private function FullMode()
    {
        echo $this->LogMessage("INFO", "Executing full mode. Please wait during the treatment...");
        echo $this->LogMessage("INFO", "Communinication with database system...");
        $selectQuery = "SELECT DATE,CONSUMPTION,UPTIME FROM greenit ORDER BY DATE;";

        $consumptionRegex = "/[0-9]+|[0-9]+[.,][0-9]+/";

        $uptimeRegex = "/[0-9]+/";

        echo $this->LogMessage("INFO", "Getting values to insert...");
        if ($query = mysql2_query_secure($selectQuery, $_SESSION['OCS']["writeServer"])) {
            foreach ($query as $values) {
                if (!isset($consumptionCount[$values["DATE"]]))
                    $consumptionCount[$values["DATE"]] = 0;
                if (!isset($uptimeCount[$values["DATE"]]))
                    $uptimeCount[$values["DATE"]] = 0;

                preg_match($consumptionRegex, $values["CONSUMPTION"], $consumptionMatches);
                preg_match($uptimeRegex, $values["UPTIME"], $uptimeMatches);
                foreach ($consumptionMatches as $match) {
                    if (isset($data[$values["DATE"]]["totalConsumption"]))
                        $data[$values["DATE"]]["totalConsumption"] += floatval(str_replace(",", ".", $match));
                    else
                        $data[$values["DATE"]]["totalConsumption"] = floatval(str_replace(",", ".", $match));
                    $consumptionCount[$values["DATE"]]++;
                }
                foreach ($uptimeMatches as $match) {
                    if ($values["CONSUMPTION"] != "VM detected") {
                        if (isset($data[$values["DATE"]]["totalUptime"]))
                            $data[$values["DATE"]]["totalUptime"] += intval($match);
                        else
                            $data[$values["DATE"]]["totalUptime"] = intval($match);
                        $uptimeCount[$values["DATE"]]++;
                    }
                }
                if ((isset($data[$values["DATE"]]["totalConsumption"]) && $data[$values["DATE"]]["totalConsumption"] == " W/h") || (isset($data[$values["DATE"]]["totalUptime"]) && $data[$values["DATE"]]["totalUptime"] == " s"))
                    unset($data[$values["DATE"]]);
            }
        }

        if (isset($data)) {
            foreach ($data as $key => $value) {
                $data[$key]["consumptionAverage"] = round($data[$key]["totalConsumption"] / $consumptionCount[$key], 6);
                $data[$key]["uptimeAverage"] = round($data[$key]["totalUptime"] / $uptimeCount[$key], 6);
            }

            echo $this->LogMessage("INFO", "Insert values to database...");
            $deleteQuery = "DELETE FROM greenit_stats WHERE DATE = '%s';";
            $alterQuery = "ALTER TABLE greenit_stats AUTO_INCREMENT 0;";
            $insertQuery = "INSERT INTO greenit_stats (DATE,DATA) VALUES ('%s','%s');";

            foreach ($data as $key => $value)
                mysql2_query_secure(sprintf($deleteQuery, $key), $_SESSION['OCS']["writeServer"]);
            mysql2_query_secure($alterQuery, $_SESSION['OCS']["writeServer"]); foreach ($data as $key => $value)
                mysql2_query_secure(sprintf($insertQuery, $key, json_encode($value)), $_SESSION['OCS']["writeServer"]);
            echo $this->LogMessage("INFO", "Job done");
        } else
            echo $this->LogMessage("ERROR", "No data");
    }

    private function LogMessage(string $type, string $message): ?string
    {
        foreach ($this->logType as $value) {
            if ($type == $value) {
                $date = date("H:i:s");
                return "[" . $value . "] | " . $date . " | " . $message . "\n";
            }
        }
        return false;
    }
}
?>