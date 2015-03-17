<?php
/**
 * Ce fichier permet de définir des variables spécifiques à passer dans la
 * méthode formulaire des objets métier
 *
 * @package openmairie_exemple
 * @version SVN : $Id: form.get.specific.inc.php 2376 2013-06-11 09:14:57Z fmichon $
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
 * Ainsi dans la methode formulaire de l'objet en question la valeur de la date
 * de naissance sera accessible
 */

?>
