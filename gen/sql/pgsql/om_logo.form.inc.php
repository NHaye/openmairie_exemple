<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("parametrage")." -> "._("om_logo");
$tableSelect=DB_PREFIXE."om_logo";
$champs=array(
    "om_logo",
    "id",
    "libelle",
    "description",
    "fichier",
    "resolution",
    "actif",
    "om_collectivite");
//champs select
$sql_om_collectivite="SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite ORDER BY om_collectivite.libelle ASC";
$sql_om_collectivite_by_id = "SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite WHERE om_collectivite = <idx>";
?>