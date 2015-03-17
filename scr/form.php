<?php
/**
 * Ce script permet de gerer l'affichage du formulaire
 *
 * @package openmairie_exemple
 * @version SVN : $Id: form.php 3070 2015-02-20 15:09:36Z vpihour $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

/**
 * Initialisation des variables
 */
// Nom de l'objet metier du formulaire
(isset($_GET['obj']) ? $obj = $f->get_submitted_get_value('obj') : $obj = "");
// Identifiant de l'objet métier du formulaire et mode d'ajout
if (isset($_GET['idx']) and $_GET['idx'] != '') {
    $idx = $f->get_submitted_get_value('idx');

    if (isset($_GET['action']) and $_GET['action'] != '') {
        $maj = $f->get_submitted_get_value('action');

        if ($maj == 0) {
            $idx = "]";
        }

    } else {
        (isset($_GET['ids']) ? $maj = 2 : $maj = 1);
    }
} else {
    $maj = 0;
    $idx = "]";
}
// Flag de validation du formulaire
(isset($_GET['validation']) ? $validation = $f->get_submitted_get_value('validation') : $validation = 0);
// Libelle de l'enregistement du formulaire
(isset($_GET['idz']) ? $idz = $f->get_submitted_get_value('idz') : $idz = "");
// Premier enregistrement a afficher sur le tableau de la page precedente (tab.php?premier=)
(isset($_GET['premier']) ? $premier = $f->get_submitted_get_value('premier') : $premier = 0);
// Colonne choisie pour le tri sur le tableau de la page precedente (tab.php?tricol=)
(isset($_GET['tricol']) ? $tricol = $f->get_submitted_get_value('tricol') : $tricol = "");
// Colonne choisie pour la selection sur le tableau de la page precedente (tab.php?selectioncol=)
(isset($_GET['selectioncol']) ? $selectioncol = $f->get_submitted_get_value('selectioncol') : $selectioncol = "");
// Id unique de la recherche avancee
(isset($_GET['advs_id']) ? $advs_id = $f->get_submitted_get_value('advs_id') : $advs_id = "");
// Valilite des objets a afficher
(isset($_GET['valide']) ? $valide = $f->get_submitted_get_value('valide') : $valide = "");
// Origine de l'action
(isset($_GET['retour']) ? $retour = $f->get_submitted_get_value('retour') : $retour = "");
// objet de sous-form
(isset($_GET['direct_form']) ? $direct_form = $f->get_submitted_get_value('direct_form') : $direct_form = "");
// idx de sous-form
(isset($_GET['direct_idx']) ? $direct_idx = $f->get_submitted_get_value('direct_idx') : $direct_idx = "");
// action sur le sous-form
(isset($_GET['direct_action']) ? $direct_action = $f->get_submitted_get_value('direct_action') : $direct_action = "");
// Chaine recherchee
(isset($_GET['recherche']) ? $recherche = $f->get_submitted_get_value('recherche') : $recherche = "");

// Ce tableau permet a chaque application de definir des variables
// supplementaires qui seront passees a l'objet metier dans le constructeur
// a travers ce tableau
// Voir le fichier dyn/form.get.specific.inc.php pour plus d'informations
$extra_parameters = array();
if (file_exists("../dyn/form.get.specific.inc.php")) {
    require "../dyn/form.get.specific.inc.php";
}

/**
 * Verification des parametres
 */
