<?php
/**
 * Ce fichier contient la déclaration des classes 'layout' et 'layout_base'. 
 *
 * La classe 'layout' est une classe d'abstraction de l'affichage. C'est cette
 * classe qui est instanciée et utilisée par d'autres scripts pour gérer
 * l'affichage de la structure des pages et ce peu importe l'affichage utilisé.
 * Son objectif est d'instancier la classe d'affichage spécifique aussi appelée
 * plugin d'affichage correspondant au paramétrage sélectionné. Cette classe
 * d'affichage spécifique hérite de la classe 'layout_base' qui lui sert de
 * modèle.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_layout.class.php 3065 2015-02-18 13:41:07Z softime $
 */

/**
 *
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));

/**
 *
 */
class layout {

    /**
     *
     */
    public $layout_type = NULL;

    /**
     *
     */
    public $layout = NULL;

    /**
     *
     */
    public $error = NULL;

    /**
     *
     */
    public function __construct($layout_type = NULL) {
        //
        if (is_null($layout_type)) {
            return $this->error(-1);
        }
        //
        if (file_exists("../obj/om_layout_".$layout_type.".class.php")) {
            require_once "../obj/om_layout_".$layout_type.".class.php";
            $layout_class = "om_layout_".$layout_type;
        } elseif (file_exists(PATH_OPENMAIRIE."om_layout_".$layout_type.".class.php")) {
            require_once PATH_OPENMAIRIE."om_layout_".$layout_type.".class.php";
            $layout_class = "layout_".$layout_type;
        } else {
            return $this->error(-2);
        }
        //
        $this->layout = new $layout_class($this);
    }

    /**
     *
     */
    //
    public function display() { $this->layout->display(); }
    //
    public function display_html_header() { $this->layout->display_html_header(); }
    public function display_header() { $this->layout->display_header(); }
    public function display_content_start() { $this->layout->display_content_start(); }
    public function display_content_end() { $this->layout->display_content_end(); }
    public function display_footer() { $this->layout->display_footer(); }
    public function display_footer_tab_sig() { $this->layout->display_footer_tab_sig(); }
    public function display_html_footer() { $this->layout->display_html_footer(); }
    public function display_page_title($page_title = "") { $this->layout->display_page_title($page_title); }
    public function display_page_title_subtext($page_title_subtext = "") { $this->layout->display_page_title_subtext($page_title_subtext); }
    public function display_page_subtitle($page_subtitle = "") { $this->layout->display_page_subtitle($page_subtitle); }
    public function display_page_description($page_description = "") { $this->layout->display_page_description($page_description); }
    public function display_link_js_close_window($js_function_close = "") { $this->layout->display_link_js_close_window($js_function_close); }
    public function display_message($class = "", $message = "") { $this->layout->display_message($class, $message); }
    public function display_messages() { $this->layout->display_messages(); }
    public function display_script_js_call($js = "") { $this->layout->display_script_js_call($js); }
    //
    public function display_icon($icon, $title, $content) { $this->layout->display_icon($icon, $title, $content); }
    public function display_action_login() { $this->layout->display_action_login(); }
    public function display_action_collectivite() { $this->layout->display_action_collectivite(); }
    public function display_action_extras() {$this->layout->display_action_extras(); }
    //
    public function display_table_start($param) { $this->layout->display_table_start($param); }
    public function display_table_start_class_default($param) { $this->layout->display_table_start_class_default($param); }
    public function display_table_pagination($params) { $this->layout->display_table_pagination($params); }
    public function display_table_search_simple($params) { $this->layout->display_table_search_simple($params); }
    public function display_table_global_actions($actions) { $this->layout->display_table_global_actions($actions); }
    public function display_table_lien_data_colonne_une($params) { $this->layout->display_table_lien_data_colonne_une($params); }
    public function display_table_lien_entete_colonne_une($param) { $this->layout->display_table_lien_entete_colonne_une($param); }
    public function display_table_cellule_entete_colonnes($param) { $this->layout->display_table_cellule_entete_colonnes($param); }
    //
    public function display_dbform_lien_retour_sousformulaire($params) { $this->layout->display_dbform_lien_retour_sousformulaire($params); }
    //  
    public function display_formulaire_debutFieldset($params) { $this->layout->display_formulaire_debutFieldset($params); }
    public function display_formulaire_finFieldset($params) { $this->layout->display_formulaire_finFieldset($params); }
    public function display_formulaire_portlet_start($params = array()) { $this->layout->display_formulaire_portlet_start($params); }
    public function display_formulaire_portlet_end($params = array()) { $this->layout->display_formulaire_portlet_end($params); }
    public function display_formulaire_conteneur_libelle_widget($type_champ) { $this->layout->display_formulaire_conteneur_libelle_widget($type_champ); }
    public function display_formulaire_conteneur_libelle_champs() { $this->layout->display_formulaire_conteneur_libelle_champs(); }
    public function display_formulaire_conteneur_champs() { $this->layout->display_formulaire_conteneur_champs(); }
    public function display_formulaire_fin_conteneur_champs() { $this->layout->display_formulaire_fin_conteneur_champs(); }
    public function display_formulaire_css() { $this->layout->display_formulaire_css(); }// a voir
    public function display_formulaire_select_personnalise($params) { $this->layout->display_formulaire_select_personnalise($params); }
    public function display_formulaire_lien_vupload_upload($champ, $obj, $id, $contraintes = null) { $this->layout->display_formulaire_lien_vupload_upload($champ, $obj, $id, $contraintes); }
    public function display_formulaire_lien_voir_upload($champ, $obj, $id) { $this->layout->display_formulaire_lien_voir_upload($champ, $obj, $id); }
    public function display_formulaire_lien_supprimer_upload($champ) { $this->layout->display_formulaire_lien_supprimer_upload($champ); }
    public function display_formulaire_localisation_lien($param) { $this->layout->display_formulaire_localisation_lien($param); }
    public function display_formulaire_text($params) { $this->layout->display_formulaire_text($params); }
    public function display_formulaire_champs_upload($params) { $this->layout->display_formulaire_champs_upload($params); }
    //
    public function display_tab_lien_onglet_un($param) { $this->layout->display_tab_lien_onglet_un($param); }
    //
    public function display_form_button($params) { $this->layout->display_form_button($params); }
    public function display_form_retour($params) { $this->layout->display_form_retour($params); }
    public function display_form_start_conteneur_onglets_accordion() { $this->layout->display_form_start_conteneur_onglets_accordion(); }
    public function display_form_close_conteneur_onglets_accordion() { $this->layout->display_form_close_conteneur_onglets_accordion(); }
    public function display_form_start_conteneur_chaque_onglet_accordion() { $this->layout->display_form_start_conteneur_chaque_onglet_accordion(); }
    public function display_form_close_conteneur_chaque_onglet_accordion() { $this->layout->display_form_close_conteneur_chaque_onglet_accordion(); }
    public function display_form_lien_onglet_accordion($params) { $this->layout->display_form_lien_onglet_accordion($params); }
    public function display_form_lien_onglet_un($param) { $this->layout->display_form_lien_onglet_un($param); }
    public function display_form_recherche_sousform($param) { $this->layout->display_form_recherche_sousform($param); }
    public function display_form_recherche_sousform_accordion($param) { $this->layout->display_form_recherche_sousform_accordion($param); }
    //
    public function display_password_input_submit() { $this->layout->display_password_input_submit(); }
    //commun    
    public function display_stop_fieldset() { $this->layout->display_stop_fieldset(); }
    public function display_stop_legend_fieldset() { $this->layout->display_stop_legend_fieldset(); }
    public function display_start_fieldset($params = array()) { $this->layout->display_start_fieldset($params); }
    //   A VOIR
    public function display_lien($param) { $this->layout->display_lien($param); }
    public function display_lien_retour($param) { $this->layout->display_lien_retour($param); }
    public function display_input($param) { $this->layout->display_input($param); }
    //  
    public function display_start_liste_responsive() { $this->layout->display_start_liste_responsive(); }
    public function display_start_block_liste_responsive($nbr_elements) { $this->layout->display_start_block_liste_responsive($nbr_elements); }
    public function display_start_block_liste_responsive_theme_c($nbr_elements) { $this->layout->display_start_block_liste_responsive_theme_c($nbr_elements); }
    public function display_close_block_liste_responsive() { $this->layout->display_close_block_liste_responsive(); }
    public function display_close_liste_responsive() { $this->layout->display_close_liste_responsive(); }
     //
    public function display_start_navbar() { $this->layout->display_start_navbar(); }
    public function display_stop_navbar() { $this->layout->display_stop_navbar(); }
    public function display_start_conteneur_grille() { $this->layout->display_start_conteneur_grille(); }
    public function display_start_conteneur_block() { $this->layout->display_start_conteneur_block(); }
    public function display_close_conteneur_grille() { $this->layout->display_close_conteneur_grille(); }
    public function display_close_conteneur_block() { $this->layout->display_close_conteneur_block(); }

