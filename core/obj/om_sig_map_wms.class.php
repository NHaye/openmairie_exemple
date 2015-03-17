<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sig_map_wms.class.php 2946 2014-11-03 10:22:38Z baldachino $
 */

//
require_once "../gen/obj/om_sig_map_wms.class.php";

/**
 *
 */
class om_sig_map_wms_core extends om_sig_map_wms_gen {

    /**
     * Définition des actions disponibles sur la classe.
     *
     * @return void
     */
    function init_class_actions() {
		parent::init_class_actions();
		$this->class_actions[4] = array(
            "identifier" => "copier_flux",
            "portlet" => array(
                "libelle" => _("Copier flux"),
                "order" => 40,
                "class" => "copy-16",
				"url" => "../scr/copy_om_sig_map_wms.php?idx="
            ),
            "permission_suffix" => "om_sig_map_wms",
        );
    }
    /**
     *
     */
    function setType(&$form,$maj) {
        parent::setType($form,$maj);
        if($maj<2){
            if($this->retourformulaire=='om_sig_map') {
				$form->setType('om_sig_wms','select');
				$form->setType('om_sig_map', 'hidden');
            } else {
				$form->setType('om_sig_wms','hidden');
				$form->setType('om_sig_map', 'select');
			}
            $form->setType('visibility', 'checkbox');
            $form->setType('panier', 'checkbox');
            $form->setType('pa_type_geometrie','select');
            $form->setType('baselayer', 'checkbox');
            $form->setType('singletile', 'checkbox');
        }
        if ($maj == 2 or $maj == 3) {
            $form->setType('om_sig_wms','');
            $form->setType('pa_type_geometrie','selectstatic');
        }
    }

    /**
     *
     */
    function setTaille(&$form,$maj) {
        parent::setTaille($form,$maj);
        //taille des champs affiches (text)
        $form->setTaille('om_sig_wms',20);
        $form->setTaille('ol_map',50);
        $form->setTaille('ordre',4);
        $form->setTaille('pa_nom',20);
        $form->setTaille('pa_layer',50);
        $form->setTaille('pa_attribut',50);
        $form->setTaille('pa_encaps',3);
        $form->setTaille('pa_type_geometrie',30);
    }

    /**
     *
     */
    function setMax(&$form,$maj) {
        parent::setMax($form,$maj); 
        $form->setMax('ol_map',50);
        $form->setMax('ordre',4);
        $form->setMax('pa_nom',50);
        $form->setMax('pa_layer',50);
        $form->setMax('pa_attribut',50);
        $form->setMax('pa_encaps',3);
        $form->setMax('maxzoomlevel',3);
    }

    /**
     *
     */
    function setLib(&$form,$maj) {
        parent::setLib($form,$maj);
        //libelle des champs
        $form->setLib('om_sig_wms',_('flux WMS : '));
        $form->setLib('ol_map',_('nom map OpenLayer : '));
        $form->setLib('baselayer',_('fond de carte :'));
        $form->setLib('maxzoomlevel',_('niveau de zoom maximum :'));
        $form->setLib('ordre',_('ordre : '));
        $form->setLib('visibility',_('visible par défaut :'));
        $form->setLib('singletile',_('singletile OL :'));
        $form->setLib('panier',_('panier :'));
        $form->setLib('pa_nom',_('nom du panier :'));
        $form->setLib('pa_layer',_('couche du panier :'));
        $form->setLib('pa_attribut',_('attribut de la couche du panier :'));
        $form->setLib('pa_encaps',_('caractère d\'encapsulation de valeur panier :'));
        $form->setLib('pa_sql',_('requète d\'union (&lst) du panier :'));
        $form->setLib('pa_type_geometrie',_('type de géometrie du panier :'));
        $form->setLib('sql_filter',_('requète de filtrage (&idx) :'));
    }

    /**
     *
     */
    function setSelect(&$form, $maj,&$db,$debug) {
        parent::setSelect($form, $maj,$db,$debug);
        if(file_exists ("../dyn/var_sig.inc")) {
            include ("../dyn/var_sig.inc");
        }

        $form->setSelect("pa_type_geometrie",$type_geometrie);
		
		$k = 0;
		$om_sig_wms = "";
        foreach ($form->select['om_sig_wms'] as $elem) {
            while ($k <count($elem)) {
				if ($form->val['om_sig_wms'] == $form->select['om_sig_wms'][0][$k]) {
					$om_sig_wms = substr($form->select['om_sig_wms'][1][$k],-4);
					$k = count($elem);
				}
				$k++;
            }
        }
		 if ($om_sig_wms == 'TCF.' || $om_sig_wms == 'SMT.') { 
			$form->setLib('baselayer', _('fond de carte :'));
			$form->setLib('singletile', _('sans objet'));
			$form->setLib('panier', _('sans objet'));
			$form->setLib('pa_nom', _('sans objet'));
			$form->setLib('lib-pa_layer', _('sans objet'));
			$form->setLib('pa_attribut', _('sans objet'));
			$form->setLib('pa_encaps', _('sans objet'));
			$form->setLib('pa_sql', _('sans objet'));
			$form->setLib('pa_type_geometrie', _('sans objet'));
			$form->setLib('sql_filter', _('sans objet'));
		 } else if ($om_sig_wms == 'IMP.' || $om_sig_wms == 'IMP*') { 
			$form->setLib('baselayer', _('sans objet'));
			$form->setLib('singletile', _('sans objet'));
			$form->setLib('panier', _('sans objet'));
			$form->setLib('pa_nom', _('sans objet'));
			$form->setLib('pa_layer', _('sans objet'));
			$form->setLib('pa_attribut', _('sans objet'));
			$form->setLib('pa_encaps', _('sans objet'));
			$form->setLib('pa_sql', _('requète de titres (&idx, &user) :'));
			$form->setLib('pa_type_geometrie', _('sans objet'));
			$form->setLib('sql_filter', _('requète de filtrage (&idx):'));
		}
		
    }

