<?php
//$Id: om_collectivite.form.inc.php 2381 2013-06-11 10:37:35Z fmichon $ 
//gen openMairie le 22/08/2012 17:05

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_collectivite");
$tableSelect=DB_PREFIXE."om_collectivite";
$champs=array(
    "om_collectivite",
    "libelle",
    "niveau");
?>