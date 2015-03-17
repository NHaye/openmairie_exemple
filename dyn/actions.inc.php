<?php
/**
 * Ce fichier permet de configurer les liens presents dans les actions
 *
 * @package openmairie_exemple
 * @version SVN : $Id: actions.inc.php 2471 2013-09-11 10:17:45Z fmichon $
 */

/**
 * $actions est le tableau associatif qui contient tous les liens presents dans
 * les actions a cote du login et du nom de la collectivite
 *
 * Caracteristiques :
 * --- action
 *     - title [obligatoire]
 *     - description (texte qui s'affiche au survol de l'element)
 *     - href [obligatoire] (contenu du lien href)
 *     - class (classe css qui s'affiche sur l'element)
 *     - right (droit que l'utilisateur doit avoir pour visionner cet element)
 *     - target (pour ouvrir le lien dans une nouvelle fenetre)
 */
$actions = array();

// Template
/*
$actions[] = array(
    "title" => _("title"),
    "description" => _("description"),
    "href" => "",
    "target" => "",
    "class" => "",
    "right" => "",
);
*/

// Mot de passe
$actions[] = array(
    "title" => _("Mot de passe"),
    "description" => _("Changer votre mot de passe"),
    "href" => "../scr/password.php",
    "class" => "actions-password",
    "right" => "password",
);

// Deconnexion
$actions[] = array(
    "title" => _("Deconnexion"),
    "description" => _("Quitter l'application"),
    "href" => "../scr/logout.php",
    "class" => "actions-logout",
);

?>
