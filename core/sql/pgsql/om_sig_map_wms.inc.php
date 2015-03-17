<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sig_map_wms.inc.php 2726 2014-03-07 08:59:25Z fmichon $
 */

//
include "../gen/sql/pgsql/om_sig_map_wms.inc.php";

//
$champAffiche = array(
    'om_sig_map_wms.om_sig_map_wms as "'._("id").'"',
    'om_sig_map.libelle as "'._("om_sig_map").'"',
    'om_sig_wms.libelle||\' (\'||CASE WHEN cache_type IS NULL OR cache_type = \'\' THEN \'WMS\' ELSE cache_type END||\')\'  as "'._("flux").'"',
    'om_sig_map_wms.ol_map as "'._("nom OL").'"',
    'om_sig_map_wms.baselayer as "'._("base").'"',
    'om_sig_map_wms.ordre as "'._("ordre").'"',
    'om_sig_map_wms.visibility as "'._("vis.").'"',
    'om_sig_map_wms.panier as "'._("panier").'"',
    );
//
$champNonAffiche = array(
    'om_sig_map_wms.pa_nom as "'._("pa_nom").'"',
    'om_sig_map_wms.pa_layer as "'._("pa_layer").'"',
    'om_sig_map_wms.pa_sql as "'._("pa_sql").'"',
    'om_sig_map_wms.pa_attribut as "'._("pa_attribut").'"',
    'om_sig_map_wms.pa_encaps as "'._("pa_encaps").'"',
    'om_sig_map_wms.pa_type_geometrie as "'._("pa_type_geometrie").'"',
    'om_sig_map_wms.sql_filter as "'._("sql_filter").'"',
    'om_sig_map_wms.singletile as "'._("singletile").'"',
    'om_sig_map_wms.maxzoomlevel as "'._("maxzoomlevel").'"',
    );
//
$champRecherche = array(
    'om_sig_map_wms.om_sig_map_wms as "'._("id").'"',
    'om_sig_map.libelle as "'._("om_sig_map").'"',
    'om_sig_wms.libelle||\' (\'||CASE WHEN cache_type IS NULL OR cache_type = \'\' THEN \'WMS\' ELSE cache_type END||\')\'  as "'._("flux").'"',
    'om_sig_map_wms.ol_map as "'._("nom OL").'"',
    'om_sig_map_wms.baselayer as "'._("base").'"',
    'om_sig_map_wms.ordre as "'._("ordre").'"',
    'om_sig_map_wms.visibility as "'._("vis.").'"',
    'om_sig_map_wms.panier as "'._("panier").'"',
    'om_sig_map_wms.pa_nom as "'._("pa_nom").'"',
    'om_sig_map_wms.pa_layer as "'._("pa_layer").'"',
    'om_sig_map_wms.pa_sql as "'._("pa_sql").'"',
    'om_sig_map_wms.pa_attribut as "'._("pa_attribut").'"',
    'om_sig_map_wms.pa_encaps as "'._("pa_encaps").'"',
    'om_sig_map_wms.pa_type_geometrie as "'._("pa_type_geometrie").'"',
    'om_sig_map_wms.sql_filter as "'._("sql_filter").'"',
    'om_sig_map_wms.singletile as "'._("singletile").'"',
    'om_sig_map_wms.maxzoomlevel as "'._("maxzoomlevel").'"',
    );

//
$tri="ORDER BY om_sig_map_wms.baselayer,om_sig_map_wms.ordre, om_sig_wms.libelle ";

?>
