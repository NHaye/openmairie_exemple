<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("parametrage")." -> "._("om_requete");
$tableSelect=DB_PREFIXE."om_requete";
$champs=array(
    "om_requete",
    "code",
    "libelle",
    "description",
    "requete",
    "merge_fields");
?>