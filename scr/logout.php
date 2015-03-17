<?php
/**
 * Ce fichier gere la deconnexion de l'utilisateur
 *
 * @package openmairie_exemple
 * @version SVN : $Id: logout.php 55 2010-08-27 12:17:00Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("logout", NULL, _("Veuillez vous connecter"));

/**
 * Formulaire d'identification
 */
$f->displayLoginForm();

?>