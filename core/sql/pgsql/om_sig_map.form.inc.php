<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sig_map.form.inc.php 2946 2014-11-03 10:22:38Z baldachino $
 */

//
include "../gen/sql/pgsql/om_sig_map.form.inc.php";

//
$sql_geometry = "select f_table_name,(f_table_name||' '||srid) as lib from geometry_columns order by f_table_name ";
$sql_geometry_champ = "select f_geometry_column,(f_table_name||' '||f_geometry_column) as lib from geometry_columns order by f_table_name ";
$champs=array("om_sig_map","om_collectivite","id",
            "libelle","actif","zoom","fond_osm","fond_bing","fond_sat","layer_info",
            "etendue","projection_externe","url","om_sql","maj",
            "lib_geometrie","type_geometrie","table_update","champ","retour");

$portlet_actions['Copier'] = array(
	'lien' => '../scr/copy_om_sig_map_etendue.php?idx=',
	'id' => '',
	'lib' => '<span class="om-prev-icon om-icon-16 om-icon-fix generate-16" title="'._('Copier étendue').'">'._('Copier étendue').'</span>',
	'rights' => array('list' => array('om_sig_map','om_sig_map_ajouter'), 'operator' => 'OR'),
	'ajax' => false,
	'ordre' => 21,
);

?>
