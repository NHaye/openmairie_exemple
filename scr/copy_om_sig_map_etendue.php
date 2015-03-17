<?php
/**
 * GEOLOCALISATION - Ce script permet ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id$
 */

require_once "../obj/utils.class.php";
$f = new utils(null, "om_sig_map_wms", _("copie"));

// Vérification de l'activation de l'option localisation
$f->handle_if_no_localisation();

// On affiche la description du traitement
$description = _("Ce traitement permet de copier l'étendue d'une carte sur toutes les autres.");
$f->displayDescription($description);

// Ce traitement peut durer longtemps, on fixe donc sa durée maximale à 3 minutes
set_time_limit(180);

// Si le paramètre n'est pas défini ou que ce n'est pas un entier
if (!isset($_GET["idx"]) || !is_numeric($_GET["idx"])) {
    // On affiche un message d'erreur
    $class = "error";
    $message = _("L'objet est invalide.");
    $f->displayMessage($class, $message);
    // On interrompt le script
    die();
}
// On prend la valeur entière du paramètre
$idx = intval($_GET["idx"]);

// Requête de mise à jour de l'étendue de toutes les cartes à partir de 
// l'étendue de la carte dont l'identifiant est passé en paramètre
$sql = "
    UPDATE ".DB_PREFIXE."om_sig_map 
    SET etendue=m.etendue 
       FROM ".DB_PREFIXE."om_sig_map m 
       WHERE m.om_sig_map=".$idx;
// Exécution de la requête
$res = $f->db->query($sql);
// Logger
$f->addToLog("../scr/copy_om_sig_map_etendue.php: db->query(\"".$sql."\");", VERBOSE_MODE);
// Vérification d'une éventuelle erreur de base de données
$f->isDatabaseError($res);
// On affiche un message de validation
$class = "valid";
$message = _("Copie terminée");
$f->displayMessage($class, $message);

?>
