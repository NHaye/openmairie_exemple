<?php
//$Id: om_collectivite.class.php 3053 2015-02-13 18:02:02Z nmeucci $ 
//gen openMairie le 13/02/2015 18:49

require_once "../obj/om_dbform.class.php";

class om_collectivite_gen extends om_dbform {

    var $table = "om_collectivite";
    var $clePrimaire = "om_collectivite";
    var $typeCle = "N";
    var $required_field = array(
        "libelle",
        "niveau",
        "om_collectivite"
    );
    
    var $foreign_keys_extended = array(
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_collectivite'])) {
            $this->valF['om_collectivite'] = ""; // -> requis
        } else {
            if($_SESSION['niveau']==1) {
                $this->valF['om_collectivite'] = $_SESSION['collectivite'];
            } else {
                $this->valF['om_collectivite'] = $val['om_collectivite'];
            }
        }
        $this->valF['libelle'] = $val['libelle'];
        $this->valF['niveau'] = $val['niveau'];
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
            $form->setType("om_collectivite", "hidden");
            $form->setType("libelle", "text");
            $form->setType("niveau", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_collectivite", "hiddenstatic");
            $form->setType("libelle", "text");
            $form->setType("niveau", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_collectivite", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("niveau", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_collectivite", "static");
            $form->setType("libelle", "static");
            $form->setType("niveau", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_collectivite','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_collectivite", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("niveau", 10);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_collectivite", 11);
        $form->setMax("libelle", 100);
        $form->setMax("niveau", 1);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_collectivite',_('om_collectivite'));
        $form->setLib('libelle',_('libelle'));
        $form->setLib('niveau',_('niveau'));
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


    function setVal(&$form, $maj, $validation, &$dnu1 = null, $dnu2 = null) {
        if($validation==0 and $maj==0 and $_SESSION['niveau']==1) {
            $form->setVal('om_collectivite', $_SESSION['collectivite']);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setVal

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
        // Verification de la cle secondaire : om_etat
        $this->rechercheTable($this->f->db, "om_etat", "om_collectivite", $id);
        // Verification de la cle secondaire : om_lettretype
        $this->rechercheTable($this->f->db, "om_lettretype", "om_collectivite", $id);
        // Verification de la cle secondaire : om_logo
        $this->rechercheTable($this->f->db, "om_logo", "om_collectivite", $id);
        // Verification de la cle secondaire : om_parametre
        $this->rechercheTable($this->f->db, "om_parametre", "om_collectivite", $id);
        // Verification de la cle secondaire : om_sig_map
        $this->rechercheTable($this->f->db, "om_sig_map", "om_collectivite", $id);
        // Verification de la cle secondaire : om_sig_wms
        $this->rechercheTable($this->f->db, "om_sig_wms", "om_collectivite", $id);
        // Verification de la cle secondaire : om_sousetat
        $this->rechercheTable($this->f->db, "om_sousetat", "om_collectivite", $id);
        // Verification de la cle secondaire : om_utilisateur
        $this->rechercheTable($this->f->db, "om_utilisateur", "om_collectivite", $id);
    }


}

?>
