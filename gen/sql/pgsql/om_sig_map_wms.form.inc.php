<?php
//$Id: om_sig_map_wms.form.inc.php 2484 2013-09-18 13:02:28Z nhaye $ 
//gen openMairie le 18/09/2013 14:46

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_sig_map_wms");
$tableSelect=DB_PREFIXE."om_sig_map_wms";
$champs=array(
    "om_sig_map_wms",
    "om_sig_wms",
    "om_sig_map",
    "ol_map",
    "ordre",
    "visibility",
    "panier",
    "pa_nom",
    "pa_layer",
    "pa_attribut",
    "pa_encaps",
    "pa_sql",
    "pa_type_geometrie",
    "sql_filter",
    "baselayer",
    "singletile",
    "maxzoomlevel");
//champs select
$sql_om_sig_map="SELECT om_sig_map.om_sig_map, om_sig_map.libelle FROM ".DB_PREFIXE."om_sig_map ORDER BY om_sig_map.libelle ASC";
$sql_om_sig_map_by_id = "SELECT om_sig_map.om_sig_map, om_sig_map.libelle FROM ".DB_PREFIXE."om_sig_map WHERE om_sig_map = <idx>";
$sql_om_sig_wms="SELECT om_sig_wms.om_sig_wms, om_sig_wms.libelle FROM ".DB_PREFIXE."om_sig_wms ORDER BY om_sig_wms.libelle ASC";
$sql_om_sig_wms_by_id = "SELECT om_sig_wms.om_sig_wms, om_sig_wms.libelle FROM ".DB_PREFIXE."om_sig_wms WHERE om_sig_wms = <idx>";
?>