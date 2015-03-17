<?php
//$Id: om_widget.inc.php 2834 2014-08-05 16:04:22Z fmichon $ 
//gen openMairie le 05/08/2014 17:59

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_widget");
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
$table = DB_PREFIXE."om_widget";
// SELECT 
$champAffiche = array(
    'om_widget.om_widget as "'._("om_widget").'"',
    'om_widget.libelle as "'._("libelle").'"',
    'om_widget.type as "'._("type").'"',
    );
//
$champNonAffiche = array(
    'om_widget.lien as "'._("lien").'"',
    'om_widget.texte as "'._("texte").'"',
    );
//
$champRecherche = array(
    'om_widget.om_widget as "'._("om_widget").'"',
    'om_widget.libelle as "'._("libelle").'"',
    'om_widget.type as "'._("type").'"',
    );
$tri="ORDER BY om_widget.libelle ASC NULLS LAST";
$edition="om_widget";
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
);

?>