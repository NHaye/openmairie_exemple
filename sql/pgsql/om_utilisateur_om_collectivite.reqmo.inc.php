<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$reqmo['libelle']=_('reqmo-libelle-om_utilisateur');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_utilisateur');
$ent=_('om_utilisateur_om_collectivite');
$reqmo['sql']="select  [om_utilisateur], [nom], [email], [login], [pwd], [om_type], [om_profil] from ".DB_PREFIXE."om_utilisateur where om_collectivite = '[om_collectivite]' order by [tri]";
$reqmo['om_utilisateur']='checked';
$reqmo['nom']='checked';
$reqmo['email']='checked';
$reqmo['login']='checked';
$reqmo['pwd']='checked';
$reqmo['om_collectivite']="select * from ".DB_PREFIXE."om_collectivite";
$reqmo['om_type']='checked';
$reqmo['om_profil']='checked';
$reqmo['tri']=array('om_utilisateur','nom','email','login','pwd','om_type','om_profil');
?>