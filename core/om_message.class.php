<?php
/**
 * Ce fichier permet de déclarer la classe message.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_message.class.php 2736 2014-03-07 14:27:48Z fmichon $
 */

/**
 *
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
require_once PATH_OPENMAIRIE."om_debug.inc.php";
(defined("DEBUG") ? "" : define("DEBUG", PRODUCTION_MODE));

/**
 *
 */
class message {

    /**
     *
     * @var array
     */
    var $message = array();

    /**
     *
     * @return void
     */
    function addToMessage($class = "", $message = "") {

        array_push($this->message,
                   array('class' => $class,
                         'message' => $message));

    }

    /**
     *
     * @return void
     */
    function displayMessage($class = "", $message = "") {

        //
        if ($class == "ok") {
            $class = "valid";
        }
        //
        echo "\n<div class=\"message ui-widget ui-corner-all ui-state-highlight ui-state-".$class."\">\n";
        echo "<p>\n";
        echo "\t<span class=\"ui-icon ui-icon-info\"><!-- --></span> \n\t";
        echo "<span class=\"text\">";
        echo $message;
        echo "</span>";
        echo "\n</p>\n";
        echo "</div>\n";

    }

    /**
     *
     * @return void
     */
    function displayMessages() {
        foreach ($this->message as $message) {
            $this->displayMessage($message['class'], $message['message']);
        }
        $this->message = array();
    }

}

?>
