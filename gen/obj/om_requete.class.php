<?php
//$Id: om_requete.class.php 3053 2015-02-13 18:02:02Z nmeucci $ 
//gen openMairie le 13/02/2015 18:49

require_once "../obj/om_dbform.class.php";

class om_requete_gen extends om_dbform {

    var $table = "om_requete";
    var $clePrimaire = "om_requete";
    var $typeCle = "N";
    var $required_field = array(
        "code",
        "libelle",
        "om_requete",
        "type"
    );
    
    var $foreign_keys_extended = array(
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_requete'])) {
            $this->valF['om_requete'] = ""; // -> requis
        } else {
            $this->valF['om_requete'] = $val['om_requete'];
        }
        $this->valF['code'] = $val['code'];
        $this->valF['libelle'] = $val['libelle'];
        if ($val['description'] == "") {
            $this->valF['description'] = NULL;
        } else {
            $this->valF['description'] = $val['description'];
        }
            $this->valF['requete'] = $val['requete'];
            $this->valF['merge_fields'] = $val['merge_fields'];
        $this->valF['type'] = $val['type'];
        if ($val['classe'] == "") {
            $this->valF['classe'] = NULL;
        } else {
            $this->valF['classe'] = $val['classe'];
        }
        if ($val['methode'] == "") {
            $this->valF['methode'] = NULL;
        } else {
            $this->valF['methode'] = $val['methode'];
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
            $form->setType("om_requete", "hidden");
            $form->setType("code", "text");
            $form->setType("libelle", "text");
            $form->setType("description", "text");
            $form->setType("requete", "textarea");
            $form->setType("merge_fields", "textarea");
            $form->setType("type", "text");
            $form->setType("classe", "text");
            $form->setType("methode", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_requete", "hiddenstatic");
            $form->setType("code", "text");
            $form->setType("libelle", "text");
            $form->setType("description", "text");
            $form->setType("requete", "textarea");
            $form->setType("merge_fields", "textarea");
            $form->setType("type", "text");
            $form->setType("classe", "text");
            $form->setType("methode", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_requete", "hiddenstatic");
            $form->setType("code", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("description", "hiddenstatic");
            $form->setType("requete", "hiddenstatic");
            $form->setType("merge_fields", "hiddenstatic");
            $form->setType("type", "hiddenstatic");
            $form->setType("classe", "hiddenstatic");
            $form->setType("methode", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_requete", "static");
            $form->setType("code", "static");
            $form->setType("libelle", "static");
            $form->setType("description", "static");
            $form->setType("requete", "textareastatic");
            $form->setType("merge_fields", "textareastatic");
            $form->setType("type", "static");
            $form->setType("classe", "static");
            $form->setType("methode", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_requete','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_requete", 11);
        $form->setTaille("code", 30);
        $form->setTaille("libelle", 30);
        $form->setTaille("description", 30);
        $form->setTaille("requete", 80);
        $form->setTaille("merge_fields", 80);
        $form->setTaille("type", 30);
        $form->setTaille("classe", 30);
        $form->setTaille("methode", 30);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_requete", 11);
        $form->setMax("code", 50);
        $form->setMax("libelle", 100);
        $form->setMax("description", 200);
        $form->setMax("requete", 6);
        $form->setMax("merge_fields", 6);
        $form->setMax("type", 200);
        $form->setMax("classe", 200);
        $form->setMax("methode", 200);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_requete',_('om_requete'));
        $form->setLib('code',_('code'));
        $form->setLib('libelle',_('libelle'));
        $form->setLib('description',_('description'));
        $form->setLib('requete',_('requete'));
        $form->setLib('merge_fields',_('merge_fields'));
        $form->setLib('type',_('type'));
        $form->setLib('classe',_('classe'));
        $form->setLib('methode',_('methode'));
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
        // Verification de la cle secondaire : om_etat
        $this->rechercheTable($this->f->db, "om_etat", "om_sql", $id);
        // Verification de la cle secondaire : om_lettretype
        $this->rechercheTable($this->f->db, "om_lettretype", "om_sql", $id);
    }


}

?>