    /**
     *
     */
    public function display_list($params = array()) { $this->layout->display_list($params); }
    public function display_link($params = array()) { $this->layout->display_link($params); }


    /**
     *      
     */
    private function error($infos = NULL) {
        $this->error = $infos;
        return $infos;
    }

    /**
     *
     */
    public function set_parameter($parameter, $value) { $this->layout->set_parameter($parameter, $value); }
    
}

/**
 *
 */
class layout_base {
    
    /**
     *
     */
    var $layout = "base";

    /**
     *
     */
    protected $html_header_displayed = false;
    protected $header_displayed = false;
    protected $footer_displayed = false;
    protected $html_footer_displayed = false;

    /**
     *
     */
    protected $html_head_css = array();
    protected $html_head_js = array();

    /**
     *
     */
    var $parameters = array();
    function get_parameter($parameter) { return (isset($this->parameters[$parameter]) ? $this->parameters[$parameter] : NULL); }
    function set_parameter($parameter, $value) { $this->parameters[$parameter] = $value; }

    // {{{ STRUCTURE GENERALE DE LA PAGE

    public function display() {
        //
        if ($this->get_parameter("flag") != "nohtml") {
            //
            $this->display_html_header();
            //
            if ($this->get_parameter("flag") != "htmlonly"
                && $this->get_parameter("flag") != "htmlonly_nodoctype") {
                //
                $this->display_header();
            }
            //display_button_page_icon_arrow_r
            $this->display_messages();
        }
    }
    /**
     *
     */
    protected function display_html_header_doctype() {
        //
        echo "<!DOCTYPE html>\n";
    }

    /**
     *
     */
    protected function display_html_header_htmltag() {
        // 
        echo "<html>\n";
    }

    /**
     *
     */
    protected function display_html_header_charset() {
        //
        echo "\t<meta http-equiv=\"Content-Type\" ";
        echo "content=\"text/html;charset=".HTTPCHARSET."\" />\n";
    }

    /**
     *
     */
    protected function display_html_header_extrametas() {
        //
        
    }

    /**
     *
     */
    public function display_html_header() {
        // Si le header HTML n'a pas déjà été affiché
        if ($this->html_header_displayed == false) {
            // Déclaration du doctype
            if ($this->get_parameter("flag") != "nodoctype"
                && $this->get_parameter("flag") != 'htmlonly_nodoctype') {
                //
                $this->display_html_header_doctype();
            }
            // Ouverture de la balise html
            echo $this->display_html_header_htmltag();
            //
            echo "<head>\n";
            // Déclaration du charset
            $this->display_html_header_charset();
            // Affichage du titre
            echo "\t<title>".$this->get_parameter("html_title")."</title>\n";
            // Déclaration des balises méta supplémentaires
            $this->display_html_header_extrametas();
            //
            $html_head_js = $this->get_parameter("html_head_js");
            if ($html_head_js != NULL && isset($html_head_js["set"])) {
                //
                foreach($html_head_js["set"] as $elem) {
                    foreach($elem as $js) {
                        $this->display_script_js_call($js);
                    }
                }
            } else {
                if ($html_head_js != NULL && isset($html_head_js["add"])) {
                    //
                    foreach ($html_head_js["add"] as $order => $value) {
                        //
                        if (!isset($this->html_head_js[$order])) {
                            $this->html_head_js[$order] = array();
                        }
                        //
                        $this->html_head_js[$order] = array_merge($this->html_head_js[$order], $value);
                    }
                }
                //
                foreach($this->html_head_js as $elem) {
                    foreach($elem as $js) {
                        $this->display_script_js_call($js);
                    }
                }
            }
            //
            foreach ($this->html_head_css as $elem) {
                foreach ($elem as $css) {
                    $this->display_script_css_call($css);
                }
            }
            //
            echo $this->get_parameter("html_head_extras");
            //
            echo "</head>\n";
            // Ouverture de la balise body
            if ($this->get_parameter("html_body") == NULL) {
                echo "<body id=\"".$this->layout."\"";
                if ($this->get_parameter("flag") != NULL) {
                    echo " class=\"".$this->get_parameter("flag")."\"";
                }
                echo ">\n\n";
            } else {
                echo $this->get_parameter("html_body");
                echo "\n\n";
            }
            //
            $this->html_header_displayed = true;
        }
    }

    /**
     * Permet d'afficher le header, c'est-à-dire le logo, les actions, les
     * raccourcis, le menu, d'ouvrir la section contenu, d'afficher le titre
     * et l'aide si le header HTML a été préalamblement affiche et que le
     * header ne l'a pas été
     */
    public function display_header() {
        // Si le header n'a pas deja ete affiche et si le header HTML a bien
        // ete affiche alors on affiche le header
        if ($this->header_displayed == false
            and $this->html_header_displayed == true) {
            //
            echo "<!-- ########## START HEADER ########## -->\n";
            //
            echo "<div id=\"header\"";
            echo ">\n";
            // Logo
            $this->display_logo();
            // Actions personnelles
            $this->display_actions();
            // Raccourcis$conf['lib']
            $this->display_shortlinks();
            // Fin du header
            echo "</div>\n";
            echo "<!-- ########## END HEADER ########## -->\n";
            // Menu
            $this->display_menu();
            // Content
            $this->display_content_start();
            // Titre
            $this->display_page_title();
            // Marqueur : le header est affiche
            $this->header_displayed = true;
        }
    }

    /**
     * Cette méthode permet d'afficher le début de la section contenu
     */
    public function display_content_start() {
        //
        echo "<!-- ########## START CONTENT ########## -->\n";
        echo "<div id=\"content\"";
        echo ">\n\n";
    }

