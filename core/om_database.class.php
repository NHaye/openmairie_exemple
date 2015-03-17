<?php
/**
 * Ce fichier permet de declarer la classe database
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_database.class.php 2476 2013-09-17 08:34:43Z fmichon $
 */

/**
 *
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
require_once PATH_OPENMAIRIE."om_debug.inc.php";
(defined("DEBUG") ? "" : define("DEBUG", PRODUCTION_MODE));
require_once PATH_OPENMAIRIE."om_logger.class.php";
require_once PATH_OPENMAIRIE."om_message.class.php";

/**
 *
 */
require_once "DB.php";

/**
 *
 */
class database extends DB {

    /**
     *
     */
    function isError($resource = NULL, $forcereturn = false) {
        
        //
        if (!DB::isError($resource)) {
            return false;
        }
        
        // Logger
        if (method_exists($this, "addToLog")) {
            $prefix = "";
            if (function_exists("get_called_class")) {
                $prefix = "class ".get_called_class();
            }
            $temp = explode('[', $resource->getDebugInfo());
            if (trim($temp[0]) != "") {
                logger::instance()->log($prefix." - database::isError(): QUERY => ".$temp[0], DEBUG_MODE);
            }
            logger::instance()->log($prefix." - database::isError(): SGBD ERROR => ".substr($temp[1], 0, strlen($temp[1])-1), DEBUG_MODE);
            logger::instance()->log($prefix." - database::isError(): PEAR ERROR => ".$resource->getMessage(), DEBUG_MODE);
        }
        
        //
        if ($forcereturn == true) {
            return true;
        }
        
        //
        $m = new message();
        
        //
        $class = "error";
        $message = _("Erreur de base de donnees. Contactez votre administrateur.");
        $m->displayMessage($class, $message);
        
        //
        echo "</div>";
        die();
        
    }
    
}

?>
