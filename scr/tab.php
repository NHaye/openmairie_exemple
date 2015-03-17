<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: tab.php 3068 2015-02-19 15:50:05Z nhaye $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

/**
 * Initialisation des variables
 */
// Nom de l'objet metier
(isset($_GET['obj']) ? $obj = $f->get_submitted_get_value('obj') : $obj = "");
// Premier enregistrement a afficher
(isset($_GET['premier']) ? $premier = $f->get_submitted_get_value('premier') : $premier = 0);
// Colonne choisie pour le tri
(isset($_GET['tricol']) ? $tricol = $f->get_submitted_get_value('tricol') : $tricol = "");
// Id unique de la recherche avancee
(isset($_GET['advs_id']) ? $advs_id = $f->get_submitted_get_value('advs_id') : $advs_id = "");
// Valilite des objets a afficher
(isset($_GET['valide']) ? $valide = $f->get_submitted_get_value('valide') : $valide = "");
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
$ico = "";
// ???
$hiddenid = 0;

/**
 * Verification des parametres
 */
if (strpos($obj, "/") !== false
    or !(file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php")
         or file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc"))) {
    $class = "error";
    $message = _("L'objet est invalide.");
    $f->addToMessage($class, $message);
    $f->setFlag(NULL);
    $f->display();
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
    array('lien' => 'form.php?obj='.$obj.'&amp;action=0',
          'id' => '&amp;advs_id='.$advs_id.'&amp;tricol='.$tricol.'&amp;valide='.$valide.'&amp;retour=tab',
          'lib' => '<span class="om-icon om-icon-16 om-icon-fix add-16" title="'._('Ajouter').'">'._('Ajouter').'</span>',
          'rights' => array('list' => array($obj, $obj.'_ajouter'), 'operator' => 'OR'),
          'ordre' => 10,);

// Actions a gauche : consulter
$tab_actions['left']['consulter'] =
    array('lien' => 'form.php?obj='.$obj.'&amp;action=3'.'&amp;idx=',
          'id' => '&amp;premier='.$premier.'&amp;advs_id='.$advs_id.'&amp;recherche='.$recherche.'&amp;tricol='.$tricol.'&amp;selectioncol='.$selectioncol.'&amp;valide='.$valide.'&amp;retour=tab',
          'lib' => '<span class="om-icon om-icon-16 om-icon-fix consult-16" title="'._('Consulter').'">'._('Consulter').'</span>',
          'rights' => array('list' => array($obj, $obj.'_consulter'), 'operator' => 'OR'),
          'ordre' => 10,);

// Actions a gauche : modifier

/* decommentez les lignes suivantes pour afficher l'action de modification sur
   tous les tableaux */

//$tab_actions['left']['modifier'] =
//    array('lien' => 'form.php?obj='.$obj.'&amp;action=1'.'&amp;idx=',
//          'id' => '&amp;premier='.$premier.'&amp;advs_id='.$advs_id.'&amp;recherche='.$recherche.'&amp;tricol='.$tricol.'&amp;selectioncol='.$selectioncol.'&amp;valide='.$valide.'&amp;retour=tab',
//          'lib' => '<span class="om-icon om-icon-16 om-icon-fix edit-16" title="'._('Modifier').'">'._('Modifier').'</span>',
//          'rights' => array('list' => array($obj, $obj.'_modifier'), 'operator' => 'OR'),
//          'ordre' => 20,);

// Actions a gauche : supprimer

/* decommentez les lignes suivantes pour afficher l'action de suppression sur
   tous les tableaux */

//$tab_actions['left']['supprimer'] =
//    array('lien' => 'form.php?obj='.$obj.'&amp;action=2&amp;idx=',
//          'id' => '&amp;premier='.$premier.'&amp;advs_id='.$advs_id.'&amp;recherche='.$recherche.'&amp;tricol='.$tricol.'&amp;selectioncol='.$selectioncol.'&amp;valide='.$valide.'&amp;retour=tab',
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
if (file_exists('../dyn/tab.inc.php')) {
    require_once '../dyn/tab.inc.php';
}
if (file_exists('../dyn/custom.inc.php')) {
    require_once '../dyn/custom.inc.php';
}
// *** custom
if(isset($custom['tab'][$obj]) and file_exists($custom['tab'][$obj])){
    require_once $custom['tab'][$obj];
}else{
    // surcharge specifique des objets
    if (file_exists("../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php")) {
       require_once "../sql/".OM_DB_PHPTYPE."/".$obj.".inc.php";
    } else {
       require_once "../sql/".OM_DB_PHPTYPE."/".$obj.".inc";
    }   
}
// ***

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
//
$f->setTitle($ent);
$f->setIcon($ico);
$f->setHelp($obj);
//
$f->setFlag(NULL);
$f->display();

/**
 * Affichage d'une description en dessous du titre de la page
 */
//
if (isset($tab_description)) {
    //
    $f->displayDescription($tab_description);
}

/**
 *
 */
//
echo "<div id=\"formulaire\">\n\n";
//
require_once "../obj/om_table.class.php";

/**
 * Affichage du titre du tableau dans un onglet ou sous une autre forme selon
 * le layout
 */
//
if (isset($tab_title)) {
    //
    $param = $tab_title;
} else {
    //
    $param = _($obj);
}
//
$f->layout->display_tab_lien_onglet_un($param);

//
echo "\n<div id=\"tabs-1\">\n";
//
if (isset($edition) && $edition != ""
    && (file_exists("../sql/".OM_DB_PHPTYPE."/".$edition.".pdf.inc")
        || file_exists("../sql/".OM_DB_PHPTYPE."/".$edition.".pdf.inc.php"))) {
    $edition = "../pdf/pdf.php?obj=".$edition;
} else {
    $edition = "";
}
if (!isset($om_validite) or $om_validite != true) {
    $om_validite = false;
}
//
if (!isset($options)) {
    $options = array();
}
//
echo "<div id=\"tab-".$obj."\">";
//
$tb = new om_table("../scr/tab.php", $table, $serie, $champAffiche, $champRecherche, $tri, $selection, $edition, $options, $advs_id,
                $om_validite);
//
$params = array(
    "obj" => $obj,
    "premier" => $premier,
    "recherche" => $recherche,
    "selectioncol" => $selectioncol,
    "tricol" => $tricol,
    "advs_id" => $advs_id,
    "valide" => $valide,
);
// Ajout de paramètre spécifique
$params = array_merge($params,$extra_parameters);
//
$tb->display($params, $tab_actions, $f->db, "tab", false);
//
echo "</div>\n";
//
echo "\n</div>\n";
//
echo "\n</div>\n";

?>
