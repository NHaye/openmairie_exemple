<?php
//$Id: om_requete.form.inc.php 2994 2014-12-05 16:03:14Z nmeucci $ 
//gen openMairie le 05/12/2014 16:49

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
    "merge_fields",
    "type",
    "classe",
    "methode");
?>