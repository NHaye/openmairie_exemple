<?php
/**
 * Ce script permet de configurer quelles actions vont être disponibles
 * dans le menu.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: menu.inc.php 2733 2014-03-07 11:46:50Z fmichon $
 */

/**
 * $menu est le tableau associatif qui contient tout le menu de
 * l'application, il contient lui meme un tableau par rubrique, puis chaque
 * rubrique contient un tableau par lien
 *
 * Caracteristiques :
 * --- tableau rubrik
 *     - title [obligatoire]
 *     - description (texte qui s'affiche au survol de la rubrique)
 *     - href (contenu du lien href)
 *     - class (classe css qui s'affiche sur la rubrique)
 *     - right [optionnel] (droit que l'utilisateur doit avoir pour visionner
 *                          cette rubrique, si aucun droit n'est mentionne alors
 *                          si aucun lien n'est present dans cette rubrique, on
 *                          ne l'affiche pas)
 *     - links [obligatoire]
 *     - open [optionnel] permet de définir les critères permettant
 *           de conserver la rubrique de menu ouverte.
 *           La définition est une liste de criteres, de type array, contenant des chaines
 *           de type "script|obj" ou "script|" ou "|obj".
 *           S'il y a un unique critere on peut ne pas mettre de array
 *           Si un critere correspond avec l'URL, la rubrique est ouverte.
 *           
 *
 * --- tableau links
 *     - title [obligatoire]
 *     - href [obligatoire] (contenu du lien href)
 *     - class (classe css qui s'affiche sur l'element)
 *     - right (droit que l'utilisateur doit avoir pour visionner cet element)
 *     - target (pour ouvrir le lien dans une nouvelle fenetre)
 *     - open [optionnel] idem à ci-dessus. Les "open" de links sont utilises pour la rubrik :
 *           pas besoin de definir le critere dans rubrik si il est defini dans links
 *           la correspondance rend le lien actif et la rubrique est ouverte
 *           exemples :
 *               open => array("tab.php|users", "form.php|users"),
 *               open => "|users"
 *               open => "script.php|"
 */
//
$menu = array();

// {{{ Rubrique APPLICATION
//
$rubrik = array(
    "title" => _("application"),
    "class" => "application",
);
//
$links = array();
//
// ---> 
//
$rubrik['links'] = $links;
//
$menu[] = $rubrik;
// }}}

// {{{ Rubrique EXPORT
//
$rubrik = array(
    "title" => _("export"),
    "class" => "edition",
);
//
$links = array();
//
$links[] = array(
    "href" => "../scr/edition.php",
    "class" => "edition",
    "title" => _("edition"),
    "right" => "edition",
    "open" => "edition.php|",
);
//
$links[] = array(
    "href" => "../scr/reqmo.php",
    "class" => "reqmo",
    "title" => _("requetes memorisees"),
    "right" => "reqmo",
    "open" => array("reqmo.php|", "requeteur.php|", ),
);
//
$rubrik['links'] = $links;
//
$menu[] = $rubrik;
// }}}

// {{{ Rubrique TRAITEMENT
//
$rubrik = array(
    "title" => _("traitement"),
    "class" => "traitement",
);
//
$links = array();
//
// ---> 
//
$rubrik['links'] = $links;
//
$menu[] = $rubrik;
// }}}

