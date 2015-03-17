<?php
/**
 * Ce script permet d'afficher une écran de composition du tableau de bord de
 * chaque profil.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: dashboard_composer.php 2921 2014-10-10 08:12:21Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils(NULL, "om_dashboard", _("administration")." -> "._("tableaux de bord")." -> "._("composition"));

/**
 * 
 */
// Ouverture de la balise - Conteneur d'onglets
echo "<div id=\"formulaire\">\n\n";
// Affichage de la liste des onglets
$f->layout->display_tab_lien_onglet_un(_("composition"));
// Ouverture de la balise - Onglet 1
echo "\t<div id=\"tabs-1\">\n\n";

/**
 * Affichage du formulaire de sélection du profil
 */
// Inclusion de la classe de gestion des formulaires
require_once "../obj/om_formulaire.class.php";
// Ouverture du formulaire
echo "\t<form";
echo " method=\"post\"";
echo " id=\"dashboard_composer_form\"";
echo " action=\"../scr/dashboard_composer.php\"";
echo ">\n";
// Paramétrage des champs du formulaire
$champs = array("om_profil");
// Création d'un nouvel objet de type formulaire
$form = new formulaire(NULL, 0, 0, $champs);
// Paramétrage des champs du formulaire
$form->setLib("om_profil", _("Tableau de bord pour le profil"));
$form->setType("om_profil", "select");
$form->setTaille("om_profil", 25);
$form->setOnChange("om_profil", "submit()");
$form->setMax("om_profil", 25);
$form->setVal("om_profil", (isset($_POST["om_profil"]) ? $_POST["om_profil"] : ""));
// Si l'option 'gestion des permissions par hiérarchie des profils' n'est pas
// activée alors on affiche pas les code de hiérarchie sinon on les affiche
// dans la liste de sélection des profils
if ($f->getParameter("permission_by_hierarchical_profile") === false) {
    //
    $sql = "
    SELECT
    om_profil.om_profil,
    om_profil.libelle as lib
    FROM ".DB_PREFIXE."om_profil
    ORDER BY lib";
} else {
    //
    $sql = "
    SELECT
    om_profil.om_profil,
    concat(om_profil.hierarchie, ' - ', om_profil.libelle) as lib
    FROM ".DB_PREFIXE."om_profil
    ORDER BY om_profil.hierarchie";
}
// Exécution de la requête
$res = $f->db->query($sql);
// Logger
$f->addToLog("scr/dashboard_composer.php: db->query(\"".$sql."\");", VERBOSE_MODE);
// Vérification d'une éventuelle erreur de base de données
$f->isDatabaseError($res);
//
$contenu = array(array(""), array(_("choisir le profil")));
while ($row =& $res->fetchrow()) {
    $contenu[0][] = $row[0];
    $contenu[1][] = $row[1];
}
$form->setSelect("om_profil", $contenu);
// Affichage du formulaire
$form->entete();
$form->afficher($champs, 0, false, false);
$form->enpied();
//// Affichage du bouton
//echo "\t<div class=\"formControls\">\n";
//$f->layout->display_form_button(array("value" => _("Valider")));
//echo "\t</div>\n";
// Fermeture du fomulaire
echo "\t</form>\n";
/**
 *
 */
if (!isset($_POST["om_profil"]) || $_POST["om_profil"] == "") {
    // Fermeture de la balise - Onglet 1
    echo "\n\t</div>\n";
    // Fermeture de la balise - Conteneur d'onglets
    echo "</div>\n";
    //
    die();
}
//
echo "<div id=\"dashboard-composer\">\n";

/**
 * Tableau de bord
 */
// Ouverture du conteneur #dashboard
echo "<div id=\"dashboard\">\n";
// Conteneur permettant de recevoir d'eventuels messages d'erreur des requetes
// Ajax
echo "<div id=\"info\">";
echo "</div>\n";
// Mode Edition
(isset($_GET['edition']) ? $edition = $_GET['edition'] : $edition = 1);
// Si le mode edition est active alors on affiche l'action pour ajouter un
// nouveau widget
if ($edition == 1) {
    $widget_add_action = "
<div class=\"widget-add-action\" id=\"dashboard_profil_%s\">
  <a href=\"#\">
    <span class=\"om-icon om-icon-25 add-25\">
      %s
    </span>
  </a>
  <div class=\"visualClear\"><!-- --></div>
</div>
    ";
    printf($widget_add_action, $_POST["om_profil"], _("Ajouter un widget"));
}
// Inclusion du fichier widgetctl.php pour acceder a la fonction d'affichage
// d'un widget
require_once "../spg/widgetctl.php";
// Ouverture du conteneur de colonnes
echo "<div class=\"col".$f->config['dashboard_nb_column']."\">\n";
// On boucle sur chacune des colonnes
for ($i = 1; $i <= $f->config['dashboard_nb_column']; $i++) {
    // Ouverture du conteneur .column
    echo "<div class=\"column\" id=\"column_".$i."\">\n";
    // Requete de selection de tous les widgets de la colonne
    $sql = " SELECT ";
    $sql .= " om_dashboard.om_dashboard, ";
    $sql .= " om_widget.om_widget as widget, ";
    $sql .= " om_widget.libelle as libelle, ";
    $sql .= " om_widget.lien as lien, ";
    $sql .= " om_widget.texte as texte, ";
    $sql .= " om_widget.type as type, ";
    $sql .= " om_dashboard.position ";
    $sql .= " FROM ".DB_PREFIXE."om_dashboard ";
    $sql .= " INNER JOIN ".DB_PREFIXE."om_widget ON om_dashboard.om_widget=om_widget.om_widget ";
    $sql .= " WHERE ";
    $sql .= " om_dashboard.bloc = 'C".intval($i)."' ";
    $sql .= " AND om_dashboard.om_profil = ".intval($_POST["om_profil"])." ";
    $sql .= " ORDER BY position";
    // Exécution de la requête
    $res = $f->db->query($sql);
    // Logger
    $f->addToLog("scr/dashboard_composer.php: db->query(\"".$sql."\");", VERBOSE_MODE);
    // Vérification d'une éventuelle erreur de base de données
    $f->isDatabaseError($res);
    // On boucle sur chacun des widgets
    while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        // Affichage du widget
        widgetView($f, $row['om_dashboard'], $row['libelle'], $row['texte'], $row['lien'], $row['type'], $edition);
    }
    // Fermeture du conteneur .column
    echo "</div>\n";
}
// On affiche un conteneur vide pour avec la propriete clear a both pour
// reinitialiser le positionnement des blocs
echo "<div class=\"both\"><!-- --></div>\n";
// Fermeture du conteneur de colonnes
echo "</div>\n";
// Fermeture du conteneur #dashboard
echo "</div>\n";
// Fermeture du conteneur #dashboard-composer
echo "</div>\n";

// Fermeture de la balise - Onglet 1
echo "\n\t</div>\n";
// Fermeture de la balise - Conteneur d'onglets
echo "</div>\n";

?>