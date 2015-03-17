<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

require_once "../obj/om_dbform.class.php";

class om_profil_gen extends om_dbform {

    var $table = "om_profil";
    var $clePrimaire = "om_profil";
    var $typeCle = "N";
    var $required_field = array(
        "libelle",
        "om_profil"
    );
    
    var $foreign_keys_extended = array(
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_profil'])) {
            $this->valF['om_profil'] = ""; // -> requis
        } else {
            $this->valF['om_profil'] = $val['om_profil'];
        }
        $this->valF['libelle'] = $val['libelle'];
        if (!is_numeric($val['hierarchie'])) {
            $this->valF['hierarchie'] = 0; // -> default
        } else {
            $this->valF['hierarchie'] = $val['hierarchie'];
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
            $form->setType("om_profil", "hidden");
            $form->setType("libelle", "text");
            $form->setType("hierarchie", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_profil", "hiddenstatic");
            $form->setType("libelle", "text");
            $form->setType("hierarchie", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_profil", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("hierarchie", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_profil", "static");
            $form->setType("libelle", "static");
            $form->setType("hierarchie", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_profil','VerifNum(this)');
        $form->setOnchange('hierarchie','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_profil", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("hierarchie", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_profil", 11);
        $form->setMax("libelle", 100);
        $form->setMax("hierarchie", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_profil',_('om_profil'));
        $form->setLib('libelle',_('libelle'));
        $form->setLib('hierarchie',_('hierarchie'));
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
    
    /**
     * Methode clesecondaire
     */
    function cleSecondaire($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        // On appelle la methode de la classe parent
        parent::cleSecondaire($id);
        // Verification de la cle secondaire : om_dashboard
        $this->rechercheTable($this->f->db, "om_dashboard", "om_profil", $id);
        // Verification de la cle secondaire : om_droit
        $this->rechercheTable($this->f->db, "om_droit", "om_profil", $id);
        // Verification de la cle secondaire : om_utilisateur
        $this->rechercheTable($this->f->db, "om_utilisateur", "om_profil", $id);
    }


}

?>
