<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_droit");
$tableSelect=DB_PREFIXE."om_droit";
$champs=array(
    "om_droit",
    "libelle",
    "om_profil");
//champs select
$sql_om_profil="SELECT om_profil.om_profil, om_profil.libelle FROM ".DB_PREFIXE."om_profil ORDER BY om_profil.libelle ASC";
$sql_om_profil_by_id = "SELECT om_profil.om_profil, om_profil.libelle FROM ".DB_PREFIXE."om_profil WHERE om_profil = <idx>";
?>