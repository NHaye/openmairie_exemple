<?php
/**
 * 
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_widget.class.php 2928 2014-10-15 15:17:16Z fmichon $
 */

//
require_once "../gen/obj/om_widget.class.php";

/**
 *
 */
class om_widget_core extends om_widget_gen {

    /**
     *
     */
    function setType(&$form, $maj) {
        //
        parent::setType($form, $maj);
        //
        if ($maj == 0 || $maj == 1) {
            $form->setType('type', 'select');
        }
        if ($maj == 2) {
            $form->setType('type', 'selectstatic');
        }
        if ($maj == 3) {
            $form->setType('type', 'selectstatic');
        }
    }

    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        //
        parent::setSelect($form, $maj);
        //
        $select = array(
            0 => array(
                "web",
                "file",
            ),
            1 => array(
                _("web - le contenu du widget provient du champs texte ci-dessous"),
                _("file - le contenu du widget provient d'un script sur le serveur"),
            ),
        );
        $form->setSelect('type', $select);
    }

    /**
     *
     */
    function setLib(&$form, $maj) {
        //
        parent::setLib($form, $maj);
        //
        if ($this->getVal("type") == "file") {
            //
            $form->setLib("lien", _("script"));
            $form->setLib("texte", _("arguments"));
        }
    }

    /**
     *
     */
    function verifier($val = array(), &$dnu1 = null, $dnu2 = null) {
        //
        parent::verifier($val);
        //
        if ($val["type"] == "file"
            && !file_exists("../app/widget_".$val["lien"].".php")) {
            //
            $this->correct = false;
            $this->addToMessage(_("Le script n'existe pas."));
        }
    }

}

?>
