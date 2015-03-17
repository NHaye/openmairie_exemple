<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_sig_wms");
$tableSelect=DB_PREFIXE."om_sig_wms";
$champs=array(
    "om_sig_wms",
    "libelle",
    "om_collectivite",
    "id",
    "chemin",
    "couches",
    "cache_type",
    "cache_gfi_chemin",
    "cache_gfi_couches");
//champs select
$sql_om_collectivite="SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite ORDER BY om_collectivite.libelle ASC";
$sql_om_collectivite_by_id = "SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite WHERE om_collectivite = <idx>";
?>