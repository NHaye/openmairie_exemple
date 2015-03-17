<?php
//$Id: om_widget.class.php 3053 2015-02-13 18:02:02Z nmeucci $ 
//gen openMairie le 13/02/2015 18:49

require_once "../obj/om_dbform.class.php";

class om_widget_gen extends om_dbform {

    var $table = "om_widget";
    var $clePrimaire = "om_widget";
    var $typeCle = "N";
    var $required_field = array(
        "libelle",
        "om_widget",
        "type"
    );
    
    var $foreign_keys_extended = array(
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_widget'])) {
            $this->valF['om_widget'] = ""; // -> requis
        } else {
            $this->valF['om_widget'] = $val['om_widget'];
        }
        $this->valF['libelle'] = $val['libelle'];
        if ($val['lien'] == "") {
            $this->valF['lien'] = ""; // -> default
        } else {
            $this->valF['lien'] = $val['lien'];
        }
            $this->valF['texte'] = $val['texte'];
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
            $form->setType("om_widget", "hidden");
            $form->setType("libelle", "text");
            $form->setType("lien", "text");
            $form->setType("texte", "textarea");
            $form->setType("type", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_widget", "hiddenstatic");
            $form->setType("libelle", "text");
            $form->setType("lien", "text");
            $form->setType("texte", "textarea");
            $form->setType("type", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_widget", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("lien", "hiddenstatic");
            $form->setType("texte", "hiddenstatic");
            $form->setType("type", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_widget", "static");
            $form->setType("libelle", "static");
            $form->setType("lien", "static");
            $form->setType("texte", "textareastatic");
            $form->setType("type", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_widget','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_widget", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("lien", 30);
        $form->setTaille("texte", 80);
        $form->setTaille("type", 30);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_widget", 11);
        $form->setMax("libelle", 100);
        $form->setMax("lien", 80);
        $form->setMax("texte", 6);
        $form->setMax("type", 40);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_widget',_('om_widget'));
        $form->setLib('libelle',_('libelle'));
        $form->setLib('lien',_('lien'));
        $form->setLib('texte',_('texte'));
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
    
    /**
     * Methode clesecondaire
     */
    function cleSecondaire($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        // On appelle la methode de la classe parent
        parent::cleSecondaire($id);
        // Verification de la cle secondaire : om_dashboard
        $this->rechercheTable($this->f->db, "om_dashboard", "om_widget", $id);
    }


}

?>