    /**
     * Cette méthode permet d'afficher la fin de la section contenu
     */
    public function display_content_end() {
        //
        echo "\n</div>\n";
        echo "<!-- ########## END CONTENT ########## -->\n";
    }

    /**
     * Permet de fermer la section contenu et d'afficher le footer qui contient
     * le nom de l'application, le numero de version et les liens presents
     * dans l'attibut footer si le header a ete prealamblement affiche et que
     * le footer ne l'a pas ete
     */
    //
    public function display_footer() {
        // Si le footer n'a pas deja ete affiche et si le header a bien ete
        // affiche alors on affiche le footer
        if ($this->footer_displayed == false
            and $this->header_displayed == true) {
            // Fin du contenu
            $this->display_content_end();
            // Footer
            echo "<!-- ########## START FOOTER ########## -->\n";
            echo "<div id=\"footer\"";
            echo " class=\"ui-widget";
            // XXX
            if (count($this->get_parameter("menu")) == 0) {
                echo " nomenu";
            }
            echo "\"";
            echo ">\n";
            //
            echo "\t<h5 class=\"hiddenStructure\">"._("Actions globales")."</h5>\n";
            echo "\t<span class=\"ui-corner-all ui-widget-content\">\n";

            // Version de l'application
            echo "\t\t".$this->get_parameter('application');
            if ($this->get_parameter("version") != NULL) {
                echo " "._("Version")." ".$this->get_parameter("version");
            }
            echo "\n";

            //
            foreach ($this->get_parameter("actions_globales") as $link) {

                //
                echo "\t\t&nbsp;|&nbsp;\n";

                //
                $this->display_link($link);

                //
                echo "\n";
            }

            // Fin du footer
            echo "\t</span>\n";
            echo "</div>\n";
            echo "<!-- ########## END FOOTER ########## -->\n";
            // Marqueur : le footer est affiche
            $this->footer_displayed = true;
        }
    }

    /**
     * Permet d'afficher le footer HTML, c'est-à-dire de fermer les balises
     * body et html uniquement si un header html a ete prealamblement affiche
     */
    public function display_html_footer() {
        // Si le footer HTML n'a pas deja ete affiche et si le header HTML a
        // bien ete affiche alors on affiche le footer HTML
        if ($this->html_footer_displayed == false
            and $this->html_header_displayed == true) {
            // Footer HTML
            echo "\n</body>\n";
            echo "</html>";
            // Marqueur : le footer HTML est affiche
            $this->html_footer_displayed = true;
        }
    }

    // }}} STRUCTURE GENERALE DE LA PAGE - END

    function display_script_js_call($js = "") {
        if (file_exists($js) || strpos($js, "http") === 0) {
            // On ajoute le numero de version du logiciel en parametre du fichier
            // pour que le fichier ne soit pas récupéré dans le cache des navigateurs
            // lors d'une mise à jour
            $js .= (strpos($js, "?") === false ? "?" : "&")."v=".urlencode($this->get_parameter("version"));
            //
            echo "\t<script type=\"text/javascript\" src=\"".$js."\"></script>\n";
        }
    }

    function display_script_css_call($css = "", $media = "screen") {
        if (file_exists($css) || strpos($css, "http") === 0) {
            // On ajoute le numero de version du logiciel en parametre du fichier
            // pour que le fichier ne soit pas récupéré dans le cache des navigateurs
            // lors d'une mise à jour
            $css .= (strpos($css, "?") === false ? "?" : "&")."v=".urlencode($this->get_parameter("version"));
            //
            echo "\t<link rel=\"stylesheet\" type=\"text/css\" media=\"".$media."\" href=\"".$css."\" />\n";
        }
    }



    
    //
    public function display_logo() {
        // Logo
        echo "\t<div id=\"logo\">\n";
        //
        echo "\t\t<h1>\n";
        // Lien vers le tableau de bord
        echo "\t\t<a class=\"logo\" ";
        echo "href=\"".$this->get_parameter("url_dashboard")."\" ";
        echo "title=\""._("Tableau de bord")."\">\n";
        //
        echo "\t\t\t<span class=\"logo\">";
        echo $this->get_parameter("application");
        echo "</span>\n";
        //
        echo "\t\t</a>\n";
        //
        echo "\t\t</h1>\n";
        // Fin du logo
        echo "\t</div>\n";
    }


    public function display_action_login() {
        echo "\t\t\t<li class=\"action-login\">";
        echo $_SESSION['login'];
        echo "</li>\n";
    }

    public function display_action_collectivite() {
        echo "\t\t\t<li class=\"action-collectivite\">";
        $collectivite = $this->get_parameter("collectivite");
        if (isset($collectivite["ville"])) {
            echo $collectivite["ville"];
        }
        echo "</li>\n";
    }

    public function display_action_extras() {}
    /**
     *
     * @return void
     */
    public function display_actions() {

        //
        if (count($this->get_parameter("actions_personnelles")) == 0) {
            return;
        }

        //
        echo "\t<div id=\"actions\">\n";
        //
        echo "\t\t<h5 class=\"hiddenStructure\">";
        echo _("Actions personnelles");
        echo "</h5>\n";
        //
        echo "\t\t<ul class=\"actions-list\">\n";
        //
        $this->display_action_login();
        $this->display_action_collectivite();
        $this->display_action_extras();
        //
        foreach ($this->get_parameter("actions_personnelles") as $key => $value) {
            //
            echo "\t\t\t<li class=\"actions-list-elem";
            //
            if (isset($value['class'])) {
                echo " ".$value['class']."";
            }
            //
            if ($key == 0) {
                echo " first";
            }
            //
            if (count($this->get_parameter("actions_personnelles")) == ($key+1)) {
                echo " last";
            }
            echo "\">";
            //
            $this->display_link($value);
            //
            echo "</li>\n";
        }
        //
        echo "\t\t</ul>\n";
        //
        echo "\t</div>\n";
        //
        return;

    }


    /**
     *
     */
    function display_shortlinks() {

        //
        if (count($this->get_parameter("raccourcis")) == 0) {
            return;
        }

        //
        echo "\t<div id=\"shortlinks\">\n";
        //
        echo "\t\t<h5 class=\"hiddenStructure\">";
        echo _("Raccourcis");
        echo "</h5>\n";
        //
        echo "\t\t<ul class=\"shortlinks-list\">\n";
        //
        foreach ($this->get_parameter("raccourcis") as $key => $value) {
            //
            echo "\t\t\t<li class=\"shortlinks-list-elem";
            //
            if (isset($value['class'])) {
                echo " ".$value['class']."";
            }
            //
            if ($key == 0) {
                echo " first";
            }
            //
            if (count($this->get_parameter("raccourcis")) == ($key+1)) {
                echo " last";
            }
            echo "\">";
            //
            $this->display_link($value);
            //
            echo "</li>\n";
        }
        //
        echo "\t\t</ul>\n";
        //
        echo "\t</div>\n";
        //
        return;

    }
  
