<?php

require_once("utilities/config.class.php");
require_once("utilities/logMessage.class.php");
require_once("utilities/data.class.php");
require_once("utilities/calculation.class.php");
require_once("utilities/diagram.class.php");

/**
 * Abstract View model
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
abstract class View
{
    /**
     * Using Config Class to get user configuration
     */
    private Config $config;

    /**
     * Using Data Class to get database data
     */
    private Data $data;

    /**
     * Using LogMessage Class to send message when error or to inform user
     */
    private LogMessage $logMessage;

    /**
     * Using Calculation Class format or calculate consumption, uptime and cost
     */
    private Calculation $calculation;

    /**
     * Using Diagram Class to organize data into diagrams
     */
    private Diagram $diagram;

    /**
     *  List of title data for the current view
     */
    private object $titleData;

    /**
     * Generate the title HTML code of the view
     * 
     * @return void Return nothing
     */
    public function ShowTitle(): void
    {
        $this->data = new Data();
        $this->titleData = new stdClass();
        $this->titleData->greenitMachines = $this->data->GetTitleData("
            SELECT 
            COUNT(DISTINCT HARDWARE_ID) AS idCount 
            FROM greenit
        ");
        $this->titleData->greenitVirtualMachines = $this->data->GetTitleData("
            SELECT 
            COUNT(DISTINCT HARDWARE_ID) AS idCount 
            FROM greenit
            WHERE 
            CONSUMPTION = 'VM detected'
        ");
        $this->titleData->greenitTotalMachines = $this->data->GetTitleData("
            SELECT 
            COUNT(DISTINCT ID) AS idCount 
            FROM hardware 
            WHERE 
            DEVICEID <> '_SYSTEMGROUP_'
        ");

        global $l;

        printEnTete($l->g(102100));

        echo "<h5>" . $this->titleData->greenitMachines->idCount . " " . $l->g(102101) . " " . $this->titleData->greenitVirtualMachines->idCount . " " . $l->g(102102) . " " . $this->titleData->greenitTotalMachines->idCount . "</h5>";

        echo "<hr>";
    }

    /**
     * Generate the menu HTML code of the view
     * 
     * @return void Return nothing
     */
    public function ShowMenu(): void
    {
        global $protectedGet;

        $menu_serializer = new XMLMenuSerializer();
        $menu = $menu_serializer->unserialize(file_get_contents('extensions/greenit/config/menu.xml'));
        $menu_renderer = new MenuRenderer();

        echo "
            <ul class='nav nav-pills nav-stacked'>
        ";
        foreach ($menu->getChildren() as $menu_elem) {
            if (isset($protectedGet["cat"]) && $protectedGet["cat"] == explode("=", $menu_elem->getUrl())[2]) {
                echo $menu_renderer->setActiveLink($menu_elem->getUrl());
            }
            echo $menu_renderer->renderElem($menu_elem);
        }
        echo "
            </ul>
        ";
    }

    /**
     * Generate the YesterdayStats HTML code of the view
     * 
     * @return void Return nothing
     */
    abstract public function ShowYesterdayStats(): void;

    /**
     * Generate the ComparaisonStats HTML code of the view
     * 
     * @return void Return nothing
     */
    abstract public function ShowComparatorStats(): void;
}

?>