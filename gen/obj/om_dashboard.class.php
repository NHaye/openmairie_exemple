<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

require_once "../obj/om_dbform.class.php";

class om_dashboard_gen extends om_dbform {

    var $table = "om_dashboard";
    var $clePrimaire = "om_dashboard";
    var $typeCle = "N";
    var $required_field = array(
        "bloc",
        "om_dashboard",
        "om_profil",
        "om_widget"
    );
    
    var $foreign_keys_extended = array(
        "om_profil" => array("om_profil", ),
        "om_widget" => array("om_widget", ),
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_dashboard'])) {
            $this->valF['om_dashboard'] = ""; // -> requis
        } else {
            $this->valF['om_dashboard'] = $val['om_dashboard'];
        }
        if (!is_numeric($val['om_profil'])) {
            $this->valF['om_profil'] = ""; // -> requis
        } else {
            $this->valF['om_profil'] = $val['om_profil'];
        }
        $this->valF['bloc'] = $val['bloc'];
        if (!is_numeric($val['position'])) {
            $this->valF['position'] = NULL;
        } else {
            $this->valF['position'] = $val['position'];
        }
        if (!is_numeric($val['om_widget'])) {
            $this->valF['om_widget'] = ""; // -> requis
        } else {
            $this->valF['om_widget'] = $val['om_widget'];
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
            $form->setType("om_dashboard", "hidden");
            if ($this->is_in_context_of_foreign_key("om_profil", $this->retourformulaire)) {
                $form->setType("om_profil", "selecthiddenstatic");
            } else {
                $form->setType("om_profil", "select");
            }
            $form->setType("bloc", "text");
            $form->setType("position", "text");
            if ($this->is_in_context_of_foreign_key("om_widget", $this->retourformulaire)) {
                $form->setType("om_widget", "selecthiddenstatic");
            } else {
                $form->setType("om_widget", "select");
            }
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_dashboard", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("om_profil", $this->retourformulaire)) {
                $form->setType("om_profil", "selecthiddenstatic");
            } else {
                $form->setType("om_profil", "select");
            }
            $form->setType("bloc", "text");
            $form->setType("position", "text");
            if ($this->is_in_context_of_foreign_key("om_widget", $this->retourformulaire)) {
                $form->setType("om_widget", "selecthiddenstatic");
            } else {
                $form->setType("om_widget", "select");
            }
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_dashboard", "hiddenstatic");
            $form->setType("om_profil", "selectstatic");
            $form->setType("bloc", "hiddenstatic");
            $form->setType("position", "hiddenstatic");
            $form->setType("om_widget", "selectstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_dashboard", "static");
            $form->setType("om_profil", "selectstatic");
            $form->setType("bloc", "static");
            $form->setType("position", "static");
            $form->setType("om_widget", "selectstatic");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_dashboard','VerifNum(this)');
        $form->setOnchange('om_profil','VerifNum(this)');
        $form->setOnchange('position','VerifNum(this)');
        $form->setOnchange('om_widget','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_dashboard", 11);
        $form->setTaille("om_profil", 11);
        $form->setTaille("bloc", 10);
        $form->setTaille("position", 11);
        $form->setTaille("om_widget", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_dashboard", 11);
        $form->setMax("om_profil", 11);
        $form->setMax("bloc", 10);
        $form->setMax("position", 11);
        $form->setMax("om_widget", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_dashboard',_('om_dashboard'));
        $form->setLib('om_profil',_('om_profil'));
        $form->setLib('bloc',_('bloc'));
        $form->setLib('position',_('position'));
        $form->setLib('om_widget',_('om_widget'));
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
        // om_widget
        $this->init_select($form, $this->f->db, $maj, null, "om_widget", $sql_om_widget, $sql_om_widget_by_id, false);
    }


    //==================================
    // sous Formulaire 
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('om_profil', $this->retourformulaire))
                $form->setVal('om_profil', $idxformulaire);
            if($this->is_in_context_of_foreign_key('om_widget', $this->retourformulaire))
                $form->setVal('om_widget', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire 
    //==================================
    

}

?>
