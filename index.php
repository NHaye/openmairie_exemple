<?php
/**
 * Ce fichier permet de faire une redirection vers la page de login de
 * l'application.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: index.php 2261 2013-04-17 09:38:29Z fmichon $
 */

//
$came_from = "";
if (isset($_GET['came_from'])) {
    $came_from = $_GET['came_from'];
}

//
header("Location: scr/login.php?came_from=".urlencode($came_from));

?>
