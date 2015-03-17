<?php
/**
 * Ce script permet d'interfacer le traitement d'import de données dans la base
 * de données à partir de fichiers CSV.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: import.php 2959 2014-11-19 11:59:42Z fmichon $
 */

//
require_once "../obj/utils.class.php";
$f = new utils(
    "nohtml",
    "import",
    _("administration")." -> "._("module import")
);

/**
 * 
 */
//
require_once PATH_OPENMAIRIE."om_import.class.php";
$i = new import();
//
$i->view_import();

?>
