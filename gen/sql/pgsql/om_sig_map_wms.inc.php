<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_sig_map_wms");
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
$table = DB_PREFIXE."om_sig_map_wms
    LEFT JOIN ".DB_PREFIXE."om_sig_map 
        ON om_sig_map_wms.om_sig_map=om_sig_map.om_sig_map 
    LEFT JOIN ".DB_PREFIXE."om_sig_wms 
        ON om_sig_map_wms.om_sig_wms=om_sig_wms.om_sig_wms ";
// SELECT 
$champAffiche = array(
    'om_sig_map_wms.om_sig_map_wms as "'._("om_sig_map_wms").'"',
    'om_sig_wms.libelle as "'._("om_sig_wms").'"',
    'om_sig_map.libelle as "'._("om_sig_map").'"',
    'om_sig_map_wms.ol_map as "'._("ol_map").'"',
    'om_sig_map_wms.ordre as "'._("ordre").'"',
    'om_sig_map_wms.visibility as "'._("visibility").'"',
    'om_sig_map_wms.panier as "'._("panier").'"',
    'om_sig_map_wms.pa_nom as "'._("pa_nom").'"',
    'om_sig_map_wms.pa_layer as "'._("pa_layer").'"',
    'om_sig_map_wms.pa_attribut as "'._("pa_attribut").'"',
    'om_sig_map_wms.pa_encaps as "'._("pa_encaps").'"',
    'om_sig_map_wms.pa_type_geometrie as "'._("pa_type_geometrie").'"',
    'om_sig_map_wms.baselayer as "'._("baselayer").'"',
    'om_sig_map_wms.singletile as "'._("singletile").'"',
    'om_sig_map_wms.maxzoomlevel as "'._("maxzoomlevel").'"',
    );
//
$champNonAffiche = array(
    'om_sig_map_wms.pa_sql as "'._("pa_sql").'"',
    'om_sig_map_wms.sql_filter as "'._("sql_filter").'"',
    );
//
$champRecherche = array(
    'om_sig_map_wms.om_sig_map_wms as "'._("om_sig_map_wms").'"',
    'om_sig_wms.libelle as "'._("om_sig_wms").'"',
    'om_sig_map.libelle as "'._("om_sig_map").'"',
    'om_sig_map_wms.ol_map as "'._("ol_map").'"',
    'om_sig_map_wms.ordre as "'._("ordre").'"',
    'om_sig_map_wms.visibility as "'._("visibility").'"',
    'om_sig_map_wms.panier as "'._("panier").'"',
    'om_sig_map_wms.pa_nom as "'._("pa_nom").'"',
    'om_sig_map_wms.pa_layer as "'._("pa_layer").'"',
    'om_sig_map_wms.pa_attribut as "'._("pa_attribut").'"',
    'om_sig_map_wms.pa_encaps as "'._("pa_encaps").'"',
    'om_sig_map_wms.pa_type_geometrie as "'._("pa_type_geometrie").'"',
    'om_sig_map_wms.baselayer as "'._("baselayer").'"',
    'om_sig_map_wms.singletile as "'._("singletile").'"',
    'om_sig_map_wms.maxzoomlevel as "'._("maxzoomlevel").'"',
    );
$tri="ORDER BY om_sig_wms.libelle ASC NULLS LAST";
$edition="om_sig_map_wms";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_sig_map" => array("om_sig_map", ),
    "om_sig_wms" => array("om_sig_wms", ),
);
// Filtre listing sous formulaire - om_sig_map
if (in_array($retourformulaire, $foreign_keys_extended["om_sig_map"])) {
    $selection = " WHERE (om_sig_map_wms.om_sig_map = '".$idx."') ";
}
// Filtre listing sous formulaire - om_sig_wms
if (in_array($retourformulaire, $foreign_keys_extended["om_sig_wms"])) {
    $selection = " WHERE (om_sig_map_wms.om_sig_wms = '".$idx."') ";
}

?>