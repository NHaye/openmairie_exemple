<?php
//$Id: om_sig_map.class.php 3053 2015-02-13 18:02:02Z nmeucci $ 
//gen openMairie le 13/02/2015 18:49

require_once "../obj/om_dbform.class.php";

class om_sig_map_gen extends om_dbform {

    var $table = "om_sig_map";
    var $clePrimaire = "om_sig_map";
    var $typeCle = "N";
    var $required_field = array(
        "champ",
        "etendue",
        "fond_bing",
        "fond_osm",
        "fond_sat",
        "id",
        "layer_info",
        "libelle",
        "maj",
        "om_collectivite",
        "om_sig_map",
        "om_sql",
        "projection_externe",
        "retour",
        "table_update",
        "url",
        "zoom"
    );
    
    var $foreign_keys_extended = array(
        "om_collectivite" => array("om_collectivite", ),
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_sig_map'])) {
            $this->valF['om_sig_map'] = ""; // -> requis
        } else {
            $this->valF['om_sig_map'] = $val['om_sig_map'];
        }
        if (!is_numeric($val['om_collectivite'])) {
            $this->valF['om_collectivite'] = ""; // -> requis
        } else {
            if($_SESSION['niveau']==1) {
                $this->valF['om_collectivite'] = $_SESSION['collectivite'];
            } else {
                $this->valF['om_collectivite'] = $val['om_collectivite'];
            }
        }
        $this->valF['id'] = $val['id'];
        $this->valF['libelle'] = $val['libelle'];
        if ($val['actif'] == 1 || $val['actif'] == "t" || $val['actif'] == "Oui") {
            $this->valF['actif'] = true;
        } else {
            $this->valF['actif'] = false;
        }
        $this->valF['zoom'] = $val['zoom'];
        $this->valF['fond_osm'] = $val['fond_osm'];
        $this->valF['fond_bing'] = $val['fond_bing'];
        $this->valF['fond_sat'] = $val['fond_sat'];
        $this->valF['layer_info'] = $val['layer_info'];
        $this->valF['etendue'] = $val['etendue'];
        $this->valF['projection_externe'] = $val['projection_externe'];
            $this->valF['url'] = $val['url'];
            $this->valF['om_sql'] = $val['om_sql'];
        $this->valF['maj'] = $val['maj'];
        $this->valF['table_update'] = $val['table_update'];
        $this->valF['champ'] = $val['champ'];
        $this->valF['retour'] = $val['retour'];
        if ($val['type_geometrie'] == "") {
            $this->valF['type_geometrie'] = NULL;
        } else {
            $this->valF['type_geometrie'] = $val['type_geometrie'];
        }
        if ($val['lib_geometrie'] == "") {
            $this->valF['lib_geometrie'] = NULL;
        } else {
            $this->valF['lib_geometrie'] = $val['lib_geometrie'];
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
            $form->setType("om_sig_map", "hidden");
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selecthiddenstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            } else {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "select");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            }
            $form->setType("id", "text");
            $form->setType("libelle", "text");
            $form->setType("actif", "checkbox");
            $form->setType("zoom", "text");
            $form->setType("fond_osm", "text");
            $form->setType("fond_bing", "text");
            $form->setType("fond_sat", "text");
            $form->setType("layer_info", "text");
            $form->setType("etendue", "text");
            $form->setType("projection_externe", "text");
            $form->setType("url", "textarea");
            $form->setType("om_sql", "textarea");
            $form->setType("maj", "text");
            $form->setType("table_update", "text");
            $form->setType("champ", "text");
            $form->setType("retour", "text");
            $form->setType("type_geometrie", "text");
            $form->setType("lib_geometrie", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_sig_map", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selecthiddenstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            } else {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "select");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            }
            $form->setType("id", "text");
            $form->setType("libelle", "text");
            $form->setType("actif", "checkbox");
            $form->setType("zoom", "text");
            $form->setType("fond_osm", "text");
            $form->setType("fond_bing", "text");
            $form->setType("fond_sat", "text");
            $form->setType("layer_info", "text");
            $form->setType("etendue", "text");
            $form->setType("projection_externe", "text");
            $form->setType("url", "textarea");
            $form->setType("om_sql", "textarea");
            $form->setType("maj", "text");
            $form->setType("table_update", "text");
            $form->setType("champ", "text");
            $form->setType("retour", "text");
            $form->setType("type_geometrie", "text");
            $form->setType("lib_geometrie", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_sig_map", "hiddenstatic");
            if ($_SESSION["niveau"] == 2) {
                $form->setType("om_collectivite", "selectstatic");
            } else {
                $form->setType("om_collectivite", "hidden");
            }
            $form->setType("id", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("actif", "hiddenstatic");
            $form->setType("zoom", "hiddenstatic");
            $form->setType("fond_osm", "hiddenstatic");
            $form->setType("fond_bing", "hiddenstatic");
            $form->setType("fond_sat", "hiddenstatic");
            $form->setType("layer_info", "hiddenstatic");
            $form->setType("etendue", "hiddenstatic");
            $form->setType("projection_externe", "hiddenstatic");
            $form->setType("url", "hiddenstatic");
            $form->setType("om_sql", "hiddenstatic");
            $form->setType("maj", "hiddenstatic");
            $form->setType("table_update", "hiddenstatic");
            $form->setType("champ", "hiddenstatic");
            $form->setType("retour", "hiddenstatic");
            $form->setType("type_geometrie", "hiddenstatic");
            $form->setType("lib_geometrie", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_sig_map", "static");
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selectstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            } else {
                if($_SESSION["niveau"] == 2) {
                    $form->setType("om_collectivite", "selectstatic");
                } else {
                    $form->setType("om_collectivite", "hidden");
                }
            }
            $form->setType("id", "static");
            $form->setType("libelle", "static");
            $form->setType("actif", "checkboxstatic");
            $form->setType("zoom", "static");
            $form->setType("fond_osm", "static");
            $form->setType("fond_bing", "static");
            $form->setType("fond_sat", "static");
            $form->setType("layer_info", "static");
            $form->setType("etendue", "static");
            $form->setType("projection_externe", "static");
            $form->setType("url", "textareastatic");
            $form->setType("om_sql", "textareastatic");
            $form->setType("maj", "static");
            $form->setType("table_update", "static");
            $form->setType("champ", "static");
            $form->setType("retour", "static");
            $form->setType("type_geometrie", "static");
            $form->setType("lib_geometrie", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_sig_map','VerifNum(this)');
        $form->setOnchange('om_collectivite','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_sig_map", 11);
        $form->setTaille("om_collectivite", 11);
        $form->setTaille("id", 30);
        $form->setTaille("libelle", 30);
        $form->setTaille("actif", 1);
        $form->setTaille("zoom", 10);
        $form->setTaille("fond_osm", 10);
        $form->setTaille("fond_bing", 10);
        $form->setTaille("fond_sat", 10);
        $form->setTaille("layer_info", 10);
        $form->setTaille("etendue", 30);
        $form->setTaille("projection_externe", 30);
        $form->setTaille("url", 80);
        $form->setTaille("om_sql", 80);
        $form->setTaille("maj", 10);
        $form->setTaille("table_update", 30);
        $form->setTaille("champ", 30);
        $form->setTaille("retour", 30);
        $form->setTaille("type_geometrie", 30);
        $form->setTaille("lib_geometrie", 30);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_sig_map", 11);
        $form->setMax("om_collectivite", 11);
        $form->setMax("id", 50);
        $form->setMax("libelle", 50);
        $form->setMax("actif", 1);
        $form->setMax("zoom", 3);
        $form->setMax("fond_osm", 3);
        $form->setMax("fond_bing", 3);
        $form->setMax("fond_sat", 3);
        $form->setMax("layer_info", 3);
        $form->setMax("etendue", 60);
        $form->setMax("projection_externe", 60);
        $form->setMax("url", 6);
        $form->setMax("om_sql", 6);
        $form->setMax("maj", 3);
        $form->setMax("table_update", 30);
        $form->setMax("champ", 30);
        $form->setMax("retour", 50);
        $form->setMax("type_geometrie", 30);
        $form->setMax("lib_geometrie", 50);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_sig_map',_('om_sig_map'));
        $form->setLib('om_collectivite',_('om_collectivite'));
        $form->setLib('id',_('id'));
        $form->setLib('libelle',_('libelle'));
        $form->setLib('actif',_('actif'));
        $form->setLib('zoom',_('zoom'));
        $form->setLib('fond_osm',_('fond_osm'));
        $form->setLib('fond_bing',_('fond_bing'));
        $form->setLib('fond_sat',_('fond_sat'));
        $form->setLib('layer_info',_('layer_info'));
        $form->setLib('etendue',_('etendue'));
        $form->setLib('projection_externe',_('projection_externe'));
        $form->setLib('url',_('url'));
        $form->setLib('om_sql',_('om_sql'));
        $form->setLib('maj',_('maj'));
        $form->setLib('table_update',_('table_update'));
        $form->setLib('champ',_('champ'));
        $form->setLib('retour',_('retour'));
        $form->setLib('type_geometrie',_('type_geometrie'));
        $form->setLib('lib_geometrie',_('lib_geometrie'));
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

        // om_collectivite
        $this->init_select($form, $this->f->db, $maj, null, "om_collectivite", $sql_om_collectivite, $sql_om_collectivite_by_id, false);
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
        if($validation==0 and $maj==0 and $_SESSION['niveau']==1) {
            $form->setVal('om_collectivite', $_SESSION['collectivite']);
        }// fin validation
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('om_collectivite', $this->retourformulaire))
                $form->setVal('om_collectivite', $idxformulaire);
        }// fin validation
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
        // Verification de la cle secondaire : om_sig_map_comp
        $this->rechercheTable($this->f->db, "om_sig_map_comp", "om_sig_map", $id);
        // Verification de la cle secondaire : om_sig_map_wms
        $this->rechercheTable($this->f->db, "om_sig_map_wms", "om_sig_map", $id);
    }


}

?>
