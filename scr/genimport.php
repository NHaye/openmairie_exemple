<?php
/**
 * ce script a pour objet de recuperer
 *  les etats
 *  les sous etats
 *  les lettres type
 *  depuis les versions anterieures a openMairie 4
 * @package openmairie_exemple
 * @version SVN : $Id: genimport.php 2732 2014-03-07 11:33:24Z fmichon $
 */

//
require_once "../obj/utils.class.php";
$f = new utils(
    null,
    "gen",
    _("administration")." -> "._("generateur")." -> "._("import des anciennes editions")
);

/*
 TRANSFERT EN base UTF8 : pb d encodage (21/07/2011)
 pour recuperer des .inc.php en ISO 8889-1
 - transformer en utf8
 - remplacer les £ (provoquant un bug d'encodage sql) par &
*/

$DEBUG=1;
function enrvb($val) {
    $temp='';
    if($val!=array()){
        for ($i = 0; $i < sizeof($val); $i++) {
            $temp.=$val[$i].'-';
        }
        $temp=substr($temp, 0, strlen($temp)-1);
    }
    return $temp;
}

function encol($val) {
    $temp=''; 
    if($val!=array()){
        for ($i = 0; $i < sizeof($val); $i++) {
            $temp.=$val[$i]."|";
        }
        $temp=substr($temp, 0, strlen($temp)-1);
    }
    return $temp;
}

function encol_rc($val) {
    $temp=''; 
    if($val!=array()){
        for ($i = 0; $i < sizeof($val); $i++) {
            $temp.=$val[$i].chr(13).chr(10);
        }
        $temp=substr($temp, 0, strlen($temp)-2);
    }
    return $temp;
}

function envar($val) {
    $val=str_replace('£','&',$val);
    return $val;
}

/**
 * Description de la page
 */
$description = _("Cette page vous permet d'importer les anciens etat, sousetat, lettretype contenu en repertoire /inc ".
                 "directement dans les tables d openMairie 4 ");
$f->displayDescription($description);

/**
 *
 */
//
set_time_limit(3600);
//
if (isset($_POST['choice-import']) and $_POST['choice-import'] != "---") {
    $obj = $_POST['choice-import'];
} elseif(isset($_GET['obj'])) {
    $obj = $_GET['obj'];
} else {
    $obj = "";    
}
if(isset($_GET['validation'])) {
    $validation = $_GET['validation'];
} else {
    $validation = 0;    
}

/**
 * On liste les fichiers .inc (compatibilite) et .inc.php dans /inc
 */
$dir = getcwd();
$dir = "../gen/inc/";
$dossier = opendir($dir);
$tab = array();
while ($entree = readdir($dossier)) {
    if (substr($entree,  strlen($entree) - 8, 8)=='etat.inc'
        or substr($entree,  strlen($entree) - 12, 12)=='etat.inc.php'
        or substr($entree,  strlen($entree) - 14, 14)=='lettretype.inc'
        or substr($entree,  strlen($entree) - 18, 18)=='lettretype.inc.php') {
        array_push($tab, array('file' => $entree));
    }
}
closedir($dossier);
asort($tab);

/**
 * Formulaire de choix de la table dans laquelle realiser l'import
 */
//
echo "\n<div id=\"form-choice-import\" class=\"formulaire\">\n";
echo "<form action=\"../scr/genimport.php\" method=\"post\">\n";
echo "<fieldset>\n";
echo "\t<legend>"._("Choix du fichier d'import :")."</legend>\n";
//
echo "\t<div class=\"field\">";
echo "<label>"._("fichier")."</label>";
echo "<select onchange=\"submit()\" name=\"choice-import\" class=\"champFormulaire\">";
echo "<option>---</option>";
foreach ($tab as $elem) {
    echo "<option value=\"".$elem['file']."\"";
    if ($obj == $elem['file']) {
        echo " selected=\"selected\" ";
    }
    echo ">".$elem['file']."</option>";
}
echo "</select>";
echo "</div>\n";
//
echo "</fieldset>\n";
echo "</form>\n";
echo "</div>\n";

/**
 * Formulaire d'import du fichier CSV
 */
