<?php
/**
 * Ce fichier permet de ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: var.inc 2675 2014-02-07 14:37:08Z nhaye $
 */

// =========================================================================
// Module de parametrage general
// =========================================================================
// plan utilise en localisation par croquis (spg localisation)
if (isset($_SESSION['coll'])){
  $chemin_plan="../trs/".$_SESSION['coll']."/";
}
// new ***
$siglien="../scr/tab_sig.php?idx=";

// XXX Désactivation du verrou par defaut en attendant de trouver une solution viable
$verrou=0; // verrou =1 actif 0= inactif (empeche actualisation sur form valide)

// test om_sig 1 = oui,   0 = non
$test_om_sig=1;

?>
