<?php
/**
 * 
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sousetat.inc.php 2860 2014-08-28 13:18:45Z fmichon $
 */

//
include "../gen/sql/pgsql/om_sousetat.inc.php";

//
$champAffiche = array(
    'om_sousetat.om_sousetat as "'._("om_sousetat").'"',
    'om_sousetat.id as "'._("id").'"',
    'om_sousetat.libelle as "'._("libelle").'"',
    "case om_sousetat.actif when 't' then 'Oui' else 'Non' end as \""._("actif")."\"",
    'om_collectivite.niveau as "'._("niveau").'"',
);
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champRecherche = array(
    'om_sousetat.om_sousetat as "'._("om_sousetat").'"',
    'om_sousetat.id as "'._("id").'"',
    'om_sousetat.libelle as "'._("libelle").'"',
    'om_collectivite.niveau as "'._("niveau").'"',
);
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
if ($_SESSION['niveau'] == '2') {
    $selection = "";
} else {
    $selection = " where (".$obj.".om_collectivite='".$_SESSION['collectivite']."' or niveau='2')";
}

?>
