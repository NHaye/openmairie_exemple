<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$reqmo['libelle']=_('reqmo-libelle-om_droit');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_droit');
$ent=_('om_droit');
$reqmo['sql']="select  [om_droit], [libelle], [om_profil] from ".DB_PREFIXE."om_droit  order by [tri]";
$reqmo['om_droit']='checked';
$reqmo['libelle']='checked';
$reqmo['om_profil']='checked';
$reqmo['tri']=array('om_droit','libelle','om_profil');
?>