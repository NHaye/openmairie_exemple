<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$reqmo['libelle']=_('reqmo-libelle-om_sig_wms');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_sig_wms');
$ent=_('om_sig_wms_om_collectivite');
$reqmo['sql']="select  [om_sig_wms], [libelle], [id], [chemin], [couches], [cache_type], [cache_gfi_chemin], [cache_gfi_couches] from ".DB_PREFIXE."om_sig_wms where om_collectivite = '[om_collectivite]' order by [tri]";
$reqmo['om_sig_wms']='checked';
$reqmo['libelle']='checked';
$reqmo['om_collectivite']="select * from ".DB_PREFIXE."om_collectivite";
$reqmo['id']='checked';
$reqmo['chemin']='checked';
$reqmo['couches']='checked';
$reqmo['cache_type']='checked';
$reqmo['cache_gfi_chemin']='checked';
$reqmo['cache_gfi_couches']='checked';
$reqmo['tri']=array('om_sig_wms','libelle','id','chemin','couches','cache_type','cache_gfi_chemin','cache_gfi_couches');
?>