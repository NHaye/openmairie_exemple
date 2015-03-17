<?php
/**
 * Ce fichier permet de surcharger des variables de /scr/tab.php.
 *
 * Ordre de surcharge:
 *
 * - Definition (ou non) des variables par /scr/tab.php
 * - Surcharge (ou definition) par /dyn/tab.inc.php
 * - Surcharge (ou definition) par /sql/sgbd/objet.inc.php
 *
 * @package openmairie_exemple
 * @version SVN : $Id: tab.inc.php 2211 2013-03-29 08:49:03Z fmichon $
 */

// {{{ Surcharge des actions

//unset($tab_actions['corner']['ajouter']);
//unset($tab_actions['left']['consulter']);
//unset($tab_actions['left']['modifier']);
//unset($tab_actions['left']['supprimer']);

// }}}

// {{{ Surcharge de la recherche avancee

/* Wildcard

    - Utilisez 'left' => '%' pour ouvrir la recherche a gauche
    - Utilisez 'right' => '%' pour ouvrir la recherche a droite

  Exemple avec
    $options[] = array('type' => 'wildcard', 'left' => '%', 'right' => '%');

  La requete
    SELECT * FROM om_collectivite WHERE libelle like 'collectivite'

  Devient
    SELECT * FROM om_collectivite WHERE libelle like '%collectivite%'
*/

//$options[] = array('type' => 'wildcard', 'left' => '', 'right' => '');

// }}}

?>
