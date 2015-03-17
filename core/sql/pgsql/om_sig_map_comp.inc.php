<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sig_map_comp.inc.php 2726 2014-03-07 08:59:25Z fmichon $
 */

//
include "../gen/sql/pgsql/om_sig_map_comp.inc.php";

//
$champAffiche = array(
    'om_sig_map_comp.om_sig_map_comp as "'._("om_sig_map_comp").'"',
    'om_sig_map.libelle as "'._("om_sig_map").'"',
    'om_sig_map_comp.libelle as "'._("nom").'"',
    'om_sig_map_comp.ordre as "'._("ord").'"',
    'om_sig_map_comp.actif as "'._("actif").'"',
    'om_sig_map_comp.comp_maj as "'._("maj").'"',
    'om_sig_map_comp.type_geometrie as "'._("rype").'"',
    'om_sig_map_comp.comp_table_update as "'._("table").'"',
    'om_sig_map_comp.comp_champ as "'._("champ").'"',
    );
//
$champNonAffiche = array(
    );
//
$champRecherche = array(
    'om_sig_map_comp.om_sig_map_comp as "'._("om_sig_map_comp").'"',
    'om_sig_map.libelle as "'._("om_sig_map").'"',
    'om_sig_map_comp.libelle as "'._("nom").'"',
    'om_sig_map_comp.ordre as "'._("ord").'"',
    'om_sig_map_comp.actif as "'._("actif").'"',
    'om_sig_map_comp.comp_maj as "'._("maj").'"',
    'om_sig_map_comp.type_geometrie as "'._("rype").'"',
    'om_sig_map_comp.comp_table_update as "'._("table").'"',
    'om_sig_map_comp.comp_champ as "'._("champ").'"',
    );
$tri="ORDER BY om_sig_map_comp.ordre, om_sig_map_comp.libelle ASC NULLS LAST";

?>
