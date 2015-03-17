<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sig_map.class.php 2946 2014-11-03 10:22:38Z baldachino $
 */

//
require_once "../gen/obj/om_sig_map.class.php";

/**
 *
 */
class om_sig_map_core extends om_sig_map_gen {

    /**
     *
     */
    var $required_field=array(
        "om_sig_map",
        "om_collectivite",
        "id",
        "libelle",
        "zoom",
        "etendue",
        "projection_externe",
        "url",
        "om_sql",
        "retour"
    );
    /**
     * Définition des actions disponibles sur la classe.
     *
     * @return void
     */
    function init_class_actions() {
		parent::init_class_actions();
		$this->class_actions[4] = array(
            "identifier" => "copier_etendue",
            "portlet" => array(
                "libelle" => _("Copier étendue"),
                "order" => 40,
                "class" => "copy-16",
				"url" => "../scr/copy_om_sig_map_etendue.php?idx="
            ),
            "permission_suffix" => "om_sig_map",
        );
    }


    /**
     *
     */
    function setType(&$form,$maj) {
        parent::setType($form,$maj);
        if($maj<2){
            $form->setType('etendue','select');
            $form->setType('projection_externe','select');
            $form->setType('layer_info', 'checkbox');
            $form->setType('fond_osm','checkbox');
            $form->setType('fond_bing','checkbox');
            $form->setType('fond_sat','checkbox');
            $form->setType('maj','checkbox');
            $form->setType('type_geometrie','select');
        }
    }

    /**
     * Methode verifier
     */
    function verifier($val = array(), &$db = NULL, $DEBUG = false) {
        // On appelle la methode de la classe parent
        parent::verifier($val, $db, $DEBUG);
        // On verifie si le champ n'est pas vide
        if ($this->valF['id'] == "") {
            $this->correct = false;
            $this->addToMessage(_("Le champ")." "._("id")." "._("est obligatoire"));
        } else {
            // On verifie si il y a un autre id 'actif' pour la collectivite
            if ($this->valF['actif'] == true) {
                if ($this->maj == 0) {
                    //
                    $this->verifieractif($db, $val, $DEBUG, ']');
                } else {
                    //
                    $this->verifieractif($db, $val, $DEBUG, $val['om_sig_map']);
                }
            }
        }
    }

    /**
     *
     */
    function setTaille(&$form,$maj) {
        parent::setTaille($form,$maj);
        //taille des champs affiches (text)
        $form->setTaille('om_sig_map',4);
        $form->setTaille('om_collectivite',4);
        $form->setTaille('id',20);
        $form->setTaille('libelle',50);
        $form->setTaille('zoom',3);
        $form->setTaille('fond_osm',1);
        $form->setTaille('fond_bing',1);
        $form->setTaille('fond_sat',1);
        $form->setTaille('etendue',60);
        $form->setTaille('projection_externe',20);
        $form->setTaille('maj',1);
        // AB-DEB 2012-05-14 - gestion champs géométrie
        $form->setTaille('lib_geometrie',50);
        $form->setTaille('type_geometrie',30);
        // AB-FIN 2012-05-14 - gestion champs géométrie
        $form->setTaille('table_update',30);
        $form->setTaille('champ',30);
        $form->setTaille('retour',50);
    }

    /**
     *
     */
    function setMax(&$form,$maj) {
        parent::setMax($form,$maj); 
        $form->setMax('om_sig_map',4);
        $form->setMax('om_collectivite',4);
        $form->setMax('id',50);
        $form->setMax('libelle',50);
        $form->setMax('zoom',3);
        $form->setMax('fond_osm',1);
        $form->setMax('fond_bing',1);
        $form->setMax('fond_sat',1);
        $form->setMax('etendue',60);
        $form->setMax('projection_externe',60);
        $form->setMax('url',2);
        $form->setMax('maj',1);
        $form->setMax('lib_geometrie',50);
        $form->setMax('table_update',30);
        $form->setMax('table_update',30);
        $form->setMax('champ',30);
        $form->setMax('retour',50);
    }

    /**
     *
     */
    function setOnchange(&$form,$maj) {
        parent::setOnchange($form,$maj);
        $form->setOnchange('zoom','VerifNum(this)');
    }

