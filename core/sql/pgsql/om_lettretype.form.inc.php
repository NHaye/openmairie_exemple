<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_lettretype.form.inc.php 2726 2014-03-07 08:59:25Z fmichon $
 */

//
include "../gen/sql/pgsql/om_lettretype.form.inc.php";

//
$tableSelect .= " LEFT JOIN ".DB_PREFIXE."om_requete ";
$tableSelect .= " ON om_lettretype.om_sql=om_requete.om_requete ";

$champs=array(
    "om_lettretype",
    "om_collectivite",
    "om_lettretype.id",
    "om_lettretype.libelle",
    "actif",
    "orientation",
    "format",
    "logo",
    "logoleft",
    "logotop",
    "margeleft",
    "margetop",
    "margeright",
    "margebottom",
    "titre_om_htmletat",
    "titreleft",
    "titretop",
    "titrelargeur",
    "titrehauteur",
    "titrebordure",
    "corps_om_htmletatex",
    "om_sql",
    "merge_fields",
    "se_font",
    "se_couleurtexte");

$sql_om_sousetat="select id, libelle from ".DB_PREFIXE."om_sousetat";
$sql_om_sousetat.=" where actif IS TRUE and om_collectivite=".$_SESSION['collectivite'];
$sql_om_sousetat.=" order by libelle";

$sql_om_sousetat_by_id="select id,libelle from ".DB_PREFIXE."om_sousetat";
$sql_om_sousetat_by_id.=" ";

$sql_om_logo="select id, (libelle||' ('||id||')') as libelle from ".DB_PREFIXE."om_logo";
$sql_om_logo.=" where actif IS TRUE and om_collectivite=".$_SESSION['collectivite'];
$sql_om_logo.=" order by libelle";

$sql_om_logo_by_id="select id, (libelle||' ('||id||')') as libelle from ".DB_PREFIXE."om_logo";
$sql_om_logo_by_id.=" WHERE id = '<idx>'";

?>
