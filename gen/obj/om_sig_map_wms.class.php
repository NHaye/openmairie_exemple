<?php
//$Id: om_sig_map_wms.class.php 3074 2015-02-25 15:22:47Z fmichon $ 
//gen openMairie le 25/02/2015 16:22

require_once "../obj/om_dbform.class.php";

class om_sig_map_wms_gen extends om_dbform {

    var $table = "om_sig_map_wms";
    var $clePrimaire = "om_sig_map_wms";
    var $typeCle = "N";
    var $required_field = array(
        "ol_map",
        "om_sig_map",
        "om_sig_map_wms",
        "om_sig_wms",
        "ordre"
    );
    
    var $foreign_keys_extended = array(
        "om_sig_map" => array("om_sig_map", ),
        "om_sig_wms" => array("om_sig_wms", ),
    );



    function setvalF($val) {
        //affectation valeur formulaire
        if (!is_numeric($val['om_sig_map_wms'])) {
            $this->valF['om_sig_map_wms'] = ""; // -> requis
        } else {
            $this->valF['om_sig_map_wms'] = $val['om_sig_map_wms'];
        }
        if (!is_numeric($val['om_sig_wms'])) {
            $this->valF['om_sig_wms'] = ""; // -> requis
        } else {
            $this->valF['om_sig_wms'] = $val['om_sig_wms'];
        }
        if (!is_numeric($val['om_sig_map'])) {
            $this->valF['om_sig_map'] = ""; // -> requis
        } else {
            $this->valF['om_sig_map'] = $val['om_sig_map'];
        }
        $this->valF['ol_map'] = $val['ol_map'];
        if (!is_numeric($val['ordre'])) {
            $this->valF['ordre'] = ""; // -> requis
        } else {
            $this->valF['ordre'] = $val['ordre'];
        }
        if ($val['visibility'] == "") {
            $this->valF['visibility'] = NULL;
        } else {
            $this->valF['visibility'] = $val['visibility'];
        }
        if ($val['panier'] == "") {
            $this->valF['panier'] = NULL;
        } else {
            $this->valF['panier'] = $val['panier'];
        }
        if ($val['pa_nom'] == "") {
            $this->valF['pa_nom'] = NULL;
        } else {
            $this->valF['pa_nom'] = $val['pa_nom'];
        }
        if ($val['pa_layer'] == "") {
            $this->valF['pa_layer'] = NULL;
        } else {
            $this->valF['pa_layer'] = $val['pa_layer'];
        }
        if ($val['pa_attribut'] == "") {
            $this->valF['pa_attribut'] = NULL;
        } else {
            $this->valF['pa_attribut'] = $val['pa_attribut'];
        }
        if ($val['pa_encaps'] == "") {
            $this->valF['pa_encaps'] = NULL;
        } else {
            $this->valF['pa_encaps'] = $val['pa_encaps'];
        }
            $this->valF['pa_sql'] = $val['pa_sql'];
        if ($val['pa_type_geometrie'] == "") {
            $this->valF['pa_type_geometrie'] = NULL;
        } else {
            $this->valF['pa_type_geometrie'] = $val['pa_type_geometrie'];
        }
            $this->valF['sql_filter'] = $val['sql_filter'];
        if ($val['baselayer'] == "") {
            $this->valF['baselayer'] = NULL;
        } else {
            $this->valF['baselayer'] = $val['baselayer'];
        }
        if ($val['singletile'] == "") {
            $this->valF['singletile'] = NULL;
        } else {
            $this->valF['singletile'] = $val['singletile'];
        }
        if (!is_numeric($val['maxzoomlevel'])) {
            $this->valF['maxzoomlevel'] = NULL;
        } else {
            $this->valF['maxzoomlevel'] = $val['maxzoomlevel'];
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
            $form->setType("om_sig_map_wms", "hidden");
            if ($this->is_in_context_of_foreign_key("om_sig_wms", $this->retourformulaire)) {
                $form->setType("om_sig_wms", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_wms", "select");
            }
            if ($this->is_in_context_of_foreign_key("om_sig_map", $this->retourformulaire)) {
                $form->setType("om_sig_map", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_map", "select");
            }
            $form->setType("ol_map", "text");
            $form->setType("ordre", "text");
            $form->setType("visibility", "text");
            $form->setType("panier", "text");
            $form->setType("pa_nom", "text");
            $form->setType("pa_layer", "text");
            $form->setType("pa_attribut", "text");
            $form->setType("pa_encaps", "text");
            $form->setType("pa_sql", "textarea");
            $form->setType("pa_type_geometrie", "text");
            $form->setType("sql_filter", "textarea");
            $form->setType("baselayer", "text");
            $form->setType("singletile", "text");
            $form->setType("maxzoomlevel", "text");
        }

        // MDOE MODIFIER
        if ($maj == 1) {
            $form->setType("om_sig_map_wms", "hiddenstatic");
            if ($this->is_in_context_of_foreign_key("om_sig_wms", $this->retourformulaire)) {
                $form->setType("om_sig_wms", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_wms", "select");
            }
            if ($this->is_in_context_of_foreign_key("om_sig_map", $this->retourformulaire)) {
                $form->setType("om_sig_map", "selecthiddenstatic");
            } else {
                $form->setType("om_sig_map", "select");
            }
            $form->setType("ol_map", "text");
            $form->setType("ordre", "text");
            $form->setType("visibility", "text");
            $form->setType("panier", "text");
            $form->setType("pa_nom", "text");
            $form->setType("pa_layer", "text");
            $form->setType("pa_attribut", "text");
            $form->setType("pa_encaps", "text");
            $form->setType("pa_sql", "textarea");
            $form->setType("pa_type_geometrie", "text");
            $form->setType("sql_filter", "textarea");
            $form->setType("baselayer", "text");
            $form->setType("singletile", "text");
            $form->setType("maxzoomlevel", "text");
        }

        // MODE SUPPRIMER
        if ($maj == 2) {
            $form->setType("om_sig_map_wms", "hiddenstatic");
            $form->setType("om_sig_wms", "selectstatic");
            $form->setType("om_sig_map", "selectstatic");
            $form->setType("ol_map", "hiddenstatic");
            $form->setType("ordre", "hiddenstatic");
            $form->setType("visibility", "hiddenstatic");
            $form->setType("panier", "hiddenstatic");
            $form->setType("pa_nom", "hiddenstatic");
            $form->setType("pa_layer", "hiddenstatic");
            $form->setType("pa_attribut", "hiddenstatic");
            $form->setType("pa_encaps", "hiddenstatic");
            $form->setType("pa_sql", "hiddenstatic");
            $form->setType("pa_type_geometrie", "hiddenstatic");
            $form->setType("sql_filter", "hiddenstatic");
            $form->setType("baselayer", "hiddenstatic");
            $form->setType("singletile", "hiddenstatic");
            $form->setType("maxzoomlevel", "hiddenstatic");
        }

        // MODE CONSULTER
        if ($maj == 3) {
            $form->setType("om_sig_map_wms", "static");
            $form->setType("om_sig_wms", "selectstatic");
            $form->setType("om_sig_map", "selectstatic");
            $form->setType("ol_map", "static");
            $form->setType("ordre", "static");
            $form->setType("visibility", "static");
            $form->setType("panier", "static");
            $form->setType("pa_nom", "static");
            $form->setType("pa_layer", "static");
            $form->setType("pa_attribut", "static");
            $form->setType("pa_encaps", "static");
            $form->setType("pa_sql", "textareastatic");
            $form->setType("pa_type_geometrie", "static");
            $form->setType("sql_filter", "textareastatic");
            $form->setType("baselayer", "static");
            $form->setType("singletile", "static");
            $form->setType("maxzoomlevel", "static");
        }

    }


    function setOnchange(&$form, $maj) {
    //javascript controle client
        $form->setOnchange('om_sig_map_wms','VerifNum(this)');
        $form->setOnchange('om_sig_wms','VerifNum(this)');
        $form->setOnchange('om_sig_map','VerifNum(this)');
        $form->setOnchange('ordre','VerifNum(this)');
        $form->setOnchange('maxzoomlevel','VerifNum(this)');
    }
    /**
     * Methode setTaille
     */
    function setTaille(&$form, $maj) {
        $form->setTaille("om_sig_map_wms", 11);
        $form->setTaille("om_sig_wms", 11);
        $form->setTaille("om_sig_map", 11);
        $form->setTaille("ol_map", 30);
        $form->setTaille("ordre", 11);
        $form->setTaille("visibility", 10);
        $form->setTaille("panier", 10);
        $form->setTaille("pa_nom", 30);
        $form->setTaille("pa_layer", 30);
        $form->setTaille("pa_attribut", 30);
        $form->setTaille("pa_encaps", 10);
        $form->setTaille("pa_sql", 80);
        $form->setTaille("pa_type_geometrie", 30);
        $form->setTaille("sql_filter", 80);
        $form->setTaille("baselayer", 10);
        $form->setTaille("singletile", 10);
        $form->setTaille("maxzoomlevel", 11);
    }

    /**
     * Methode setMax
     */
    function setMax(&$form, $maj) {
        $form->setMax("om_sig_map_wms", 11);
        $form->setMax("om_sig_wms", 11);
        $form->setMax("om_sig_map", 11);
        $form->setMax("ol_map", 50);
        $form->setMax("ordre", 11);
        $form->setMax("visibility", 3);
        $form->setMax("panier", 3);
        $form->setMax("pa_nom", 50);
        $form->setMax("pa_layer", 50);
        $form->setMax("pa_attribut", 50);
        $form->setMax("pa_encaps", 3);
        $form->setMax("pa_sql", 6);
        $form->setMax("pa_type_geometrie", 30);
        $form->setMax("sql_filter", 6);
        $form->setMax("baselayer", 3);
        $form->setMax("singletile", 3);
        $form->setMax("maxzoomlevel", 11);
    }


    function setLib(&$form, $maj) {
    //libelle des champs
        $form->setLib('om_sig_map_wms',_('om_sig_map_wms'));
        $form->setLib('om_sig_wms',_('om_sig_wms'));
        $form->setLib('om_sig_map',_('om_sig_map'));
        $form->setLib('ol_map',_('ol_map'));
        $form->setLib('ordre',_('ordre'));
        $form->setLib('visibility',_('visibility'));
        $form->setLib('panier',_('panier'));
        $form->setLib('pa_nom',_('pa_nom'));
        $form->setLib('pa_layer',_('pa_layer'));
        $form->setLib('pa_attribut',_('pa_attribut'));
        $form->setLib('pa_encaps',_('pa_encaps'));
        $form->setLib('pa_sql',_('pa_sql'));
        $form->setLib('pa_type_geometrie',_('pa_type_geometrie'));
        $form->setLib('sql_filter',_('sql_filter'));
        $form->setLib('baselayer',_('baselayer'));
        $form->setLib('singletile',_('singletile'));
        $form->setLib('maxzoomlevel',_('maxzoomlevel'));
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
        // om_sig_wms
        $this->init_select($form, $this->f->db, $maj, null, "om_sig_wms", $sql_om_sig_wms, $sql_om_sig_wms_by_id, false);
    }


    //==================================
    // sous Formulaire 
    //==================================
    

    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        $this->retourformulaire = $retourformulaire;
        if($validation == 0) {
            if($this->is_in_context_of_foreign_key('om_sig_map', $this->retourformulaire))
                $form->setVal('om_sig_map', $idxformulaire);
            if($this->is_in_context_of_foreign_key('om_sig_wms', $this->retourformulaire))
                $form->setVal('om_sig_wms', $idxformulaire);
        }// fin validation
        $this->set_form_default_values($form, $maj, $validation);
    }// fin setValsousformulaire

    //==================================
    // cle secondaire 
    //==================================
    

}

?>
