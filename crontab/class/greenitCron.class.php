<?php

require_once(__DIR__ . "/../../../../var.php");
require_once(CONF_MYSQL);
require_once(ETC_DIR . "/require/fichierConf.class.php");
require_once(ETC_DIR . "/require/config/include.php");
require_once(ETC_DIR . "/require/function_commun.php");
require_once(ETC_DIR . "/extensions/greenit/config/utilities/config.class.php");
require_once(ETC_DIR . "/extensions/greenit/config/utilities/data.class.php");
require_once(ETC_DIR . "/extensions/greenit/config/utilities/logMessage.class.php");

class CronStats
{
    private Config $config;
    private LogMessage $logMessage;
    private array $options;
    private array $logType;

    function __construct()
    {
        $_SESSION["OCS"]["writeServer"] = dbconnect(SERVER_WRITE, COMPTE_BASE, PSWD_BASE, DB_NAME, SSL_KEY, SSL_CERT, CA_CERT, SERVER_PORT);
        $_SESSION["OCS"]["readServer"] = dbconnect(SERVER_READ, COMPTE_BASE, PSWD_BASE, DB_NAME, SSL_KEY, SSL_CERT, CA_CERT, SERVER_PORT);

        $this->config = new Config();
        $this->data = new Data();
        $this->logMessage = new LogMessage();

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

        $this->logType = array(
            "INFO",
            "WARNING",
            "ERROR"
        );
    }

    public function Options()
    {
        if (sizeof($this->options) == 0) {
            echo $this->logMessage->NewMessage("ERROR", "Mode required to start the script. --help for more information.");
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
            default:
                echo $this->logMessage->NewMessage("ERROR", "Mode unknown. --help for more information.");
                break;
        }
    }

    private function DeltaMode()
    {
    }