// {{{ Rubrique PARAMETRAGE
//
$rubrik = array(
    "title" => _("parametrage"),
    "class" => "parametrage",
);
//
$links = array();
//
$links[] = array(
    "class" => "category",
    "title" => _("editions"),
    "right" => array(
        "om_etat", "om_etat_tab", "om_sousetat", "om_sousetat_tab",
        "om_lettretype", "om_lettretype_tab", "om_requete", "om_requete_tab",
        "om_logo", "om_logo_tab",
    ),
);
//
$links[] = array(
    "title" => "<hr/>",
    "right" => array(
        "om_etat", "om_etat_tab", "om_lettretype", "om_lettretype_tab",
    ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_etat",
    "class" => "om_etat",
    "title" => _("om_etat"),
    "right" => array("om_etat", "om_etat_tab", ),
    "open" => array("tab.php|om_etat", "form.php|om_etat", ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_lettretype",
    "class" => "om_lettretype",
    "title" => _("om_lettretype"),
    "right" => array("om_lettretype", "om_lettretype_tab"),
    "open" => array("tab.php|om_lettretype", "form.php|om_lettretype", ),
);
//
$links[] = array(
    "title" => "<hr/>",
    "right" => array(
        "om_logo", "om_logo_tab",
    ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_logo",
    "class" => "om_logo",
    "title" => _("om_logo"),
    "right" => array("om_logo", "om_logo_tab", ),
    "open" => array("tab.php|om_logo", "form.php|om_logo", ),
);
//
$links[] = array(
    "title" => "<hr/>",
    "right" => array(
        "om_sousetat", "om_sousetat_tab",
        "om_requete", "om_requete_tab",
    ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_sousetat",
    "class" => "om_sousetat",
    "title" => _("om_sousetat"),
    "right" => array("om_sousetat", "om_sousetat_tab", ),
    "open" => array("tab.php|om_sousetat", "form.php|om_sousetat", ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_requete",
    "class" => "om_requete",
    "title" => _("om_requete"),
    "right" => array("om_requete", "om_requete_tab", ),
    "open" => array("tab.php|om_requete", "form.php|om_requete", ),
);
//
$rubrik['links'] = $links;
//
$menu[] = $rubrik;
// }}}

// {{{ Rubrique ADMINISTRATION
//
$rubrik = array(
    "title" => _("administration"),
    "class" => "administration",
);
//
$links = array();
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_collectivite",
    "class" => "collectivite",
    "title" => _("om_collectivite"),
    "right" => array("om_collectivite", "om_collectivite_tab", ),
    "open" => array("tab.php|om_collectivite", "form.php|om_collectivite", ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_parametre",
    "class" => "parametre",
    "title" => _("om_parametre"),
    "right" => array("om_parametre", "om_parametre_tab", ),
    "open" => array("tab.php|om_parametre", "form.php|om_parametre", ),
);
//
$links[] = array(
    "class" => "category",
    "title" => _("gestion des utilisateurs"),
    "right" => array(
        "om_utilisateur", "om_utilisateur_tab", "om_profil", "om_profil_tab",
        "om_droit", "om_droit_tab", "directory",
    ),
);
//
$links[] = array(
    "title" => "<hr/>",
    "right" => array(
        "om_utilisateur", "om_utilisateur_tab", "om_profil", "om_profil_tab",
        "om_droit", "om_droit_tab",
    ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_profil",
    "class" => "profil",
    "title" => _("om_profil"),
    "right" => array("om_profil", "om_profil_tab", ),
    "open" => array("tab.php|om_profil", "form.php|om_profil", ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_droit",
    "class" => "droit",
    "title" => _("om_droit"),
    "right" => array("om_droit", "om_droit_tab", ),
    "open" => array("tab.php|om_droit", "form.php|om_droit", ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_utilisateur",
    "class" => "utilisateur",
    "title" => _("om_utilisateur"),
    "right" => array("om_utilisateur", "om_utilisateur_tab", ),
    "open" => array("tab.php|om_utilisateur", "form.php|om_utilisateur", ),
);
//
$links[] = array(
    "title" => "<hr/>",
    "right" => array("directory", ),
    "parameters" => array("isDirectoryOptionEnabled" => true, ),
);
//
$links[] = array(
    "href" => "../scr/directory.php",
    "class" => "directory",
    "title" => _("annuaire"),
    "right" => array("directory", ),
    "open" => array("directory.php|", ),
    "parameters" => array("isDirectoryOptionEnabled" => true, ),
);
//
$links[] = array(
    "class" => "category",
    "title" => _("tableaux de bord"),
    "right" => array(
        "om_widget", "om_widget_tab", "om_dashboard",
    ),
);
//
$links[] = array(
    "title" => "<hr/>",
    "right" => array(
        "om_widget", "om_widget_tab", "om_dashboard",
    ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_widget",
    "class" => "om_widget",
    "title" => _("om_widget"),
    "right" => array("om_widget", "om_widget_tab", ),
    "open" => array("tab.php|om_widget", "form.php|om_widget", ),
);
//
$links[] = array(
    "href" => "../scr/dashboard_composer.php",
    "class" => "om_dashboard",
    "title" => _("composition"),
    "right" => array("om_dashboard", ),
    "open" => array("dashboard_composer.php|", ),
);
//
$links[] = array(
    "class" => "category",
    "title" => _("sig"),
    "right" => array(
        "om_sig_map", "om_sig_map_tab", "om_sig_wms", "om_sig_wms_tab",
    ),
    "parameters" => array("option_localisation" => "sig_interne", ),
);
//
$links[] = array(
    "title" => "<hr/>",
    "right" => array(
        "om_sig_map", "om_sig_map_tab", "om_sig_wms", "om_sig_wms_tab",
    ),
    "parameters" => array("option_localisation" => "sig_interne", ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_sig_map",
    "class" => "om_sig_map",
    "title" => _("om_sig_map"),
    "right" => array("om_sig_map", "om_sig_map_tab", ),
    "open" => array("tab.php|om_sig_map", "form.php|om_sig_map", ),
    "parameters" => array("option_localisation" => "sig_interne", ),
);
//
$links[] = array(
    "href" => "../scr/tab.php?obj=om_sig_wms",
    "class" => "om_sig_wms",
    "title" => _("om_sig_wms"),
    "right" => array("om_sig_wms", "om_sig_wms_tab", ),
    "open" => array("tab.php|om_sig_wms", "form.php|om_sig_wms", ),
    "parameters" => array("option_localisation" => "sig_interne", ),
);
//
$links[] = array(
    "class" => "category",
    "title" => _("options avancees"),
    "right" => array("import", "gen", ),
);
//
$links[] = array(
    "title" => "<hr/>",
    "right" => array("import", ),
);
//
$links[] = array(
    "href" => "../scr/import.php",
    "class" => "import",
    "title" => _("import"),
    "right" => array("import", ),
    "open" => array("import.php|", ),
);
//
$links[] = array(
    "title" => "<hr/>",
    "right" => array("gen", ),
);
//
$links[] = array(
    "title" => _("generateur"),
    "href" => "../scr/gen.php",
    "class" => "generator",
    "right" => array("gen", ),
    "open" => array(
        "gen.php|","genauto.php|", "gensup.php|", "genfull.php|",
        "genetat.php|", "gensousetat.php|", "genlettretype.php|",
        "genimport.php|",
    ),
);
//
$rubrik['links'] = $links;
//
$menu[] = $rubrik;
// }}}

?>
