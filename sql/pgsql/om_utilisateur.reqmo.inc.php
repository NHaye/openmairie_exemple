<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$reqmo['libelle']=_('reqmo-libelle-om_utilisateur');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_utilisateur');
$ent=_('om_utilisateur');
$reqmo['sql']="select  [om_utilisateur], [nom], [email], [login], [pwd], [om_collectivite], [om_type], [om_profil] from ".DB_PREFIXE."om_utilisateur  order by [tri]";
$reqmo['om_utilisateur']='checked';
$reqmo['nom']='checked';
$reqmo['email']='checked';
$reqmo['login']='checked';
$reqmo['pwd']='checked';
$reqmo['om_collectivite']='checked';
$reqmo['om_type']='checked';
$reqmo['om_profil']='checked';
$reqmo['tri']=array('om_utilisateur','nom','email','login','pwd','om_collectivite','om_type','om_profil');
?>