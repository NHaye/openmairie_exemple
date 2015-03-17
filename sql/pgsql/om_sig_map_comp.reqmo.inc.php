<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$reqmo['libelle']=_('reqmo-libelle-om_sig_map_comp');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_sig_map_comp');
$ent=_('om_sig_map_comp');
$reqmo['sql']="select  [om_sig_map_comp], [om_sig_map], [libelle], [ordre], [actif], [comp_maj], [type_geometrie], [comp_table_update], [comp_champ] from ".DB_PREFIXE."om_sig_map_comp  order by [tri]";
$reqmo['om_sig_map_comp']='checked';
$reqmo['om_sig_map']='checked';
$reqmo['libelle']='checked';
$reqmo['ordre']='checked';
$reqmo['actif']='checked';
$reqmo['comp_maj']='checked';
$reqmo['type_geometrie']='checked';
$reqmo['comp_table_update']='checked';
$reqmo['comp_champ']='checked';
$reqmo['tri']=array('om_sig_map_comp','om_sig_map','libelle','ordre','actif','comp_maj','type_geometrie','comp_table_update','comp_champ');
?>