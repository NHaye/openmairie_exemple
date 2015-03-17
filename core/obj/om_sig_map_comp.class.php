<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sig_map_comp.class.php 2946 2014-11-03 10:22:38Z baldachino $
 */

//
require_once "../gen/obj/om_sig_map_comp.class.php";

/**
 *
 */
class om_sig_map_comp_core extends om_sig_map_comp_gen {

    /**
     *
     */
    var $required_field=array(
        "om_sig_map_comp",
        "om_sig_map",
        "libelle",
        "ordre"
    );

    /**
     *
     */
    function setType(&$form,$maj) {
        parent::setType($form,$maj);
        if($maj<2){
            $form->setType('actif', 'checkbox');
            $form->setType('comp_maj','checkbox');
            $form->setType('type_geometrie','select');
        }

        if ($maj == 2 or $maj == 3) {
            $form->setType('type_geometrie', 'selectstatic');
        }
    }

    /**
     *
     */
    function setTaille(&$form,$maj) {
        parent::setTaille($form,$maj);
        //taille des champs affiches (text)
        $form->setTaille('libelle',50);
        $form->setTaille('ordre',3);
        $form->setTaille('comp_table_update',30);
        $form->setTaille('comp_champ',30);
        $form->setTaille('type_geometrie',30);
        $form->setTaille('comp_maj',1);
    }

    /**
     *
     */
    function setMax(&$form,$maj) {
        parent::setMax($form,$maj); 
        $form->setMax('libelle',50);
        $form->setMax('comp_table_update',30);
        $form->setMax('comp_champ',30);
    }

    /**
     *
     */
    function setSelect(&$form, $maj,&$db,$debug) {
        parent::setSelect($form, $maj,$db,$debug);

        if(file_exists ("../dyn/var_sig.inc")) {
            include ("../dyn/var_sig.inc");
        }

        $form->setSelect("type_geometrie", $type_geometrie);
    }

    /**
     *
     */
    function setLib(&$form,$maj) {
        parent::setLib($form,$maj);
        //libelle des champs
        $form->setLib('libelle',_("Nom géométrie : "));
        $form->setLib('actif',_("Actif : "));
        $form->setLib('ordre',_("Ordre d'affichage : "));
        $form->setLib('comp_maj',_("Mis a jour : "));
        $form->setLib('type_geometrie',_("Type de geometrie : "));
        $form->setLib('comp_table_update',_("Table :"));
        $form->setLib('comp_champ',_("Champ :"));
    }

    /**
     *
     */
    function setGroupe (&$form, $maj) {
        $form->setGroupe('comp_maj','D');
        $form->setGroupe('type_geometrie','F');
        $form->setGroupe('comp_table_update','D');
        $form->setGroupe('comp_champ','F');
    }

    /**
     *
     */
    function setRegroupe (&$form, $maj) {
		$form->setRegroupe('comp_maj','D',' '._('Mise a jour').' ', "collapsible");
        $form->setRegroupe('type_geometrie','G','');    
        $form->setRegroupe('comp_table_update','G','');    
        $form->setRegroupe('comp_champ','F','');    
    }

}

?>
