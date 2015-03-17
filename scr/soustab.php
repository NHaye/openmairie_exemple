<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: soustab.php 3068 2015-02-19 15:50:05Z nhaye $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

/**
 * Definition du charset de la page
 */
header("Content-type: text/html; charset=".HTTPCHARSET."");

/**
 * Initialisation des variables
 */
// Nom de l'objet metier du tableau
(isset($_GET['obj']) ? $obj = $f->get_submitted_get_value('obj') : $obj = "");
// Premier enregistrement a afficher dans le tableau
(isset($_GET['premier']) ? $premier = $f->get_submitted_get_value('premier') : $premier = 0);
// Colonne choisie pour le tri dans le tableau
(isset($_GET['tricol']) ? $tricol = $f->get_submitted_get_value('tricol') : $tricol = "");
// Colonne choisie pour la recherche dans le tableau
(isset($_GET['selectioncol']) ? $selectioncol = $f->get_submitted_get_value('selectioncol') : $selectioncol = "");
// Objet du formulaire parent (form.php?obj=)
(isset($_GET['retourformulaire']) ? $retourformulaire = $f->get_submitted_get_value('retourformulaire') : $retourformulaire = "");
// Identifiant de l'objet du formulaire parent (form.php?idx=)
(isset($_GET['idxformulaire']) ? $idxformulaire = $f->get_submitted_get_value('idxformulaire') : $idxformulaire = "");
// Chaine recherchee
if (isset($_POST['recherche'])) {
    $recherche = $f->get_submitted_post_value('recherche');
} elseif (isset($_GET['recherche'])) {
    $recherche = $f->get_submitted_get_value('recherche');
} else {
    $recherche = "";
}

// Colonne choisie pour la selection
if (isset($_POST['selectioncol'])) {
    $selectioncol = $f->get_submitted_post_value('selectioncol');
} elseif (isset($_GET['selectioncol'])) {
    $selectioncol = $f->get_submitted_get_value('selectioncol');
} else {
    $selectioncol = "";
}
// ???
$idx = $idxformulaire;

/**
 * Verification des parametres
 */