    /**
     *
     */
    function setSelect(&$form, $maj,&$db,$debug) {
        parent::setSelect($form, $maj,$db,$debug);
        if($maj<2){
            if(file_exists ("../dyn/var_sig.inc"))
                include ("../dyn/var_sig.inc");
            $form->setSelect("etendue",$contenu_etendue);
            $form->setSelect("projection_externe",$contenu_epsg);
        // AB-DEB 2012-05-14 - gestion champs géométrie
        $form->setSelect("type_geometrie",$type_geometrie);
        // AB-FIN 2012-05-14 - gestion champs géométrie     
        }// fin maj
    }

    /**
     *
     */
    function setGroupe (&$form, $maj) {
        
        $form->setGroupe('id','D');
        $form->setGroupe('libelle','G');
        $form->setGroupe('actif','F');
        
        $form->setGroupe('zoom','D');
        $form->setGroupe('fond_osm','G');
        $form->setGroupe('fond_bing','G');
        $form->setGroupe('fond_sat','G');
        $form->setGroupe('layer_info','F');
        
        $form->setGroupe('etendue','D');
        $form->setGroupe('projection_externe','F');
        
        $form->setGroupe('maj','D');
        $form->setGroupe('lib_geometrie','G');
        $form->setGroupe('type_geometrie','G');
        $form->setGroupe('table_update','G');
        $form->setGroupe('champ','F');
    }

    /**
     *
     */
    function setRegroupe (&$form, $maj) {
        
        $form->setRegroupe('zoom','D',' '._('fond').' ', "collapsible");
        $form->setRegroupe('fond_osm','G','');
        $form->setRegroupe('fond_bing','G','');
        $form->setRegroupe('fond_sat','G','');
        $form->setRegroupe('layer_info','F','');
        
        $form->setRegroupe('id','D',' '._('titre').' ', "collapsible");
        $form->setRegroupe('libelle','G','');
        $form->setRegroupe('actif','F','');
        
        $form->setRegroupe('etendue','D',' '._('etendue').' ', "collapsible");
        $form->setRegroupe('projection_externe','F','');
        
        $form->setRegroupe('url','D',' '._('marqueurs').' ', "collapsible");
        $form->setRegroupe('om_sql','F','');
        
		$form->setRegroupe('maj','D',' '._('Mise a jour').' ', "collapsible");
        $form->setRegroupe('lib_geometrie','G',''); 
        $form->setRegroupe('type_geometrie','G','');    

        $form->setRegroupe('table_update','G','');    
        $form->setRegroupe('champ','F','');    
    }

    /**
     *
     */
    function setLib(&$form,$maj) {
        parent::setLib($form,$maj);
        //libelle des champs
        $form->setLib('fond_osm',_('osm : '));
        $form->setLib('fond_bing',_('bing : '));
        $form->setLib('fond_sat',_('sat : '));
        $form->setLib('etendue',_('etendue'));
        $form->setLib('projection_externe',_('projection'));
        $form->setLib('url',_('url'));
        $form->setLib('om_sql',_('requete sql'));
        $form->setLib('maj',_('maj'));
        $form->setLib('lib_geometrie',_('nom geometrie'));
        $form->setLib('type_geometrie',_('type de geometrie'));
        $form->setLib('table_update',_(' table :'));
    }

    /**
     *
     */
    function setVal(&$form, $maj, $validation, &$db, $DEBUG=null) {
        parent::setVal($form, $maj, $validation, $db, $DEBUG=null);
        $this->maj=$maj;
    }

    /**
     *
     */
    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$db, $DEBUG=null) {
        parent::setValsousformulaire($form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, $db, $DEBUG=null);
        $this->maj=$maj;
    }

    /**
     * verification sur existence d un etat deja actif pour la collectivite
     */
    function verifieractif(&$db, $val, $DEBUG,$id){
        $sql = "select om_sig_map from ".DB_PREFIXE."om_sig_map where id ='".$val['id']."'";
        $sql.= " and om_collectivite ='".$val['om_collectivite']."'";
        $sql.= " and actif =true";
        if($id!=']')
            $sql.=" and  om_sig_map !='".$id."'";
        $res = $db->query($sql);
        if($DEBUG==1) echo $sql;
        if (database::isError($res))
           die($res->getMessage(). " => Echec  ".$sql);
        else{
           $nbligne=$res->numrows();
           if ($nbligne>0){
               $this->addToMessage($nbligne." "._("sig_map")." "._("existant").
               " "._("actif")." ! "._("vous ne pouvez avoir qu un sig_map")." '".
               $val['id']."' "._("actif")."  "._("par collectivite"));
               $this->correct=False;
            }
        }
    }

}
?>
