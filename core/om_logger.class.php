<?php
/**
 * Ce fichier contient ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_logger.class.php 2476 2013-09-17 08:34:43Z fmichon $
 */

/**
 *
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
require_once PATH_OPENMAIRIE."om_locales.inc.php";
require_once PATH_OPENMAIRIE."om_debug.inc.php";
(defined("DEBUG") ? "" : define("DEBUG", PRODUCTION_MODE));

/**
 *
 */
class logger {

    /**
     *
     */
    private static $_instance;

    /**
     *
     */
    private function __construct() {
        $this->path = getcwd();
    }

    /**
     *
     */
    static function instance() {
        //
        if (!isset(self::$_instance)) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }
        //
        return self::$_instance;
    }

    /**
     * Prevent users to clone the instance
     */
    public function __clone() {
        throw new Exception('Cannot clone the logger object.');
    }

    //
    var $types_to_show = array(
        DEBUG_MODE => "DEBUG",
        VERBOSE_MODE => "VERBOSE",
        EXTRA_VERBOSE_MODE => "EXTRA_VERBOSE",
    );

    /**
     *
     */
    public function log($message = "", $type = DEBUG_MODE) {

        //
        array_push($this->storage,
            array(
                "message" => $message,
                "type" => $type,
                "date" => date("\nY-m-d H:i:s"),
            )
        );

    }

    /**
     *
     */
    var $storage = array();

    /**
     *
     */
    var $display_log = true;

    /**
     *
     */
    function displayLog() {
        //
        if ($this->display_log == true
            && DEBUG > PRODUCTION_MODE
            && count($this->storage) > 0) {
            //
            echo "\n<div class=\"log-box\">\n";
            //
            echo "<fieldset class=\"cadre ui-widget-content ui-corner-all\">\n";
            //
            echo "<legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
            echo _("Logger");
            echo "</legend>\n";
            //
            echo "<div class=\"even\"><span class=\"url\">".htmlentities($_SERVER['REQUEST_URI'])."</span></div>\n";
            foreach($this->storage as $key => $log) {
                //
                if (DEBUG >= $log["type"] && in_array($log["type"], array_keys($this->types_to_show))) {
                    //
                    echo "<div class=\"".($key % 2 == 0 ? "odd" : "even")."\">";
                    echo "<span class=\"".strtolower($this->types_to_show[$log["type"]])."\">";
                    echo "<span class=\"message\">".$log["message"]."</span>";
                    echo "&nbsp;";
                    echo "<span class=\"type\">".$this->types_to_show[$log["type"]]."</span>";
                    echo "</span>";
                    echo "</div>\n";
                }
            }
            //
            echo "</fieldset>\n";
            //
            echo "</div>\n";
        }
    }

    /**
     * Cette méthode est dépréciée et ne doit plus être utilisée.
     */
    function writeLogToFile() {
        //
    }
    
    /**
     * Cette méthode permet d'écrire tous les messages de log de type
     * DEBUG_MODE dans le fichier ../tmp/error_log.txt peu importe
     * le niveau de log configuré dans le fichier ../dyn/debug.inc.php.
     * Attention si le fichier ../tmp/error_log.txt ne peut pas être écrit
     * aucune erreur n'est levée.
     */
    function writeErrorLogToFile() {
        //
        $to_write = "";
        //
        foreach($this->storage as $key => $log) {
            //
            if ($log["type"] == DEBUG_MODE) {
                //
                $to_write .= $log["date"]." ".$log["message"]." [".$this->types_to_show[$log["type"]]."]";
            }
        }
        if ($to_write != "") {
            //
            @$fp = fopen($this->path."/../tmp/error_log.txt", "a");
            //
            if ($fp != false) {
                //
                fwrite($fp, date("\nY-m-d H:i:s")." ERROR [".(isset($_SESSION["login"]) ? $_SESSION["login"] : ".")."] ".$_SERVER["REQUEST_URI"]."");
                fwrite($fp, $to_write);
                fwrite($fp, "\n\n");
                //
                fclose($fp);
            }
        }
    }
    
    /**
     * Vide le contenu de l'attribut storage
     */
    function cleanLog() {
        unset($this->storage);
        $this->storage = array();
    }

}

?>
