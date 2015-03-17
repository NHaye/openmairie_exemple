<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_utilisateur");
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
$table = DB_PREFIXE."om_utilisateur
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_utilisateur.om_collectivite=om_collectivite.om_collectivite 
    LEFT JOIN ".DB_PREFIXE."om_profil 
        ON om_utilisateur.om_profil=om_profil.om_profil ";
// SELECT 
$champAffiche = array(
    'om_utilisateur.om_utilisateur as "'._("om_utilisateur").'"',
    'om_utilisateur.nom as "'._("nom").'"',
    'om_utilisateur.email as "'._("email").'"',
    'om_utilisateur.login as "'._("login").'"',
    'om_profil.libelle as "'._("om_profil").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_utilisateur.pwd as "'._("pwd").'"',
    'om_utilisateur.om_collectivite as "'._("om_collectivite").'"',
    'om_utilisateur.om_type as "'._("om_type").'"',
    );
//
$champRecherche = array(
    'om_utilisateur.om_utilisateur as "'._("om_utilisateur").'"',
    'om_utilisateur.nom as "'._("nom").'"',
    'om_utilisateur.email as "'._("email").'"',
    'om_utilisateur.login as "'._("login").'"',
    'om_profil.libelle as "'._("om_profil").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
$tri="ORDER BY om_utilisateur.nom ASC NULLS LAST";
$edition="om_utilisateur";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_utilisateur.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
    "om_profil" => array("om_profil", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_utilisateur.om_collectivite = '".$idx."') ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_utilisateur.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_utilisateur.om_collectivite = '".$idx."') ";
    }
}
// Filtre listing sous formulaire - om_profil
if (in_array($retourformulaire, $foreign_keys_extended["om_profil"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_utilisateur.om_profil = '".$idx."') ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_utilisateur.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_utilisateur.om_profil = '".$idx."') ";
    }
}

?>