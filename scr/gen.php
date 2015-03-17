<?php
/**
 * Ce script permet d'interfacer les actions possibles avec le générateur et 
 * avec les assistants.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: gen.php 3045 2015-02-13 16:12:52Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils(
    null,
    "gen",
    _("administration")." -> "._("generateur")
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

// On instancie l'utilitaire de génération
require_once PATH_OPENMAIRIE."om_gen.class.php";
$g = new gen();

if (isset($_GET["view"]) && $_GET["view"] == "permissions") {
    //
    $g->view_gen_permissions();
    die();
}


/**
 * Page - Start
 */
// Ouverture du container de la page
echo "\n<div id=\"generator\">\n";

/**
 * Génération basée sur les tables de la base de données.
 */
// // On instancie l'utilitaire de génération
// require_once PATH_OPENMAIRIE."om_gen.class.php";
// $g = new gen();
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
// Composition de la liste de liens vers les éditions disponibles.
// En partant de la liste d'éditions disponibles, on compose une liste 
// d'éléments composés d'une URL, d'un libellé, et de tous les paramètres 
// permettant l'affichage de l'élément comme un élément de liste.
$list = array();
//
$list[] = array(
    "href" => "../scr/genfull.php",
    "title" => _("generer tout"),
    "class" => "om-prev-icon",
    "description" => _("Cela aura pour effet d'ecraser tous les fichiers existants du repertoire gen/ et creer les fichiers dans core/, sql/ et obj/ s'ils n'existent pas."),
    "id" => "gen-action-gen-all"
);
//
foreach ($tables as $key => $value) {
    //
    $links = array(
        array(
            "href" => "../scr/gensup.php?table=".$value,
            "title" => _("supprimer"),
            "class" => "om-icon om-icon-right om-icon-25 delete-25",
            "description" => _("supprimer"),
            "id" => "gen-action-delete-".$value,
        ),
        array(
            "href" => "../scr/genauto.php?table=".$value,
            "title" => _("generer"),
            "class" => "om-icon om-icon-right om-icon-25 generate-25",
            "description" => _("generer"),
            "id" => "gen-action-generate-".$value,
        ),
    );
    //
    $list[] = array(
        "title" => $value,
        "links" => $links,
    );
}
//
$f->layout->display_list(
    array(
        "title" => _("generation basee sur les tables de la base de donnees"),
        "list" => $list,
        "class" => "collapsible"
    )
);

/**
 * Assistants permettant la creation d'etats, sous etats, lettres types ou
 * la migration/l'import de ces mêmes éléments depuis des anciennes versions
 * d'openMairie.
 */
// On définit les différents assistants disponibles
$assistants = array(
    0 => array(
        "href" => "../scr/gen.php?view=permissions",
        "title" => _("Génération des permissions"),
    ),
    1 => array(
        "href" => "../scr/genimport.php",
        "title" => _("Migration etat, sous etat, lettre type"),
    ),
    2 => array(
        "href" => "../scr/genetat.php",
        "title" => _("Creation etat"),
    ),
    3 => array(
        "href" => "../scr/gensousetat.php",
        "title" => _("Creation sous etat"),
    ),
    4 => array(
        "href" => "../scr/genlettretype.php",
        "title" => _("Creation lettre type"),
    ),
);
// Composition de la liste de liens vers les éditions disponibles.
// En partant de la liste d'éditions disponibles, on compose une liste 
// d'éléments composés d'une URL, d'un libellé, et de tous les paramètres 
// permettant l'affichage de l'élément comme un élément de liste.
$list = array();
foreach ($assistants as $key => $value) {
    //
    $list[] = array(
        "href" => $value["href"],
        "title" => $value["title"],
        "class" => "om-prev-icon wizard-16",
    );
}
//
$f->layout->display_list(
    array(
        "title" => _("assistants"),
        "list" => $list,
    )
);

/**
 * Page - End
 */
// Fermeture du container de la page
echo "</div>\n";

?>
