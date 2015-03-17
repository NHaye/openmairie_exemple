<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

require_once "../obj/om_dbform.class.php";

class om_sig_map_comp_gen extends om_dbform {

    var $table = "om_sig_map_comp";
    var $clePrimaire = "om_sig_map_comp";
    var $typeCle = "N";
    var $required_field = array(
        "libelle",
        "om_sig_map",
        "om_sig_map_comp",
        "ordre"
    );
    
    var $foreign_keys_extended = array(
        "om_sig_map" => array("om_sig_map", ),
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_sig_map_comp'])) {
            $this->valF['om_sig_map_comp'] = ""; // -> requis
        } else {
            $this->valF['om_sig_map_comp'] = $val['om_sig_map_comp'];
        }
        if (!is_numeric($val['om_sig_map'])) {
            $this->valF['om_sig_map'] = ""; // -> requis
        } else {
            $this->valF['om_sig_map'] = $val['om_sig_map'];
        }
        $this->valF['libelle'] = $val['libelle'];
        if (!is_numeric($val['ordre'])) {
            $this->valF['ordre'] = ""; // -> requis
        } else {
            $this->valF['ordre'] = $val['ordre'];
        }
        if ($val['actif'] == "") {
            $this->valF['actif'] = NULL;
        } else {
            $this->valF['actif'] = $val['actif'];
        }
        if ($val['comp_maj'] == "") {
            $this->valF['comp_maj'] = NULL;
        } else {
            $this->valF['comp_maj'] = $val['comp_maj'];
        }
        if ($val['type_geometrie'] == "") {
            $this->valF['type_geometrie'] = NULL;
        } else {
            $this->valF['type_geometrie'] = $val['type_geometrie'];
        }
        if ($val['comp_table_update'] == "") {
            $this->valF['comp_table_update'] = NULL;
        } else {
            $this->valF['comp_table_update'] = $val['comp_table_update'];
        }
        if ($val['comp_champ'] == "") {
            $this->valF['comp_champ'] = NULL;
        } else {
            $this->valF['comp_champ'] = $val['comp_champ'];
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
            $form->setType("om_sig_map_comp", "hidden");
            if ($this->is_in_context_of_foreign_key("om_sig_map", $this->retourformulaire)) {
                $form->setType("om_sig_map", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_map", "select");
            }
            $form->setType("libelle", "text");
            $form->setType("ordre", "text");
            $form->setType("actif", "text");
            $form->setType("comp_maj", "text");
            $form->setType("type_geometrie", "text");
            $form->setType("comp_table_update", "text");
            $form->setType("comp_champ", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_sig_map_comp", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("om_sig_map", $this->retourformulaire)) {
                $form->setType("om_sig_map", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_map", "select");
            }
            $form->setType("libelle", "text");
            $form->setType("ordre", "text");
            $form->setType("actif", "text");
            $form->setType("comp_maj", "text");
            $form->setType("type_geometrie", "text");
            $form->setType("comp_table_update", "text");
            $form->setType("comp_champ", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_sig_map_comp", "hiddenstatic");
            $form->setType("om_sig_map", "selectstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("ordre", "hiddenstatic");
            $form->setType("actif", "hiddenstatic");
            $form->setType("comp_maj", "hiddenstatic");
            $form->setType("type_geometrie", "hiddenstatic");
            $form->setType("comp_table_update", "hiddenstatic");
            $form->setType("comp_champ", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_sig_map_comp", "static");
            $form->setType("om_sig_map", "selectstatic");
            $form->setType("libelle", "static");
            $form->setType("ordre", "static");
            $form->setType("actif", "static");
            $form->setType("comp_maj", "static");
            $form->setType("type_geometrie", "static");
            $form->setType("comp_table_update", "static");
            $form->setType("comp_champ", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_sig_map_comp','VerifNum(this)');
        $form->setOnchange('om_sig_map','VerifNum(this)');
        $form->setOnchange('ordre','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_sig_map_comp", 11);
        $form->setTaille("om_sig_map", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("ordre", 11);
        $form->setTaille("actif", 10);
        $form->setTaille("comp_maj", 10);
        $form->setTaille("type_geometrie", 30);
        $form->setTaille("comp_table_update", 30);
        $form->setTaille("comp_champ", 30);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_sig_map_comp", 11);
        $form->setMax("om_sig_map", 11);
        $form->setMax("libelle", 50);
        $form->setMax("ordre", 11);
        $form->setMax("actif", 3);
        $form->setMax("comp_maj", 3);
        $form->setMax("type_geometrie", 30);
        $form->setMax("comp_table_update", 30);
        $form->setMax("comp_champ", 30);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_sig_map_comp',_('om_sig_map_comp'));
        $form->setLib('om_sig_map',_('om_sig_map'));
        $form->setLib('libelle',_('libelle'));
        $form->setLib('ordre',_('ordre'));
        $form->setLib('actif',_('actif'));
        $form->setLib('comp_maj',_('comp_maj'));
        $form->setLib('type_geometrie',_('type_geometrie'));
        $form->setLib('comp_table_update',_('comp_table_update'));
        $form->setLib('comp_champ',_('comp_champ'));
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

        // om_sig_map
        $this->init_select($form, $this->f->db, $maj, null, "om_sig_map", $sql_om_sig_map, $sql_om_sig_map_by_id, false);
    }


    //==================================
    // sous Formulaire 
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('om_sig_map', $this->retourformulaire))
                $form->setVal('om_sig_map', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire 
    //==================================
    

}

?>
