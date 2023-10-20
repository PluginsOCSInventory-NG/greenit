<?php

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