if (strpos($obj, "/") !== false
    || !(file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php")
         || file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc"))
    || !file_exists("../obj/".$obj.".class.php")) {
    $class = "error";
    $message = _("L'objet est invalide.");
    $f->addToMessage($class, $message);
    $f->setFlag(NULL);
    $f->display();
    die();
}

// Dictionnaire des actions
// ------------------------

// Declaration du dictionnaire
$portlet_actions = array();

if ($maj == 3) {

    // Action : modifier
    $portlet_actions['modifier'] =
        array('lien' => '../scr/form.php?obj='.$obj.'&amp;action=1'.'&amp;idx=',
              'id' => '&amp;idz='.$idz.'&amp;premier='.$premier.'&amp;advs_id='.$advs_id.'&amp;recherche='.$recherche.'&amp;tricol='.$tricol.'&amp;selectioncol='.$selectioncol.'&amp;valide='.$valide.'&amp;retour=form',
              'lib' => '<span class="om-prev-icon om-icon-16 edit-16" title="'._('Modifier').'">'._('Modifier').'</span>',
              'rights' => array('list' => array($obj, $obj.'_modifier'), 'operator' => 'OR'),
              'ordre' => 10,);

    // Action : supprimer
    $portlet_actions['supprimer'] =
        array('lien' => '../scr/form.php?obj='.$obj.'&amp;action=2&amp;idx=',
              'id' => '&amp;idz='.$idz.'&amp;premier='.$premier.'&amp;advs_id='.$advs_id.'&amp;recherche='.$recherche.'&amp;tricol='.$tricol.'&amp;selectioncol='.$selectioncol.'&amp;valide='.$valide.'&amp;retour=form',
              'lib' => '<span class="om-prev-icon om-icon-16 delete-16" title="'._('Supprimer').'">'._('Supprimer').'</span>',
              'rights' => array('list' => array($obj, $obj.'_supprimer'), 'operator' => 'OR'),
              'ordre' => 20,);
}

/**
 *
 */
// Initialisation des variables presentes dans le fichier inclus juste apres
$table = "";
$ico = "";
$ent = "";

// Type d'affichage de la page
//  include ?
$display_accordion = false;
$display_tabs=true;
if( $_SESSION["layout"]=="jquerymobile"){
    $display_accordion = true;
    $display_tabs=false;
}
//

// surcharge globale
if (file_exists('../dyn/form.inc.php')) {
    require_once '../dyn/form.inc.php';
}

// Custom - Inclusion d'un éventuel fichier de paramétrage
if (file_exists('../dyn/custom.inc.php')) {
    require_once '../dyn/custom.inc.php';
}
// Inclusion du fichier de configuration spécifique du tab
// Custom - Surcharge spécifique du fichier .inc.php
if (isset($custom['tab'][$obj]) && file_exists($custom['tab'][$obj])) {
    require_once $custom['tab'][$obj];
} else {
    if (file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php")) {
        require_once "../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
    } else {
        require_once "../sql/".OM_DB_PHPTYPE."/".$obj.".inc";
    }
}
// Inclusion de la classe objet
// Custom - Surcharge spécifique de la classe métier
if (isset($custom['obj'][$obj]) && file_exists($custom['obj'][$obj])) {
    require_once $custom['obj'][$obj];
    $objc = $obj.'_custom';
    $enr = new $objc($idx, $f->db, 0);
} else {
    require_once "../obj/".$obj.".class.php";
    $enr = new $obj($idx, $f->db, 0);
}

// Incrementation du compteur de validation du formulaire
$validation++;
// Enclenchement de la tamporisation de sortie
ob_start();


//
// Affectation des parametres de la vue dans un attribut de l'objet
$parameters = array(
    "aff" => "",
    "validation" => $validation,
    "maj" => $maj,
    "idx" => $idx,
    "premier" => $premier,
    "recherche" => $recherche,
    "tricol" => $tricol,
    "idz" => $idz,
    "selectioncol" => $selectioncol,
    "advs_id" => $advs_id,
    "valide" => $valide,
    "retour" => $retour,
    "actions" => $portlet_actions,
    "postvar" => $_POST,
);
// Affectation du tableau precedant dans l'attribut 'parameters'
$enr->setParameters($parameters);
// Affectation du tableau passe en parametre dans l'attribut 'parameters'
$enr->setParameters($extra_parameters);
//
if ($enr->is_option_class_action_activated()===true) {
    //
    $view_parameter = $enr->get_action_param($maj, 'view');
    //
    if (method_exists($enr, $view_parameter)) {
        $enr->$view_parameter();
    } else {
        $enr->formulaire();
    }
} else {
    $enr->formulaire();
}

// Affecte le contenu courant du tampon de sortie a $return puis l'efface
$return = ob_get_clean();

// Récupère le fil d'Ariane
$ent = $enr->getFormTitle($ent);

/**
 * Affichage de la structure de la page
 */
// Verification des credentials de l'utilisateur
$right_suffix = "_";
switch ($maj) {
    case "0" : $right_suffix .= "ajouter"; break;
    case "1" : $right_suffix .= "modifier"; break;
    case "2" : $right_suffix .= "supprimer"; break;
    case "3" : $right_suffix .= "consulter"; break;
    default :
        if($enr->is_option_class_action_activated()===true) {
            $right_suffix .= $enr->get_action_param($maj, "permission_suffix");
        }
        break;
}
$f->isAuthorized(array($obj.$right_suffix, $obj), "OR");
// Affichage du titre 
$f->setTitle($ent);
$f->setIcon($ico);
$f->setHelp($obj);
//
if ($f->isAjaxRequest()) {
    //
    header("Content-type: text/html; charset=".HTTPCHARSET."");
    // Affichage du retour de la methode formulaire
    echo $return;
    //
    die();
} else {
    // Affichage des elements
    $f->setFlag(NULL);
    $f->display();
}

/**
 *
 */
//
echo "\n<div id=\"formulaire\">\n\n";

// Si formulaire en mode ajout et formulaire valide et enregistrement correct
// alors on recupere $idx pour le passer aux sous formulaires
if ($maj == 0 and $validation>1 and $enr->correct==1 and $idx ==']') {
    $idx = $enr->valF[$enr->clePrimaire];
}

//premier onglet

/**
 * Affichage du titre du tableau dans un onglet ou sous une autre forme selon
 * le layout
 */
//
if (isset($form_title)) {
    //
    $param = $form_title;
} elseif (isset($tab_title)) {
    //
    $param = $tab_title;
} else {
    //
    $param = _($obj);
}
$f->layout->display_form_lien_onglet_un($param);


// Affichage des sous formulaires en onglets
$tabs = array();
if (isset($sousformulaire) and $display_tabs) {
     //

    foreach ($sousformulaire as $elem) {
        //
        if ($f->isAccredited(array($elem, $elem."_tab"), "OR") == false) {
            continue;
        }
        //
        $tabs[] = $elem;
        // ouverture lien onglet 
        echo "\t\t<li>";
        echo "<a id=\"".$elem."\"";
        //
        if (isset($sousformulaire_parameters[$elem]["href"])) {
            echo " href=\"".$sousformulaire_parameters[$elem]["href"]."?retourformulaire=".$obj."&amp;idxformulaire=".$idx."\">";
        } else {
            echo " href=\"../scr/soustab.php?obj=".$elem."&amp;retourformulaire=".$obj."&amp;idxformulaire=".$idx."\">";
        }
        //
        if (isset($sousformulaire_parameters[$elem]["title"])) {
            echo $sousformulaire_parameters[$elem]["title"];
        } else {
            echo _($elem);
        }
       // fermeture lien onglet 
        echo "</a>";
        echo "</li>\n";
       
       
    }
}
if ($display_accordion == false){
    // Affichage de la recherche pour les sous formulaires
    $link = "soustab.php?retourformulaire=".$obj."&amp;idxformulaire=".$idx;
    $param = array("link" => $link);
    $f->layout->display_form_recherche_sousform($param);
}
// Fermeture de la liste des onglets
echo "\t</ul>\n\n";


// Ouverture de la balise - Onglet 1
echo "\t<div id=\"tabs-1\">\n\n";

// Affichage du retour de la methode formulaire
echo "<div id=\"form-message\">";
echo "<!-- -->";
echo "</div>";
echo "<div id=\"form-container\">";
echo $return;
echo "</div>";

// Condition pour la désactivation des onglets dans certains cas de figure
$tab_disabled_condition = false;
if ( 
    // En mode ajout et si le formulaire n'est pas validé
    ($maj == 0 && $enr->correct == false) 
    // En mode modification  et si le formulaire n'est pas validé et si l'option de désactivation en modification est activée
    || ($maj == 1 && $enr->correct == false && isset($option_tab_disabled_on_edit) && $option_tab_disabled_on_edit == true)
    // En mode suppression
    || $maj == 2 
    // Dans tous les autres modes
    || $maj > 3
) {
    $tab_disabled_condition = true;
}

// Javascript pour la desactivation des onglets lorsque nécessaire
if ($tab_disabled_condition) {
    echo "<script type=\"text/javascript\">";
    echo "$(function() {";
    echo "$(\"#formulaire\").tabs(\"option\", \"disabled\", [";
    foreach($tabs as $key => $tab) {
        echo ($key+1);
        if (count($tabs) > $key + 1 ) {
            echo ",";
        }
    }
    echo "]);";
    echo "});";
    echo "</script>";
} elseif(in_array($direct_form,$tabs)) {
    // si le parametre direct_form est dans la liste des sous tab
    echo "<script type=\"text/javascript\">";
    echo "$(function() {";
    if($direct_idx!="") {
        echo "waitUntilExists('sousform-".$direct_form."',function(){
        // si un idx est defini on charge le formulaire de l'objet correspondant
        ajaxIt('".$direct_form."','../scr/sousform.php?obj=".$direct_form.
            "&action=3&idx=".$direct_idx."&retourformulaire=".$obj."&idxformulaire=".$idx."&action=".$direct_action."');
        })('sousform-".$direct_form."');";
    }
    echo "});";

    echo "</script>";
}

