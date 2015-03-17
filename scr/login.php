<?php
/**
 * Ce fichier gere la connexion de l'utilisateur
 *
 * @package openmairie_exemple
 * @version SVN : $Id: login.php 45 2010-08-26 07:03:11Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("login", NULL, _("Veuillez vous connecter"));

/**
 * Formulaire d'identification
 */
$f->displayLoginForm();

?>