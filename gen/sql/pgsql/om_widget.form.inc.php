<?php
//$Id: om_widget.form.inc.php 2447 2013-08-27 16:46:03Z fmichon $ 
//gen openMairie le 23/08/2013 18:26

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_widget");
$tableSelect=DB_PREFIXE."om_widget";
$champs=array(
    "om_widget",
    "libelle",
    "lien",
    "texte",
    "type");
?>