<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$reqmo['libelle']=_('reqmo-libelle-om_collectivite');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_collectivite');
$ent=_('om_collectivite');
$reqmo['sql']="select  [om_collectivite], [libelle], [niveau] from ".DB_PREFIXE."om_collectivite  order by [tri]";
$reqmo['om_collectivite']='checked';
$reqmo['libelle']='checked';
$reqmo['niveau']='checked';
$reqmo['tri']=array('om_collectivite','libelle','niveau');
?>