    /**
     * Affichage du menu
     */
    public function display_menu() {
        //
        if ($this->get_parameter("menu") == NULL) {
            return;
        }

        // Initialisation des variables de menu
        $compteurMenu = 0;
        $menuOpen = null;
        
        echo "<!-- ########## START MENU ########## -->\n";
        echo "<div id=\"menu\">\n";
        echo "<h5 class=\"hiddenStructure\">"._("Menu")."</h5>\n";

        //
        echo "<ul id=\"menu-list\">\n";

        // Boucle sur les rubriques
        foreach ($this->get_parameter("menu") as $m => $rubrik) {

            //
            $cpt_links = 0;
            
            if (isset($rubrik["selected"])) {
                $menuOpen = $m;
            }

            echo "\t<li class=\"rubrik\">\n";
            // Titre de la rubrique
            echo "\t\t<h3";
            if (isset($rubrik['description']) and $rubrik['description'] != "") {
                echo " title=\"".$rubrik['description']."\"";
            }
            echo ">";
            echo "<a href=\"";
            if (isset($rubrik['href']) and $rubrik['href'] != "") {
                echo $rubrik['href'];
            } else {
                echo "#";
            }
            echo "\"";
            if (isset($rubrik['class']) and $rubrik['class'] != "") {
                echo " class=\"".$rubrik['class']."-20\"";
            }
            echo ">";
            echo $rubrik['title'];
            echo "</a>";
            echo "</h3>\n";
            //
            if (count($rubrik['links']) != 0) {
                // Contenu de la rubrique
                echo "\t\t<div class=\"rubrik\">\n";
                echo "\t\t\t<ul class=\"rubrik\">\n";
                // Boucle sur les entrees de menu
                foreach ($rubrik['links'] as $link) {
                    // Entree de menu
                    echo "\t\t\t\t";
                    if (trim($link['title']) != "<hr />"
                        && trim($link['title']) != "<hr/>"
                        && trim($link['title']) != "<hr>") {
                        //
                        echo "<li class=\"elem";
                        if (isset($link["selected"])) {
                            echo " ui-state-focus";
                        }
                        if (isset($link['class'])) {
                            echo " ".$link['class']."";
                        }
                        echo "\">";
                        //
                        if (isset($link["href"])) {
                            echo "<a";
                            if (isset($link['class']) and $link['class'] != "") {
                                echo " class=\"".$link['class']."-16\"";
                            }
                            echo " href=\"";
                            if (isset($link['href']) and $link['href'] != "") {
                                echo $link['href'];
                            } else {
                                echo "#";
                            }
                            echo "\"";
                            if (isset($link['target']) and $link['target'] != "") {
                                echo " target=\"".$link['target']."\"";
                            }
                            echo ">";
                        }
                        //
                        echo $link['title'];
                        //
                        if (isset($link["href"])) {
                            echo "</a>";
                        }
                        echo "</li>";
                    } else {
                        echo "<li class=\"elem hr\"><!-- --></li>";
                    }
                    echo "\n";
                }
                // Fin de la rubrique
                echo "\t\t\t</ul>\n";
                echo "\t\t</div>\n";

            }
            // Fermeture de le rubrique
            echo "\t</li>\n";
        }
        // Fin du menu
        echo "</ul>\n";
        echo "</div>\n";
        // Positionnement d'une variable recuperee en javascript pour ouvrir la rubrique active
        echo "<span id='menuopen_val'>$menuOpen</span>";
        //
        echo "<!-- ########## END MENU ########## -->\n";
    }

    /**
     *
     */
    function display_page_title($page_title = "") {
        //
        if ($page_title == "") {
            $page_title = $this->get_parameter("page_title");
        }
        //
        if (!is_null($page_title) && $page_title != "") {
            //
            echo "<div id=\"title\" class=\"";
            echo $this->get_parameter("style_title");
            echo "\">\n";
            echo "<h2>\n";
            // Remplacement les caracteres -> par une image de fleche
            $ent = str_replace("->", " <span class=\"om-icon om-icon-16 om-icon-fix arrow-right-16\">></span> ", $page_title);
            $ent = str_replace("&nbsp;", " ", $ent);
            // Afichage du titre
            echo "\t".$ent."\n";
            // Fin du title
            echo "</h2>\n";
            echo "</div>\n\n";
        }
    }

    /**
     *
     */
    function display_page_title_subtext($page_title_subtext = "") { 
        //
        if ($page_title_subtext == "") {
            $page_title_subtext = $this->get_parameter("page_title_subtext");
        }
        //
        if (!is_null($page_title_subtext) && $page_title_subtext != "") {
            //
            echo "<div id=\"title_subtext\">\n";
            echo "<h2>\n";
            echo "<small>\n";
            // Afichage du titre
            echo "\t".$page_title_subtext."\n";
            // Fin du title
            echo "</small>\n";
            echo "</h2>\n";
            echo "</div>\n\n";
        }
    }

    /**
     *
     */
    function display_page_description($page_description = "") {
        //
        if ($page_description == "") {
            $page_description = $this->get_parameter("page_description");
        }
        //
        if (!is_null($page_description) && $page_description != "") {
            //
            echo "<div class=\"pageDescription\">\n";
            echo "\t<p>\n";
            echo "\t\t".$page_description."\n";
            echo "\t</p>\n";
            echo "</div>\n";
        }
    }

    function display_page_subtitle($page_subtitle = "") {
        //
        if ($page_subtitle == "") {
            $page_subtitle = $this->get_parameter("page_subtitle");
        }
        //
        if (!is_null($page_subtitle) && $page_subtitle != "") {
            // Title
            echo "<div class=\"subtitle\">\n";
            echo "<h3>\n";
            // Remplacement les caracteres -> par une image de fleche
            $ent = str_replace("->", " <span class=\"om-icon om-icon-16 om-icon-fix arrow-right-16\">></span> ", $page_subtitle);
            $ent = str_replace("&nbsp;", " ", $ent);
            // Afichage du titre
            echo "\t".$ent."\n";
            // Fin du title
            echo "</h3>\n";
            echo "</div>\n\n";
        }
    }

    function display_link_js_close_window($js_function_close = "") {
        //
        if ($js_function_close == "") {
            $js_function_close = "window.close();";
        }
        //
        echo "\n<p class=\"linkjsclosewindow\">";
        echo "<a class=\"linkjsclosewindow\" href=\"#\" ";
        echo "onclick=\"".$js_function_close."\">";
        echo _("Fermer");
        echo "</a>";
        echo "</p>\n";
    }

    /**
     *
     * @return void
     */
    public function display_message($class = "", $message = "") {

        //
        if ($class == "ok") {
            $class = "valid";
        }
        //
        echo "\n<div class=\"message ui-widget ui-corner-all ui-state-highlight ui-state-".$class."\">\n";
        echo "<p>\n";
        echo "\t<span class=\"ui-icon ui-icon-info\"><!-- --></span> \n\t";
        echo "<span class=\"text\">";
        echo $message;
        echo "</span>";
        echo "\n</p>\n";
        echo "</div>\n";

    }

    /**
     *
     * @return void
     */
    function display_messages() {
        foreach ($this->get_parameter("messages") as $message) {
            $this->display_message($message['class'], $message['message']);
        }
        $this->message = array();
    }

    public function display_table_start_class_default($param) {
        // Affichage de la table
        echo "<!-- tab-tab -->\n";
        echo "<table class=\"tab-tab\">\n";
    }
    public function display_table_start($param) {
        $class=$param['class'];
        // Affichage de la table
        echo "<!-- tab-tab -->\n";
        echo "<table class=\"".$class."-tab\">\n";
    }

