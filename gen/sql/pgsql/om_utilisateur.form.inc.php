<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_utilisateur");
$tableSelect=DB_PREFIXE."om_utilisateur";
$champs=array(
    "om_utilisateur",
    "nom",
    "email",
    "login",
    "pwd",
    "om_collectivite",
    "om_type",
    "om_profil");
//champs select
$sql_om_collectivite="SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite ORDER BY om_collectivite.libelle ASC";
$sql_om_collectivite_by_id = "SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite WHERE om_collectivite = <idx>";
$sql_om_profil="SELECT om_profil.om_profil, om_profil.libelle FROM ".DB_PREFIXE."om_profil ORDER BY om_profil.libelle ASC";
$sql_om_profil_by_id = "SELECT om_profil.om_profil, om_profil.libelle FROM ".DB_PREFIXE."om_profil WHERE om_profil = <idx>";
?>