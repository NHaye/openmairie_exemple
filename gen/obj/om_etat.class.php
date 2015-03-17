<?php
//$Id: om_etat.class.php 3074 2015-02-25 15:22:47Z fmichon $ 
//gen openMairie le 25/02/2015 16:22

require_once "../obj/om_dbform.class.php";

class om_etat_gen extends om_dbform {

    var $table = "om_etat";
    var $clePrimaire = "om_etat";
    var $typeCle = "N";
    var $required_field = array(
        "corps_om_htmletatex",
        "format",
        "id",
        "libelle",
        "logoleft",
        "logotop",
        "om_collectivite",
        "om_etat",
        "om_sql",
        "orientation",
        "se_couleurtexte",
        "se_font",
        "titrebordure",
        "titrehauteur",
        "titrelargeur",
        "titreleft",
        "titre_om_htmletat",
        "titretop"
    );
    
    var $foreign_keys_extended = array(
        "om_collectivite" => array("om_collectivite", ),
        "om_requete" => array("om_requete", ),
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_etat'])) {
            $this->valF['om_etat'] = ""; // -> requis
        } else {
            $this->valF['om_etat'] = $val['om_etat'];
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
        $this->valF['orientation'] = $val['orientation'];
        $this->valF['format'] = $val['format'];
        if ($val['logo'] == "") {
            $this->valF['logo'] = NULL;
        } else {
            $this->valF['logo'] = $val['logo'];
        }
        if (!is_numeric($val['logoleft'])) {
            $this->valF['logoleft'] = ""; // -> requis
        } else {
            $this->valF['logoleft'] = $val['logoleft'];
        }
        if (!is_numeric($val['logotop'])) {
            $this->valF['logotop'] = ""; // -> requis
        } else {
            $this->valF['logotop'] = $val['logotop'];
        }
            $this->valF['titre_om_htmletat'] = $val['titre_om_htmletat'];
        if (!is_numeric($val['titreleft'])) {
            $this->valF['titreleft'] = ""; // -> requis
        } else {
            $this->valF['titreleft'] = $val['titreleft'];
        }
        if (!is_numeric($val['titretop'])) {
            $this->valF['titretop'] = ""; // -> requis
        } else {
            $this->valF['titretop'] = $val['titretop'];
        }
        if (!is_numeric($val['titrelargeur'])) {
            $this->valF['titrelargeur'] = ""; // -> requis
        } else {
            $this->valF['titrelargeur'] = $val['titrelargeur'];
        }
        if (!is_numeric($val['titrehauteur'])) {
            $this->valF['titrehauteur'] = ""; // -> requis
        } else {
            $this->valF['titrehauteur'] = $val['titrehauteur'];
        }
        $this->valF['titrebordure'] = $val['titrebordure'];
            $this->valF['corps_om_htmletatex'] = $val['corps_om_htmletatex'];
        if (!is_numeric($val['om_sql'])) {
            $this->valF['om_sql'] = ""; // -> requis
        } else {
            $this->valF['om_sql'] = $val['om_sql'];
        }
        $this->valF['se_font'] = $val['se_font'];
        $this->valF['se_couleurtexte'] = $val['se_couleurtexte'];
        if (!is_numeric($val['margeleft'])) {
            $this->valF['margeleft'] = 0; // -> default
        } else {
            $this->valF['margeleft'] = $val['margeleft'];
        }
        if (!is_numeric($val['margetop'])) {
            $this->valF['margetop'] = 0; // -> default
        } else {
            $this->valF['margetop'] = $val['margetop'];
        }
        if (!is_numeric($val['margeright'])) {
            $this->valF['margeright'] = 0; // -> default
        } else {
            $this->valF['margeright'] = $val['margeright'];
        }
        if (!is_numeric($val['margebottom'])) {
            $this->valF['margebottom'] = 0; // -> default
        } else {
            $this->valF['margebottom'] = $val['margebottom'];
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
            $form->setType("om_etat", "hidden");
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
            $form->setType("orientation", "text");
            $form->setType("format", "text");
            $form->setType("logo", "text");
            $form->setType("logoleft", "text");
            $form->setType("logotop", "text");
            $form->setType("titre_om_htmletat", "htmlEtat");
            $form->setType("titreleft", "text");
            $form->setType("titretop", "text");
            $form->setType("titrelargeur", "text");
            $form->setType("titrehauteur", "text");
            $form->setType("titrebordure", "text");
            $form->setType("corps_om_htmletatex", "htmlEtatEx");
            if ($this->is_in_context_of_foreign_key("om_requete", $this->retourformulaire)) {
                $form->setType("om_sql", "selecthiddenstatic");
            } else {
                $form->setType("om_sql", "select");
            }
            $form->setType("se_font", "text");
            $form->setType("se_couleurtexte", "text");
            $form->setType("margeleft", "text");
            $form->setType("margetop", "text");
            $form->setType("margeright", "text");
            $form->setType("margebottom", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_etat", "hiddenstatic");
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
            $form->setType("orientation", "text");
            $form->setType("format", "text");
            $form->setType("logo", "text");
            $form->setType("logoleft", "text");
            $form->setType("logotop", "text");
            $form->setType("titre_om_htmletat", "htmlEtat");
            $form->setType("titreleft", "text");
            $form->setType("titretop", "text");
            $form->setType("titrelargeur", "text");
            $form->setType("titrehauteur", "text");
            $form->setType("titrebordure", "text");
            $form->setType("corps_om_htmletatex", "htmlEtatEx");
            if ($this->is_in_context_of_foreign_key("om_requete", $this->retourformulaire)) {
                $form->setType("om_sql", "selecthiddenstatic");
            } else {
                $form->setType("om_sql", "select");
            }
            $form->setType("se_font", "text");
            $form->setType("se_couleurtexte", "text");
            $form->setType("margeleft", "text");
            $form->setType("margetop", "text");
            $form->setType("margeright", "text");
            $form->setType("margebottom", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_etat", "hiddenstatic");
            if ($_SESSION["niveau"] == 2) {
                $form->setType("om_collectivite", "selectstatic");
            } else {
                $form->setType("om_collectivite", "hidden");
            }
            $form->setType("id", "hiddenstatic");
            $form->setType("libelle", "hiddenstatic");
            $form->setType("actif", "hiddenstatic");
            $form->setType("orientation", "hiddenstatic");
            $form->setType("format", "hiddenstatic");
            $form->setType("logo", "hiddenstatic");
            $form->setType("logoleft", "hiddenstatic");
            $form->setType("logotop", "hiddenstatic");
            $form->setType("titre_om_htmletat", "hiddenstatic");
            $form->setType("titreleft", "hiddenstatic");
            $form->setType("titretop", "hiddenstatic");
            $form->setType("titrelargeur", "hiddenstatic");
            $form->setType("titrehauteur", "hiddenstatic");
            $form->setType("titrebordure", "hiddenstatic");
            $form->setType("corps_om_htmletatex", "hiddenstatic");
            $form->setType("om_sql", "selectstatic");
            $form->setType("se_font", "hiddenstatic");
            $form->setType("se_couleurtexte", "hiddenstatic");
            $form->setType("margeleft", "hiddenstatic");
            $form->setType("margetop", "hiddenstatic");
            $form->setType("margeright", "hiddenstatic");
            $form->setType("margebottom", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_etat", "static");
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
            $form->setType("orientation", "static");
            $form->setType("format", "static");
            $form->setType("logo", "static");
            $form->setType("logoleft", "static");
            $form->setType("logotop", "static");
            $form->setType("titre_om_htmletat", "htmlstatic");
            $form->setType("titreleft", "static");
            $form->setType("titretop", "static");
            $form->setType("titrelargeur", "static");
            $form->setType("titrehauteur", "static");
            $form->setType("titrebordure", "static");
            $form->setType("corps_om_htmletatex", "htmlstatic");
            $form->setType("om_sql", "selectstatic");
            $form->setType("se_font", "static");
            $form->setType("se_couleurtexte", "static");
            $form->setType("margeleft", "static");
            $form->setType("margetop", "static");
            $form->setType("margeright", "static");
            $form->setType("margebottom", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_etat','VerifNum(this)');
        $form->setOnchange('om_collectivite','VerifNum(this)');
        $form->setOnchange('logoleft','VerifNum(this)');
        $form->setOnchange('logotop','VerifNum(this)');
        $form->setOnchange('titreleft','VerifNum(this)');
        $form->setOnchange('titretop','VerifNum(this)');
        $form->setOnchange('titrelargeur','VerifNum(this)');
        $form->setOnchange('titrehauteur','VerifNum(this)');
        $form->setOnchange('om_sql','VerifNum(this)');
        $form->setOnchange('margeleft','VerifNum(this)');
        $form->setOnchange('margetop','VerifNum(this)');
        $form->setOnchange('margeright','VerifNum(this)');
        $form->setOnchange('margebottom','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_etat", 11);
        $form->setTaille("om_collectivite", 11);
        $form->setTaille("id", 30);
        $form->setTaille("libelle", 30);
        $form->setTaille("actif", 1);
        $form->setTaille("orientation", 10);
        $form->setTaille("format", 10);
        $form->setTaille("logo", 30);
        $form->setTaille("logoleft", 11);
        $form->setTaille("logotop", 11);
        $form->setTaille("titre_om_htmletat", 80);
        $form->setTaille("titreleft", 11);
        $form->setTaille("titretop", 11);
        $form->setTaille("titrelargeur", 11);
        $form->setTaille("titrehauteur", 11);
        $form->setTaille("titrebordure", 20);
        $form->setTaille("corps_om_htmletatex", 80);
        $form->setTaille("om_sql", 11);
        $form->setTaille("se_font", 20);
        $form->setTaille("se_couleurtexte", 11);
        $form->setTaille("margeleft", 11);
        $form->setTaille("margetop", 11);
        $form->setTaille("margeright", 11);
        $form->setTaille("margebottom", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_etat", 11);
        $form->setMax("om_collectivite", 11);
        $form->setMax("id", 50);
        $form->setMax("libelle", 100);
        $form->setMax("actif", 1);
        $form->setMax("orientation", 2);
        $form->setMax("format", 5);
        $form->setMax("logo", 30);
        $form->setMax("logoleft", 11);
        $form->setMax("logotop", 11);
        $form->setMax("titre_om_htmletat", 6);
        $form->setMax("titreleft", 11);
        $form->setMax("titretop", 11);
        $form->setMax("titrelargeur", 11);
        $form->setMax("titrehauteur", 11);
        $form->setMax("titrebordure", 20);
        $form->setMax("corps_om_htmletatex", 6);
        $form->setMax("om_sql", 11);
        $form->setMax("se_font", 20);
        $form->setMax("se_couleurtexte", 11);
        $form->setMax("margeleft", 11);
        $form->setMax("margetop", 11);
        $form->setMax("margeright", 11);
        $form->setMax("margebottom", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_etat',_('om_etat'));
        $form->setLib('om_collectivite',_('om_collectivite'));
        $form->setLib('id',_('id'));
        $form->setLib('libelle',_('libelle'));
        $form->setLib('actif',_('actif'));
        $form->setLib('orientation',_('orientation'));
        $form->setLib('format',_('format'));
        $form->setLib('logo',_('logo'));
        $form->setLib('logoleft',_('logoleft'));
        $form->setLib('logotop',_('logotop'));
        $form->setLib('titre_om_htmletat',_('titre_om_htmletat'));
        $form->setLib('titreleft',_('titreleft'));
        $form->setLib('titretop',_('titretop'));
        $form->setLib('titrelargeur',_('titrelargeur'));
        $form->setLib('titrehauteur',_('titrehauteur'));
        $form->setLib('titrebordure',_('titrebordure'));
        $form->setLib('corps_om_htmletatex',_('corps_om_htmletatex'));
        $form->setLib('om_sql',_('om_sql'));
        $form->setLib('se_font',_('se_font'));
        $form->setLib('se_couleurtexte',_('se_couleurtexte'));
        $form->setLib('margeleft',_('margeleft'));
        $form->setLib('margetop',_('margetop'));
        $form->setLib('margeright',_('margeright'));
        $form->setLib('margebottom',_('margebottom'));
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
        // om_sql
        $this->init_select($form, $this->f->db, $maj, null, "om_sql", $sql_om_sql, $sql_om_sql_by_id, false);
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
            if($this->is_in_context_of_foreign_key('om_requete', $this->retourformulaire))
                $form->setVal('om_sql', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire 
    //==================================
    

}

?>