    public function display_icon($icon, $title, $content) {
        echo "<span class=\"ui-icon ui-icon-".$icon."\"";
        echo " title=\"".$title."\">";
        echo $content;
        echo "</span>";
    }

    // {{{

    /**
     * Cette méthode permet d'afficher le système de pagination d'un tableau.
     * Les paramètres attendus sont :
     * $params = array(
     *     "obj" => ,
     *     "style" => , 
     *     "first" => ,
     *     "last" => ,
     *     "total" => ,
     *     "search" => ,
     *     "previous" => ,
     *     "next" => ,
     * );
     */
    public function display_table_pagination($params) {
        //
        echo "<div class=\"".$params["style"]."-pagination ui-state-default ui-corner-top ui-tabs-selected ui-state-active\">\n";

        echo "<div class=\"pagination-nb\">";

        // Si il y a une page precedente
        if ($params["previous"] != "") {
            // Affichage du lien vers la page precedente
            echo " <a href=\"";
            //
            if ($params["onglet"]) {
                echo "#";
                echo "\" ";
                echo "onclick=\"ajaxIt('".$params["obj"]."','";
            }
            //
            echo $params["previous"];
            //
            if ($params["onglet"]) {
                echo "');";
            }
            echo "\" ";
            echo "title=\""._("Page precedente")."\" ";
            echo "class=\"pagination-prev\"";
            echo ">";
            // Affichage de l'image representant la page precedente
            echo "<span class=\"ui-icon ui-icon-circle-triangle-w\">"._("Page precedente")."</span>";
            // Fermeture de la balise lien
            echo "</a> ";
        }

        // Affichage du conteneur de message de pagination
        echo "<span class=\"pagination-text\">";
        // Construction du message de pagination
        echo $params["first"]." - ".$params["last"]." ";
        echo _("enregistrement(s) sur")." ".$params["total"];
        // Affichage du message de pagination
        if ($params["search"] != "") {
            echo " = [".$params["search"]."] ";
        }
        // Fermeture de la balise span
        echo "</span>";

        // Si il y a une page suivante
        if ($params["next"] != "") {
            // Affichage du lien vers la page suivante
            echo " <a href=\"";
            //
            if ($params["onglet"]) {
                echo "#";
                echo "\" ";
                echo "onclick=\"ajaxIt('".$params["obj"]."','";
            }
            //
            echo $params["next"];
            //
            if ($params["onglet"]) {
                echo "');";
            }
            echo "\" ";
            echo "title=\""._("Page suivante")."\" ";
            echo "class=\"pagination-next\"";
            echo ">";
            // Affichage de l'image representant la page suivante
            echo "<span class=\"ui-icon ui-icon-circle-triangle-e\">"._("Page suivante")."</span>";
            // Fermeture de la balise lien
            echo "</a> ";
        }
        //
        echo "</div>";

        //
        if ($params["pagination_select"]["active"] == true) {
            //
            echo "<div class=\"pagination-select\">";
            // Affichage du formulaire
            echo "<form action=\"\">\n";
            // Affichage du mot Page
            echo "&nbsp;"._('Page')."&nbsp;";
            // Affichage de la liste de choix
            echo "<select name=\"page\" size='1' ";
            if ($params["onglet"]) {
                echo "onchange=\"ajaxIt('".$params["obj"]."', '";
                echo $params["pagination_select"]["link"];
                echo "');";
                echo "\"";
            } else {
                echo "onchange=\"allerpage('";
                echo $params["pagination_select"]["link"];
                echo "');\"";
            }
            echo " class='champFormulaire' >";
            // Boucle sur le nombre de page pour l'affichage de chaque item de
            // la liste
            for ($i = 1; $i <= $params["pagination_select"]["page_number"]; $i++) {
                // Affichage de l'item selectionne sur la page en cours
                if (($i - 1) * $params["pagination_select"]["serie"] == $params["pagination_select"]["premier"]) {
                    echo "<option value=\"".($i - 1) * $params["pagination_select"]["serie"]."\" selected=\"selected\">";
                    echo $i." / ".$params["pagination_select"]["page_number"];
                    echo "</option>";
                } else {
                    echo "<option value=\"".($i - 1) * $params["pagination_select"]["serie"]."\">";
                    echo $i." / ".$params["pagination_select"]["page_number"];
                    echo "</option>";
                }
            }
            // Fermeture de la balise liste de choix
            echo "</select>";
            // Fonction javascript allerpage()
            echo "<script type=\"text/javascript\">";
            echo "function allerpage(link) { ";
            echo "location=link.replace(/&amp;/g, \"&\")";
            echo "} ";
            echo "</script>";

            // Fermeture de la balise formulaire
            echo "</form>\n";

            //
            echo "</div>";
        }

        //
        echo "\t<div class=\"visualClear\"><!-- --></div>\n";
        echo "</div>\n";
    }

    //
    public function display_table_search_simple($params) {
        //
        echo "<!-- tab-search -->\n";
        // Affichage de la table
        echo "<div class=\"".$params["style"]."-search ui-widget-content ui-corner-all\">\n";
        // Affichage du formulaire
        echo "\t<form action=\"";
        echo $params["form_action"];
        echo "\" method=\"post\" id=\"f1\" name=\"f1\">\n";
        // Affichage du champ permettant la saisie du terme a rechercher
        echo "\t\t<input type=\"text\" name=\"recherche\" ";
        echo "value=\"".$params["search"]."\" ";
        echo "class=\"champFormulaire\" />\n";
        // Affichage du champ select permettant de choisir le champ sur lequel
        // doit agir la recherche
        echo "\t\t<select name=\"selectioncol\" class=\"champFormulaire\">\n";
        if ($params["column_search_selected_key"] == "") {
            echo "\t\t\t<option value=\"\" selected=\"selected\">"._("Tous")."</option>\n";
            foreach($params["column_search"] as $key => $elem) {
                echo "\t\t\t<option value=\"".$key."\">".$elem."</option>\n";
            }
        } else {
            echo "\t\t\t<option value=\"\">"._("Tous")."</option>\n";
            foreach($params["column_search"] as $key => $elem) {
                if($params["column_search_selected_key"] == $key) {
                    echo "\t\t\t<option value=\"".$key."\" selected=\"selected\">".$elem."</option>\n";
                } else {
                    echo "\t\t\t<option value=\"".$key."\">".$elem."</option>\n";
                }
            }
        }
        echo "\t\t</select>\n";
        // Affichage du bouton de soumission du formulaire
        echo "\t\t<button type=\"submit\" name=\"s1\">";
        echo _("Recherche");
        echo "</button>\n";
        // Fermeture de la balise formulaire
        echo "\t</form>\n";
        // Fermeture de la balise table
        echo "</div>\n";
    }


