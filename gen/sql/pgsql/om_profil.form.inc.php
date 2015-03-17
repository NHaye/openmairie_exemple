<?php
//$Id: om_profil.form.inc.php 2381 2013-06-11 10:37:35Z fmichon $ 
//gen openMairie le 05/10/2012 13:40

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_profil");
$tableSelect=DB_PREFIXE."om_profil";
$champs=array(
    "om_profil",
    "libelle",
    "hierarchie");
?>