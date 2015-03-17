<?php
/**
 * Ce fichier permet de configurer les liens presents dans la barre de
 * raccourcis presente en dessous des actions
 *
 * @package openmairie_exemple
 * @version SVN : $Id: shortlinks.inc.php 2472 2013-09-11 10:23:52Z fmichon $
 */

/**
 * $shortlinks est le tableau associatif qui contient tous les liens presents
 * dans les raccourcis en dessous des actions
 *
 * Caracteristiques :
 * --- shortlink
 *     - title [obligatoire]
 *     - description (texte qui s'affiche au survol de l'element)
 *     - href [obligatoire] (contenu du lien href)
 *     - class (classe css qui s'affiche sur l'element)
 *     - right (droit que l'utilisateur doit avoir pour visionner cet element)
 *     - target (pour ouvrir le lien dans une nouvelle fenetre)
 */
$shortlinks = array();

// Template
/*
$shortlinks[] = array(
    "title" => _("title"),
    "description" => _("description"),
    "href" => "",
    "target" => "",
    "class" => "",
    "right" => "",
);
*/

// Tableau de bord
$shortlinks[] = array(
    "title" => _("Tableau de bord"),
    "description" => _("Acceder a la page d'accueil de l'application"),
    "href" => "../scr/dashboard.php",
    "class" => "shortlinks-dashboard",
);

?>
