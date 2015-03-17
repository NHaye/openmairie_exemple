<?php
/**
 * Ce fichier permet de paramétrer le générateur.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: gen.inc.php 2835 2014-08-05 16:42:02Z fmichon $
 */

/**
 * Ce tableau permet de lister les tables qui ne doivent pas être prises en
 * compte dans le générateur. Elles n'apparaîtront donc pas dans l'interface
 * et ne seront pas automatiquement générées par le 'genfull'.
 */
$tables_to_avoid = array(
    "geometry_columns",
    "om_version",
    "spatial_ref_sys",
);

/**
 * Ce tableau de configuration permet de donner des informations de surcharges
 * sur certains objets pour qu'elles soient prises en compte par le générateur.
 * $tables_to_overload = array(
 *    "<table>" => array(
 *        // définition de la liste des classes qui surchargent la classe
 *        // <table> pour que le générateur puisse générer ces surcharges 
 *        // et les inclure dans les tests de sous formulaire
 *        "extended_class" => array("<classe_surcharge_1_de_table>", ),
 *        // définition de la liste des champs à afficher dans l'affichage du
 *        // tableau champAffiche dans <table>.inc.php
 *        "displayed_fields_in_tableinc" => array("<champ_1>", ),
 *    ),
 * );
 */
$tables_to_overload = array(
    //
    "om_utilisateur" => array(
        //
        "displayed_fields_in_tableinc" => array(
            "nom", "email", "login", "om_profil",
        ),
    ),
    //
    "om_widget" => array(
        //
        "displayed_fields_in_tableinc" => array(
            "libelle", "om_profil", "type", 
        ),
    ),
);

?>