// Affichage des sous formulaires en accordeon sous le formulaire

if ($display_accordion) {

    if ($maj == 1 or $maj == 3 or ($maj == 0 and $validation>1 and $enr->correct==1 and $idx ==']')){
         if (isset ($sousformulaire)) {
             echo "<div class=\"visualClear\"><!-- --></div>";
             $f->layout->display_form_start_conteneur_onglets_accordion();
             echo "<h3>";
             // Affichage de la recherche pour les sous formulaires
            $link = "soustab.php?retourformulaire=".$obj."&amp;idxformulaire=".$idx;
            $param = array("link" => $link);
            $f->layout->display_form_recherche_sousform_accordion($param);
            foreach ($sousformulaire as $elem) {
                $f->layout->display_form_start_conteneur_chaque_onglet_accordion();
                
                // A VOIR AND ?????????????????????????????????????????????????
                if (isset($sousformulaire_parameters[$elem]["href"]) and isset($sousformulaire_parameters[$elem]["href"])) {
                    $params = array(
                        "elem" => $elem,
                        "href" => $sousformulaire_parameters[$elem]["href"],
                        "idx" =>$idx,
                        "obj" =>$obj,
                        "title" =>$sousformulaire_parameters[$elem]["title"]
                    );
                } else {
                     $params = array(
                        "elem" => $elem,
                        "idx" =>$idx,
                        "obj" =>$obj
                    );
                }
     
                $f->layout->display_form_lien_onglet_accordion($params);
                echo "<div id=\"sousform-$elem\">";
                //
                echo "</div>";
                $f->layout->display_form_close_conteneur_chaque_onglet_accordion();
                //
            }
            //
            $f->layout->display_form_close_conteneur_onglets_accordion();
            //
        }
    }
}

// Fermeture de la balise - Onglet 1
echo "\n\t</div>\n";

// Fermeture de la balise - Conteneur d'onglets
echo "</div>\n";

?>
