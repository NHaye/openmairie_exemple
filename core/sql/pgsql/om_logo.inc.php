<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_logo.inc.php 2860 2014-08-28 13:18:45Z fmichon $
 */

//
include "../gen/sql/pgsql/om_logo.inc.php";

//
$champAffiche = array(
    'om_logo.om_logo as "'._("om_logo").'"',
    'om_logo.id as "'._("id").'"',
    'om_logo.libelle as "'._("libelle").'"',
    'om_logo.description as "'._("description").'"',
    'om_logo.fichier as "'._("fichier").'"',
    'om_logo.resolution as "'._("resolution").'"',
    "case om_logo.actif when 't' then 'Oui' else 'Non' end as \""._("actif")."\"",
    'om_collectivite.niveau as "'._("niveau").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champRecherche = array(
    'om_logo.om_logo as "'._("om_logo").'"',
    'om_logo.id as "'._("id").'"',
    'om_logo.libelle as "'._("libelle").'"',
    'om_logo.description as "'._("description").'"',
    'om_logo.resolution as "'._("resolution").'"',
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
