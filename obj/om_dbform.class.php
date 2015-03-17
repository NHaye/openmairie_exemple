<?php
/**
 * Ce fichier est destine a permettre la surcharge de certaines methodes de
 * la classe om_dbform pour des besoins specifiques de l'application
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_dbform.class.php 2045 2013-01-22 14:03:35Z jbastide $
 */

/**
 *
 */
require_once PATH_OPENMAIRIE."om_dbform.class.php";

/**
 *
 */
require_once "om_formulaire.class.php";

/**
 *
 */
class om_dbform extends dbForm {
    
    /**
     *
     */
    var $om_formulaire = "om_formulaire";


}

?>
