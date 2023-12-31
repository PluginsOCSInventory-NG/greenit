<?php
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
        `CONSUMPTION` DOUBLE NOT NULL,
        `UPTIME` INTEGER NOT NULL,
        PRIMARY KEY (ID,HARDWARE_ID)) ENGINE=INNODB;"
    );

    $commonObject->sqlQuery(
        "CREATE TABLE IF NOT EXISTS `greenit_config` (
        `ID` INTEGER NOT NULL AUTO_INCREMENT,
        `COLLECT_INFO_PERIOD` INTEGER NOT NULL,
        `CONSUMPTION_ROUND` INTEGER NOT NULL,
        `COST_ROUND` INTEGER NOT NULL,
        `COST_UNIT` varchar(255) NOT NULL,
        `KILOWATT_COST` FLOAT NOT NULL,
        `UPTIME_FORMAT` VARCHAR(255) NOT NULL,
        PRIMARY KEY (ID)) ENGINE=INNODB;"
    );

    $commonObject->sqlQuery(
        "INSERT INTO `greenit_config` (COLLECT_INFO_PERIOD,CONSUMPTION_ROUND,COST_ROUND,COST_UNIT,KILOWATT_COST,UPTIME_FORMAT) VALUES ('30','5','5','€','','h-m-s');"
    );

    $commonObject->sqlQuery(
        "CREATE TABLE IF NOT EXISTS `greenit_stats` (
        `ID` INTEGER NOT NULL AUTO_INCREMENT,
        `DATE` DATE NOT NULL,
        `DATA` JSON NOT NULL,
        CHECK (JSON_VALID(DATA)),
        PRIMARY KEY (ID)) ENGINE=INNODB;"
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