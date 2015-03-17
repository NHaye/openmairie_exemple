<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$reqmo['libelle']=_('reqmo-libelle-om_parametre');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_parametre');
$ent=_('om_parametre_om_collectivite');
$reqmo['sql']="select  [om_parametre], [libelle], [valeur] from ".DB_PREFIXE."om_parametre where om_collectivite = '[om_collectivite]' order by [tri]";
$reqmo['om_parametre']='checked';
$reqmo['libelle']='checked';
$reqmo['valeur']='checked';
$reqmo['om_collectivite']="select * from ".DB_PREFIXE."om_collectivite";
$reqmo['tri']=array('om_parametre','libelle','valeur');
?>