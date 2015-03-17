<?php
/**
 * Ce fichier est destine a permettre la surcharge de certaines methodes de
 * la classe om_application pour des besoins specifiques de l'application
 *
 * @package openmairie_exemple
 * @version SVN : $Id: utils.class.php 547 2011-08-25 12:03:42Z fmichon $
 */

/**
 *
 */
require_once "../dyn/locales.inc.php";

/**
 *
 */
require_once "../dyn/include.inc.php";

/**
 *
 */
require_once "../dyn/debug.inc.php";

/**
 *
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));

/**
 *
 */
require_once PATH_OPENMAIRIE."om_application.class.php";

/**
 *
 */
class utils extends application {

    

}

?>
