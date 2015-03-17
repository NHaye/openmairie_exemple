<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_etat.inc.php 2860 2014-08-28 13:18:45Z fmichon $
 */

//
include "../gen/sql/pgsql/om_etat.inc.php";

//
$champAffiche = array(
    'om_etat.om_etat as "'._("om_etat").'"',
    'om_etat.id as "'._("id").'"',
    'om_etat.libelle as "'._("libelle").'"',
    "case om_etat.actif when 't' then 'Oui' else 'Non' end as \""._("actif")."\"",
    'om_collectivite.niveau as "'._("niveau").'"',
);
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champRecherche = array(
    'om_etat.om_etat as "'._("om_etat").'"',
    'om_etat.id as "'._("id").'"',
    'om_etat.libelle as "'._("libelle").'"',
    'om_collectivite.niveau as "'._("niveau").'"',
);
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
if ($_SESSION['niveau'] == '2') {
    $selection = "";
    if ($retourformulaire== 'om_requete') {
        $selection .= " WHERE (".$obj.".om_sql ='".$idx."')";
    }
} else {
    $selection = " WHERE (".$obj.".om_collectivite='".$_SESSION['collectivite']."' or niveau='2')";
    if ($retourformulaire== 'om_requete') {
        $selection .= " AND (".$obj.".om_sql ='".$idx."')";
    }
}

?>
