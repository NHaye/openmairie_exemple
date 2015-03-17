<?php
/**
 * Ce fichier permet de configurer le stockage des fichiers sur le filesystem
 *
 * @package openmairie_exemple
 * @version SVN : $Id: filestorage.inc.php 2492 2013-09-20 11:59:26Z fmichon $
 */

/**
 *
 */
$filestorage = array();


$filestorage["filestorage-default"] = array (
    "storage" => "filesystem", // l'attribut storage est obligatoire
    "path" => "../trs/1/", // le repertoire de stockage
    "temporary" => array(
        "storage" => "filesystem", // l'attribut storage est obligatoire
        "path" => "../tmp/", // le repertoire de stockage
    ),
);

?>
