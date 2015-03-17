<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$reqmo['libelle']=_('reqmo-libelle-om_widget');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_widget');
$ent=_('om_widget');
$reqmo['sql']="select  [om_widget], [libelle], [lien], [texte], [type] from ".DB_PREFIXE."om_widget  order by [tri]";
$reqmo['om_widget']='checked';
$reqmo['libelle']='checked';
$reqmo['lien']='checked';
$reqmo['texte']='checked';
$reqmo['type']='checked';
$reqmo['tri']=array('om_widget','libelle','lien','texte','type');
?>