<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$reqmo['libelle']=_('reqmo-libelle-om_sig_map');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_sig_map');
$ent=_('om_sig_map');
$reqmo['sql']="select  [om_sig_map], [om_collectivite], [id], [libelle], [actif], [zoom], [fond_osm], [fond_bing], [fond_sat], [layer_info], [etendue], [projection_externe], [url], [om_sql], [maj], [table_update], [champ], [retour], [type_geometrie], [lib_geometrie] from ".DB_PREFIXE."om_sig_map  order by [tri]";
$reqmo['om_sig_map']='checked';
$reqmo['om_collectivite']='checked';
$reqmo['id']='checked';
$reqmo['libelle']='checked';
$reqmo['actif']='checked';
$reqmo['zoom']='checked';
$reqmo['fond_osm']='checked';
$reqmo['fond_bing']='checked';
$reqmo['fond_sat']='checked';
$reqmo['layer_info']='checked';
$reqmo['etendue']='checked';
$reqmo['projection_externe']='checked';
$reqmo['url']='checked';
$reqmo['om_sql']='checked';
$reqmo['maj']='checked';
$reqmo['table_update']='checked';
$reqmo['champ']='checked';
$reqmo['retour']='checked';
$reqmo['type_geometrie']='checked';
$reqmo['lib_geometrie']='checked';
$reqmo['tri']=array('om_sig_map','om_collectivite','id','libelle','actif','zoom','fond_osm','fond_bing','fond_sat','layer_info','etendue','projection_externe','url','om_sql','maj','table_update','champ','retour','type_geometrie','lib_geometrie');
?>