    /**
     *
     */
    function setGroupe (&$form, $maj) {
        $form->setGroupe('baselayer','D');
        $form->setGroupe('maxzoomlevel','G');
        $form->setGroupe('ordre','F');
		$form->setGroupe('visibility','D');
		$form->setGroupe('singletile','F');
    }

    /**
     *
     */
    function setRegroupe (&$form, $maj) {
		$form->setRegroupe('baselayer','D',' '._('Paramètres généraux').' ', "collapsible");
        $form->setRegroupe('maxzoomlevel','G','');
        $form->setRegroupe('ordre','G','');
        $form->setRegroupe('visibility','G','');
        $form->setRegroupe('singletile','F','');

		$form->setRegroupe('panier','D',' '._('Paramètres avancés').' ', "collapsible");
        $form->setRegroupe('pa_nom','G','');
        $form->setRegroupe('pa_layer','G','');
        $form->setRegroupe('pa_attribut','G','');
        $form->setRegroupe('pa_encaps','G','');
        $form->setRegroupe('pa_sql','G','');
        $form->setRegroupe('pa_type_geometrie','G','');
		$form->setRegroupe('sql_filter','F','');
	}

    /**
     *
     */
	function setOnchange(&$form,$maj){
		parent::setOnchange($form,$maj);
		$form->setOnchange("om_sig_wms",
		" var elt = document.getElementById('om_sig_wms');".
		" var choix = elt.options[elt.selectedIndex].text.substr(elt.options[elt.selectedIndex].text.length - 4);".
		" if (choix == 'TCF.' || choix == 'SMT.') { ".
		"   document.getElementById('lib-baselayer').innerHTML='fond de carte :';".
		"   document.getElementById('lib-singletile').innerHTML='sans objet';".
		"   document.getElementById('lib-panier').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_nom').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_layer').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_attribut').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_encaps').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_sql').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_type_geometrie').innerHTML='sans objet';".	
		"   document.getElementById('lib-sql_filter').innerHTML='sans objet';".
		" } else if  (choix == 'IMP.' || choix == 'IMP*') { ".
		"   document.getElementById('lib-baselayer').innerHTML='sans objet';".
		"   document.getElementById('lib-singletile').innerHTML='sans objet';".
		"   document.getElementById('lib-panier').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_nom').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_layer').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_attribut').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_encaps').innerHTML='sans objet';".	
		"   document.getElementById('lib-pa_sql').innerHTML='requète de titres (&idx, &user) :';".	
		"   document.getElementById('lib-pa_type_geometrie').innerHTML='sans objet';".	
		"   document.getElementById('lib-sql_filter').innerHTML='requète de filtrage (&idx):';".
		" } else { ".
		"   document.getElementById('lib-baselayer').innerHTML='fond de carte :';".
		"   document.getElementById('lib-singletile').innerHTML='singletile OL :';".
		"   document.getElementById('lib-panier').innerHTML='panier :';".	
		"   document.getElementById('lib-pa_nom').innerHTML='nom du panier : ';".	
		"   document.getElementById('lib-pa_layer').innerHTML='couche du panier : ';".	
		"   document.getElementById('lib-pa_attribut').innerHTML='attribut de la couche du panier :';".	
		"   document.getElementById('lib-pa_encaps').innerHTML='caractère d\'encapsulation de valeur panier :';".	
		"   document.getElementById('lib-pa_sql').innerHTML='requète d\'union (&lst) du panier :';".	
		"   document.getElementById('lib-pa_type_geometrie').innerHTML='type de géometrie du panier :';".	
		"   document.getElementById('lib-sql_filter').innerHTML='requète de filtrage (&idx):';".
		" } ");
	}

    /**
     *
     */
    function verifier($val = array(), &$db = NULL, $DEBUG = false) {
        // On appelle la methode de la classe parent
        parent::verifier($val, $db, $DEBUG);
		
        if($this->valF['baselayer'] == 'Oui' && $this->valF['maxzoomlevel'] == '') {
            $this->correct = false;
            $msg = _("niveau de zoom maximum obligatoire");
            $this->addToMessage($msg);
        }
		if($this->valF['panier'] == 'Oui' && ($this->valF['pa_nom'] == '' || $this->valF['pa_layer'] == '' || $this->valF['pa_attribut'] == '' || $this->valF['pa_sql'] == '' || $this->valF['pa_type_geometrie'] == '')) {
            $this->correct = false;
            $msg = _("Tous les champs de définitions du panier sont obligatoires à l'exception du caractère d'encapsulation");
            $this->addToMessage($msg);
        }
    }

}

?>
