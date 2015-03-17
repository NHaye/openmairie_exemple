<?php
/**
 * Ce script permet d'interfacer la synchronisation des utilisateurs entre 
 * l'application et un annuaire.
 *
 * Pour utiliser ce script il faut avant tout configurer la connexion a
 * l'annuaire ainsi que la correspondance des champs avec les colonnes de la
 * base dans le fichier de paramétrage  dyn/directory.inc.php.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: directory.php 2730 2014-03-07 10:35:39Z fmichon $
 */

//
require_once "../obj/utils.class.php";
$f = new utils(
    null,
    "directory",
    _("Annuaire")
);

//
$description = _("Cette page vous permet de synchroniser vos utilisateurs ".
                 "depuis un annuaire.");
//
$f->displayDescription($description);

// On recupere les mouvements a effectuer
$results = $f->initSynchronization();

//
if ($results != NULL) {
    //
    if (isset($_POST['submit-directory'])) {
        $f->synchronizeUsers($results);
    } else {
        //
        echo "\n<div id=\"directory\" class=\"formulaire\">\n";
        //
        echo "<fieldset class=\"cadre ui-corner-all ui-widget-content\">\n";
        //
        echo "\t<legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
        echo _("Synchronisation des utilisateurs");
        echo "</legend>\n";
        //
        echo "<form action=\"../scr/directory.php\" method=\"post\" name=\"f1\">\n";
        //
        echo _("Il y a")." ".count($results['userToAdd'])." ";
        echo _("utilisateur(s) present(s) dans l'annuaire et non present(s) dans la base");
        echo " => ".count($results['userToAdd'])." "._("ajout(s)");
        //
        echo "<br/>";
        //
        echo _("Il y a")." ".count($results['userToDelete'])." ";
        echo _("utilisateur(s) present(s) dans la base et non present(s) dans l'annuaire");
        echo " => ".count($results['userToDelete'])." "._("suppression(s)");
        //
        echo "<br/>";
        //
        echo _("Il y a")." ".count($results['userToUpdate'])." ";
        echo _("utilisateur(s) present(s) a la fois dans la base et l'annuaire");
        echo " => ".count($results['userToUpdate'])." "._("mise(s) a jour");
        //
        echo "\n<!-- ########## START FORMCONTROLS ########## -->\n";
        echo "<div class=\"formControls\">\n";
        echo "<input type=\"submit\" name=\"submit-directory\" value=\""._("Synchroniser")."\" class=\"boutonFormulaire\" />\n";
        // Lien retour
        $f->layout->display_lien_retour(array(
            "href" => $f->url_dashboard,
        ));
        echo "</div>\n";
        echo "<!-- ########## END FORMCONTROLS ########## -->\n";
        //
        echo "</form>\n";
        //
        echo "</fieldset>\n";
        echo "</div>\n";
    }
}

?>