    public function display_table_global_actions($actions) {
        //
        foreach($actions as $params) {
            //
            if ($params["type"] == "om_validite") {
                
                echo "<span class=\"om_validite_link\">";
                echo "<a href=\"".$params["link"]."\">".$params["title"]."</a>";
                echo "</span>";
                
            } elseif ($params["type"] == "edition") {
                
                echo "<!-- tab-edition -->\n";
                // Ouverture du conteneur
                echo "<div class=\"".$params["style"]."-edition\">\n";
                // Affichage du lien vers le fichier php d'edition pdf
                echo "<a href=\"".$params["link"]."\" ";
                echo "title=\"".$params["title"]."\" ";
                echo "target='_blank'>";
                // Affichage de l'image representant l'edition
                echo "<span class=\"om-icon om-icon-25 print-25\">"._("Edition")."</span>";
                // Fermeture de la balise lien
                echo "</a>";
    
                // Fermeture du conteneur
                echo "</div>\n";
                
            } elseif ($params["type"] == "export") {
                echo "<!-- tab-edition -->\n";
                // Ouverture du conteneur
                echo "<div class=\"".$params["style"]."-export\">\n";
                // Affichage du lien vers le fichier php d'edition pdf
                echo "<a href=\"".$params["link"]."\" ";
                echo "title=\"".$params["title"]."\" ";
                echo "target='_blank'>";
                // Affichage de l'image representant l'edition
                echo "<span class=\"om-icon om-icon-25 ".$params['class']."-25\">"._("Edition")."</span>";
                // Fermeture de la balise lien
                echo "</a>";
    
                // Fermeture du conteneur
                echo "</div>\n";
            }
        }
    }
    // }}}

    // {{{ FORM

    /**
     * Cette méthode permet d'afficher le bouton de validation du formulaire
     */
    public function display_form_button($params) {
        //
        echo "<input";
        echo " type=\"submit\"";
        echo " value=\"".$params["value"]."\" ";
        if (isset($params["name"]) && $params["name"] != "") {
            echo " name=\"".$params["name"]."\"";
        }
        if (isset($params["onclick"]) && $params["onclick"] != "") {
            echo " onclick=\"".$params["onclick"]."\"";
        }
        echo " class=\"om-button\"";
        echo " />";
    }

    /**
     * Cette méthode permet d'afficher un lien retour
     */
    public function display_form_retour($params) {
        //
        echo "\n";
        //
        echo "<a";
        // Attribut class
        echo " class=\"retour";
        if (isset($params["class"]) && $params["class"] != "") {
            echo " ".$params["class"];
        }
        echo "\"";
        // Attribut href
        echo " href=";
        echo "\"";
        if (isset($params["href"]) && $params["href"] != "") {
            echo $params["href"];
        }
        echo "\"";
        //
        echo ">";
        //
        echo _("Retour");
        //
        echo "</a>";
        //
        echo "\n";
    }

    /**
     * Cette methode permet d'ouvrir un fieldset
    */
    public function display_formulaire_debutFieldset($params) {
        // Ouverture du fieldset
        echo "      <fieldset";
        echo (isset($params["identifier"]) ? " id=\"".$params["identifier"]."\"" : "");
        echo " class=\"cadre ui-corner-all ui-widget-content ".$params["action2"]."\">\n";
        echo "        <legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
        echo $params["action1"];
        echo "        </legend>\n";
        // Ouverture du conteneur interne du fieldset
        echo "        <div class=\"fieldsetContent\">\n";
    }
    /**
     * Cette methode permet de fermer un fieldset
    */
    public function display_formulaire_finFieldset($params) {
        // Fermeture du fieldset
        echo "          <div class=\"visualClear\"><!-- --></div>\n";
        echo "        </div>\n";
        echo "      </fieldset>\n";
    }
    public function display_formulaire_portlet_start($params = array()) {
        // affichage du portlet d'actions contextuelles
        echo "<div id=\"portlet-actions\" class=\"ui-widget-content ui-corner-all ui-state-default\">";
        echo "<ul class=\"portlet-list\">";
    }
    public function display_formulaire_portlet_end($params = array()) {
        // fermeture du portlet d'actions contextuelles
        echo "</ul>";
        echo "</div>";
    }
    public function display_formulaire_conteneur_libelle_widget($type_champ) {
        //ouverture div contenant  libelle champs
         echo "      <div class=\"field field-type-".$type_champ."\">\n";
    }
    
    public function display_formulaire_conteneur_libelle_champs() {
        //ouverture conteneur libelle champs
         echo "        <div class=\"form-libelle\">\n";
    }
    public function display_formulaire_conteneur_champs() {
        //ouverture conteneur libelle champs
         echo "        <div class=\"form-content\">\n";
    }
    public function display_formulaire_fin_conteneur_champs() {
        //fin conteneur libelle champs et champs
        echo "        </div>\n";
    }
    public function display_tab_lien_onglet_un($param) {
        // premier onglet
        echo "<ul>\n";
        echo "\t<li><a href=\"#tabs-1\">".$param."</a></li>\n";
        echo "</ul>\n";
    }
    public function display_form_lien_onglet_un($param) {
        // Ouverture de la liste des onglets
        echo "\t<ul>\n";
        // Affichage du premier onglet
        echo "\t\t<li><a id=\"main\" href=\"#tabs-1\">".$param."</a></li>\n";
    }
    
    
    public function display_form_start_conteneur_onglets_accordion() {
        // ouverture conteneur onglets sous formulaire 
        // affichage en accordeon sous le formulaire
        //echo "<div id=\"accordion\" >";
    
    }
    public function display_form_close_conteneur_onglets_accordion() {
        // fermeture conteneur onglets sous formulaire 
        // affichage en accordeon sous le formulaire
        //echo  "</div>";
        
    }
    public function display_form_lien_onglet_accordion($params) {
        //  
        //  lien onglets accordion
        //
        
        //
        echo "\t<legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
        echo "<a ";
         if (isset($params["href"])) {
             echo " onclick=\"ajaxIt('".$params["elem"]."', '".$params["href"]."');\"";
         } else {
             echo " onclick=\"ajaxIt('".$params["elem"]."', 'soustab.php?obj=".$params["elem"]."&retourformulaire=".$params["obj"]."&idxformulaire=".$params["idx"]."');\"";
         }
         echo " href=\"#\">";
         //
         if (isset($params["title"])) {
             echo $params["title"];
         } else {
             echo _($params["elem"]);
         }
        // 
        echo "</a>";
        echo "\t</legend>";
        
    //
    }
    public function display_form_start_conteneur_chaque_onglet_accordion() {
        //  
        //  conteneur de chaque onglet  accordion
        //
         echo "<div>\n";
         echo "<fieldset class=\"cadre ui-corner-all ui-widget-content collapsed\">\n";
        //
    }
    public function display_form_close_conteneur_chaque_onglet_accordion() {
        //  
        //  conteneur de chaque onglet  accordion
        //
        echo "</fieldset>";
        echo  "</div>";
         
        // 
    }
    public function display_form_recherche_sousform($param) {
    //  Affichage de la recherche pour les sous formulaires 
        echo "\t\t<li>\n";
        echo "\t\t\t<span  id=\"recherche_onglet\" style=\"display:none;\">\n";
        echo "\t\t\t\t";
        echo "<input  type=\"text\" name=\"recherchedyn\" id=\"recherchedyn\" ";
        echo "value=\"\" class=\"champFormulaire\" ";
        echo "onkeyup=\"recherche('".$param["link"]."');\" />";
        echo "\n";
        echo "\t\t\t</span>\n";
        echo "\t\t</li>\n";
    }
    public function display_form_recherche_sousform_accordion($param) {
    //  Affichage de la recherche pour les sous formulaires 
        echo "\t\t<li>\n";
        echo "\t\t\t<span  id=\"recherche_onglet\" style=\"display:none;\">\n";
        echo "\t\t\t\t";
        echo "<input  type=\"text\" name=\"recherchedyn\" id=\"recherchedyn\" ";
        echo "value=\"\" class=\"champFormulaire\" ";
        echo "onkeyup=\"recherche('".$param["link"]."');\" />";
        echo "\n";
        echo "\t\t\t</span>\n";
        echo "\t\t</li>\n";
    }
    public function display_formulaire_css() {
        //  
        //   class formulaire
        // 
        echo " class=\"'champFormulaire\" \n";
    }
        
