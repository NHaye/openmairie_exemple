<?php
//$Id: om_sig_wms.class.php 3053 2015-02-13 18:02:02Z nmeucci $ 
//gen openMairie le 13/02/2015 18:49

require_once "../obj/om_dbform.class.php";

class om_sig_wms_gen extends om_dbform {

    var $table = "om_sig_wms";
    var $clePrimaire = "om_sig_wms";
    var $typeCle = "N";
    var $required_field = array(
        "chemin",
        "couches",
        "id",
        "libelle",
        "om_collectivite",
        "om_sig_wms"
    );
    
    var $foreign_keys_extended = array(
        "om_collectivite" => array("om_collectivite", ),
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_sig_wms'])) {
            $this->valF['om_sig_wms'] = ""; // -> requis
        } else {
            $this->valF['om_sig_wms'] = $val['om_sig_wms'];
        }
        $this->valF['libelle'] = $val['libelle'];
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
        $this->valF['chemin'] = $val['chemin'];
        $this->valF['couches'] = $val['couches'];
        if ($val['cache_type'] == "") {
            $this->valF['cache_type'] = NULL;
        } else {
            $this->valF['cache_type'] = $val['cache_type'];
        }
        if ($val['cache_gfi_chemin'] == "") {
            $this->valF['cache_gfi_chemin'] = NULL;
        } else {
            $this->valF['cache_gfi_chemin'] = $val['cache_gfi_chemin'];
        }
        if ($val['cache_gfi_couches'] == "") {
            $this->valF['cache_gfi_couches'] = NULL;
        } else {
            $this->valF['cache_gfi_couches'] = $val['cache_gfi_couches'];
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
            $form->setType("om_sig_wms", "hidden");
            $form->setType("libelle", "text");
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
            $form->setType("chemin", "text");
            $form->setType("couches", "text");
            $form->setType("cache_type", "text");
            $form->setType("cache_gfi_chemin", "text");
            $form->setType("cache_gfi_couches", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_sig_wms", "hiddenstatic");
            $form->setType("libelle", "text");
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
            $form->setType("chemin", "text");
            $form->setType("couches", "text");
            $form->setType("cache_type", "text");
            $form->setType("cache_gfi_chemin", "text");
            $form->setType("cache_gfi_couches", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_sig_wms", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            if ($_SESSION["niveau"] == 2) {
                $form->setType("om_collectivite", "selectstatic");
            } else {
                $form->setType("om_collectivite", "hidden");
            }
            $form->setType("id", "hiddenstatic");
            $form->setType("chemin", "hiddenstatic");
            $form->setType("couches", "hiddenstatic");
            $form->setType("cache_type", "hiddenstatic");
            $form->setType("cache_gfi_chemin", "hiddenstatic");
            $form->setType("cache_gfi_couches", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_sig_wms", "static");
            $form->setType("libelle", "static");
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
            $form->setType("chemin", "static");
            $form->setType("couches", "static");
            $form->setType("cache_type", "static");
            $form->setType("cache_gfi_chemin", "static");
            $form->setType("cache_gfi_couches", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_sig_wms','VerifNum(this)');
        $form->setOnchange('om_collectivite','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_sig_wms", 11);
        $form->setTaille("libelle", 30);
        $form->setTaille("om_collectivite", 11);
        $form->setTaille("id", 30);
        $form->setTaille("chemin", 30);
        $form->setTaille("couches", 30);
        $form->setTaille("cache_type", 10);
        $form->setTaille("cache_gfi_chemin", 30);
        $form->setTaille("cache_gfi_couches", 30);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_sig_wms", 11);
        $form->setMax("libelle", 50);
        $form->setMax("om_collectivite", 11);
        $form->setMax("id", 50);
        $form->setMax("chemin", 255);
        $form->setMax("couches", 255);
        $form->setMax("cache_type", 3);
        $form->setMax("cache_gfi_chemin", 255);
        $form->setMax("cache_gfi_couches", 255);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_sig_wms',_('om_sig_wms'));
        $form->setLib('libelle',_('libelle'));
        $form->setLib('om_collectivite',_('om_collectivite'));
        $form->setLib('id',_('id'));
        $form->setLib('chemin',_('chemin'));
        $form->setLib('couches',_('couches'));
        $form->setLib('cache_type',_('cache_type'));
        $form->setLib('cache_gfi_chemin',_('cache_gfi_chemin'));
        $form->setLib('cache_gfi_couches',_('cache_gfi_couches'));
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
        // Verification de la cle secondaire : om_sig_map_wms
        $this->rechercheTable($this->f->db, "om_sig_map_wms", "om_sig_wms", $id);
    }


}

?>
