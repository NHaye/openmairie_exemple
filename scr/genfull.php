<?php
/**
 * Ce script permet d'interfacer le traitement de génération complète automatique 
 * des fichiers de toutes les tables.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: genfull.php 2917 2014-10-09 12:52:39Z fmichon $
 */

//
require_once "../obj/utils.class.php";
$f = new utils(
    null,
    "gen",
    _("administration")." -> "._("generateur")." -> "._("generation complete")
);

//
$title = _("base de donnees")." ".OM_DB_PHPTYPE." '";
$title .= (OM_DB_SCHEMA == "" ?"":OM_DB_SCHEMA.".").OM_DB_DATABASE."'";
$f->layout->display_page_title_subtext($title);

//
if (OM_DB_PHPTYPE != "pgsql") {
    //
    $message = _(
        "Le generateur ne prend pas en charge le type de base de donnees 
        utilise."
    );
    $f->displayMessage("error", $message);
    //
    die();
}

/**
 * Page - Start
 */
// Ouverture du container de la page
echo "\n<div id=\"generator\">\n";

/**
 * Récupération de la liste des tables à générer
 */
// On instancie l'utilitaire de génération
require_once PATH_OPENMAIRIE."om_gen.class.php";
$g = new gen();
// On récupère le paramètre si le fichier de paramétrage existe qui permet
// de ne pas générer des tables non souhaitées
$tables_to_avoid = array();
if (file_exists("../gen/dyn/gen.inc.php")) {
    include "../gen/dyn/gen.inc.php";
}
// On récupère la liste des tables de la base de données à laquelle on
// enlève les tables à éviter récupérées du paramétrage
$tables = array_diff(
    $g->get_all_tables_from_database(),
    $tables_to_avoid
);
//
foreach ($tables as $table) {
    //
    $title = "-> "._("table")." '".$table."'";
    $f->displaySubTitle($title);
    // Classe gen
    $g = new gen();
    $g->init_generation_for_table($table);
    $params = $g->get_gen_parameters();
    //
    if ($g->is_generable() == true) {
        // On intialise le marqueur d'erreur à false avant de lancer la
        // boucle de génération
        $rightError = false;
        // On boucle sur chaque fichier à générer
        foreach ($params as $key => $param) {
            // Si le fichier doit être généré (checked = true) 
            // ou seulement si il n'existe pas (notexist)
            if ($param["checked_generate"] === true 
                or ($param["checked_generate"] === "not_exists" 
                    and !file_exists($param["path"]))
            ) {
                // On écrit le fichier sur le disque
                if (!isset($param["method_param"])) {
                    $result = $g->ecrirefichier(
                        $param["path"],
                        $g->{$param["method"]}()
                    );
                } else {
                    //
                    $result = $g->ecrirefichier(
                        $param["path"],
                        $g->{$param["method"]}($param["method_param"])
                    );
                }
                // Si une erreur s'est produite pendant l'écriture du 
                // fichier sur le disque alors on positionne le marqueur
                // d'erreur à true
                if (!$result) {
                    $rightError = true;
                }
            }
        }
        // Affichage du message des erreurs de droits d'ecriture
        if ($rightError) {
            $f->displayMessage(
                "error",
                _("Erreur de droits d'ecriture lors de la generation des fichiers")
            );
        }
        // Affichage du message de fin de traitement
        $f->displayMessage("valid", $g->msg);
    }
}

/**
 * Page - End
 */
// Lien retour
$f->layout->display_lien_retour(array(
    "href" => "../scr/gen.php",
));
// Fermeture du container de la page
echo "</div>\n";

?>
