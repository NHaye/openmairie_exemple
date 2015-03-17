<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_collectivite");
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
$table = DB_PREFIXE."om_collectivite";
// SELECT 
$champAffiche = array(
    'om_collectivite.om_collectivite as "'._("om_collectivite").'"',
    'om_collectivite.libelle as "'._("libelle").'"',
    'om_collectivite.niveau as "'._("niveau").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_collectivite.om_collectivite as "'._("om_collectivite").'"',
    'om_collectivite.libelle as "'._("libelle").'"',
    'om_collectivite.niveau as "'._("niveau").'"',
    );
$tri="ORDER BY om_collectivite.libelle ASC NULLS LAST";
$edition="om_collectivite";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_collectivite.om_collectivite = '".$_SESSION["collectivite"]."') ";
}

/**
 * Gestion SOUSFORMULAIRE => $sousformulaire
 */
$sousformulaire = array(
    'om_etat',
    'om_lettretype',
    'om_logo',
    'om_parametre',
    'om_sig_map',
    'om_sig_wms',
    'om_sousetat',
    'om_utilisateur',
);

?>