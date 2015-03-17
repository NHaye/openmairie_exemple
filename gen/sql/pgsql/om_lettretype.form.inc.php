<?php
//$Id: om_lettretype.form.inc.php 2619 2014-01-07 14:47:22Z nhaye $ 
//gen openMairie le 07/01/2014 15:45

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("parametrage")." -> "._("om_lettretype");
$tableSelect=DB_PREFIXE."om_lettretype";
$champs=array(
    "om_lettretype",
    "om_collectivite",
    "id",
    "libelle",
    "actif",
    "orientation",
    "format",
    "logo",
    "logoleft",
    "logotop",
    "titre_om_htmletat",
    "titreleft",
    "titretop",
    "titrelargeur",
    "titrehauteur",
    "titrebordure",
    "corps_om_htmletatex",
    "om_sql",
    "margeleft",
    "margetop",
    "margeright",
    "margebottom",
    "se_font",
    "se_couleurtexte");
//champs select
$sql_om_collectivite="SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite ORDER BY om_collectivite.libelle ASC";
$sql_om_collectivite_by_id = "SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite WHERE om_collectivite = <idx>";
$sql_om_sql="SELECT om_requete.om_requete, om_requete.libelle FROM ".DB_PREFIXE."om_requete ORDER BY om_requete.libelle ASC";
$sql_om_sql_by_id = "SELECT om_requete.om_requete, om_requete.libelle FROM ".DB_PREFIXE."om_requete WHERE om_requete = <idx>";
?>