<?php
/**
 * GEOLOCALISATION - Ce script permet ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: sig_session.php 3012 2015-01-26 10:48:12Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils ('nohtml');
$f->disableLog();

// Vérification de l'activation de l'option localisation
$f->handle_if_no_localisation();

//
$obj=$f->db->escapeSimple($_POST['obj']);
$zoom=$f->db->escapeSimple($_POST['zoom']);
$base=$f->db->escapeSimple($_POST['base']);
if (isset ($_POST['visibility'])) {
    $visibility=$_POST['visibility'];
} else {
    $visibility=null;
}
$seli=$f->db->escapeSimple($_POST['seli']);
$_SESSION['sig_'.$obj]=array("zoom" => $zoom, "base" => $base, "seli" => $seli, "visibility" => $visibility);
$result='ok';
echo $result;

?>
