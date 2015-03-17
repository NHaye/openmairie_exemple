<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_dashboard");
$tableSelect=DB_PREFIXE."om_dashboard";
$champs=array(
    "om_dashboard",
    "om_profil",
    "bloc",
    "position",
    "om_widget");
//champs select
$sql_om_profil="SELECT om_profil.om_profil, om_profil.libelle FROM ".DB_PREFIXE."om_profil ORDER BY om_profil.libelle ASC";
$sql_om_profil_by_id = "SELECT om_profil.om_profil, om_profil.libelle FROM ".DB_PREFIXE."om_profil WHERE om_profil = <idx>";
$sql_om_widget="SELECT om_widget.om_widget, om_widget.libelle FROM ".DB_PREFIXE."om_widget ORDER BY om_widget.libelle ASC";
$sql_om_widget_by_id = "SELECT om_widget.om_widget, om_widget.libelle FROM ".DB_PREFIXE."om_widget WHERE om_widget = <idx>";
?>