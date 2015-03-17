<?php
/**
 * Ce fichier contient les parametres d'encodage et d'internationalisation
 *
 * @package openmairie_exemple
 * @version SVN : $Id: locales.inc.php 2376 2013-06-11 09:14:57Z fmichon $
 */

/**
 * Codage des caracteres
 */
define('DBCHARSET', 'UTF8');
define('HTTPCHARSET', 'UTF-8');

/**
 * Pour voir les autres locales disponibles, il faut voir le contenu du dossier
 * locales/ et il faut que cette locale soit installee sur votre systeme
 */
define('LOCALE', 'fr_FR');

/**
 * Le dossier contenant les locales et les fichiers de traduction
 */
define('LOCALES_DIRECTORY', '../locales');

/**
 * Le domaine de traduction
 */
define('DOMAIN', 'openmairie');

?>
