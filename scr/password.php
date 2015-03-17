<?php
/**
 * Ce fichier permet d'aficher un formulaire de changement de mot de passe de
 * l'utilisateur et de traiter les resultats en les validant dans la base de
 * donnees
 *
 * @package openmairie_exemple
 * @version SVN : $Id: password.php 2240 2013-04-04 15:44:16Z jbastide $
 */

require_once "../obj/utils.class.php";
$f = new utils(NULL, "password", _("Mon compte")." -> "._("Mot de passe"));

/**
 * Description de la page
 */
$description = _("Cette page vous permet de changer votre mot de passe. Pour ".
                 "cela, il vous suffit de saisir votre mot de passe ".
                 "actuel puis votre nouveau mot de passe deux fois.");
$f->displayDescription($description);

/**
 * Affichage en onglet
 */
//
echo "<div id=\"formulaire\">\n\n";
//
$f->layout->display_start_navbar();
echo "<ul>";
echo "<li><a ";
echo " href=\"#tabs-1\">"._("Mot de passe")."</a></li>";
echo "</ul>\n";
 $f->layout->display_stop_navbar();
/**
 * Onglet changement du mot de passe
 */
//
echo "<div id=\"tabs-1\">\n";
// Traitement si validation du formulaire
if (isset($_POST['submit-change-password'])) {
    
    // Recuperation des valeurs du formulaire
    $current_password = md5($_POST['current-password']);
    $new_password = $_POST['new-password'];
    $new_password_confirmation = $_POST['new-password-confirmation'];
    
    // Verification du mot de passe actuel de l'utilisateur
    $authenticated = $f->processDatabaseAuthentication($_SESSION['login'], $_POST['current-password']);

    // Si la saisie n'est pas correcte on affiche un message d'erreur sinon
    // on change le mot de passe
    if ($authenticated == false) {
        
        // Affichage du message d'erreur
        $class = "error";
        $message = _("Mot de passe actuel incorrect");
        $f->displayMessage($class, $message);
        
    } elseif ($new_password != $new_password_confirmation or $new_password == "") {
        
        // Affichage du message d'erreur
        $class = "error";
        $message = _("Nouveau mot de passe incorrect");
        $f->displayMessage($class, $message);
        
    } else {
        
        // Changement du mot de passe
        $f->changeDatabaseUserPassword($_SESSION['login'], $new_password);
        
        // Affichage du message de validation
        $class = "ok";
        $message = _("Votre mot de passe a ete change correctement");
        $f->displayMessage($class, $message);
        
    }
    
}

/**
 *
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
require_once PATH_OPENMAIRIE."om_formulaire.class.php";

// Affichage du formulaire de changement de mot de passe
echo "\n<div id=\"form-change-password\" class=\"formulaire\">\n";
echo "<form action=\"../scr/password.php\" method=\"post\">\n";
//
$validation = 0;
$maj = 0;
$champs = array("current-password", "new-password", "new-password-confirmation");
//
$form = new formulaire(NULL, $validation, $maj, $champs);
//
$form->setLib("current-password", _("Mot de passe actuel"));
$form->setType("current-password", "password");
$form->setTaille("current-password", 20);
$form->setMax("current-password", 20);
//
$form->setLib("new-password", _("Nouveau mot de passe"));
$form->setType("new-password", "password");
$form->setTaille("new-password", 20);
$form->setMax("new-password", 20);
//
$form->setLib("new-password-confirmation", _("Confirmation du nouveau mot de passe"));
$form->setType("new-password-confirmation", "password");
$form->setTaille("new-password-confirmation", 20);
$form->setMax("new-password-confirmation", 20);
//
$form->entete();
$form->afficher($champs, $validation, false, false);
$form->enpied();
//
echo "\t<div class=\"formControls\">";
$f->layout->display_password_input_submit();
echo "</div>\n";
//
echo "</form>\n";
echo "</div>\n";
//
echo "</div>\n";

/**
 * Fin de l'onglet changement du mot de passe
 */
//
echo "\n</div>\n";

?>