    public function display_formulaire_lien_vupload_upload($champ, $obj = "", $id = "", $contraintes = null) {
        //
        //   Gestion des contraintes   
        //
        if ( isset($contraintes) && is_array($contraintes) && 
            isset($contraintes['constraint']) && is_array($contraintes['constraint'])){
            
            $contraintes = (isset($contraintes['constraint']['size_max']) && 
                is_numeric($contraintes['constraint']['size_max']))? 
                "'".$contraintes['constraint']['size_max']."',": "'',";
            $contraintes .= (isset($contraintes['constraint']['extension']) && 
                is_string($contraintes['constraint']['extension']))? 
                "'".$contraintes['constraint']['extension']."'": "''";
        }
        else {
            
            $contraintes = "'', ''";
        }
        
        //  
        //  fonction upload -> lien  vupload
        // 
        echo "<a class=\"upload ui-state-default ui-corner-all\" href=\"javascript:vupload('".$champ."', '".$obj."', '".$id."', ".$contraintes.");\">";
        echo "<span class=\"ui-icon ui-icon-arrowthickstop-1-s\" ";
        echo "title=\""._("Cliquer ici pour telecharger un fichier depuis votre poste de travail")."\">";
        echo _("Telecharger");
        echo "</span>";
        echo "</a>\n";
        //
    }
    public function display_formulaire_lien_voir_upload($champ, $obj = "" , $id = "") {
        //  
        //  fonction upload -> -> lien  voir
        // 
        echo "<a class=\"voir ui-state-default ui-corner-all\" href=\"javascript:voir('".$champ."', '".$obj."', '".$id."');\">\n";
        echo "<span class=\"ui-icon ui-icon-newwin\" ";
        echo "title=\""._("Cliquer ici pour voir le fichier")."\">";
        echo _("Voir");
        echo "</span>";
        echo "</a>\n";
        //
    }

    /**
     * Function permettant de vider le champ de formulaire
     * @param  string $champ champ surlequel ajouter le bouton supprimer
     */
    public function display_formulaire_lien_supprimer_upload($champ) {
        //  
        //  fonction upload -> -> lien  voir
        // 
        echo "<a class=\"voir ui-state-default ui-corner-all\" href=\"javascript:supprimerUpload('".$champ."');\">\n";
        echo "<span class=\"ui-icon ui-icon-closethick\" ";
        echo "title=\""._("Cliquer ici pour supprimer le fichier")."\">";
        echo _("Voir");
        echo "</span>";
        echo "</a>\n";
        //
    }
    public function display_formulaire_localisation_lien($params) {
        //  
        //  localisation 
        //
        echo "<a ";
        //
        echo " class=\"localisation ui-state-default ui-corner-all\" href=\"javascript:localisation('".$params["champ"]."','".$params["plan"]."','".$params["positionx"]."');\">";
        //
        echo "<span class=\"ui-icon ui-icon-pin-s\" ";
        echo "title=\""._("Cliquer ici pour positionner l'element")."\">";
        echo _("Localisation");
        echo "</span>";
        echo "</a>";
        //
    }
    public function display_password_input_submit() {
        //  
        //   bouton  valider password
        //
         echo "<input ";
         echo " type=\"submit\" name=\"submit-change-password\" value=\""._("Valider")."\" class=\"boutonFormulaire\" />";
        //
    }
    public function display_start_fieldset($params = array()) {
        // Rétro-compatibilité
        if (count($params) == 0) {
            //
            echo "<fieldset class=\"cadre ui-corner-all ui-widget-content collapsible\">\n";
            //
            echo "\t<legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
            //
            return;
        }
        //
        echo "<fieldset";
        if (isset($params["fieldset_class"])) {
            echo " class=\"".$params["fieldset_class"]."\"";
        }
        echo ">";
        echo "<legend";
        if (isset($params["legend_class"])) {
            echo " class=\"".$params["legend_class"]."\"";
        }
        echo ">";
        if (isset($params["legend_content"])) {
            echo $params["legend_content"];
        } else {
            echo "...";
        }
        echo "</legend>";
    }
    public function display_stop_fieldset() {
        //  
        //   fiedset 
        //
        echo "</fieldset>\n";
        //
    }
    public function display_stop_legend_fieldset() {
        //  
        //   fiedset 
        //
        echo "</legend>\n";
        //
    }

    /**
     * Affiche un lien.
     *
     * @param $param mixed Tableau de paramètres.
     */
    public function display_lien($params) {
        // XXX Rétro compatibilité
        if (isset($params["lien"])) {
            echo $params["lien"];
            return;
        }
        //
        $this->display_link($params);
    }



    /**
     * Affiche le lien retour présent sur beaucoup de pages de l'applicatif.
     *
     * @param $param mixed Tableau de paramètres.
     */
    public function display_lien_retour($params) {
        // Rétro compatibilité
        if (isset($params["lien"])) {
            echo $params["lien"];
            return;
        }
        //
        $this->display_link(
            array(
                "href" => $params["href"],
                "title" => _("Retour"),
                "class" => "retour",
            )
        );
    }
    public function display_input($param) {
        //  
        //   lien 
        //
        echo $param['input'];
        //
    }
    public function display_start_liste_responsive() {
        //  
        //   liste responsive mobile    - general
        // 
        //
    }
    public function display_start_block_liste_responsive($nbr_elements) {
        //  
        //   block liste responsive mobile  - general
        //
        echo "<div class=\"choice ui-corner-all ui-widget-content\">\n";
        //
    }
    public function display_start_block_liste_responsive_theme_c($nbr_elements) {
        //  
        //   block liste responsive mobile  - general
        //
        echo "<div class=\"choice ui-corner-all ui-widget-content\">\n";
        //
    }
    public function display_close_block_liste_responsive() {
        //  
        //   close block liste responsive mobile    - general
        //
        //
        echo "</div>\n";
        //
    }
    public function display_close_liste_responsive() {
        //  
        //   close liste responsive mobile  - general
        // 
        //
    }
    public function display_start_navbar() {
        //  
        //   barre navigation -  mobile
        // 
        //
    }
    public function display_stop_navbar() {
        //  
        //   barre navigation -  mobile
        // 
        //
    }
    public function display_start_conteneur_grille() {
        //  
        //   grille de mise en page(colonne) -  mobile
        // 
        //
    }
    public function display_start_conteneur_block() {
        //  
        //   grille de mise en page(block) -  mobile
        // 
        //
    }
    public function display_close_conteneur_block() {
        //  
        //   grille de mise en page(block) -  mobile
        // 
        //
    }
    public function display_close_conteneur_grille() {
        //  
        //   grille de mise en page(colonne) -  mobile
        // 
        //
    }
    
