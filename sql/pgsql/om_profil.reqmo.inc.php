<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$reqmo['libelle']=_('reqmo-libelle-om_profil');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_profil');
$ent=_('om_profil');
$reqmo['sql']="select  [om_profil], [libelle], [hierarchie] from ".DB_PREFIXE."om_profil  order by [tri]";
$reqmo['om_profil']='checked';
$reqmo['libelle']='checked';
$reqmo['hierarchie']='checked';
$reqmo['tri']=array('om_profil','libelle','hierarchie');
?>