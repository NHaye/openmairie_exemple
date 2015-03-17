<?php
/**
 * Ce script permet d'interfacer le traitement de génération des fichiers 
 * d'une table.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: genauto.php 2926 2014-10-13 13:20:24Z fmichon $
 */

//
require_once "../obj/utils.class.php";
$f = new utils(
    null,
    "gen",
    _("administration")." -> "._("generateur")." -> "._("generation")
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

//
(isset($_GET["table"]) ? $table = $_GET["table"] : $table = "");
$title = "-> "._("table")." '".$table."'";
$f->displaySubTitle($title);

/**
 * Page - Start
 */
// Ouverture du container de la page
echo "\n<div id=\"generator\">\n";

/**
 *
 */
// On instancie l'utilitaire de génération
require_once PATH_OPENMAIRIE."om_gen.class.php";
$g = new gen();
//
$g->init_generation_for_table($table);
$params = $g->get_gen_parameters();

/**
 * Si la table n'est pas générable alors on arrête le script
 */
if ($g->is_generable() != true) {
    // Fermeture du container de la page
    echo "</div>\n";
    // Arrêt du script
    die();
}

/**
 * TRAITEMENT DE GENERATION
 */
// Traitement si validation du formulaire
if (isset($_POST["valid_gen_generer"])) {
    //
    foreach ($params as $key => $param) {
        //
        if (isset($_POST[$key])) {
            //
            if (!isset($param["method_param"])) {
                $g->ecrirefichier(
                    $param["path"],
                    $g->{$param["method"]}()
                );
            } else {
                //
                $g->ecrirefichier(
                    $param["path"],
                    $g->{$param["method"]}($param["method_param"])
                );
            }
        }
    }
    // Affichage du message de validation du traitement
    $f->displayMessage("valid", $g->msg);
}

/**
 * Affichage du bloc de l'analyse de la table 
 */
$g->display_analyse();

/**
 * Affichage du bloc de sélection des fichiers à générer
 */
//
$f->layout->display_start_fieldset(array(
    "fieldset_class" => "collapsible",
    "legend_content" => _("selection des fichiers"),
));
// Ouverture de la balise formulaire
echo "<form method=\"post\" action=\"genauto.php?table=".$table."\" name=\"f1\">\n";
// Ouverture de la balise table
$param = array(
    'idcolumntoggle' => "generer"
);
$f->layout->display_table_start_class_default($param);
$array_entete_gen = array("selection","Nom Fichier","generer");
echo "<thead>\n";
echo "<tr class=\"ui-tabs-nav ui-accordion ui-state-default tab-title\">\n";
$param = array(
            "key" => 0,
            "info" =>  $array_entete_gen
     );
$f->layout->display_table_cellule_entete_colonnes($param);
echo "&nbsp;";
echo "</th>";
$param = array(
            "key" => 1,
            "info" => $array_entete_gen
     );
$f->layout->display_table_cellule_entete_colonnes($param);
echo _("fichier");
echo "</th>";
$param = array(
            "key" => 0,
            "info" => $array_entete_gen
     );
$f->layout->display_table_cellule_entete_colonnes($param);
echo _("informations");
echo "</th>\n";
echo "</tr>\n";
echo "</thead>\n";
//
$rubrik = null;
// On boucle sur chaque fichier à générer
foreach ($params as $key => $param) {
    //
    if (isset($param["rubrik"])
        && $param["rubrik"] != $rubrik) {
        //
        $rubrik = $param["rubrik"];
        $g->affichetitre("<span class=\"bold\">"._($rubrik)."</span>");
    }
    // XXX
    $path_to_file = $param["path"];
    // On récupère le répertoire du fichier à générer
    $path_to_folder = $g->getPathFromFile($path_to_file);
    // XXX
    $disabled = false;
    $check = false;
    // Si l'attribut "checked" est defini a true ou que l'attribut 
    // check est défini à "notexist" et que le fichier n'existe pas
    // => Alors la case sera cochée par défaut
    if ($param["checked_generate"] === true 
        or ($param["checked_generate"] === "not_exists" 
            and !file_exists($path_to_file))
    ) {
        $check = true;
    }
    // Si le fichier existe et qu'on a pas les droits d'écriture sur le 
    // fichier ou que le fichier n'existe pas et qu'on a pas le droit 
    // d'écrire dans le répertoire du fichier à générer
    // => Alors on affiche la case comme décochée et on la désactive 
    // (impossible pour l'utilisateur de la cocher)
    if ((!is_writable($path_to_file)
         and file_exists($path_to_file))
        or (!file_exists($path_to_file)
            and !is_writable($path_to_folder))
    ) {
        $check = false;
        $disabled = true;
    }
    // On construit la case à cocher
    $box = "<input type=\"checkbox\" name=\"".$key."\"";
    $box .= ($check ? " checked=\"checked\"" : "");
    $box .= ($disabled ? " disabled=\"disabled\"" : "");
    $box .= " class=\"champFormulaire\" />";
    //
    if (file_exists($path_to_file)) {
        $link_file = "<a href=\"javascript:genaff('".$path_to_file."')\">";
        $link_file .= $path_to_file;
        $link_file .= "</a>";
    } else {
        $link_file = $path_to_file;
    }
    // On récupère les infos sur le fichier
    $msg = $g->returnFSRightOnFile($path_to_file);
    // On affiche les éléments ci-dessus
    $g->affichecol($box, $link_file, $msg);
}
// Fermeture de la balise table
echo "</table>\n";
// Affichage des actions de controles du formulaire
echo "<div class=\"formControls\">";
// Bouton de validation du formulaire
$f->layout->display_form_button(array(
    "value" => sprintf(_("generer les fichiers de la table : %s"), $table),
    "name" => "valid.gen.generer",
));
// Fermeture du conteneur des actions de controles du formulaire
echo "</div>";
// Fermeture de la balise formulaire
echo "\n</form>\n";
// Fermeture du fieldset
$f->layout->display_stop_fieldset();

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
