<?php
//$Id$ 
//gen openMairie le 13/02/2015 17:31

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("administration")." -> "._("om_permission");
$tableSelect=DB_PREFIXE."om_permission";
$champs=array(
    "om_permission",
    "libelle",
    "type");
?>