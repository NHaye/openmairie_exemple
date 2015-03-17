<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_sig_map");
$tableSelect=DB_PREFIXE."om_sig_map";
$champs=array(
    "om_sig_map",
    "om_collectivite",
    "id",
    "libelle",
    "actif",
    "zoom",
    "fond_osm",
    "fond_bing",
    "fond_sat",
    "layer_info",
    "etendue",
    "projection_externe",
    "url",
    "om_sql",
    "maj",
    "table_update",
    "champ",
    "retour",
    "type_geometrie",
    "lib_geometrie");
//champs select
$sql_om_collectivite="SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite ORDER BY om_collectivite.libelle ASC";
$sql_om_collectivite_by_id = "SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite WHERE om_collectivite = <idx>";
?>