    public function display_formulaire_select_personnalise($params) {
        //  
        //  select 
        //  
        echo "<select ";
         echo " name='".$params["champ"]."' ";
        echo " id=\"".$params["champ"]."\" ";
        echo " size='1' ";
        echo " class=\"'champFormulaire\" \n";
        echo " >\n";
       // 
        //
    }
    public function display_table_lien_data_colonne_une($params) {
        //  
        //   tableau :  lien colonne une -> consulter,.....
        //
        echo "<a ";
        if ($params["onglet"] == false or $params["no_ajax"] == true) {
    
            echo "href=\"".$params["lien"].urlencode($params["row"]).
                 (isset($params["id"]) ? $params["id"] : "")."\"";
            echo " id=\"".$params["identifier"]."\"";
            // Gestion de l'attribut target
            if (isset($params["target"])
                and $params["target"] == '_blank') {
                echo " target=\"_blank\" ";
            }
    
        // En visualisation par onglet ..
        } else {
    
            // Gestion de l'attribut target
            if (isset($params["target"])
                and $params["target"] == "_blank") {
    
                echo "href=\"".$params["lien"].
                     urlencode($params["row"]).$params["id"]."\"";
                echo " id=\"".$params["identifier"]."\"";
                echo " target=\"_blank\" ";
    
            // Sans target, rechargement du bloc en ajax
            } else {
                echo "href=\"";
                echo "#";
                echo "\" ";
                echo " id=\"".$params["identifier"]."\"";
                echo " onclick=\"ajaxIt('";
                echo $params["obj"]."','";
                echo $params["lien"].urlencode($params["row"]);
                echo $params["id"];
                echo "');\"";
            }
        }
    
        echo ">";
        echo $params["lib"];
        echo "</a>";
        echo "&nbsp;";
        //
    }
    public function display_table_lien_entete_colonne_une($param) {
        //  
        //   tableau :  entete -> lien colonne une ->creation...
        //
        echo "<a href=\"";
        if ($param["onglet"]) {
            echo "#";
            echo "\" ";
            echo " onclick=\"ajaxIt('".$param["obj"]."','";
            echo $param["lien"].$param["id"];
            echo "');";
        } else {
            echo $param["lien"].$param["id"];
        }

        echo "\"";
        echo " id=\"".$param["identifier"]."\"";
        echo ">";
        echo $param["lib"];
        echo "</a>";
        //
    }
    public function display_table_cellule_entete_colonnes($param) {
        //  
        //   tableau :  cellule entete colonnes...
        //
        echo "\t\t\t<th class=\"title col-". $param["key"]."";
        //
        if ( $param["key"] == 0) {
            echo " firstcol";
        }
        //
        if ( $param["key"] ==  count($param["info"])-1) {
            echo " lastcol";
        }
        // fermeture balise th -  entete table 
        echo "\">";
        //
    }
    public function display_dbform_lien_retour_sousformulaire($params) {
        //  
        //  lien retour sous formulaire
        //
        echo "\n<a class=\"retour\" ";
        echo "href=\"";
        echo "#";
        echo  "\" ";
        echo "onclick=\"ajaxIt('".$params["objsf"]."', '";
        //

        if($params["retour"]=="form" AND !($params["validation"]>0 AND $params["maj"]==2 AND $params["correct"])) {
            echo "../scr/sousform.php?";
        } else {
            echo "../scr/soustab.php?";
        }
        echo "obj=".$params["objsf"];
        if($params["retour"]=="form") {
            echo "&amp;idx=".$params["idx"];
            echo "&amp;action=3";
        }
        echo "&amp;retourformulaire=".$params["retourformulaire"];
        echo "&amp;idxformulaire=".$params["idxformulaire"];
        echo "&amp;premier=".$params["premiersf"];
        echo "&amp;tricol=".$params["tricolsf"];
        //
        echo "');";
        echo "\"";
        echo ">";
        //
        echo _("Retour");
        //
        echo "</a>\n";
        //
    }
    public function display_formulaire_text($params) {
        //  
        //  formulaire champs texte
        //
         echo "<input";
        echo " type=\"".$params["type"]."\"";
        echo " name=\"".$params["name"]."\"";
        echo " id=\"".$params["id"]."\" ";
        echo " value=\"".$params["value"]."\"";
        echo " size=\"".$params["size"]."\"";
        echo " maxlength=\"".$params["maxlength"]."\"";
        echo " class=\"champFormulaire\"";
        if (!$params["correct"]) {
            if ($params["onchange"] != "") {
                echo $params["onchange"];
            }
            if ( $params["onkeyup"] != "") {
                echo $params["onkeyup"];
            }
            if ($params["onclick"] != "") {
                echo  $params["onclick"];
            }
        } else {
            echo " disabled=\"disabled\"";
        }
       //
        echo " />\n";
       //
    }
    public function display_formulaire_champs_upload($params) {
        //  
        //  formulaire champs bouton upload
        //
     echo "<input type=\"text\"";
        echo " name=\"".$params["name"]."_upload\"";
        echo " id=\"".$params["id"]."_upload\" ";
        if ($params["value"] != "") {
            echo $params["value"];
        } else {
            echo " value=\"\" ";
        }
        
        echo " class=\"champFormulaire upload\"";
        if (!$params["correct"]) {
            if ($params["onchange"] != "") {
                echo $params["onchange"];
            }
            if ( $params["onkeyup"] != "") {
                echo $params["onkeyup"];
            }
            if ($params["onclick"] != "") {
                echo  $params["onclick"];
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";
       //
    }
    public function display_start_regroup_horizontal() {
        // regroupement bouton - uiliser pour mobile
    }
    public function display_stop_regroup_horizontal() {
        // regroupement bouton - uiliser pour mobile
    }

    // {{{ 

    /**
     *
     */
    function display_list($params) {
        //
        if (isset($params["title"])) {
            echo $params["title"];
        }
        //
        echo "<ul>";
        //
        foreach($params["list"] as $key => $value) {
            //
            echo "<li>";
            //
            $this->display_link($value);
            //
            if (isset($value["links"]) && is_array($value["links"]) && count($value["links"]) > 0) {
                //
                foreach ($value["links"] as $link) {
                    //
                    $this->display_link($link);
                }
            }
            //
            echo "</li>";
        }
        echo "</ul>";
    }

    /**
     * Affiche un lien.
     *
     * @param $param mixed Tableau de paramètres.
     */
    public function display_link($params) {
        //
        if (isset($params['href'])) {
            //
            echo "<a";
            echo " href=\"".$params['href']."\"";
            //
            if (isset($params['description'])) {
                echo " title=\"".$params['description']."\"";
            }
            //
            if (isset($params['class'])) {
                echo " class=\"".$params['class']."\"";
            }
            //
            if (isset($params['target'])) {
                echo " target=\"".$params['target']."\"";
            }
            //
            if (isset($params['id'])) {
                echo " id=\"".$params['id']."\"";
            }
            //
            echo ">";
        }
        //
        echo $params['title'];
        //
        if (isset($params['href'])) {
            //
            echo "</a>";
        }
    }

    // }}}

}

?>