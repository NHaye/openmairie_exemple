<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_parametre.inc.php 2726 2014-03-07 08:59:25Z fmichon $
 */

//
include "../gen/sql/pgsql/om_parametre.inc.php";

// SELECT 
$champAffiche = array(
    'om_parametre.om_parametre as "'._("om_parametre").'"',
    'om_parametre.libelle as "'._("libelle").'"',
    'om_parametre.valeur as "'._("valeur").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champRecherche = array(
    'om_parametre.om_parametre as "'._("om_parametre").'"',
    'om_parametre.libelle as "'._("libelle").'"',
    'om_parametre.valeur as "'._("valeur").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \""._("collectivite")."\"");
}

?>
