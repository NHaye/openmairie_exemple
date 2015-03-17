<?php
/**
 * Ce fichier ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_layout_bootstrap.class.php 2476 2013-09-17 08:34:43Z fmichon $
 */

class layout_bootstrap extends layout_base {

    /**
     *
     */
    var $layout = "bootstrap";

    /**
     *
     */
    var $html_head_css = array(
        10 => array(
            "../lib/jquery-thirdparty/jquery-minicolors/jquery.minicolors.css",
        ),
        20 => array(
            "../css/layout_bootstrap_before.css",
            "../lib/bootstrap/css/bootstrap.min.css",
            "../css/layout_bootstrap_after.css",
        ),
        30 => array(
            "../app/css/app.css",
        ),
    );

    /**
     *
     */
    var $html_head_js = array(
        10 => array(
            "../js/iepngfix_tilebg.js",
            "../lib/jquery/jquery.min.js",
            "../lib/jquery-thirdparty/jquery.form.js",
            "../lib/jquery-thirdparty/jquery.collapsible.js",
            "../lib/jquery-thirdparty/jquery-minicolors/jquery.minicolors.min.js",
        ),
        20 => array(
            "../js/layout_bootstrap_before.js",
            "../lib/bootstrap/js/bootstrap.min.js",
            "../js/layout_bootstrap_after.js",
        ),
        30 => array(
            "../app/js/script.js",
        )
    );

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
            echo " class=\"navbar navbar-inverse\"";
            echo ">\n";
            echo "<div class=\"navbar-inner\">\n";
            echo "<div class=\"container-fluid\">\n";
            // Logo
            $this->display_logo();
            echo "<div class=\"nav-collapse collapse\">";
            // Actions personnelles
            $this->display_actions();
            // Raccourcis
            $this->display_shortlinks();
            // Menu
            $this->display_menu();
            echo "</div>\n";
            // Fin du header
            echo "</div>\n";
            echo "</div>\n";
            echo "</div>\n";
            echo "<!-- ########## END HEADER ########## -->\n";
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
            echo "<div class=\"container-fluid\">";
            echo "<div class=\"row-fluid\">";
        echo "<div id=\"content\"";
        echo ">\n\n";
    }

    /**
     * Cette méthode permet d'afficher la fin de la section contenu
     */
    public function display_content_end() {
        //
        echo "\n</div>\n";
        echo "\n</div>\n";
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
            echo "<hr/>";
            echo "<footer class=\"footer\">";
            echo "<div id=\"footer\"";
            echo " class=\"container-fluid";
            // XXX
            if (count($this->get_parameter("menu")) == 0) {
                echo " nomenu";
            }
            echo "\"";
            echo ">\n";
            //
            echo "\t<h5 class=\"hiddenStructure\">"._("Actions globales")."</h5>\n";
            echo "<p class=\"muted credit\">";
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
                if (isset($link['href'])) {
                    echo "\t\t<a href=\"".$link['href']."\"";
                    //
                    if (isset($link['description'])) {
                        echo " title=\"".$link['description']."\"";
                    }
                    //
                    if (isset($link['class'])) {
                        echo " class=\"".$link['class']."\"";
                    }
                    //
                    if (isset($link['target'])) {
                        echo " target=\"".$link['target']."\"";
                    }
                    echo ">";
                }

                //
                echo $link['title'];

                //
                if (isset($link['href'])) {
                    echo "</a>";
                }

                //
                echo "\n";
            }

            // Fin du footer
            echo "\t</span>\n";
            echo "</p>";
            echo "</div>\n";
            echo "</footer>\n";
            echo "<!-- ########## END FOOTER ########## -->\n";
            // Marqueur : le footer est affiche
            $this->footer_displayed = true;
        }
    }

    
    //
    public function display_logo() {
        // Logo
        echo "\t<div id=\"logo\" class=\"brand\">\n";
        //
        //echo "\t\t<h1>\n";
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
        //echo "\t\t</h1>\n";
        // Fin du logo
        echo "\t</div>\n";
    }

    /**
     *
     * @return void
     */
    public function display_message($class = "", $message = "") {

        if ($class == "ok" || $class == "valid") {
            $class = "success";
        }
        echo "\n<div class=\"message";
        echo " alert alert-".$class."";
        echo "\">\n";
        echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>";
        //echo "<p>\n";
        echo "<span class=\"text\">";
        echo $message;
        echo "</span>";
        //echo "\n</p>\n";
        echo "</div>\n";

    }
    /**
     *
     * @return void
     */
    public function display_actions() {

        //
        if (count($this->get_parameter("actions_personnelles")) == 0) {
            return;
        }
            echo "\t<div id=\"actions\" class=\"btn-group pull-right\" >\n";
            //echo "\t\t<h5 class=\"hiddenStructure\">"._("Actions personnelles")."</h5>\n";
            //
            //echo "<div class=\"btn-group\">";
            //echo <a class=\"btn dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\" id=\"dLabel\" role=\"button\">";
            echo "<button class=\"btn\">\n";
            echo "<i class=\"icon-user\"></i>\n";
            echo $_SESSION['login'];
            echo "</button>\n";
            echo "<button class=\"btn dropdown-toggle\" data-toggle=\"dropdown\">\n";
            echo "<span class=\"caret\"></span>\n";
            echo "</button>\n";
            //echo "</a>";
            echo "\t\t<ul class=\"dropdown-menu\" role=\"menu\" >\n";

        ////
        //echo "\t<div id=\"actions\">\n";
        ////
        //echo "\t\t<h5 class=\"hiddenStructure\">";
        //echo _("Actions personnelles");
        //echo "</h5>\n";
        ////
        //echo "\t\t<ul class=\"actions-list\">\n";
        //
        $this->display_action_login();
        $this->display_action_collectivite();
        $this->display_action_extras();
        //
        foreach ($this->get_parameter("actions_personnelles") as $key => $value) {
            //
            echo "\t\t\t<li role=\"menuitem\" class=\"actions-list-elem";
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
            if (isset($value['href'])) {
                //
                echo "<a href=\"".$value['href']."\"";
                //
                if (isset($value['description'])) {
                    echo " title=\"".$value['description']."\"";
                }
                //
                if (isset($value['class'])) {
                    echo " class=\"".$value['class']."\"";
                }
                //
                if (isset($value['target'])) {
                    echo " target=\"".$value['target']."\"";
                }
                //
                echo ">";
            }
            //
            echo $value['title'];
            //
            if (isset($value['href'])) {
                //
                echo "</a>";
            }
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
        //echo "\t\t<h5 class=\"hiddenStructure\">";
        //echo _("Raccourcis");
        //echo "</h5>\n";
            //echo "\t\t<h5 class=\"hiddenStructure\">"._("Raccourcis")."</h5>\n";
        echo "\t\t<ul class=\"nav nav-pills pull-right\">\n";
        //
        //echo "\t\t<ul class=\"shortlinks-list\">\n";
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
            if (isset($value['href'])) {
                //
                echo "<a href=\"".$value['href']."\"";
                //
                if (isset($value['description'])) {
                    echo " title=\"".$value['description']."\"";
                }
                //
                if (isset($value['class'])) {
                    echo " class=\"".$value['class']."\"";
                }
                //
                if (isset($value['target'])) {
                    echo " target=\"".$value['target']."\"";
                }
                //
                echo ">";
            }
            //
            echo $value['title'];
            //
            if (isset($value['href'])) {
                //
                echo "</a>";
            }
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
            //echo "<div class=\"span3\">";
            echo "<div>";
            echo "<div id=\"menu\" class=\"nav-collapse collapse\">\n";
            //echo "<h5 class=\"hiddenStructure\">"._("Menu")."</h5>\n";

            //
            echo "<ul id=\"menu-list\" class=\"nav\">\n";
        //echo "<div id=\"menu\">\n";
        //echo "<h5 class=\"hiddenStructure\">"._("Menu")."</h5>\n";

        //
        //echo "<ul id=\"menu-list\">\n";

        // Boucle sur les rubriques
        foreach ($this->get_parameter("menu") as $m => $rubrik) {

            //
            $cpt_links = 0;
            
            if (isset($rubrik["selected"])) {
                $menuOpen = $m;
            }

            //echo "\t<li class=\"rubrik\">\n";
            //// Titre de la rubrique
            //echo "\t\t<h3";
            //if (isset($rubrik['description']) and $rubrik['description'] != "") {
            //    echo " title=\"".$rubrik['description']."\"";
            //}
            //echo ">";
            //echo "<a href=\"";
            //if (isset($rubrik['href']) and $rubrik['href'] != "") {
            //    echo $rubrik['href'];
            //} else {
            //    echo "#";
            //}
            //echo "\"";
            //if (isset($rubrik['class']) and $rubrik['class'] != "") {
            //    echo " class=\"".$rubrik['class']."-20\"";
            //}
            //echo ">";
            //echo $rubrik['title'];
            //echo "</a>";
            //echo "</h3>\n";
                            echo "\t<li class=\"rubrik dropdown";
                                        if (isset($rubrik["selected"])) {
                echo " active";
            }
                            echo "\">";// accordion-group\">\n";
                // Titre de la rubrique
                //echo "\t\t<p";
                ////echo " class=\"accordion-heading\"";
                //if (isset($rubrik['description']) and $rubrik['description'] != "") {
                //    echo " title=\"".$rubrik['description']."\"";
                //}
                //echo ">";
                echo "<a href=\"";
                //echo "#rubrik".$m;
                if (isset($rubrik['href']) and $rubrik['href'] != "") {
                    echo $rubrik['href'];
                } else {
                    echo "#";
                }
                echo "\"";
                echo " class=\"dropdown-toggle";
                if (isset($rubrik['class']) and $rubrik['class'] != "") {
                    echo " ".$rubrik['class']."-20";
                }
                echo "\"";
                //echo " data-parent=\"#menu\"";
                echo " data-toggle=\"dropdown\"";
                echo ">";
                
                echo $rubrik['title'];
                echo "<span class=\"caret\"></span>";
                echo "</a>";
                //echo "</p>\n";
            //
            if (count($rubrik['links']) != 0) {
                // Contenu de la rubrique
                //echo "\t\t<div class=\"rubrik\">\n";
                //echo "\t\t\t<ul class=\"rubrik\">\n";
                echo "\t\t\t<ul class=\"rubrik dropdown-menu\" role=\"menu\">\n";
                // Boucle sur les entrees de menu
                foreach ($rubrik['links'] as $link) {
                    // Entree de menu
                    echo "\t\t\t\t";
                    if (trim($link['title']) != "<hr />"
                        && trim($link['title']) != "<hr/>"
                        && trim($link['title']) != "<hr>") {
                        //
                        echo "<li role=\"menuitem\"  class=\"elem";
                        if (isset($link["selected"])) {
                            echo " active";
                        }
                        if (isset($link['class'])) {
                            echo " ".$link['class']."";
                            if ($link['class'] == "category") {
                                echo " nav-header";
                            }
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
                        if (!isset($link['class']) || $link['class'] != "category") {
                            echo "<i class=\"icon-chevron-right\"></i>";
                        }
                        echo $link['title'];
                        //
                        if (isset($link["href"])) {
                            echo "</a>";
                        }
                        echo "</li>";
                    } else {
                        echo "<li role=\"menuitem\"  class=\"elem hr\"><!-- --></li>";
                    }
                    echo "\n";
                }
                // Fin de la rubrique
                echo "\t\t\t</ul>\n";
                //echo "\t\t</div>\n";

            }
            // Fermeture de le rubrique
            echo "\t</li>\n";
        }
        // Fin du menu
        echo "</ul>\n";
        echo "</div>\n";
        //
        echo "</div>\n";
        echo "<!-- ########## END MENU ########## -->\n";
    }

    public function display_table_start($class = "") {
        // Affichage de la table
        echo "<!-- tab-tab -->\n";
        echo "<table class=\"table table-bordered table-hover table-condensed".$class."-tab\">\n";
    }
    public function display_icon($icon, $title, $content) {
        echo "<i class=\"icon-".$icon."\"";
        echo " title=\"".$title."\"";
        echo ">";
        //echo $content;
        echo "</i>";
    }

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
        echo "<div class=\"".$params["style"]."-pagination pull-right\">\n";
        
        // Ouverture du conteneur de message de pagination
        echo "\t<span class=\"pagination-text\">\n";
        // Affichage du message de pagination
        echo "\t\t<span class=\"pagination-first-last bold\">";
        echo "<span class=\"pagination-first\">";
        echo $params["first"];
        echo "</span>";
        echo " - ";
        echo "<span class=\"pagination-last\">";
        echo $params["last"];
        echo "</span>";
        echo "</span>\n";
        echo "\t\t "._("sur")." \n";
        echo "\t\t<span class=\"pagination-total bold\">";
        echo $params["total"];
        echo "</span>\n";
        // Affichage d'une éventuelle recherche en cours
        if (strcmp($params["search"], "")) {
            echo "\t\t ";
            echo "<span class=\"pagination-search-filter\">";
            echo "= [".$params["search"]."] ";
            echo "</span>\n";
        }
        // Fermeture du conteneur de message de pagination
        echo "\t</span>\n";

        // Ouverture du conteneur de liens vers les pages précédentes et
        // suivantes
        echo "\t<div class=\"btn-group\">\n";
        // Gestion du lien vers la page précédente
        if ($params["previous"] != "") {
            // Affichage du lien vers la page precedente
            echo "\t\t";
            echo "<a";
            echo " class=\"btn bold pagination-prev\"";
            echo " href=\"";
            echo $params["previous"];
            echo "\"";
            echo " title=\""._("Page precedente")."\"";
            echo ">";
            // Affichage de l'image representant la page precedente
            echo "&lt;";
            // Fermeture de la balise lien
            echo "</a>\n";
        } else {
            echo "\t\t";
            echo "<button class=\"btn disabled\">";
            echo "&lt;";
            echo "</button>\n";
        }
        // Gestion du lien vers la page suivante
        if ($params["next"] != "") {
            // Affichage du lien vers la page suivante
            echo "\t\t";
            echo "<a";
            echo " class=\"btn bold pagination-next\"";
            echo " href=\"";
            echo $params["next"];
            echo "\"";
            echo " title=\""._("Page suivante")."\"";
            echo ">";
            // Affichage de l'image representant la page suivante
            echo "&gt;";
            // Fermeture de la balise lien
            echo "</a>\n";
        } else {
            echo "\t\t";
            echo "<button class=\"btn disabled\">";
            echo "&gt;";
            echo "</button>\n";
        }
        // Fermeture du conteneur de liens vers les pages précédentes et
        // suivantes
        echo "\t</div>\n";

        //
        echo "\t<div class=\"visualClear\"><!-- --></div>\n";
        echo "</div>\n";
        echo "\t<div class=\"visualClear\"><!-- --></div>\n";
    }
    //
    public function display_table_search_simple($params) {
        //
        echo "<!-- tab-search -->\n";
        // Affichage de la table
        echo "<div class=\"".$params["style"]."-search pull-left ui-widget-content ui-corner-all\">\n";
        // Affichage du formulaire
        echo "\t<form action=\"";
        echo $params["form_action"];
        echo "\" method=\"post\" class=\"form-search\" id=\"f1\" name=\"f1\">\n";
        // Affichage du champ permettant la saisie du terme a rechercher
        echo "\t\t<input type=\"text\" name=\"recherche\" ";
        echo "value=\"".$params["search"]."\" ";
        echo "autocomplete=\"off\" ";
        echo "class=\"champFormulaire\" />\n";
        //    <input class="span2" id="appendedDropdownButton" type="text">
        // Affichage du champ select permettant de choisir le champ sur lequel
        // doit agir la recherche
        //echo "\t\t<select name=\"selectioncol\" class=\"champFormulaire\">\n";
        //if ($params["column_search_selected_key"] == "") {
        //    echo "\t\t\t<option value=\"\" selected=\"selected\">"._("Tous")."</option>\n";
        //    foreach($params["column_search"] as $key => $elem) {
        //        echo "\t\t\t<option value=\"".$key."\">".$elem."</option>\n";
        //    }
        //} else {
        //    echo "\t\t\t<option value=\"\">"._("Tous")."</option>\n";
        //    foreach($params["column_search"] as $key => $elem) {
        //        if($params["column_search_selected_key"] == $key) {
        //            echo "\t\t\t<option value=\"".$key."\" selected=\"selected\">".$elem."</option>\n";
        //        } else {
        //            echo "\t\t\t<option value=\"".$key."\">".$elem."</option>\n";
        //        }
        //    }
        //}
        //echo "\t\t</select>\n";
        //
        //echo "<div class=\"btn-group\">";
        ////
        //echo "<button class=\"btn dropdown-toggle\" data-toggle=\"dropdown\">";
        //echo _("Tous");
        //echo "<span class=\"caret\"></span>";
        //echo "</button>";
        //echo "<ul class=\"dropdown-menu\">";
        ////...
        //echo "</ul>";
        //echo "</div>\n";
        // Affichage du bouton de soumission du formulaire
        echo "<button id=\"advsearchlink\" class=\"btn\" type=\"button\">";
        echo "<span class=\"caret\"></span>";
        echo "</button>\n";
        //
        echo "\t\t<button class=\"btn btn-primary\" type=\"submit\" title=\""._("Recherche")."\" name=\"s1\">";
        //echo ;
        echo "<i class=\"icon-search icon-white\"></i>";
        echo "</button>\n";
        // Fermeture de la balise formulaire
        echo "\t</form>\n";
        // Fermeture de la balise table
        echo "</div>\n";
    }


    public function display_table_global_actions($actions) {
        //
        echo "<div class=\"btn-group pull-right\" style=\"margin-left: 10px;\">";
        //
        if (count($actions) == 0) {
            echo "<button class=\"btn disabled\">";
            echo _("Plus");
            echo "<span class=\"caret\"></span>";
            echo "</button>\n";
        } else {
            echo "<a class=\"btn dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">";
            echo _("Plus");
            echo "<span class=\"caret\"></span>";
            echo "</a>";
            echo "<ul class=\"dropdown-menu\">";
            foreach($actions as $params) {
                echo "<li>";
                echo "<a";
                echo " href=\"".$params["link"]."\"";
                if (isset($params["target"])) {
                    echo " target=\"_blank\"";
                }
                echo ">";
                echo $params["title"];
                echo "</a>";
                echo "</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
    }
    // }}}
    
}

?>
