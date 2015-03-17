<?php
//$Id: om_sig_wms.inc.php 2839 2014-08-06 17:02:39Z fmichon $ 
//gen openMairie le 06/08/2014 18:57

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_sig_wms");
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
$table = DB_PREFIXE."om_sig_wms
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_sig_wms.om_collectivite=om_collectivite.om_collectivite ";
// SELECT 
$champAffiche = array(
    'om_sig_wms.om_sig_wms as "'._("om_sig_wms").'"',
    'om_sig_wms.libelle as "'._("libelle").'"',
    'om_sig_wms.id as "'._("id").'"',
    'om_sig_wms.chemin as "'._("chemin").'"',
    'om_sig_wms.couches as "'._("couches").'"',
    'om_sig_wms.cache_type as "'._("cache_type").'"',
    'om_sig_wms.cache_gfi_chemin as "'._("cache_gfi_chemin").'"',
    'om_sig_wms.cache_gfi_couches as "'._("cache_gfi_couches").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_sig_wms.om_collectivite as "'._("om_collectivite").'"',
    );
//
$champRecherche = array(
    'om_sig_wms.om_sig_wms as "'._("om_sig_wms").'"',
    'om_sig_wms.libelle as "'._("libelle").'"',
    'om_sig_wms.id as "'._("id").'"',
    'om_sig_wms.chemin as "'._("chemin").'"',
    'om_sig_wms.couches as "'._("couches").'"',
    'om_sig_wms.cache_type as "'._("cache_type").'"',
    'om_sig_wms.cache_gfi_chemin as "'._("cache_gfi_chemin").'"',
    'om_sig_wms.cache_gfi_couches as "'._("cache_gfi_couches").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
$tri="ORDER BY om_sig_wms.libelle ASC NULLS LAST";
$edition="om_sig_wms";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_sig_wms.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_sig_wms.om_collectivite = '".$idx."') ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_sig_wms.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_sig_wms.om_collectivite = '".$idx."') ";
    }
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_sig_map_wms',
);

?>