<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("parametrage")." -> "._("om_logo");
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
$table = DB_PREFIXE."om_logo
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_logo.om_collectivite=om_collectivite.om_collectivite ";
// SELECT 
$champAffiche = array(
    'om_logo.om_logo as "'._("om_logo").'"',
    'om_logo.id as "'._("id").'"',
    'om_logo.libelle as "'._("libelle").'"',
    'om_logo.description as "'._("description").'"',
    'om_logo.fichier as "'._("fichier").'"',
    'om_logo.resolution as "'._("resolution").'"',
    "case om_logo.actif when 't' then 'Oui' else 'Non' end as \""._("actif")."\"",
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_logo.om_collectivite as "'._("om_collectivite").'"',
    );
//
$champRecherche = array(
    'om_logo.om_logo as "'._("om_logo").'"',
    'om_logo.id as "'._("id").'"',
    'om_logo.libelle as "'._("libelle").'"',
    'om_logo.description as "'._("description").'"',
    'om_logo.fichier as "'._("fichier").'"',
    'om_logo.resolution as "'._("resolution").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
$tri="ORDER BY om_logo.libelle ASC NULLS LAST";
$edition="om_logo";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_logo.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_logo.om_collectivite = '".$idx."') ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_logo.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_logo.om_collectivite = '".$idx."') ";
    }
}

?>