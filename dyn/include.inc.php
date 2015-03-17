<?php
/**
 * Ce fichier permet de configurer quels paths vont etre ajoutes a la
 * directive include_path du fichier php.ini
 *
 * @package openmairie_exemple
 * @version SVN : $Id: include.inc.php 2838 2014-08-06 14:11:39Z fmichon $
 */

/**
 * Ce tableau permet de stocker la liste des chemins a ajouter a la directive
 * include_path, vous pouvez modifier ces chemins avec vos propres valeurs si
 * vos chemins ne sont pas deja inclus dans votre installation, par contre si
 * vous avez deja configurer ces chemins dans votre installation vous pouvez
 * commenter les lignes suivantes
 */
$include = array();

/**
 * @todo Remplacer 'getcwd()."/../"' par un moyen plus generique de recuperer
 *       le chemin racine de l'application
 */
// PEAR
array_push($include, getcwd()."/../php/pear");

// DB
array_push($include, getcwd()."/../php/db");

// FPDF
array_push($include, getcwd()."/../php/fpdf");

// PHPMAILER
array_push($include, getcwd()."/../php/phpmailer");

// OPENMAIRIE
//array_push($include, getcwd()."/../php/openmairie");
define("PATH_OPENMAIRIE", getcwd()."/../core/");

/**
 * Ici on modifie la valeur de la directive de configuration include_path en
 * fonction du parametrage present dans le fichier dyn/include.inc.php
 */
if (isset($include)) {
    set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR, $include));
}

?>
