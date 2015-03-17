<?php
//$Id: om_dashboard.inc.php 2839 2014-08-06 17:02:39Z fmichon $ 
//gen openMairie le 06/08/2014 18:57

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_dashboard");
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
$table = DB_PREFIXE."om_dashboard
    LEFT JOIN ".DB_PREFIXE."om_profil 
        ON om_dashboard.om_profil=om_profil.om_profil 
    LEFT JOIN ".DB_PREFIXE."om_widget 
        ON om_dashboard.om_widget=om_widget.om_widget ";
// SELECT 
$champAffiche = array(
    'om_dashboard.om_dashboard as "'._("om_dashboard").'"',
    'om_profil.libelle as "'._("om_profil").'"',
    'om_dashboard.bloc as "'._("bloc").'"',
    'om_dashboard.position as "'._("position").'"',
    'om_widget.libelle as "'._("om_widget").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_dashboard.om_dashboard as "'._("om_dashboard").'"',
    'om_profil.libelle as "'._("om_profil").'"',
    'om_dashboard.bloc as "'._("bloc").'"',
    'om_dashboard.position as "'._("position").'"',
    'om_widget.libelle as "'._("om_widget").'"',
    );
$tri="ORDER BY om_profil.libelle ASC NULLS LAST";
$edition="om_dashboard";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_profil" => array("om_profil", ),
    "om_widget" => array("om_widget", ),
);
// Filtre listing sous formulaire - om_profil
if (in_array($retourformulaire, $foreign_keys_extended["om_profil"])) {
    $selection = " WHERE (om_dashboard.om_profil = '".$idx."') ";
}
// Filtre listing sous formulaire - om_widget
if (in_array($retourformulaire, $foreign_keys_extended["om_widget"])) {
    $selection = " WHERE (om_dashboard.om_widget = '".$idx."') ";
}

?>