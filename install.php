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

/**
 * The following functions are used by the extension engine to generate a new table
 * for the plugin / destroy it on removal.
 */


/**
 * This function is called on installation and is used to
 * create database schema for the plugin
 */
function extension_install_greenit()
{
    $commonObject = new ExtensionCommon;

    $commonObject->sqlQuery(
        "CREATE TABLE IF NOT EXISTS `greenit` (
        `ID` INTEGER NOT NULL AUTO_INCREMENT,
        `HARDWARE_ID` INTEGER NOT NULL,
        `DATE` DATE NOT NULL,
        `CONSUMPTION` VARCHAR(255) NOT NULL,
        `UPTIME` INTEGER NOT NULL,
        PRIMARY KEY (ID,HARDWARE_ID)) ENGINE=INNODB;"
    );

    $commonObject->sqlQuery(
        "CREATE INDEX DATE_INDEX ON `greenit`(DATE);"
    );
    $commonObject->sqlQuery(
        "CREATE INDEX HARDWARE_ID_INDEX ON `greenit`(HARDWARE_ID);"
    );

    $commonObject->sqlQuery(
        "CREATE TABLE IF NOT EXISTS `greenit_config` (
        `ID` INTEGER NOT NULL AUTO_INCREMENT,
        `COLLECT_INFO_PERIOD` INTEGER NOT NULL,
        `COMPARE_INFO_PERIOD` INTEGER NOT NULL,
        `CONSUMPTION_ROUND` INTEGER NOT NULL,
        `COST_ROUND` INTEGER NOT NULL,
        `COST_UNIT` varchar(255) NOT NULL,
        `UPTIME_FORMAT` VARCHAR(255) NOT NULL,
        `API_KEY` VARCHAR(255) NOT NULL,
        `CONSUMPTION_TYPE` VARCHAR(255) NOT NULL,
        PRIMARY KEY (ID)) ENGINE=INNODB;"
    );

    $commonObject->sqlQuery(
        "INSERT INTO `greenit_config` (COLLECT_INFO_PERIOD,COMPARE_INFO_PERIOD,CONSUMPTION_ROUND,COST_ROUND,COST_UNIT,UPTIME_FORMAT,API_KEY,CONSUMPTION_TYPE) VALUES ('30','365','2','2','€','h-m-s', '', 'PX_ELE_I_TTES_TRANCHES');"
    );

    $commonObject->sqlQuery(
        "CREATE TABLE IF NOT EXISTS `greenit_stats` (
        `ID` INTEGER NOT NULL AUTO_INCREMENT,
        `TYPE` VARCHAR(255) NOT NULL, 
        `DATE` DATE NOT NULL,
        `DATA` JSON NOT NULL,
        CHECK (JSON_VALID(DATA)),
        PRIMARY KEY (ID)) ENGINE=INNODB;"
    );

    $commonObject->sqlQuery(
        "CREATE INDEX DATE_INDEX ON `greenit_stats`(DATE);"
    );

    $commonObject->sqlQuery(
        "CREATE INDEX TYPE_INDEX ON `greenit_stats`(TYPE);"
    );
}

/**
 * This function is called on removal and is used to
 * destroy database schema for the plugin
 */
function extension_delete_greenit()
{
    $commonObject = new ExtensionCommon;
    $commonObject->sqlQuery("DROP TABLE IF EXISTS `greenit`");
    $commonObject->sqlQuery("DROP TABLE IF EXISTS `greenit_config`");
    $commonObject->sqlQuery("DROP TABLE IF EXISTS `greenit_stats`");
}

/**
 * This function is called on plugin upgrade
 */
function extension_upgrade_greenit()
{

}

?>