<?php
/**
 * Ce fichier permet de configurer les liens presents dans le footer
 *
 * @package openmairie_exemple
 * @version SVN : $Id: footer.inc.php 2474 2013-09-11 11:34:06Z fmichon $
 */

/**
 * $footer est le tableau associatif qui contient tous les liens presents dans
 * le footer de l'application
 *
 * Caracteristiques :
 * --- footer
 *     - title [obligatoire]
 *     - description (texte qui s'affiche au survol de l'element)
 *     - href [obligatoire] (contenu du lien href)
 *     - class (classe css qui s'affiche sur l'element)
 *     - right (droit que l'utilisateur doit avoir pour visionner cet element)
 *     - target (pour ouvrir le lien dans une nouvelle fenetre)
 */
$footer = array();

// Template
/*
$footer[] = array(
    "title" => _(""),
    "description" => _(""),
    "href" => "",
    "target" => "",
    "class" => "",
);
*/

// Documentation du site
$footer[] = array(
    "title" => _("Documentation"),
    "description" => _("Acceder a l'espace documentation de l'application"),
    "href" => "http://www.openmairie.org/documentation",
    "target" => "_blank",
    "class" => "footer-documentation",
);

// Portail openMairie
$footer[] = array(
    "title" => _("openMairie.org"),
    "description" => _("Site officiel du projet openMairie"),
    "href" => "http://www.openmairie.org/",
    "target" => "_blank",
    "class" => "footer-openmairie",
);

?>
