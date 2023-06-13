<?php
Class CronStats
{
    private array $options;
    private datetime $messageTime;
    private array $logType;
    
    function __construct()
    {
        require_once(__DIR__.'/../../../../var.php');
        require_once(CONF_MYSQL);
        require_once(ETC_DIR.'/require/function_commun.php');
        require_once(ETC_DIR.'/require/config/include.php');
        require_once(ETC_DIR.'/require/fichierConf.class.php');

        $_SESSION['OCS']["writeServer"] = dbconnect(SERVER_WRITE, COMPTE_BASE, PSWD_BASE, DB_NAME, SSL_KEY, SSL_CERT, CA_CERT, SERVER_PORT);
        $_SESSION['OCS']["readServer"] = dbconnect(SERVER_READ, COMPTE_BASE, PSWD_BASE, DB_NAME, SSL_KEY, SSL_CERT, CA_CERT, SERVER_PORT);

        $shortopts = "h::";
        $shortopts .= "m:";
        $longopts  = array(
            "help::",
            "mode:",
        );
        $this->options = getopt($shortopts, $longopts);

        if(isset($this->options["h"])) $this->options["help"] = $this->options["h"];
        if(isset($this->options["m"])) $this->options["mode"] = $this->options["m"];

        $logType = ["INFO", "WARNING", "ERROR"];
        $this->logType = $logType;
    }

    public function Options()
    {
        if(sizeof($this->options) == 0)
        {
            echo $this->LogMessage("ERROR", "Mode required to start the script. --help for more information.");
            return;
        }
        if(isset($this->options["help"]))
        {
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
        
        switch($this->options["mode"])
        {
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
        echo $this->LogMessage("INFO", "Executing delta mode. Processing...");
        echo $this->LogMessage("INFO", "Communication with database system...");
        $date = new DateTime("NOW");
        $dateString = $date->format("Y-m-d");
        $selectQuery = "SELECT CONSUMPTION,UPTIME FROM greenit WHERE DATE = '%s' ORDER BY UPTIME;";

        echo $this->LogMessage("INFO", "Getting values to insert...");

        if($query = mysql2_query_secure($selectQuery, $_SESSION['OCS']["readServer"], $dateString))
        {
            foreach ($query as $values)
            {
                if(!isset($consumptionCount[$date->format("Y-m-d")])) $consumptionCount[$date->format("Y-m-d")] = 0;
                if(!isset($uptimeCount[$date->format("Y-m-d")])) $uptimeCount[$date->format("Y-m-d")] = 0;
    
                if(isset($data[$date->format("Y-m-d")]["totalConsumption"])) $data[$date->format("Y-m-d")]["totalConsumption"] += floatval($values["CONSUMPTION"]);
                else $data[$date->format("Y-m-d")]["totalConsumption"] = floatval($values["CONSUMPTION"]);
                $consumptionCount[$date->format("Y-m-d")]++;
    
                if($values["CONSUMPTION"] != "VM detected")
                {
                    if(isset($data[$date->format("Y-m-d")]["totalUptime"])) $data[$date->format("Y-m-d")]["totalUptime"] += intval($values["UPTIME"]);
                    else $data[$date->format("Y-m-d")]["totalUptime"] = intval($values["UPTIME"]);
                    $uptimeCount[$date->format("Y-m-d")]++;
                }
            }
        }

        if(isset($data))
        {
            foreach ($data as $key => $value) {
                $data[$key]["consumptionAverage"] = round($data[$key]["totalConsumption"]/$consumptionCount[$key], 6);
                $data[$key]["uptimeAverage"] = round($data[$key]["totalUptime"]/$uptimeCount[$key], 6);
            }
            
            echo $this->LogMessage("INFO", "Inserting values into database...");
            $deleteQuery = "DELETE FROM greenit_stats WHERE DATE = '%s';";
            $alterQuery = "ALTER TABLE greenit_stats AUTO_INCREMENT 0;";
            $insertQuery = "INSERT INTO greenit_stats (DATE,DATA) VALUES ('%s','%s');";
            
            foreach ($data as $key => $value) mysql2_query_secure(sprintf($deleteQuery, $key), $_SESSION['OCS']["writeServer"]);
            mysql2_query_secure($alterQuery, $_SESSION['OCS']["writeServer"]);
            foreach ($data as $key => $value) mysql2_query_secure(sprintf($insertQuery, $key, json_encode($value)), $_SESSION['OCS']["writeServer"]);
            echo $this->LogMessage("INFO", "Job done");
        }
        else echo $this->LogMessage("ERROR", "No data");
    }
    
    private function FullMode()
    {
        echo $this->LogMessage("INFO", "Executing full mode. Processing...");
        echo $this->LogMessage("INFO", "Communication with database system...");
        $selectQuery = "SELECT DATE,CONSUMPTION,UPTIME FROM greenit ORDER BY DATE;";

        echo $this->LogMessage("INFO", "Getting values to insert...");
        if($query = mysql2_query_secure($selectQuery, $_SESSION['OCS']["readServer"]))
        {
            foreach ($query as $values)
            {
                if(!isset($consumptionCount[$values["DATE"]])) $consumptionCount[$values["DATE"]] = 0;
                if(!isset($uptimeCount[$values["DATE"]])) $uptimeCount[$values["DATE"]] = 0;
    
                if(isset($data[$values["DATE"]]["totalConsumption"])) $data[$values["DATE"]]["totalConsumption"] += floatval($values["CONSUMPTION"]);
                else $data[$values["DATE"]]["totalConsumption"] = floatval($values["CONSUMPTION"]);
                $consumptionCount[$values["DATE"]]++;
    
                if($values["CONSUMPTION"] != "VM detected")
                {
                    if(isset($data[$values["DATE"]]["totalUptime"])) $data[$values["DATE"]]["totalUptime"] += intval($values["UPTIME"]);
                    else $data[$values["DATE"]]["totalUptime"] = intval($values["UPTIME"]);
                    $uptimeCount[$values["DATE"]]++;
                }
            }
        }

        if(isset($data))
        {
            foreach ($data as $key => $value) {
                $data[$key]["consumptionAverage"] = round($data[$key]["totalConsumption"]/$consumptionCount[$key], 6);
                $data[$key]["uptimeAverage"] = round($data[$key]["totalUptime"]/$uptimeCount[$key], 6);
            }

            echo $this->LogMessage("INFO", "Inserting values into database...");
            $deleteQuery = "DELETE FROM greenit_stats WHERE DATE = '%s';";
            $alterQuery = "ALTER TABLE greenit_stats AUTO_INCREMENT 0;";
            $insertQuery = "INSERT INTO greenit_stats (DATE,DATA) VALUES ('%s','%s');";

            foreach ($data as $key => $value) mysql2_query_secure(sprintf($deleteQuery, $key), $_SESSION['OCS']["writeServer"]);
            mysql2_query_secure($alterQuery, $_SESSION['OCS']["writeServer"]);
            foreach ($data as $key => $value) mysql2_query_secure(sprintf($insertQuery, $key, json_encode($value)), $_SESSION['OCS']["writeServer"]);
            echo $this->LogMessage("INFO", "Job done");
        }
        else echo $this->LogMessage("ERROR", "No data");
    }

    private function LogMessage(string $type, string $message) : ?string
    {
        foreach($this->logType as $value)
        {
            if($type == $value)
            {
                $date = date("H:i:s");
                return "[".$value."] | ".$date." | ".$message."\n";
            }
        }
        return false;
    }
}
?>