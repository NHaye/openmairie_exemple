<?php
/**
 * ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: export_csv.php 2933 2014-10-16 10:21:44Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

/**
 * Initialisation des variables
 */
// Nom de l'objet metier
(isset($_GET['obj']) ? $obj = $_GET['obj'] : $obj = "");
// Premier enregistrement a afficher
(isset($_GET['premier']) ? $premier = $_GET['premier'] : $premier = 0);
// Colonne choisie pour le tri
(isset($_GET['tricol']) ? $tricol = $_GET['tricol'] : $tricol = "");
// Id unique de la recherche avancee
(isset($_GET['advs_id']) ? $advs_id = $_GET['advs_id'] : $advs_id = "");
// Valilite des objets a afficher
(isset($_GET['valide']) ? $valide = $_GET['valide'] : $valide = "");
// Chaine recherchee
if (isset($_POST['recherche'])) {
    $recherche = $_POST['recherche'];
    if (get_magic_quotes_gpc()) {
        $recherche1 = StripSlashes($recherche);
    } else {
        $recherche1 = $recherche;
    }
} else {
    if (isset($_GET['recherche'])) {
        $recherche = $_GET['recherche'];
        if (get_magic_quotes_gpc()) {
            $recherche1 = StripSlashes($recherche);
        } else {
            $recherche1 = $recherche;
        }
    } else {
        $recherche = "";
        $recherche1 = "";
    }
}
// Colonne choisie pour la selection
if (isset($_POST['selectioncol'])) {
    $selectioncol = $_POST['selectioncol'];
} else {
    if (isset($_GET['selectioncol'])) {
        $selectioncol = $_GET['selectioncol'];
    } else {
        $selectioncol = "";
    }
}

// Nom du fichier
$filename=$obj."-".date("d-m-Y");

$f->isAuthorized(array($obj,$obj."_exporter"),"OR");
$f->disableLog();

// Récupération des fichiers d'include
if (file_exists ("../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php")) {
   include ("../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php");
}
elseif (file_exists ("../sql/".OM_DB_PHPTYPE."/".$obj.".inc")) {
   include ("../sql/".OM_DB_PHPTYPE."/".$obj.".inc");
}

// Les variables sont surcharger pour afficher plus ou moins de champs
if (file_exists ("../sql/".OM_DB_PHPTYPE."/".$obj.".reqmo.inc.php")) {
    include ("../sql/".OM_DB_PHPTYPE."/".$obj.".reqmo.inc.php");
}
elseif (file_exists ("../sql/".OM_DB_PHPTYPE."/".$obj.".reqmo.inc")) {
    include ("../sql/".OM_DB_PHPTYPE."/".$obj.".reqmo.inc");
}

// Instanciation d'om_table
require_once "../obj/om_table.class.php";
$tb = new om_table("../scr/tab.php", $table, $serie, $champAffiche, $champRecherche,
                   $tri, $selection, $edition, $options, $advs_id);
// Affectation des parametres
$params = array(
    "obj" => $obj,
    "premier" => $premier,
    "recherche" => $recherche,
    "selectioncol" => $selectioncol,
    "tricol" => $tricol,
    "advs_id" => $advs_id,
    "valide" => $valide,
);
$tb->setParams($params);
// Methode permettant de definir si la recherche doit etre faite
// sur la recherche simple ou avncee
$tb->composeSearchTab();
// Generation de la requete de recherche
$tb->composeQuery();
// Exécution de la requête
$res = $f->db->query($tb->sql);
// Logger
$f->addToLog("scr/export_csv.php: db->query(\"".$tb->sql."\");", VERBOSE_MODE);
// Vérification d'une éventuelle erreur de base de données
$f->isDatabaseError($res);
//
$nbligne=$res->numrows();
if($nbligne>0){
	//OUPUT HEADERS
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Type: application/csv");
    header("Content-Disposition: attachment; filename=\"$filename.csv\";" );
    header("Content-Transfer-Encoding: binary");
    $header=true;
    // Ouverture du flux de sortie
    $out = fopen('php://output', 'w');
    // Formatage de chaque ligne pour csv
    while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
        if($header) {
            fputcsv($out, array_keys($row), ';','"');
            $header=false;
        }
        fputcsv($out, $row, ';','"');
    }
    // Affichage de la sortie standard
    readfile('php://output');
    // Fermeture de la sortie
    fclose($out);
}else {
    header('Location: ../scr/tab.php?obj='.$obj.
           '&premier='.$premier.
           '&tricol='.$tricol.
           '&advs_id='.$advs_id.
           '&valide='.$valide.
           '&recherche='.$recherche.
           '&selectioncol='.$selectioncol
           );
}

?>
