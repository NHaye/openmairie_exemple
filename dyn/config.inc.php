<?php
/**
 * Ce fichier permet de configurer divers parametres de l'application
 *
 * @package openmairie_exemple
 * @version SVN : $Id: config.inc.php 2675 2014-02-07 14:37:08Z nhaye $
 */

/**
 * 
 */
$config = array();

//
$config['application'] = _("openExemple");

//
$config['title'] = ":: "._("openMairie")." :: "._("openExemple - Framework");

//
$config['session_name'] = "1bb484de79f96a7d0b00ff463c18fcbf";

/**
 * Mode demonstration de l'application
 * Permet de pre-remplir le formulaire de login avec l'identifiant 'demo' et le 
 * mot de passe 'demo'
 * Default : $config['demo'] = false;
 */
//$config['demo'] = false;

/**
 * Configuration des extensions autorisees dans le module upload.php
 * Pour ajouter votre configuration, decommenter la ligne et modifier les extensions
 * avec des ; comme separateur
 * Default : $config['upload_extension'] = ".gif;.jpg;.jpeg;.png;.txt;.pdf;.csv;";
 */
//$config['upload_extension'] = ".gif;.jpg;.jpeg;.png;.txt;.pdf;.csv;";

/**
 * Configuration de la taille maximale autorisée dans le module upload.php
 * Pour ajouter votre configuration, decommenter la ligne et modifier la taille
 * La taille maximale est en mo.
 * Default : $config['upload_taille_max'] = str_replace('M', '', ini_get('upload_max_filesize')) * 1024
 */
//$config['upload_taille_max'] = str_replace('M', '', ini_get('upload_max_filesize')) * 1024;

/**
 * Nombre de colonnes sur le tableau de bord
 * Permet de choisir le nombre de colonnes presentes sur le tableau de bord de
 * l'application
 * (Attention la modification de cette option doit etre suivie de la
 * modification des donnees dans la base car des widgets existent peut etre dans
 * des colonnes supprimees)
 * Default : $config['dashboard_nb_column'] = 3;
 */
//$config['dashboard_nb_column'] = 3;

/**
 * Activation de la redefinition du mot de passe
 * Permet de redefinir son mot de passe en cas d'oubli via un lien sur le formulaire
 * de login (Attention un serveur de mail doit etre configure)
 * Default : $config['password_reset'] = false;
 */
//$config['password_reset'] = false;

/**
 * Parametre de securite
 * Permet de definir que si le droit necessaire pour acceder a une fonction
 * n'est pas paramatre alors la permission est donne a l'utilisateur
 * (Attention cette option ne doit etre utilisee que pour le developpement)
 * Default : $config['permission_if_right_does_not_exist'] = false;
 */
//$config['permission_if_right_does_not_exist'] = true;

/**
 * Parametre de securite
 * Permet de definir si la gestion des profils se fait de maniere
 * hierarchique ou non. Si on decide d'utiliser les profils hierarchiques alors
 * un utilisateur qui a le profil SUPER UTILISATEUR (hierarchie 4) peut
 * effectuer toutes les actions possibles pour un utilisateur qui a le profil
 * UTILISATEUR (hierarchie 3). Par contre si on decide d'utiliser les profils
 * non hierarchiques, l'utilisateur qui a le profil SUPER UTILISATEUR ne peut
 * effectuer que les actions sui lui sont permises specifiquement.
 * (Attention la modification de cette option doit etre suivie de la
 * modification complete du parametrage des droits)
 * Default : $config['permission_by_hierarchical_profile'] = true;
 */
//$config['permission_by_hierarchical_profile'] = false;

/**
 * Parametre de gestion des nouvelles actions
 * Permet de definir si la gestion des actions se fait dans la classe ou non.
 * Si on decide d'utiliser les nouvelles actions alors il n'y à pas de
 * retro-compatibilité, les actions supplémentaires de portlet initialement 
 * initialisée dans sql/pgsql/*.form.inc.php ne fonctioneront plus et devront
 * être initialisées dans les attributs de la classe ciblée.
 * Default : $config['activate_class_action'] = true;
 */
// $config['activate_class_action'] = false;
?>
