<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: sousform.php 3070 2015-02-20 15:09:36Z vpihour $
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
// Premier enregistrement a afficher sur le tableau de la page precedente (soustab.php?premier=)
(isset($_GET['premiersf']) ? $premiersf = $f->get_submitted_get_value('premiersf') : $premiersf = 0);
// Colonne choisie pour le tri sur le tableau de la page precedente (soustab.php?tricol=)
(isset($_GET['trisf']) ? $tricolsf = $f->get_submitted_get_value('trisf') : $tricolsf = "");
// Objet du formulaire parent (form.php?obj=)
(isset($_GET['retourformulaire']) ? $retourformulaire = $f->get_submitted_get_value('retourformulaire') : $retourformulaire = 0);
// Identifiant de l'objet du formulaire parent (form.php?idx=)
(isset($_GET['idxformulaire']) ? $idxformulaire = $f->get_submitted_get_value('idxformulaire') : $idxformulaire = "");
// Origine de l'action
(isset($_GET['retour']) ? $retour = $f->get_submitted_get_value('retour') : $retour = "");
// ???
$typeformulaire = "";

// Ce tableau permet a chaque application de definir des variables
// supplementaires qui seront passees a l'objet metier dans le constructeur
// a travers ce tableau
// Voir le fichier dyn/sousform.get.specific.inc.php pour plus d'informations
$extra_parameters = array();
if (file_exists("../dyn/sousform.get.specific.inc.php")) {
    require "../dyn/sousform.get.specific.inc.php";
}

/**
 * Verification des parametres
 */
if (strpos($obj, "/") !== false
    || !(file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php")
         || file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc"))
    || !file_exists("../obj/".$obj.".class.php")) {
    if ($f->isAjaxRequest() == false) {
        $f->setFlag(NULL);
        $f->display();
    }
    $class = "error";
    $message = _("L'objet est invalide.");
    $f->displayMessage($class, $message);
    die();
}

// Dictionnaire des actions
// ------------------------

// Declaration du dictionnaire
$portlet_actions = array();

if ($maj == 3) {

    // Action : modifier
    $portlet_actions['modifier'] =
        array('lien' => '../scr/sousform.php?obj='.$obj.'&amp;action=1'.'&amp;idx=',
              'id' => '&amp;premiersf='.$premiersf.'&amp;trisf='.$tricolsf.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;retour=form',
              'lib' => '<span class="om-prev-icon om-icon-16 edit-16" title="'._('Modifier').'">'._('Modifier').'</span>',
              'rights' => array('list' => array($obj, $obj.'_modifier'), 'operator' => 'OR'),
              'ordre' => 10,);

    // Action : supprimer
    $portlet_actions['supprimer'] =
        array('lien' => '../scr/sousform.php?obj='.$obj.'&amp;action=2&amp;idx=',
              'id' => '&amp;premiersf='.$premiersf.'&amp;trisf='.$tricolsf.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;retour=form',
              'lib' => '<span class="om-prev-icon om-icon-16 delete-16" title="'._('Supprimer').'">'._('Supprimer').'</span>',
              'rights' => array('list' => array($obj, $obj.'_supprimer'), 'operator' => 'OR'),
              'ordre' => 20,);
}

// surcharge globale
if (file_exists('../dyn/sousform.inc.php')) {
    require_once '../dyn/sousform.inc.php';
}

// Custom - Inclusion d'un éventuel fichier de paramétrage
if (file_exists('../dyn/custom.inc.php')) {
    require_once '../dyn/custom.inc.php';
}
// Inclusion du fichier de configuration spécifique du soustab
// Custom - Surcharge spécifique du fichier .inc.php
if (isset($custom['soustab'][$obj]) && file_exists($custom['soustab'][$obj])) {
    require_once $custom['soustab'][$obj];
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
//
$decodedPost = $_POST;
array_walk_recursive($decodedPost, function(&$value, $key) {
    if (is_string($value)) {
        $value = utf8_decode($value);
    }
});
// Enclenchement de la tamporisation de sortie
ob_start();

// Affectation des parametres dans un tableau associatif pour le
// stocker en attribut de l'objet
$parameters = array(
    "validation" => $validation,
    "maj" => $maj,
    "idx" => $idx,
    "idxformulaire" => $idxformulaire,
    "premiersf" => $premiersf,
    "tricolsf" => $tricolsf,
    "retour" => $retour,
    "retourformulaire" => $retourformulaire,
    "typeformulaire" => $typeformulaire,
    "objsf" => $obj,
    "actions" => $portlet_actions,
    "postvar" => $decodedPost,
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
    if ($view_parameter == "formulaire") {
        $view_parameter = "sousformulaire";
    }
    //
    if (method_exists($enr, $view_parameter)) {
        $enr->$view_parameter();
    } else {
        $enr->sousformulaire();
    }
} else {
    $enr->sousformulaire();
}

// Affecte le contenu courant du tampon de sortie a $return puis l'efface
$return = ob_get_clean();

// Récupère le fil d'Ariane
$ent = $enr->getSubFormTitle($ent);

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
//
if ($f->isAjaxRequest()) {
    //
    header("Content-type: text/html; charset=".HTTPCHARSET."");
    //
    if (isset($_GET["contentonly"])) {
        // Affichage du retour de la methode formulaire
        echo $return;
        //
        die();
    }
    //
    $f->displaySubTitle($ent);
} else {
    // Affichage du titre 
    $f->setTitle($ent);
    // Affichage des elements
    $f->setFlag(NULL);
    $f->display();
}

/**
 *
 */
//
echo "\n<div id=\"sformulaire\">\n";

// Affichage du retour de la methode formulaire
echo "<div id=\"sousform-message\">";
echo "<!-- -->";
echo "</div>";
echo "<div id=\"sousform-container\">";
echo $return;
echo "</div>";

//
echo "</div>";

?>
