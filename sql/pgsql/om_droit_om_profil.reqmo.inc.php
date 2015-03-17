<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$reqmo['libelle']=_('reqmo-libelle-om_droit');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_droit');
$ent=_('om_droit_om_profil');
$reqmo['sql']="select  [om_droit], [libelle] from ".DB_PREFIXE."om_droit where om_profil = '[om_profil]' order by [tri]";
$reqmo['om_droit']='checked';
$reqmo['libelle']='checked';
$reqmo['om_profil']="select * from ".DB_PREFIXE."om_profil";
$reqmo['tri']=array('om_droit','libelle');
?>