if (strpos($obj, "/") !== false
    or $idxformulaire == ""
    or $retourformulaire == ""
    or !(file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php")
         or file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc"))) {
    if ($f->isAjaxRequest() == false) {
        $f->setFlag(NULL);
        $f->display();
    }
    $class = "error";
    $message = _("L'objet est invalide.");
    $f->displayMessage($class, $message);
    die();
}

// Liste des options
// -----------------

if (!isset($options)) {
    $options = array();
}

// Dictionnaire des actions
// ------------------------

// Ancien tableau (retro-compatibilite)
$href = array();

// Declaration du dictionnaire
$tab_actions = array('corner' => array(),
                     'left' => array(),
                     'content' => array(),
                     'specific_content' => array(),);

// Actions en coin : ajouter
$tab_actions['corner']['ajouter'] =
    array('lien' => '../scr/sousform.php?obj='.$obj.'&amp;action=0',
          'id' => '&amp;tri='.$tricol.'&amp;objsf='.$obj.'&amp;premiersf='.$premier.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;trisf='.$tricol.'&amp;retour=tab',
          'lib' => '<span class="om-icon om-icon-16 om-icon-fix add-16" title="'._('Ajouter').'">'._('Ajouter').'</span>',
          'rights' => array('list' => array($obj, $obj.'_ajouter'), 'operator' => 'OR'),
          'ordre' => 10,);

// Actions a gauche : consulter
$tab_actions['left']['consulter'] =
    array('lien' => '../scr/sousform.php?obj='.$obj.'&amp;action=3'.'&amp;idx=',
          'id' => '&amp;tri='.$tricol.'&amp;premier='.$premier.'&amp;recherche='.$recherche.'&amp;objsf='.$obj.'&amp;premiersf='.$premier.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;trisf='.$tricol.'&amp;retour=tab',
          'lib' => '<span class="om-icon om-icon-16 om-icon-fix consult-16" title="'._('Consulter').'">'._('Consulter').'</span>',
          'rights' => array('list' => array($obj, $obj.'_consulter'), 'operator' => 'OR'),
          'ordre' => 10,);

// Actions a gauche : modifier

/* decommentez les lignes suivantes pour afficher l'action de modification sur
   tous les tableaux */

//$tab_actions['left']['modifier'] =
//    array('lien' => '../scr/sousform.php?obj='.$obj.'&amp;action=1&amp;idx=',
//          'id' => '&amp;tri='.$tricol.'&amp;premier='.$premier.'&amp;recherche='.$recherche.'&amp;objsf='.$obj.'&amp;premiersf='.$premier.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;trisf='.$tricol.'&amp;retour=tab',
//          'lib' => '<span class="om-icon om-icon-16 om-icon-fix edit-16" title="'._('Modifier').'">'._('Modifier').'</span>',
//          'rights' => array('list' => array($obj, $obj.'_modifier'), 'operator' => 'OR'),
//          'ordre' => 20,);

// Actions a gauche : supprimer

/* decommentez les lignes suivantes pour afficher l'action de suppression sur
   tous les tableaux */

//$tab_actions['left']['supprimer'] =
//    array('lien' => '../scr/sousform.php?obj='.$obj.'&amp;action=2&amp;idx=',
//          'id' => '&amp;tri='.$tricol.'&amp;premier='.$premier.'&amp;recherche='.$recherche.'&amp;objsf='.$obj.'&amp;premiersf='.$premier.'&amp;retourformulaire='.$retourformulaire.'&amp;idxformulaire='.$idxformulaire.'&amp;trisf='.$tricol,
//          'lib' => '<span class="om-icon om-icon-16 om-icon-fix delete-16" title="'._('Supprimer').'">'._('Supprimer').'</span>',
//          'rights' => array('list' => array($obj, $obj.'_supprimer'), 'operator' => 'OR'),
//          'ordre' => 30,);

// Action du contenu : consulter
$tab_actions['content'] = $tab_actions['left']['consulter'];

// Ce tableau permet a chaque application de definir des variables
// supplementaires qui seront passees a l'objet metier dans le constructeur
// a travers ce tableau
// Voir le fichier dyn/form.get.specific.inc.php pour plus d'informations
$extra_parameters = array();

// surcharge globale
if (file_exists('../dyn/soustab.inc.php')) {
    require_once '../dyn/soustab.inc.php';
}
if (file_exists('../dyn/custom.inc.php')) {
    require_once '../dyn/custom.inc.php';
}
// *** custom
if(isset($custom['soustab'][$obj]) and file_exists($custom['soustab'][$obj])){
    require_once $custom['soustab'][$obj];
}else{
    // surcharge specifique des objets
    if(file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php")) {
        require_once "../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
    } else {
        require_once "../sql/".OM_DB_PHPTYPE."/".$obj.".inc";
    }
}
// Surchage du dictionnaire d'actions par l'ancien tableau (retro-compatibilite)
// -----------------------------------------------------------------------------

function retro_overload_action(&$to_overload, $action, $ordre = null) {

    if (isset($action['lien'])) {
        $to_overload['lien'] = $action['lien'];
    }

    if (isset($action['id'])) {
        $to_overload['id'] = $action['id'];
    }

    if (isset($action['lib'])) {
        $to_overload['lib'] = $action['lib'];
    }

    if ($ordre != null) {
        $to_overload['ordre'] = $ordre;
    }
}

// surchage : ajouter
if (isset($href[0])) {
    retro_overload_action($tab_actions['corner']['ajouter'], $href[0]);
    unset($href[0]);
}

// surchage : modifier
if (isset($href[1])) {
    retro_overload_action($tab_actions['left']['modifier'], $href[1]);
    unset($href[1]);
    $tab_actions['content'] = $tab_actions['left']['modifier'];
}

// surchage : supprimer
if (isset($href[2])) {
    retro_overload_action($tab_actions['left']['supprimer'], $href[2]);
    unset($href[2]);
}

// surchage : autres actions
if (!empty($href)) {
    $ordre = 101;

    foreach ($href as $key => $conf) {
        retro_overload_action($tab_actions['left']['retro_'.$key], $conf, $ordre);
        $ordre += 1;
    }
}

/**
 *
 */
//
$f->isAuthorized(array($obj."_tab", $obj), "OR");

/**
 *
 */
//
if (!isset($options)) {
    $options = array();
}

/**
 *
 */
echo "<div id=\"sousform-href\"><!-- --></div>";
//
echo "<div id=\"sousform-".$obj."\">";
//
require_once "../obj/om_table.class.php";
//
$tb = new om_table("../scr/soustab.php", $table, $serie, $champAffiche, $champRecherche, $tri, $selection, $edition, $options);
//
$params = array(
    "obj" => $obj,
    "premier" => $premier,
    "recherche" => $recherche,
    "selectioncol" => $selectioncol,
    "tricol" => $tricol,
    "retourformulaire" => $retourformulaire,
    "idxformulaire" => $idxformulaire,
);
// Ajout de paramètre spécifique
$params = array_merge($params,$extra_parameters);
//
$tb->display($params, $tab_actions, $f->db, "tab", true);
//
echo "</div>";

?>
