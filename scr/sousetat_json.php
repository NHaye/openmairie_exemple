<?php
/**
 * Script permettant de récupérer les sous états pour les intégrer à tinymce
 *
 * @package openmairie_exemple
 * @version SVN : $Id: sousetat_json.php 2921 2014-10-10 08:12:21Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");
$f->disableLog();

// XXX
$f->isAccredited(array("om_lettretype_modifier", "om_etat_modifier"), "OR");

//
$sql = "SELECT id, libelle FROM ".DB_PREFIXE."om_sousetat WHERE actif IS TRUE";
// Exécution de la requête
$res = $f->db->query($sql);
// Logger
$f->addToLog("scr/sousetat_json.php: db->query(\"".$sql."\");", VERBOSE_MODE);
// Gestion d'une éventuelle erreur de base de données
$f->isDatabaseError();

//
while($row = $res->fetchrow(DB_FETCHMODE_ASSOC)) {
    $tab[$row['id']] = $row['libelle'];
}

//
echo  json_encode($tab);

?>
