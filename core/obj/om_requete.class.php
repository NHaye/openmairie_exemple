<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_requete.class.php 2994 2014-12-05 16:03:14Z nmeucci $
 */

//
require_once "../gen/obj/om_requete.class.php";

/**
 *
 */
class om_requete_core extends om_requete_gen {

    function __construct($id, &$dnu1 = null, $dnu2 = null) {
        parent::__construct($id);
    }// fin constructeur

    /**
     * Permet de définir le type des champs
     */
    function setType(&$form, $maj) {
        //
        parent::setType($form, $maj);

        // En modes "ajout" et "modification"
        if ($maj == 0 || $maj == 1) {

            $form->setType('type', 'select');
        }

        // En mode "consultation" et "suppression"
        if ($maj == 2 || $maj == 3) {

            $form->setType('type', 'selectstatic');
        }
    }

    /**
     * Permet de construire le contenu d'un select
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        //
        parent::setSelect($form, $maj);

        $type = array();
        $type[0][0] = 'sql';
        $type[1][0] = _("SQL");
        $type[0][1] = 'objet';
        $type[1][1] = _("objet");
        $form->setSelect("type", $type);
    }
}

?>
