<?php
//$Id: om_requete.inc.php 2994 2014-12-05 16:03:14Z nmeucci $ 
//gen openMairie le 05/12/2014 16:49

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("parametrage")." -> "._("om_requete");
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
$table = DB_PREFIXE."om_requete";
// SELECT 
$champAffiche = array(
    'om_requete.om_requete as "'._("om_requete").'"',
    'om_requete.code as "'._("code").'"',
    'om_requete.libelle as "'._("libelle").'"',
    'om_requete.description as "'._("description").'"',
    'om_requete.type as "'._("type").'"',
    'om_requete.classe as "'._("classe").'"',
    'om_requete.methode as "'._("methode").'"',
    );
//
$champNonAffiche = array(
    'om_requete.requete as "'._("requete").'"',
    'om_requete.merge_fields as "'._("merge_fields").'"',
    );
//
$champRecherche = array(
    'om_requete.om_requete as "'._("om_requete").'"',
    'om_requete.code as "'._("code").'"',
    'om_requete.libelle as "'._("libelle").'"',
    'om_requete.description as "'._("description").'"',
    'om_requete.type as "'._("type").'"',
    'om_requete.classe as "'._("classe").'"',
    'om_requete.methode as "'._("methode").'"',
    );
$tri="ORDER BY om_requete.libelle ASC NULLS LAST";
$edition="om_requete";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
$selection = "";

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_etat',
    'om_lettretype',
);

?>