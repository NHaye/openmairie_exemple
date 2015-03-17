<?php
/**
 * Ce script permet d'afficher le tableau de bord de l'utilisateur actuellement
 * connecté.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: dashboard.php 2924 2014-10-10 14:49:17Z fmichon $
 */

require_once "../obj/utils.class.php";
if (!isset($f)) {
    $f = new utils(NULL, NULL, _("Tableau de bord"));
}

// Inclusion d'un fichier de configuration qui permet de surcharger le systeme
// de tableau de bord standard
if (file_exists("../dyn/dashboard.inc.php")) {
    require "../dyn/dashboard.inc.php";
}

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
$edition = 0;
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
    $sql .= " om_dashboard.bloc ='C".intval($i)."' ";
    $sql .= " AND om_dashboard.om_profil = ".intval($f->user_infos['om_profil'])." ";
    $sql .= " ORDER BY position";
    // Exécution de la requête
    $res = $f->db->query($sql);
    // Logger
    $f->addToLog("scr/dashboard.php: db->query(\"".$sql."\");", VERBOSE_MODE);
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

?>