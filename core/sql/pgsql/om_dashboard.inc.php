<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_dashboard.inc.php 2726 2014-03-07 08:59:25Z fmichon $
 */

//
include "../gen/sql/pgsql/om_dashboard.inc.php";

// Titre de la page (Fil d'ariane)
$ent = _("administration")." -> "._("tableaux de bord")." -> "._("om_dashboard");
if (isset($idx) && $idx != ']' && trim($idx) != '') {
    $ent .= "->&nbsp;".$idx."&nbsp;";
}
if (isset($idz) && trim($idz) != '') {
    $ent .= "&nbsp;".strtoupper($idz)."&nbsp;";
}

?>
