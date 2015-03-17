<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$reqmo['libelle']=_('reqmo-libelle-om_requete');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_requete');
$ent=_('om_requete');
$reqmo['sql']="select  [om_requete], [code], [libelle], [description], [requete], [merge_fields] from ".DB_PREFIXE."om_requete  order by [tri]";
$reqmo['om_requete']='checked';
$reqmo['code']='checked';
$reqmo['libelle']='checked';
$reqmo['description']='checked';
$reqmo['requete']='checked';
$reqmo['merge_fields']='checked';
$reqmo['tri']=array('om_requete','code','libelle','description','requete','merge_fields');
?>