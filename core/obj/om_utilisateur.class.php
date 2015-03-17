<?php
/**
 * 
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_utilisateur.class.php 2928 2014-10-15 15:17:16Z fmichon $
 */

//
require_once "../gen/obj/om_utilisateur.class.php";

/**
 *
 */
class om_utilisateur_core extends om_utilisateur_gen {

    /**
     * Définition des actions disponibles sur la classe.
     *
     * @return void
     */
    function init_class_actions() {

        // On récupère les actions génériques définies dans la méthode 
        // d'initialisation de la classe parente
        parent::init_class_actions();

        // ACTION - 002 - supprimer
        //
        $this->class_actions[2]["condition"] = "delete_user_condition";

    }

    /**
     *
     */
    function setvalF($val) {
        //
        parent::setvalF($val);

        /* Gestion des mises a jour du mot de passe */

        // si un mot de passe est soumis par formulaire
        if ($val["pwd"] != '') {

            // si le mot de passe contient une valeur 'valide' (!= "*****")
            if ($val["pwd"] != "*****") {

                // calcul du md5 et mise a jour dans la base
                $this->valF["pwd"] = md5($val["pwd"]);

            // si le mot de passe n'a pas ete modifie, aucune maj dans la base
            } else {
                unset($this->valF["pwd"]);
            }
        }
    }

    /**
     *
     */
    function setType(&$form,$maj) {
        //
        parent::setType($form, $maj);
        // Gestion du type d'utilisateur (DB ou LDAP)
        $form->setType("om_type", "hidden");
        // Test du MODE
        if ($maj == 0) {
            // Modes : AJOUTER
            // Gestion du mot de passe
            $form->setType("pwd", "password");
        } elseif ($maj == 1) {
            // Modes : AJOUTER
            // Gestion du mot de passe
            $form->setType("pwd", "password");
            // Gestion du login
            $form->setType("login", "hiddenstatic");
        }
    }

    /**
     *
     */
    function setVal(&$form,$maj,$validation, &$dnu1 = null, $dnu2 = null) {
        //
        parent::setVal($form, $maj, $validation);
        //
        if ($validation == 0) {
            // Test du MODE
            if ($maj == 0) {
                // Mode : AJOUTER
                // Gestion du type d'utilisateur (DB ou LDAP)
                $form->setVal("om_type", "db");
            } else {
                // Modes : MODIFIER & SUPPRIMER
                // Gestion du mot de passe
                // Lié a setValF()
                $form->setVal('pwd', "*****");
            }
        }
    }

    /**
     *
     */
    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        //
        parent::setValsousformulaire($form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire);
        //
        if ($validation == 0) {
            // Test du MODE
            if ($maj == 0) {
                // Mode : AJOUTER
                // Gestion du type d'utilisateur (DB ou LDAP)
                $form->setVal("om_type", "db");
            } else {
                // Modes : MODIFIER & SUPPRIMER
                // Gestion du mot de passe
                // Lié a setValF()
                $form->setVal("pwd", "*****");
            }
        }
    }

    /**
     * CONDITION - delete_user_condition.
     * 
     * Méthode permettant de tester la condition d'affichage du bouton de
     * suppression de l'objet.
     * 
     * @return boolean
     */
    function delete_user_condition() {
        // true si l'utilisateur connecté n'est pas celui à supprimer.
        if($_SESSION['login'] != $this->getVal("login")) {
            return true;
        }
        $this->addToMessage(_("Vous ne pouvez pas supprimer votre utilisateur."));
        return false;
    }

}

?>
