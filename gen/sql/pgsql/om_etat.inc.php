<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("parametrage")." -> "._("om_etat");
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
$table = DB_PREFIXE."om_etat
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_etat.om_collectivite=om_collectivite.om_collectivite 
    LEFT JOIN ".DB_PREFIXE."om_requete 
        ON om_etat.om_sql=om_requete.om_requete ";
// SELECT 
$champAffiche = array(
    'om_etat.om_etat as "'._("om_etat").'"',
    'om_etat.id as "'._("id").'"',
    'om_etat.libelle as "'._("libelle").'"',
    "case om_etat.actif when 't' then 'Oui' else 'Non' end as \""._("actif")."\"",
    'om_etat.orientation as "'._("orientation").'"',
    'om_etat.format as "'._("format").'"',
    'om_etat.logo as "'._("logo").'"',
    'om_etat.logoleft as "'._("logoleft").'"',
    'om_etat.logotop as "'._("logotop").'"',
    'om_etat.titreleft as "'._("titreleft").'"',
    'om_etat.titretop as "'._("titretop").'"',
    'om_etat.titrelargeur as "'._("titrelargeur").'"',
    'om_etat.titrehauteur as "'._("titrehauteur").'"',
    'om_etat.titrebordure as "'._("titrebordure").'"',
    'om_requete.libelle as "'._("om_sql").'"',
    'om_etat.se_font as "'._("se_font").'"',
    'om_etat.se_couleurtexte as "'._("se_couleurtexte").'"',
    'om_etat.margeleft as "'._("margeleft").'"',
    'om_etat.margetop as "'._("margetop").'"',
    'om_etat.margeright as "'._("margeright").'"',
    'om_etat.margebottom as "'._("margebottom").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_etat.om_collectivite as "'._("om_collectivite").'"',
    'om_etat.titre_om_htmletat as "'._("titre_om_htmletat").'"',
    'om_etat.corps_om_htmletatex as "'._("corps_om_htmletatex").'"',
    );
//
$champRecherche = array(
    'om_etat.om_etat as "'._("om_etat").'"',
    'om_etat.id as "'._("id").'"',
    'om_etat.libelle as "'._("libelle").'"',
    'om_etat.orientation as "'._("orientation").'"',
    'om_etat.format as "'._("format").'"',
    'om_etat.logo as "'._("logo").'"',
    'om_etat.logoleft as "'._("logoleft").'"',
    'om_etat.logotop as "'._("logotop").'"',
    'om_etat.titreleft as "'._("titreleft").'"',
    'om_etat.titretop as "'._("titretop").'"',
    'om_etat.titrelargeur as "'._("titrelargeur").'"',
    'om_etat.titrehauteur as "'._("titrehauteur").'"',
    'om_etat.titrebordure as "'._("titrebordure").'"',
    'om_requete.libelle as "'._("om_sql").'"',
    'om_etat.se_font as "'._("se_font").'"',
    'om_etat.se_couleurtexte as "'._("se_couleurtexte").'"',
    'om_etat.margeleft as "'._("margeleft").'"',
    'om_etat.margetop as "'._("margetop").'"',
    'om_etat.margeright as "'._("margeright").'"',
    'om_etat.margebottom as "'._("margebottom").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
$tri="ORDER BY om_etat.libelle ASC NULLS LAST";
$edition="om_etat";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_etat.om_collectivite = '".$_SESSION["collectivite"]."') ";
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
        $selection = " WHERE (om_etat.om_collectivite = '".$idx."') ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_etat.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_etat.om_collectivite = '".$idx."') ";
    }
}
// Filtre listing sous formulaire - om_requete
if (in_array($retourformulaire, $foreign_keys_extended["om_requete"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_etat.om_sql = '".$idx."') ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_etat.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_etat.om_sql = '".$idx."') ";
    }
}

?>