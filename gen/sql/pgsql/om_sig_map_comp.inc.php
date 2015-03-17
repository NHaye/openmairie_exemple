<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_sig_map_comp");
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
$table = DB_PREFIXE."om_sig_map_comp
    LEFT JOIN ".DB_PREFIXE."om_sig_map 
        ON om_sig_map_comp.om_sig_map=om_sig_map.om_sig_map ";
// SELECT 
$champAffiche = array(
    'om_sig_map_comp.om_sig_map_comp as "'._("om_sig_map_comp").'"',
    'om_sig_map.libelle as "'._("om_sig_map").'"',
    'om_sig_map_comp.libelle as "'._("libelle").'"',
    'om_sig_map_comp.ordre as "'._("ordre").'"',
    'om_sig_map_comp.actif as "'._("actif").'"',
    'om_sig_map_comp.comp_maj as "'._("comp_maj").'"',
    'om_sig_map_comp.type_geometrie as "'._("type_geometrie").'"',
    'om_sig_map_comp.comp_table_update as "'._("comp_table_update").'"',
    'om_sig_map_comp.comp_champ as "'._("comp_champ").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_sig_map_comp.om_sig_map_comp as "'._("om_sig_map_comp").'"',
    'om_sig_map.libelle as "'._("om_sig_map").'"',
    'om_sig_map_comp.libelle as "'._("libelle").'"',
    'om_sig_map_comp.ordre as "'._("ordre").'"',
    'om_sig_map_comp.actif as "'._("actif").'"',
    'om_sig_map_comp.comp_maj as "'._("comp_maj").'"',
    'om_sig_map_comp.type_geometrie as "'._("type_geometrie").'"',
    'om_sig_map_comp.comp_table_update as "'._("comp_table_update").'"',
    'om_sig_map_comp.comp_champ as "'._("comp_champ").'"',
    );
$tri="ORDER BY om_sig_map_comp.libelle ASC NULLS LAST";
$edition="om_sig_map_comp";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_sig_map" => array("om_sig_map", ),
);
// Filtre listing sous formulaire - om_sig_map
if (in_array($retourformulaire, $foreign_keys_extended["om_sig_map"])) {
    $selection = " WHERE (om_sig_map_comp.om_sig_map = '".$idx."') ";
}

?>