if ($obj != "" and $validation==0) {
    //
    echo "\n<br>&nbsp;<div id=\"form-csv-import\" class=\"formulaire\">\n";
    echo "<form action=\"../scr/genimport.php?obj=".$obj."&validation=1\" method=\"post\" name=\"f1\">\n";
    echo "<fieldset>\n";
    echo "\t<legend>"._("Import du fichier ")."openMairie < 4 </legend>";
    include("../gen/inc/".$obj);
    echo $obj."<br><br>";
    if (substr($obj,  strlen($obj) - 12, 12)=='sousetat.inc'
        or substr($obj,  strlen($obj) - 16, 16)=='sousetat.inc.php'){
        echo $sousetat['titre']."<br><br>";
        echo $sousetat['sql']."<br><br>";
    }else{
        if (substr($obj,  strlen($obj) - 8, 8)=='etat.inc'
            or substr($obj,  strlen($obj) - 12, 12)=='etat.inc.php'){
            echo $etat['titre']."<br><br>";
            echo $etat['sql']."<br><br>";
        }
    }

    echo "\t<div class=\"formControls\">";
    echo "<input type=\"submit\" name=\"submit-csv-import\" value=\""._("Import")." ".
            $obj." "._("dans la base")."\" class=\"boutonFormulaire\" />";
    echo "</div>";
    echo "</fieldset>";
    echo "</form>";
    echo "</div>\n";
}

/**
 *  transfert dans la base
 */

