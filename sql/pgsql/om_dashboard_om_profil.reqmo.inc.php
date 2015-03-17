<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$reqmo['libelle']=_('reqmo-libelle-om_dashboard');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_dashboard');
$ent=_('om_dashboard_om_profil');
$reqmo['sql']="select  [om_dashboard], [bloc], [position], [om_widget] from ".DB_PREFIXE."om_dashboard where om_profil = '[om_profil]' order by [tri]";
$reqmo['om_dashboard']='checked';
$reqmo['om_profil']="select * from ".DB_PREFIXE."om_profil";
$reqmo['bloc']='checked';
$reqmo['position']='checked';
$reqmo['om_widget']='checked';
$reqmo['tri']=array('om_dashboard','bloc','position','om_widget');
?>