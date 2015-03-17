<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sig_map_wms.form.inc.php 2946 2014-11-03 10:22:38Z baldachino $
 */

//
include "../gen/sql/pgsql/om_sig_map_wms.form.inc.php";

//
$champs=array(
    "om_sig_map_wms",
    "om_sig_wms",
    "om_sig_map",
    "ol_map",
    "baselayer",
    "maxzoomlevel",
	"ordre",
    "visibility",
    "singletile",
    "panier",
    "pa_nom",
    "pa_layer",
    "pa_attribut",
    "pa_encaps",
    "pa_sql",
    "pa_type_geometrie",
    "sql_filter");

//champs select
$sql_om_sig_map="SELECT om_sig_map.om_sig_map, om_sig_map.libelle FROM ".DB_PREFIXE."om_sig_map ORDER BY om_sig_map.libelle ASC";
$sql_om_sig_map_by_id = "SELECT om_sig_map.om_sig_map, om_sig_map.libelle FROM ".DB_PREFIXE."om_sig_map WHERE om_sig_map = <idx>";
$sql_om_sig_wms="SELECT om_sig_wms.om_sig_wms, om_sig_wms.libelle||' - '||CASE WHEN cache_type IS NULL OR cache_type='' THEN 'WMS' ELSE cache_type END||CASE WHEN length(cache_gfi_chemin||cache_gfi_couches) > 0 THEN '*' ELSE '.' END FROM ".DB_PREFIXE."om_sig_wms ORDER BY om_sig_wms.libelle ASC";
$sql_om_sig_wms_by_id = "SELECT om_sig_wms.om_sig_wms, om_sig_wms.libelle||' - '||CASE WHEN cache_type IS NULL OR cache_type='' THEN 'WMS' ELSE cache_type END||CASE WHEN length(cache_gfi_chemin||cache_gfi_couches) > 0 THEN '*' ELSE '.' END FROM ".DB_PREFIXE."om_sig_wms WHERE om_sig_wms = <idx>";
$portlet_actions['Copier'] = array(
	'lien' => '../scr/copy_om_sig_map_wms.php?idx=',
	'id' => '',
	'lib' => '<span class="om-prev-icon om-icon-16 om-icon-fix generate-16" title="'._('Copier flux').'">'._('Copier flux').'</span>',
	'rights' => array('list' => array('om_sig_map_wms','om_sig_map_wms_ajouter'), 'operator' => 'OR'),
	'ajax' => false,
	'ordre' => 21,
);
?>
