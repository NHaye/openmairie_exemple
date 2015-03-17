<?php
/**
 * Ce fichier permet de configurer l'envoie de mail
 *
 * @package openmairie_exemple
 * @version SVN : $Id: mail.inc.php 2494 2013-09-20 12:18:16Z fmichon $
 */

/**
 *
 */
$mail = array();

/**
 * Informations sur $mail
 *
 *  La variable $mail est un tableau associatif. Ce tableau peut, de ce
 *  fait, contenir plusieurs configurations de serveur mail différentes.
 *
 *  Chaque serveur est représente par une cle de tableau. Ces cles se
 *  retrouvent dans le fichier database.inc.php et permettent d'associer
 *  une base de donnees precise a un serveur mail precis.
 *
 *  Les autres cles de configuration :
 *
 *       mail_host -> Adresse du serveur de mail
 *       mail_port -> Port d'ecoute du serveur de mail
 *       
 *       mail_username -> Identifiant de l'utilisateur du serveur de mail
 *       mail_pass     -> Mot de passe de cet utilisateur
 *
 *       mail_from      -> Adresse email de l'expediteur
 *       mail_from_name -> Nom de l'expediteur
 */
$mail["mail-default"] = array(
    'mail_host' => 'mail.exemple.com',
    'mail_port' => '25',
    'mail_username' => 'nospam@exemple.com',
    'mail_pass' => 'mot_de_passe',
    'mail_from' => 'contact@exemple.com',
    'mail_from_name' => 'exemple',
);

?>
