<?php
//$Id: om_droit.class.php 3074 2015-02-25 15:22:47Z fmichon $ 
//gen openMairie le 25/02/2015 16:22

require_once "../obj/om_dbform.class.php";

class om_droit_gen extends om_dbform {

    var $table = "om_droit";
    var $clePrimaire = "om_droit";
    var $typeCle = "N";
    var $required_field = array(
        "libelle",
        "om_droit",
        "om_profil"
    );
    var $unique_key = array(
      array("libelle","om_profil"),
    );
    var $foreign_keys_extended = array(
        "om_profil" => array("om_profil", ),
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_droit'])) {
            $this->valF['om_droit'] = ""; // -> requis
        } else {
            $this->valF['om_droit'] = $val['om_droit'];
        }
        $this->valF['libelle'] = $val['libelle'];
        if (!is_numeric($val['om_profil'])) {
            $this->valF['om_profil'] = ""; // -> requis
        } else {
            $this->valF['om_profil'] = $val['om_profil'];
        }
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
            $form->setType("om_droit", "hidden");
            $form->setType("libelle", "text");
            if ($this->is_in_context_of_foreign_key("om_profil", $this->retourformulaire)) {
                $form->setType("om_profil", "selecthiddenstatic");
            } else {
                $form->setType("om_profil", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_droit", "hiddenstatic");
            $form->setType("libelle", "text");
            if ($this->is_in_context_of_foreign_key("om_profil", $this->retourformulaire)) {
                $form->setType("om_profil", "selecthiddenstatic");
            } else {
                $form->setType("om_profil", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_droit", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("om_profil", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_droit", "static");
            $form->setType("libelle", "static");
            $form->setType("om_profil", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_droit','VerifNum(this)');
        $form->setOnchange('om_profil','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_droit", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("om_profil", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_droit", 11);
        $form->setMax("libelle", 100);
        $form->setMax("om_profil", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_droit',_('om_droit'));
        $form->setLib('libelle',_('libelle'));
        $form->setLib('om_profil',_('om_profil'));
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

        // om_profil
        $this->init_select($form, $this->f->db, $maj, null, "om_profil", $sql_om_profil, $sql_om_profil_by_id, false);
    }


    //==================================
    // sous Formulaire 
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('om_profil', $this->retourformulaire))
                $form->setVal('om_profil', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire 
    //==================================
    

}

?>
