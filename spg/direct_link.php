<?php
/**
 * Script permettant de definir les parametres necessaire a
 * l'acces d'un sous formulaire depuis un autre objet
 *
 * @param obj string : objet de l'objet parent
 * @param action string : action sur l'objet parent
 * @param direct_field string : nom du champ contenant l'id de l'objet parent
 * @param direct_form string : nom de l'objet du sous form a afficher
 * @param direct_action string : action a effectuer sur le sous-form
 * @param direct_idx mixed : id de l'objet du sous-form a afficher
 *
 * @package openmairie_exemple
 * @version SVN : $Id: direct_link.php 2206 2013-03-28 18:34:19Z fmichon $
 */

//Instantiation d'om
require_once "../obj/utils.class.php";
$f = new utils("nohtml");
//$f->disableLog();
//Recuperation des valeurs du GET
$param = $_GET;

if(isset($param['obj']) AND !empty($param['obj']) AND
   isset($param['action']) AND !empty($param['action']) AND
   isset($param['direct_field']) AND !empty($param['direct_field']) AND
   isset($param['direct_form']) AND !empty($param['direct_form']) AND
   isset($param['direct_action']) AND !empty($param['direct_action']) AND
   isset($param['direct_idx']) AND !empty($param['direct_idx'])) {
    
    //Verification de la presence de la classe
    if (strpos($param['direct_form'], "/") !== false
        or !file_exists("../obj/".$param['direct_form'].".class.php")) {
        $class = "error";
        $message = _("L'objet est invalide.");
        $f->addToMessage($class, $message);
        $f->setFlag(NULL);
        $f->display();
        die();
    }
    if (file_exists("../sql/pgsql/".$param['obj'].".inc.php")) {
        require_once "../sql/pgsql/".$param['obj'].".inc.php";
    }
    if (file_exists("../obj/".$param['direct_form'].".class.php")) {
        require_once "../obj/".$param['direct_form'].".class.php";
    }

    //Instanciation de la classe
    $object = new $param['direct_form']($param['direct_idx'], $f->db, 0);

	// Recuperation de l'id de l'onglet
    $tabs_id=0;
    foreach($sousformulaire as $souform) {
        $droit=array();
        $droit[]=$souform;
        $droit[]=$souform."_tab";
        
        if($f->isAccredited($droit,"OR")) {
            $tabs_id++;
            if($souform==$param['direct_form']) {
                break;
            }
        }
    }
    //Appel du sous-form avec l'id du formulaire parent recupere dans les valeur de l'objet instancie
    header("Location: ../scr/form.php?obj=".$param['obj'].
                                        "&action=".$param['action'].
                                        "&idx=".$object->val[array_search($param['direct_field'],$object->champs)].
                                        "&direct_form=".$param['direct_form'].
                                        "&direct_idx=".$param['direct_idx'].
                                        "&direct_action=".$param['direct_action'].
                                        "#ui-tabs-".$tabs_id);
    
} else {
    $class = "error";
    $message = _("L'element n'est pas accessible.");
    $f->addToMessage($class, $message);
    $f->setFlag(NULL);
    $f->display();
}




?>