if ($obj != "" and $validation==1) {
    echo "\n<br>&nbsp";
    echo "<fieldset>\n";
    echo "\t<legend>"._("Import du fichier ")."openMairie < 4 </legend>";
    $variable='&'; 
    if (substr($obj,  strlen($obj) - 12, 12)=='sousetat.inc'
        or substr($obj,  strlen($obj) - 16, 16)=='sousetat.inc.php'){
        // **** parametres de base ****
        $longueurtableau= 195;
        $sousetat['titrehauteur']=10;
        $sousetat['titrefont']='helvetica';
        $sousetat['titreattribut']='B';
        $sousetat['titretaille']=10;
        $sousetat['titrebordure']=0;
        $sousetat['titrealign']='L';
        $sousetat['titrefond']=0;
        $sousetat['titrefondcouleur']="255-255-255";
        $sousetat['titretextecouleur']="0-0-0";
        // intervalle
        $sousetat['intervalle_debut']=0;
        $sousetat['intervalle_fin']=5;
        // entete
        $sousetat['entete_flag']=1;
        $sousetat['entete_fond']=1;
        $sousetat['entete_hauteur']=7;
        $sousetat['entete_fondcouleur']="255-255-255";
        $sousetat['entete_textecouleur']="0-0-0";
        // tableau
        $sousetat['tableau_bordure']=1;
        $sousetat['tableau_fontaille']=10;
        // bordure
        $sousetat['bordure_couleur']="0-0-0";
        // sous etat fond
        $sousetat['se_fond1']="243-246-246";
        $sousetat['se_fond2']="255-255-255";
        // cellule
        $sousetat['cellule_fond']=1;
        $sousetat['cellule_hauteur']=7;
        // total
        $sousetat['cellule_fond_total']=1;
        $sousetat['cellule_fontaille_total']=10;
        $sousetat['cellule_hauteur_total']=15;
        $sousetat['cellule_fondcouleur_total']="255-255-255";
        // moyenne
        $sousetat['cellule_fond_moyenne']=1;
        $sousetat['cellule_fontaille_moyenne']=10;
        $sousetat['cellule_hauteur_moyenne']=5;
        $sousetat['cellule_fondcouleur_moyenne']="212-219-220";
        // nombre d enregistrement
        $sousetat['cellule_fond_nbr']=1;
        $sousetat['cellule_fontaille_nbr']=10;
        $sousetat['cellule_hauteur_nbr']=7;
        $sousetat['cellule_fondcouleur_nbr']="255-255-255";
        include("../gen/inc/".$obj);
        echo $obj;
        // sql
        $sousetat['om_sql']=envar($sousetat['sql']);
        unset($sousetat['sql']);
        // id
        $sousetat['id']= substr($obj, 0,  strlen($obj) - 13);
        $sousetat['libelle']= 'import du '.date('d/m/Y');
        $sousetat['titre']=envar($sousetat['titre']);
        // om_collectivite
        $sousetat['om_collectivite']= $_SESSION['collectivite'];
        // tableau enrvb et encol
        $sousetat['titrefondcouleur']=enrvb($sousetat['titrefondcouleur']);
        $sousetat['titretextecouleur']=enrvb($sousetat['titretextecouleur']);
        $sousetat['entete_orientation']=encol($sousetat['entete_orientation']);
        $sousetat['entetecolone_bordure']=encol($sousetat['entetecolone_bordure']);
        $sousetat['entetecolone_align']=encol($sousetat['entetecolone_align']);
        $sousetat['entete_fondcouleur']=enrvb($sousetat['entete_fondcouleur']);
        $sousetat['entete_textecouleur']=enrvb($sousetat['entete_textecouleur']);
        $sousetat['bordure_couleur']=enrvb($sousetat['bordure_couleur']);
        $sousetat['se_fond1']=enrvb($sousetat['se_fond1']);
        $sousetat['se_fond2']=enrvb($sousetat['se_fond2']);
        $sousetat['cellule_largeur']=encol($sousetat['cellule_largeur']);
        $sousetat['cellule_bordure_un']=encol($sousetat['cellule_bordure_un']);
        $sousetat['cellule_bordure']=encol($sousetat['cellule_bordure']);
        $sousetat['cellule_align']=encol($sousetat['cellule_align']);
        $sousetat['cellule_fondcouleur_total']=enrvb($sousetat['cellule_fondcouleur_total']);
        $sousetat['cellule_bordure_total']=encol($sousetat['cellule_bordure_total']);
        $sousetat['cellule_align_total']=encol($sousetat['cellule_align_total']);
        $sousetat['cellule_fondcouleur_moyenne']=enrvb($sousetat['cellule_fondcouleur_moyenne']);
        $sousetat['cellule_bordure_moyenne']=encol($sousetat['cellule_bordure_moyenne']);
        $sousetat['cellule_align_moyenne']=encol($sousetat['cellule_align_moyenne']);
        $sousetat['cellule_fondcouleur_nbr']=enrvb($sousetat['cellule_fondcouleur_nbr']);
        $sousetat['cellule_bordure_nbr']=encol($sousetat['cellule_bordure_nbr']);
        $sousetat['cellule_align_nbr']=encol($sousetat['cellule_align_nbr']);
        $sousetat['cellule_numerique']=encol($sousetat['cellule_numerique']);
        $sousetat['cellule_total']=encol($sousetat['cellule_total']);
        $sousetat['cellule_moyenne']=encol($sousetat['cellule_moyenne']);
        $sousetat['cellule_compteur']=encol($sousetat['cellule_compteur']);
        $sousetat['actif']=FALSE;
        // cle
        $sousetat['om_sousetat']=$f-> db -> nextId(DB_PREFIXE.'om_sousetat');
        $res= $f-> db -> autoExecute(DB_PREFIXE.'om_sousetat',$sousetat,DB_AUTOQUERY_INSERT);
        if (database::isError($res))
               die($res->getDebugInfo()." => echec requete insertion sousetat");
    }else{
        // etat
        if (substr($obj,  strlen($obj) - 8, 8)=='etat.inc'
            or substr($obj,  strlen($obj) - 12, 12)=='etat.inc.php'){
            // *** parametre de base ***
            $etat['orientation']='P';
            $etat['format']='A4';
            // footer
            $etat['footerfont']='helvetica';
            $etat['footerattribut']='I';
            $etat['footertaille']='8';
            // logo
            $etat['logo']='logopdf.png';
            $etat['logoleft']='58';
            $etat['logotop']='7';
            // titre
            $etat['titreleft']='41';
            $etat['titretop']='36';
            $etat['titrelargeur']='130';
            $etat['titrehauteur']='10';
            $etat['titrefont']='helvetica';
            $etat['titreattribut']='B';
            $etat['titretaille']='15';
            $etat['titrebordure']='0';
            $etat['titrealign']='C'; 
            // corps
            $etat['corpsleft']='7';
            $etat['corpstop']='57';
            $etat['corpslargeur']='195';
            $etat['corpshauteur']='5';
            $etat['corpsfont']='helvetica';
            $etat['corpsattribut']='';
            $etat['corpstaille']='10';
            $etat['corpsbordure']='0';
            $etat['corpsalign']='J';
            // sous etat
            $etat['se_font']='helvetica';
            $etat['se_margeleft']='8';
            $etat['se_margetop']='5';
            $etat['se_margeright']='5';
            $etat['se_couleurtexte']="0-0-0";
            echo $obj;
            // sql
            $etat['om_sql']=envar($etat['sql']);
            unset($etat['sql']);
            // id
            $etat['id']= substr($obj, 0,  strlen($obj) - 9);
            $etat['libelle']= 'import du '.date('d/m/Y');
            $etat['titre']=envar($etat['titre']);
            $etat['corps']=envar($etat['corps']);
            // om_collectivite
            $etat['om_collectivite']= $_SESSION['collectivite'];
            // tableau enrvb et encol
            $etat['se_couleurtexte']=enrvb($etat['se_couleurtexte']);
            $etat['sousetat']=encol_rc($etat['sousetat']);
            $etat['actif']=FALSE;
            $etat['om_etat']=$f-> db -> nextId(DB_PREFIXE.'om_etat');
            print_r($etat);
            $res= $f-> db -> autoExecute(DB_PREFIXE.'om_etat',$etat,DB_AUTOQUERY_INSERT);
            if (database::isError($res))
               die($res->getDebugInfo()." => echec requete insertion etat");
            else
                echo $obj." "._("importe"); 
        }
        // lettretype
        if (substr($obj,  strlen($obj) - 14, 14)=='lettretype.inc'
            or substr($obj,  strlen($obj) - 18, 18)=='lettretype.inc.php'){
            // *** parametre de base ***
            $lettretype['orientation']='P';
            $lettretype['format']='A4';
            // logo
            $lettretype['logo']='logopdf.png';
            $lettretype['logoleft']='58';
            $lettretype['logotop']='7';
            // titre
            $lettretype['titreleft']='41';
            $lettretype['titretop']='36';
            $lettretype['titrelargeur']='130';
            $lettretype['titrehauteur']='10';
            $lettretype['titrefont']='helvetica';
            $lettretype['titreattribut']='B';
            $lettretype['titretaille']='15';
            $lettretype['titrebordure']='0';
            $lettretype['titrealign']='C'; 
            // corps
            $lettretype['corpsleft']='7';
            $lettretype['corpstop']='57';
            $lettretype['corpslargeur']='195';
            $lettretype['corpshauteur']='5';
            $lettretype['corpsfont']='helvetica';
            $lettretype['corpsattribut']='';
            $lettretype['corpstaille']='10';
            $lettretype['corpsbordure']='0';
            $lettretype['corpsalign']='J';
            echo $obj;
            // sql
            $lettretype['om_sql']=envar($lettretype['sql']);
            unset($lettretype['sql']);
            // id _lettretype.inc.php 15 car *** bug 17/07/2011
            $lettretype['id']= substr($obj, 0,  strlen($obj) - 15);
            $lettretype['libelle']= 'import du '.date('d/m/Y');
            $lettretype['titre']=envar($lettretype['titre']);
            $lettretype['corps']=envar($lettretype['corps']);
            $lettretype['actif']=FALSE;
            // om_collectivite
            $lettretype['om_collectivite']= $_SESSION['collectivite'];
            // tableau enrvb et encol
            // *** bug du 17/07/2011
            //$lettretype['se_couleurtexte']=enrvb($lettretype['se_couleurtexte']);
            $lettretype['om_lettretype']=$f-> db -> nextId(DB_PREFIXE.'om_lettretype');
            // print_r($lettretype);
            $res= $f-> db -> autoExecute(DB_PREFIXE.'om_lettretype',$lettretype,DB_AUTOQUERY_INSERT);
            if (database::isError($res))
               die($res->getDebugInfo()." => echec requete insertion lettretype");
            else
                echo $obj." "._("importe");
        }
    }
    echo "</fieldset>";
}
?>
