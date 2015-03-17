<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_collectivite.inc.php 2726 2014-03-07 08:59:25Z fmichon $ 
 */

//
include "../gen/sql/pgsql/om_collectivite.inc.php";

// Pas d'action ajouter en niveau 1
if ($_SESSION['niveau'] == "1") {
    $tab_actions['corner']['ajouter'] = array('lien' => '#');
}

//
$sousformulaire = array(
    //
    'om_utilisateur',
    //
    'om_parametre',
    //
    'om_etat',
    'om_lettretype',
    'om_sousetat',
);

//
if (isset($f) && $f->getParameter("option_localisation") == "sig_interne") {
    $sousformulaire[] = "om_sig_map";
    $sousformulaire[] = "om_sig_wms";
}

?>
