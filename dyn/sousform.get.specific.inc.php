<?php
/**
 * Ce fichier permet de definir des variables specifiques a passer dans la
 * methode sousformulaire des objets metier
 *
 * @package openmairie_exemple
 * @version SVN : $Id: sousform.get.specific.inc.php 2200 2013-03-28 17:14:08Z fmichon $
 */

/**
 * Exemple : un ecran specifique me permet de passer la date de naissance de
 * l'utilisateur au formulaire uniquement lorsque l'objet est "agenda".
 *
 * if ($obj == "agenda") {
 *     $datenaissance = "";
 *     if (isset($_GET['datenaissance'])) {
 *         $datenaissance = $_GET['datenaissance'];
 *     }
 *     $extra_parameters["datenaissance"] = $datenaissance;
 * }
 *
 * Ainsi dans la methode sousformulaire de l'objet en question la valeur de la date
 * de naissance sera accessible
 */

?>
