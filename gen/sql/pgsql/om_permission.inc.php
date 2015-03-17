<?php
//$Id$ 
//gen openMairie le 13/02/2015 17:31

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_permission");
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
$table = DB_PREFIXE."om_permission";
// SELECT 
$champAffiche = array(
    'om_permission.om_permission as "'._("om_permission").'"',
    'om_permission.libelle as "'._("libelle").'"',
    'om_permission.type as "'._("type").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_permission.om_permission as "'._("om_permission").'"',
    'om_permission.libelle as "'._("libelle").'"',
    'om_permission.type as "'._("type").'"',
    );
$tri="ORDER BY om_permission.libelle ASC NULLS LAST";
$edition="om_permission";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

?>