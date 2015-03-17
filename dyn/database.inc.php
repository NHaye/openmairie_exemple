<?php
/**
 * Ce fichier permet le paramétrage de la connexion à la base de données,
 * chaque entrée du tableau correspond à une base différente. Attention
 * l'index du tableau conn représente l'identifiant du dossier dans lequel
 * seront stockés les fichiers propres a cette base dans l'application.
 * 
 * @package openmairie_exemple
 * @version SVN : $Id: database.inc.php 2566 2013-12-11 15:44:58Z nhaye $
 */

// PostGreSQL
$conn[1] = array(
    "openExemple", // Titre 
    "pgsql", // Type de base
    "pgsql", // Type de base
    "postgres", // Login
    "postgres", // Mot de passe
    "tcp", // Protocole de connexion 
    "localhost", // Nom d'hote
    "5432", // Port du serveur
    "", // Socket
    "openexemple", // Nom de la base
    "AAAA-MM-JJ", // Format de la date
    "openexemple", // Nom du schéma
    "", // Préfixe
    NULL, // Paramétrage pour l'annuaire LDAP
    NULL, // Paramétarge pour le serveur de mail
    "filestorage-default", // Paramétrage pour le stockage des fichiers
);

?>
