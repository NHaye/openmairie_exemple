<?php
/**
 * Ce fichier contient ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_locales.inc.php 2476 2013-09-17 08:34:43Z fmichon $
 */

/**
 * Definition du charset pour la base de donnees et pour le web
 */
//CHARSET est remplace par DBCHARSET et HTTPCHARSET
//Un mecanisme de compatibilite est conserve, mais la coherence est faible car
//le nom du charset est different selon la base de donnees
//et il est toujours different entre base de donnees et web
//
//compatibilite
//(defined("CHARSET") ? "" : define("CHARSET", 'ISO-8859-1'));
(defined("CHARSET") ? "" : define("CHARSET", 'UTF-8'));
(!defined("DBCHARSET") and CHARSET!="UTF8" ? define("DBCHARSET", CHARSET):"");
(!defined("HTTPCHARSET") and CHARSET!="UTF8" ? define("HTTPCHARSET", CHARSET):"");
//definitions des valeurs par defaut
(defined("DBCHARSET") ? "" : define("DBCHARSET", 'UTF8'));
(defined("HTTPCHARSET") ? "" : define("HTTPCHARSET", 'UTF-8'));
(defined("LOCALE") ? "" : define("LOCALE", 'fr_FR'));
(defined("LOCALES_DIRECTORY") ? "" : define("LOCALES_DIRECTORY", '../locales'));
(defined("DOMAIN") ? "" : define("DOMAIN", 'openmairie'));


/**
 *
 */
function setMyLocale() {
    //
    putenv("LC_ALL=".LOCALE.".".HTTPCHARSET);
    setlocale(LC_ALL, LOCALE.".".HTTPCHARSET);
    bindtextdomain(DOMAIN, LOCALES_DIRECTORY);
    textdomain(DOMAIN);
}

/**
 *
 */
if (!function_exists("_")) {
    function _($msgid) {
        return $msgid;
    }
} else {
    setMyLocale();
}

?>
