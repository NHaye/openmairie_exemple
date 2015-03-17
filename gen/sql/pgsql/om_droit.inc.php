<?php
//$Id: om_droit.inc.php 2839 2014-08-06 17:02:39Z fmichon $ 
//gen openMairie le 06/08/2014 18:57

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_droit");
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
$table = DB_PREFIXE."om_droit
    LEFT JOIN ".DB_PREFIXE."om_profil 
        ON om_droit.om_profil=om_profil.om_profil ";
// SELECT 
$champAffiche = array(
    'om_droit.om_droit as "'._("om_droit").'"',
    'om_droit.libelle as "'._("libelle").'"',
    'om_profil.libelle as "'._("om_profil").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_droit.om_droit as "'._("om_droit").'"',
    'om_droit.libelle as "'._("libelle").'"',
    'om_profil.libelle as "'._("om_profil").'"',
    );
$tri="ORDER BY om_droit.libelle ASC NULLS LAST";
$edition="om_droit";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_profil" => array("om_profil", ),
);
// Filtre listing sous formulaire - om_profil
if (in_array($retourformulaire, $foreign_keys_extended["om_profil"])) {
    $selection = " WHERE (om_droit.om_profil = '".$idx."') ";
}

?>