    private function FullMode()
    {
        echo $this->logMessage->NewMessage("INFO", "Executing full mode. Processing...");
        echo $this->logMessage->NewMessage("INFO", "Communication with database system...");

        echo $this->logMessage->NewMessage("INFO", "GlobalStats");
        echo $this->logMessage->NewMessage("INFO", "Getting values to insert...");

        $globalQuery = "
            SELECT 
            DATE,
            COUNT(DISTINCT HARDWARE_ID) AS totalMachines,
            SUM(CONSUMPTION) AS totalConsumption,
            SUM(UPTIME) AS totalUptime 
            FROM greenit 
            WHERE 
            CONSUMPTION <> 'VM detected' 
            GROUP BY DATE
        ";
        if ($query = mysql2_query_secure($globalQuery, $_SESSION["OCS"]["readServer"])) {
            foreach ($query as $values) {
                $data["GLOBALSTATS"][$values["DATE"]]["totalMachines"] = intval($values["totalMachines"]);
                $data["GLOBALSTATS"][$values["DATE"]]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["GLOBALSTATS"][$values["DATE"]]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $globalCollectTotalQuery = "
            SELECT 
            COUNT(DISTINCT HARDWARE_ID) AS totalMachines,
            SUM(CONSUMPTION) AS totalConsumption,
            SUM(UPTIME) AS totalUptime 
            FROM greenit 
            WHERE 
            DATE BETWEEN '" . $this->config->GetCollectDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            AND CONSUMPTION <> 'VM detected' 
        ";
        if ($query = mysql2_query_secure($globalCollectTotalQuery, $_SESSION['OCS']["readServer"])) {
            foreach ($query as $values) {
                $data["GLOBAL_COLLECT_TOTAL_STATS"]["0000-00-00"]["totalMachines"] = intval($values["totalMachines"]);
                $data["GLOBAL_COLLECT_TOTAL_STATS"]["0000-00-00"]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["GLOBAL_COLLECT_TOTAL_STATS"]["0000-00-00"]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $globalCompareTotalQuery = "
            SELECT 
            COUNT(DISTINCT HARDWARE_ID) AS totalMachines,
            SUM(CONSUMPTION) AS totalConsumption,
            SUM(UPTIME) AS totalUptime 
            FROM greenit 
            WHERE 
            DATE BETWEEN '" . $this->config->GetCompareDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            AND CONSUMPTION <> 'VM detected' 
        ";
        if ($query = mysql2_query_secure($globalCompareTotalQuery, $_SESSION['OCS']["readServer"])) {
            foreach ($query as $values) {
                $data["GLOBAL_COMPARE_TOTAL_STATS"]["0000-00-00"]["totalMachines"] = intval($values["totalMachines"]);
                $data["GLOBAL_COMPARE_TOTAL_STATS"]["0000-00-00"]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["GLOBAL_COMPARE_TOTAL_STATS"]["0000-00-00"]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        echo $this->logMessage->NewMessage("INFO", "OSStats");
        echo $this->logMessage->NewMessage("INFO", "Getting values to insert...");

        $clientsOSQuery = "
            SELECT 
            DATE,
            COUNT(DISTINCT HARDWARE_ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption, 
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            WHERE
            hardware.OSNAME LIKE '%Windows%' 
            AND hardware.OSNAME NOT IN (SELECT hardware.OSNAME FROM hardware WHERE hardware.OSNAME LIKE '%Windows Server%') 
            AND CONSUMPTION <> 'VM detected' 
            GROUP BY DATE
        ";
        if ($query = mysql2_query_secure($clientsOSQuery, $_SESSION["OCS"]["readServer"])) {
            foreach ($query as $values) {
                $data["OSSTATS_CLIENTS"][$values["DATE"]]["totalMachines"] = intval($values["totalMachines"]);
                $data["OSSTATS_CLIENTS"][$values["DATE"]]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["OSSTATS_CLIENTS"][$values["DATE"]]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $serversOSQuery = "
            SELECT 
            DATE,
            COUNT(DISTINCT HARDWARE_ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption, 
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            WHERE
            hardware.OSNAME LIKE '%Windows Server%' 
            AND CONSUMPTION <> 'VM detected' 
            GROUP BY DATE
        ";
        if ($query = mysql2_query_secure($serversOSQuery, $_SESSION["OCS"]["readServer"])) {
            foreach ($query as $values) {
                $data["OSSTATS_SERVERS"][$values["DATE"]]["totalMachines"] = intval($values["totalMachines"]);
                $data["OSSTATS_SERVERS"][$values["DATE"]]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["OSSTATS_SERVERS"][$values["DATE"]]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $clientsOSCollectTotalQuery = "
            SELECT 
            COUNT(DISTINCT greenit.HARDWARE_ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption,
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            WHERE 
            hardware.OSNAME LIKE '%Windows%' 
            AND hardware.OSNAME NOT IN (SELECT hardware.OSNAME FROM hardware WHERE hardware.OSNAME LIKE '%Windows Server%') 
            AND greenit.DATE BETWEEN '" . $this->config->GetCollectDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            AND greenit.CONSUMPTION <> 'VM detected' 
        ";
        if ($query = mysql2_query_secure($clientsOSCollectTotalQuery, $_SESSION['OCS']["readServer"])) {
            foreach ($query as $values) {
                $data["OS_COLLECT_TOTAL_STATS_CLIENTS"]["0000-00-00"]["totalMachines"] = intval($values["totalMachines"]);
                $data["OS_COLLECT_TOTAL_STATS_CLIENTS"]["0000-00-00"]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["OS_COLLECT_TOTAL_STATS_CLIENTS"]["0000-00-00"]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $serversOSCollectTotalQuery = "
            SELECT 
            COUNT(DISTINCT greenit.HARDWARE_ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption,
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            WHERE 
            hardware.OSNAME LIKE '%Windows Server%' 
            AND greenit.DATE BETWEEN '" . $this->config->GetCollectDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            AND greenit.CONSUMPTION <> 'VM detected' 
        ";
        if ($query = mysql2_query_secure($serversOSCollectTotalQuery, $_SESSION['OCS']["readServer"])) {
            foreach ($query as $values) {
                $data["OS_COLLECT_TOTAL_STATS_SERVERS"]["0000-00-00"]["totalMachines"] = intval($values["totalMachines"]);
                $data["OS_COLLECT_TOTAL_STATS_SERVERS"]["0000-00-00"]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["OS_COLLECT_TOTAL_STATS_SERVERS"]["0000-00-00"]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $clientsOSCompareTotalQuery = "
            SELECT 
            COUNT(DISTINCT greenit.HARDWARE_ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption,
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            WHERE 
            hardware.OSNAME LIKE '%Windows%' 
            AND hardware.OSNAME NOT IN (SELECT hardware.OSNAME FROM hardware WHERE hardware.OSNAME LIKE '%Windows Server%') 
            AND greenit.DATE BETWEEN '" . $this->config->GetCompareDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            AND greenit.CONSUMPTION <> 'VM detected' 
        ";
        if ($query = mysql2_query_secure($clientsOSCompareTotalQuery, $_SESSION['OCS']["readServer"])) {
            foreach ($query as $values) {
                $data["OS_COMPARE_TOTAL_STATS_CLIENTS"]["0000-00-00"]["totalMachines"] = intval($values["totalMachines"]);
                $data["OS_COMPARE_TOTAL_STATS_CLIENTS"]["0000-00-00"]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["OS_COMPARE_TOTAL_STATS_CLIENTS"]["0000-00-00"]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $serversOSCompareTotalQuery = "
            SELECT 
            COUNT(DISTINCT greenit.HARDWARE_ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption,
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            WHERE 
            hardware.OSNAME LIKE '%Windows Server%' 
            AND greenit.DATE BETWEEN '" . $this->config->GetCompareDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            AND greenit.CONSUMPTION <> 'VM detected' 
        ";
        if ($query = mysql2_query_secure($serversOSCompareTotalQuery, $_SESSION['OCS']["readServer"])) {
            foreach ($query as $values) {
                $data["OS_COMPARE_TOTAL_STATS_SERVERS"]["0000-00-00"]["totalMachines"] = intval($values["totalMachines"]);
                $data["OS_COMPARE_TOTAL_STATS_SERVERS"]["0000-00-00"]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["OS_COMPARE_TOTAL_STATS_SERVERS"]["0000-00-00"]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        echo $this->logMessage->NewMessage("INFO", "ComputerTypesStats");
        echo $this->logMessage->NewMessage("INFO", "Getting values to insert...");

        $computerTypesQuery = "
            SELECT 
            DATE,
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
            ) AS COMPUTER_TYPE, 
            COUNT(DISTINCT hardware.ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption, 
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID
            WHERE
            CONSUMPTION <> 'VM detected'
            GROUP BY COMPUTER_TYPE, DATE
        ";
        if ($query = mysql2_query_secure($computerTypesQuery, $_SESSION["OCS"]["readServer"])) {
            foreach ($query as $values) {
                $data["COMPUTERTYPESSTATS_" . strtoupper(str_replace(" ", "_", $values["COMPUTER_TYPE"]))][$values["DATE"]]["totalMachines"] = intval($values["totalMachines"]);
                $data["COMPUTERTYPESSTATS_" . strtoupper(str_replace(" ", "_", $values["COMPUTER_TYPE"]))][$values["DATE"]]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["COMPUTERTYPESSTATS_" . strtoupper(str_replace(" ", "_", $values["COMPUTER_TYPE"]))][$values["DATE"]]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $computerTypesCollectTotalQuery = "
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
            ) AS COMPUTER_TYPE, 
            COUNT(DISTINCT hardware.ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption, 
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID
            WHERE
            CONSUMPTION <> 'VM detected' 
            AND greenit.DATE BETWEEN '" . $this->config->GetCollectDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            GROUP BY COMPUTER_TYPE
        ";
        if ($query = mysql2_query_secure($computerTypesCollectTotalQuery, $_SESSION['OCS']["readServer"])) {
            foreach ($query as $values) {
                $data["COMPUTERTYPES_COLLECT_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["COMPUTER_TYPE"]))]["0000-00-00"]["totalMachines"] = intval($values["totalMachines"]);
                $data["COMPUTERTYPES_COLLECT_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["COMPUTER_TYPE"]))]["0000-00-00"]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["COMPUTERTYPES_COLLECT_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["COMPUTER_TYPE"]))]["0000-00-00"]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $computerTypesCompareTotalQuery = "
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
            ) AS COMPUTER_TYPE, 
            COUNT(DISTINCT hardware.ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption, 
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID
            WHERE
            CONSUMPTION <> 'VM detected' 
            AND greenit.DATE BETWEEN '" . $this->config->GetCompareDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            GROUP BY COMPUTER_TYPE
        ";
        if ($query = mysql2_query_secure($computerTypesCompareTotalQuery, $_SESSION['OCS']["readServer"])) {
            foreach ($query as $values) {
                $data["COMPUTERTYPES_COMPARE_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["COMPUTER_TYPE"]))]["0000-00-00"]["totalMachines"] = intval($values["totalMachines"]);
                $data["COMPUTERTYPES_COMPARE_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["COMPUTER_TYPE"]))]["0000-00-00"]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["COMPUTERTYPES_COMPARE_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["COMPUTER_TYPE"]))]["0000-00-00"]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        echo $this->logMessage->NewMessage("INFO", "ManufacturersStats");
        echo $this->logMessage->NewMessage("INFO", "Getting values to insert...");

        $ManufacturersQuery = "
            SELECT 
            DATE,
            bios.SMANUFACTURER AS MANUFACTURER, 
            COUNT(DISTINCT hardware.ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption, 
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
            WHERE
            CONSUMPTION <> 'VM detected' 
            GROUP BY MANUFACTURER, DATE
        ";
        if ($query = mysql2_query_secure($ManufacturersQuery, $_SESSION["OCS"]["readServer"])) {
            foreach ($query as $values) {
                $data["MANUFACTURERSSTATS_" . strtoupper(str_replace(" ", "_", $values["MANUFACTURER"]))][$values["DATE"]]["totalMachines"] = intval($values["totalMachines"]);
                $data["MANUFACTURERSSTATS_" . strtoupper(str_replace(" ", "_", $values["MANUFACTURER"]))][$values["DATE"]]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["MANUFACTURERSSTATS_" . strtoupper(str_replace(" ", "_", $values["MANUFACTURER"]))][$values["DATE"]]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $manufacturerCollectTotalQuery = "
            SELECT 
            bios.SMANUFACTURER AS MANUFACTURER, 
            COUNT(DISTINCT hardware.ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption, 
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
            WHERE
            CONSUMPTION <> 'VM detected' 
            AND greenit.DATE BETWEEN '" . $this->config->GetCollectDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            GROUP BY MANUFACTURER
        ";
        if ($query = mysql2_query_secure($manufacturerCollectTotalQuery, $_SESSION["OCS"]["readServer"])) {
            foreach ($query as $values) {
                $data["MANUFACTURERSSTATS_COLLECT_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["MANUFACTURER"]))]["0000-00-00"]["totalMachines"] = intval($values["totalMachines"]);
                $data["MANUFACTURERSSTATS_COLLECT_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["MANUFACTURER"]))]["0000-00-00"]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["MANUFACTURERSSTATS_COLLECT_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["MANUFACTURER"]))]["0000-00-00"]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        $manufacturerCompareTotalQuery = "
            SELECT 
            bios.SMANUFACTURER AS MANUFACTURER, 
            COUNT(DISTINCT hardware.ID) AS totalMachines,
            SUM(greenit.CONSUMPTION) AS totalConsumption, 
            SUM(greenit.UPTIME) AS totalUptime 
            FROM greenit 
            INNER JOIN hardware ON greenit.HARDWARE_ID=hardware.ID
            INNER JOIN bios ON greenit.HARDWARE_ID=bios.HARDWARE_ID 
            WHERE
            CONSUMPTION <> 'VM detected' 
            AND greenit.DATE BETWEEN '" . $this->config->GetCompareDate() . "' AND '" . $this->config->GetYesterdayDate() . "'
            GROUP BY MANUFACTURER
        ";
        if ($query = mysql2_query_secure($manufacturerCompareTotalQuery, $_SESSION["OCS"]["readServer"])) {
            foreach ($query as $values) {
                $data["MANUFACTURERSSTATS_COMPARE_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["MANUFACTURER"]))]["0000-00-00"]["totalMachines"] = intval($values["totalMachines"]);
                $data["MANUFACTURERSSTATS_COMPARE_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["MANUFACTURER"]))]["0000-00-00"]["totalConsumption"] = floatval($values["totalConsumption"]);
                $data["MANUFACTURERSSTATS_COMPARE_TOTAL_STATS_" . strtoupper(str_replace(" ", "_", $values["MANUFACTURER"]))]["0000-00-00"]["totalUptime"] = intval($values["totalUptime"]);
            }
        } else {
            echo $this->logMessage->NewMessage("ERROR", "Can't communicate with the database.");
            die();
        }

        if (isset($data)) {
            foreach ($data as $type => $date) {
                foreach ($date as $date => $value) {
                    $data[$type][$date]["consumptionAverage"] = round($data[$type][$date]["totalConsumption"] / $data[$type][$date]["totalMachines"], 6);
                    $data[$type][$date]["uptimeAverage"] = round($data[$type][$date]["totalUptime"] / $data[$type][$date]["totalMachines"], 6);
                }
            }

            echo $this->logMessage->NewMessage("INFO", "Inserting values into database...");
            $deleteQuery = "DELETE FROM greenit_stats";
            $alterQuery = "ALTER TABLE greenit_stats AUTO_INCREMENT 0";
            $insertQuery = "INSERT INTO greenit_stats (TYPE,DATE,DATA) VALUES ('%s','%s','%s')";
            mysql2_query_secure($deleteQuery, $_SESSION['OCS']["writeServer"]);
            mysql2_query_secure($alterQuery, $_SESSION['OCS']["writeServer"]);
            foreach ($data as $type => $date) {
                foreach ($date as $date => $value) {
                    mysql2_query_secure(sprintf($insertQuery, $type, $date, json_encode($value)), $_SESSION['OCS']["writeServer"]);
                }
            }
            echo $this->logMessage->NewMessage("INFO", "Job done\n");
        } else
            echo $this->logMessage->NewMessage("ERROR", "No data");
    }
}
?>