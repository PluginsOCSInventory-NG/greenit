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
 * Used to debug or inform user with messages
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
class LogMessage
{
    /**
     * List of message type can be used by the class
     */
    private $logType = array(
        "INFO",
        "WARNING",
        "ERROR"
    );

    /**
     * Constructor of the class which define everything the view need to work
     */
    function __construct()
    {
    }

    /**
     * List of color can be generate by the class
     * 
     * @param string $type Define the type of the message
     * @param string $message Define the message
     * 
     * @return string|false Return the formated string message or false if the type isn't correct
     */
    public function NewMessage(string $type, string $message): string|false
    {
        foreach ($this->logType as $value) {
            if ($type == $value) {
                return "[" . $value . "] " . $message . "\n";
            }
        }
        return false;
    }
}

?>