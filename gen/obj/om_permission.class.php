<?php
//$Id$ 
//gen openMairie le 13/02/2015 18:49

require_once "../obj/om_dbform.class.php";

class om_permission_gen extends om_dbform {

    var $table = "om_permission";
    var $clePrimaire = "om_permission";
    var $typeCle = "N";
    var $required_field = array(
        "libelle",
        "om_permission",
        "type"
    );
    
    var $foreign_keys_extended = array(
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_permission'])) {
            $this->valF['om_permission'] = ""; // -> requis
        } else {
            $this->valF['om_permission'] = $val['om_permission'];
        }
        $this->valF['libelle'] = $val['libelle'];
        $this->valF['type'] = $val['type'];
    }

    //=================================================
    //cle primaire automatique [automatic primary key]
    //==================================================

    function setId(&$dnu1 = null) {
    //numero automatique
        $this->valF[$this->clePrimaire] = $this->f->db->nextId(DB_PREFIXE.$this->table);
    }

    function setValFAjout($val) {
    //numero automatique -> pas de controle ajout cle primaire
    }

    function verifierAjout() {
    //numero automatique -> pas de verfication de cle primaire
    }

    //==========================
    // Formulaire  [form]
    //==========================
    /**
     *
     */
    function setType(&$form, $maj) {

        // MODE AJOUTER
        if ($maj == 0) {
            $form->setType("om_permission", "hidden");
            $form->setType("libelle", "text");
            $form->setType("type", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_permission", "hiddenstatic");
            $form->setType("libelle", "text");
            $form->setType("type", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_permission", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("type", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_permission", "static");
            $form->setType("libelle", "static");
            $form->setType("type", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_permission','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_permission", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("type", 30);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_permission", 11);
        $form->setMax("libelle", 100);
        $form->setMax("type", 100);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_permission',_('om_permission'));
        $form->setLib('libelle',_('libelle'));
        $form->setLib('type',_('type'));
    }
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // Inclusion du fichier de requêtes
        if (file_exists("../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php")) {
            include "../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php";
        } elseif (file_exists("../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc")) {
            include "../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc";
        }

    }


    //==================================
    // sous Formulaire 
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire 
    //==================================
    

}

?>
