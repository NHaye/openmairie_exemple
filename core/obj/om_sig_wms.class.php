<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sig_wms.class.php 2946 2014-11-03 10:22:38Z baldachino $
 */

//
require_once "../gen/obj/om_sig_wms.class.php";

/**
 *
 */
class om_sig_wms_core extends om_sig_wms_gen {

    /**
     *
     */
    function setTaille(&$form,$maj) {
        parent::setTaille($form,$maj);
        //taille des champs affiches (text)
        $form->setTaille('om_sig_wms',4);
        $form->setTaille('om_collectivite',4);
        $form->setTaille('id',20);
        $form->setTaille('libelle',30);
        $form->setTaille('chemin',70);
        $form->setTaille('couches',70);
        $form->setTaille('cache_gfi_chemin',70);
        $form->setTaille('cache_gfi_couches',70);
    }

    /**
     *
     */
    function setMax(&$form,$maj) {
        parent::setMax($form,$maj); 
        $form->setMax('om_sig_wms',4);
        $form->setMax('om_collectivite',4);
        $form->setMax('id',50);
        $form->setMax('libelle',50);
        $form->setMax('chemin',6);
        $form->setMax('couches',3);
        $form->setMax('cache_gfi_chemin',6);
        $form->setMax('cache_gfi_couches',3);
    }

    /**
     *
     */
    function setType(&$form,$maj) {
        parent::setType($form,$maj);
        if($maj<2){
            $form->setType('cache_type','select');
			$form->setType('chemin','textarea');
			$form->setType('couches','textarea');
			$form->setType('cache_gfi_chemin','textarea');
			$form->setType('cache_gfi_couches','textarea');
        } else {
			$form->setType('chemin','textareastatic');
			$form->setType('couches','textareastatic');
			$form->setType('cache_gfi_chemin','textareastatic');
			$form->setType('cache_gfi_couches','textareastatic');
		}
    }

    /**
     *
     */
    function setLib(&$form,$maj) {
        parent::setLib($form,$maj);
        //libelle des champs
        $form->setLib('libelle',_('libelle : '));
		$form->setLib('cache_type',_('Type de flux : '));
        $form->setLib('couches',_('couches (séparées par ,) : '));
		if ($form->val['cache_type'] == '') {
			$form->setLib('chemin',_('URL : '));
			$form->setLib('cache_gfi_chemin',_('sans objet'));
			$form->setLib('cache_gfi_couches',_('sans objet'));
		} else if ($form->val['cache_type'] == 'IMP') {
			$form->setLib('chemin',_('URL ([EXTENT], [LAYERS]) : '));
			$form->setLib('cache_gfi_chemin',_('largeur carte dans composeur x 2 : '));
			$form->setLib('cache_gfi_couches',_('hauteur carte dans composeur x 2 : '));
		} else {
			$form->setLib('chemin',_('URL : '));
			$form->setLib('cache_gfi_chemin',_('URL pour GetFeatureInfo : '));
			$form->setLib('cache_gfi_couches',_('couches pour GetFeatureInfo : '));
		}
    }

    /**
     *
     */
    function setSelect(&$form, $maj,&$db,$debug) {
        parent::setSelect($form, $maj,$db,$debug);
        if($maj<2){
            $contenu_cache_type[0] = array("","TCF","SMT","IMP");
			$contenu_cache_type[1] = array("WMS",'Flux TileCache (via OpenLayers.layer.WMS)',"Slippy Map Tiles (type OSM)","Impression");
			$form->setSelect("cache_type",$contenu_cache_type);
        }
    }

    /**
     *
     */
	function setOnchange(&$form,$maj){
		parent::setOnchange($form,$maj);
		$form->setOnchange("cache_type",
		"if ( this.value=='') { ".
			"document.getElementById('lib-chemin').innerHTML='URL : ';".
			"document.getElementById('lib-cache_gfi_chemin').innerHTML='sans objet'; ".
			"document.getElementById('lib-cache_gfi_couches').innerHTML='sans objet'; ".
		"} else if ( this.value=='IMP') { ".
			"document.getElementById('lib-chemin').innerHTML='URL ([EXTENT], [LAYERS]) : ';".
			"document.getElementById('lib-cache_gfi_chemin').innerHTML='largeur carte dans composeur x 2 : '; ".
			"document.getElementById('lib-cache_gfi_couches').innerHTML='hauteur carte dans composeur x 2 : '; ".
		"} else { ".
			"document.getElementById('lib-chemin').innerHTML='URL : ';".
			"document.getElementById('lib-cache_gfi_chemin').innerHTML='URL pour GetFeatureInfo : '; ".
			"document.getElementById('lib-cache_gfi_couches').innerHTML='couches pour GetFeatureInfo : '; ".
		" }");
	}

    /**
     *
     */
    function setRegroupe (&$form, $maj) {
        parent::setRegroupe ($form, $maj);
        $form->setRegroupe('chemin','D',' '._('adresse').' ', "collapsible");
        $form->setRegroupe('couches','F','');
        
        $form->setRegroupe('cache_gfi_chemin','D',' '._('paramètres').' ', "collapsible");
        $form->setRegroupe('cache_gfi_couches','F','');

    }

}

?>
