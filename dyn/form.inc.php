<?php
/**
 * Ce fichier permet de surcharger des variables de /scr/form.php.
 *
 * Ordre de surcharge:
 *
 * - Definition (ou non) des variables par /scr/form.php
 * - Surcharge (ou definition) par /dyn/form.inc.php
 * - Surcharge (ou definition) par /sql/sgbd/objet.form.inc.php
 *
 * @package openmairie_exemple
 * @version SVN : $Id: form.inc.php 2762 2014-03-26 19:17:13Z fmichon $
 */

// {{{ Surcharge des actions

//unset($portlet_actions['modifier']);
//unset($portlet_actions['supprimer']);

// }}}

/**
 * Option pour la désactivation des onglets en mode modification.
 *  - true : les onglets sont désactivés,
 *  - false : les onglets sont activés.
 * Default : false.
 */
$option_tab_disabled_on_edit = false;

?>
