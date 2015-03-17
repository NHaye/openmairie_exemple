<?php
//$Id: om_lettretype.inc.php 2839 2014-08-06 17:02:39Z fmichon $ 
//gen openMairie le 06/08/2014 18:57

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("parametrage")." -> "._("om_lettretype");
if(!isset($premier)) $premier='';
if(!isset($recherche1)) $recherche1='';
if(!isset($tricolsf)) $tricolsf='';
if(!isset($premiersf)) $premiersf='';
if(!isset($selection)) $selection='';
if(!isset($retourformulaire)) $retourformulaire='';
if (isset($idx) && $idx != ']' && trim($idx) != '') {
    $ent .= "->&nbsp;".$idx."&nbsp;";
}
if (isset($idz) && trim($idz) != '') {
    $ent .= "&nbsp;".strtoupper($idz)."&nbsp;";
}
// FROM 
$table = DB_PREFIXE."om_lettretype
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_lettretype.om_collectivite=om_collectivite.om_collectivite 
    LEFT JOIN ".DB_PREFIXE."om_requete 
        ON om_lettretype.om_sql=om_requete.om_requete ";
// SELECT 
$champAffiche = array(
    'om_lettretype.om_lettretype as "'._("om_lettretype").'"',
    'om_lettretype.id as "'._("id").'"',
    'om_lettretype.libelle as "'._("libelle").'"',
    "case om_lettretype.actif when 't' then 'Oui' else 'Non' end as \""._("actif")."\"",
    'om_lettretype.orientation as "'._("orientation").'"',
    'om_lettretype.format as "'._("format").'"',
    'om_lettretype.logo as "'._("logo").'"',
    'om_lettretype.logoleft as "'._("logoleft").'"',
    'om_lettretype.logotop as "'._("logotop").'"',
    'om_lettretype.titreleft as "'._("titreleft").'"',
    'om_lettretype.titretop as "'._("titretop").'"',
    'om_lettretype.titrelargeur as "'._("titrelargeur").'"',
    'om_lettretype.titrehauteur as "'._("titrehauteur").'"',
    'om_lettretype.titrebordure as "'._("titrebordure").'"',
    'om_requete.libelle as "'._("om_sql").'"',
    'om_lettretype.margeleft as "'._("margeleft").'"',
    'om_lettretype.margetop as "'._("margetop").'"',
    'om_lettretype.margeright as "'._("margeright").'"',
    'om_lettretype.margebottom as "'._("margebottom").'"',
    'om_lettretype.se_font as "'._("se_font").'"',
    'om_lettretype.se_couleurtexte as "'._("se_couleurtexte").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_lettretype.om_collectivite as "'._("om_collectivite").'"',
    'om_lettretype.titre_om_htmletat as "'._("titre_om_htmletat").'"',
    'om_lettretype.corps_om_htmletatex as "'._("corps_om_htmletatex").'"',
    );
//
$champRecherche = array(
    'om_lettretype.om_lettretype as "'._("om_lettretype").'"',
    'om_lettretype.id as "'._("id").'"',
    'om_lettretype.libelle as "'._("libelle").'"',
    'om_lettretype.orientation as "'._("orientation").'"',
    'om_lettretype.format as "'._("format").'"',
    'om_lettretype.logo as "'._("logo").'"',
    'om_lettretype.logoleft as "'._("logoleft").'"',
    'om_lettretype.logotop as "'._("logotop").'"',
    'om_lettretype.titreleft as "'._("titreleft").'"',
    'om_lettretype.titretop as "'._("titretop").'"',
    'om_lettretype.titrelargeur as "'._("titrelargeur").'"',
    'om_lettretype.titrehauteur as "'._("titrehauteur").'"',
    'om_lettretype.titrebordure as "'._("titrebordure").'"',
    'om_requete.libelle as "'._("om_sql").'"',
    'om_lettretype.margeleft as "'._("margeleft").'"',
    'om_lettretype.margetop as "'._("margetop").'"',
    'om_lettretype.margeright as "'._("margeright").'"',
    'om_lettretype.margebottom as "'._("margebottom").'"',
    'om_lettretype.se_font as "'._("se_font").'"',
    'om_lettretype.se_couleurtexte as "'._("se_couleurtexte").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
$tri="ORDER BY om_lettretype.libelle ASC NULLS LAST";
$edition="om_lettretype";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_lettretype.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
    "om_requete" => array("om_requete", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_lettretype.om_collectivite = '".$idx."') ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_lettretype.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_lettretype.om_collectivite = '".$idx."') ";
    }
}
// Filtre listing sous formulaire - om_requete
if (in_array($retourformulaire, $foreign_keys_extended["om_requete"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_lettretype.om_sql = '".$idx."') ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_lettretype.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_lettretype.om_sql = '".$idx."') ";
    }
}

?>