<?php
/**
 * Script permettant de récupérer les sous états pour les intégrer à tinymce
 *
 * @package openmairie_exemple
 * @version SVN : $Id: sousetat_json.php 3088 2015-03-05 06:28:28Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");
$f->disableLog();

// XXX
$f->isAccredited(array("om_lettretype_modifier", "om_etat_modifier"), "OR");

//
$sql = "SELECT id, libelle FROM ".DB_PREFIXE."om_sousetat WHERE actif IS TRUE order by om_sousetat.libelle";
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
