<?php
//$Id: om_profil.inc.php 2834 2014-08-05 16:04:22Z fmichon $ 
//gen openMairie le 05/08/2014 17:59

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_profil");
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
$table = DB_PREFIXE."om_profil";
// SELECT 
$champAffiche = array(
    'om_profil.om_profil as "'._("om_profil").'"',
    'om_profil.libelle as "'._("libelle").'"',
    'om_profil.hierarchie as "'._("hierarchie").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_profil.om_profil as "'._("om_profil").'"',
    'om_profil.libelle as "'._("libelle").'"',
    'om_profil.hierarchie as "'._("hierarchie").'"',
    );
$tri="ORDER BY om_profil.libelle ASC NULLS LAST";
$edition="om_profil";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_dashboard',
    'om_droit',
    'om_utilisateur',
);

?>