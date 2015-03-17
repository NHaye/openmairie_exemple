<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$reqmo['libelle']=_('reqmo-libelle-om_logo');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_logo');
$ent=_('om_logo_om_collectivite');
$reqmo['sql']="select  [om_logo], [id], [libelle], [description], [fichier], [resolution], [actif] from ".DB_PREFIXE."om_logo where om_collectivite = '[om_collectivite]' order by [tri]";
$reqmo['om_logo']='checked';
$reqmo['id']='checked';
$reqmo['libelle']='checked';
$reqmo['description']='checked';
$reqmo['fichier']='checked';
$reqmo['resolution']='checked';
$reqmo['actif']='checked';
$reqmo['om_collectivite']="select * from ".DB_PREFIXE."om_collectivite";
$reqmo['tri']=array('om_logo','id','libelle','description','fichier','resolution','actif');
?>