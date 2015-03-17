<?php
/**
 * Ce fichier permet de configurer les connexions a des annuaires LDAP.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: directory.inc.php 2198 2013-03-28 17:08:33Z fmichon $
 */

/**
 * Tableau de configuration de l'annuaire LDAP.
 */
$directory = array();

/**
 * Informations sur $directory
 *
 *  La variable $directory est un tableau associatif. Ce tableau peut, de ce
 *  fait, contenir plusieurs configurations d'annuaires LDAP differentes.
 *  Chaque connexion est representee par une clef de tableau.
 *
 *  Ces clefs se retrouvent dans le fichier database.inc.php et permettent
 *  d'associer une base de donnees precise a un annuaire LDAP precis.
 *
 *  Les autres clefs de configuration :
 *
 *       ldap_server      -> Adresse du serveur LDAP
 *       ldap_server_port -> Port d'ecoute du serveur LDAP
 *       
 *       ldap_admin_login  -> identifiant de l'administrateur LDAP
 *       ldap_admin_passwd -> mot de passe de cet administrateur
 *
 *       ldap_base       -> Base de l'arbre LDAP
 *       ldap_base_users -> Base utilisateurs de l'arbre LDAP
 *
 *       ldap_user_filter  -> Filtre utiliser par la fonction ldap_search
 *       ldap_login_attrib -> Attribut LDAP qui sera utilise comme login dans la base
 *
 *       ldap_more_attrib -> Correspondance des champs entre l'annuaire et la base
 *
 *          EX: 'ldap_more_attrib' => array('nom' => 'name',
 *                                          'email' => array('mail', 'mailAddress'))
 *
 *          Ici la colonne 'nom' de la base de donnees sera synchronisee avec
 *          l'attribut 'name' de l'annuaire.
 *
 *          De plus la colonne 'email' sera synchronisee avec l'attribut 'mail'
 *          de l'annuaire. Si l'attribut 'mail' n'est pas trouve dans le schema
 *          LDAP, l'attribut 'mailAddress' sera utilise a la place. Il est
 *          possible de specifier plusieurs attributs en utilisant un tableau de
 *          cette maniere.
 *
 *       default_om_profil -> Profil des utilisateurs ajoutes depuis l'annuaire
 */

$directory["ldap-default"] = array(
    'ldap_server' => 'localhost',
    'ldap_server_port' => '389',
    'ldap_admin_login' => 'cn=admin,dc=openmairie,dc=org',
    'ldap_admin_passwd' => 'admin',
    'ldap_base' => 'dc=openmairie,dc=org',
    'ldap_base_users' => 'dc=openmairie,dc=org',
    'ldap_user_filter' => 'objectclass=person',
    'ldap_login_attrib' => 'cn',
    'ldap_more_attrib' => array(),
    'default_om_profil' => 1,
);

?>
