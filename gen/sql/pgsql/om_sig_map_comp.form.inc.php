<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_sig_map_comp");
$tableSelect=DB_PREFIXE."om_sig_map_comp";
$champs=array(
    "om_sig_map_comp",
    "om_sig_map",
    "libelle",
    "ordre",
    "actif",
    "comp_maj",
    "type_geometrie",
    "comp_table_update",
    "comp_champ");
//champs select
$sql_om_sig_map="SELECT om_sig_map.om_sig_map, om_sig_map.libelle FROM ".DB_PREFIXE."om_sig_map ORDER BY om_sig_map.libelle ASC";
$sql_om_sig_map_by_id = "SELECT om_sig_map.om_sig_map, om_sig_map.libelle FROM ".DB_PREFIXE."om_sig_map WHERE om_sig_map = <idx>";
?>