<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_sig_map");
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
$table = DB_PREFIXE."om_sig_map
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_sig_map.om_collectivite=om_collectivite.om_collectivite ";
// SELECT 
$champAffiche = array(
    'om_sig_map.om_sig_map as "'._("om_sig_map").'"',
    'om_sig_map.id as "'._("id").'"',
    'om_sig_map.libelle as "'._("libelle").'"',
    "case om_sig_map.actif when 't' then 'Oui' else 'Non' end as \""._("actif")."\"",
    'om_sig_map.zoom as "'._("zoom").'"',
    'om_sig_map.fond_osm as "'._("fond_osm").'"',
    'om_sig_map.fond_bing as "'._("fond_bing").'"',
    'om_sig_map.fond_sat as "'._("fond_sat").'"',
    'om_sig_map.layer_info as "'._("layer_info").'"',
    'om_sig_map.etendue as "'._("etendue").'"',
    'om_sig_map.projection_externe as "'._("projection_externe").'"',
    'om_sig_map.maj as "'._("maj").'"',
    'om_sig_map.table_update as "'._("table_update").'"',
    'om_sig_map.champ as "'._("champ").'"',
    'om_sig_map.retour as "'._("retour").'"',
    'om_sig_map.type_geometrie as "'._("type_geometrie").'"',
    'om_sig_map.lib_geometrie as "'._("lib_geometrie").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_sig_map.om_collectivite as "'._("om_collectivite").'"',
    'om_sig_map.url as "'._("url").'"',
    'om_sig_map.om_sql as "'._("om_sql").'"',
    );
//
$champRecherche = array(
    'om_sig_map.om_sig_map as "'._("om_sig_map").'"',
    'om_sig_map.id as "'._("id").'"',
    'om_sig_map.libelle as "'._("libelle").'"',
    'om_sig_map.zoom as "'._("zoom").'"',
    'om_sig_map.fond_osm as "'._("fond_osm").'"',
    'om_sig_map.fond_bing as "'._("fond_bing").'"',
    'om_sig_map.fond_sat as "'._("fond_sat").'"',
    'om_sig_map.layer_info as "'._("layer_info").'"',
    'om_sig_map.etendue as "'._("etendue").'"',
    'om_sig_map.projection_externe as "'._("projection_externe").'"',
    'om_sig_map.maj as "'._("maj").'"',
    'om_sig_map.table_update as "'._("table_update").'"',
    'om_sig_map.champ as "'._("champ").'"',
    'om_sig_map.retour as "'._("retour").'"',
    'om_sig_map.type_geometrie as "'._("type_geometrie").'"',
    'om_sig_map.lib_geometrie as "'._("lib_geometrie").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
$tri="ORDER BY om_sig_map.libelle ASC NULLS LAST";
$edition="om_sig_map";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_sig_map.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_sig_map.om_collectivite = '".$idx."') ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_sig_map.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_sig_map.om_collectivite = '".$idx."') ";
    }
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_sig_map_comp',
    'om_sig_map_wms',
);

?>