<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$reqmo['libelle']=_('reqmo-libelle-om_sig_map_wms');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_sig_map_wms');
$ent=_('om_sig_map_wms_om_sig_map');
$reqmo['sql']="select  [om_sig_map_wms], [om_sig_wms], [ol_map], [ordre], [visibility], [panier], [pa_nom], [pa_layer], [pa_attribut], [pa_encaps], [pa_sql], [pa_type_geometrie], [sql_filter], [baselayer], [singletile], [maxzoomlevel] from ".DB_PREFIXE."om_sig_map_wms where om_sig_map = '[om_sig_map]' order by [tri]";
$reqmo['om_sig_map_wms']='checked';
$reqmo['om_sig_wms']='checked';
$reqmo['om_sig_map']="select * from ".DB_PREFIXE."om_sig_map";
$reqmo['ol_map']='checked';
$reqmo['ordre']='checked';
$reqmo['visibility']='checked';
$reqmo['panier']='checked';
$reqmo['pa_nom']='checked';
$reqmo['pa_layer']='checked';
$reqmo['pa_attribut']='checked';
$reqmo['pa_encaps']='checked';
$reqmo['pa_sql']='checked';
$reqmo['pa_type_geometrie']='checked';
$reqmo['sql_filter']='checked';
$reqmo['baselayer']='checked';
$reqmo['singletile']='checked';
$reqmo['maxzoomlevel']='checked';
$reqmo['tri']=array('om_sig_map_wms','om_sig_wms','ol_map','ordre','visibility','panier','pa_nom','pa_layer','pa_attribut','pa_encaps','pa_sql','pa_type_geometrie','sql_filter','baselayer','singletile','maxzoomlevel');
?>