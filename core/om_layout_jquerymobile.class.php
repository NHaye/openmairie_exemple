<?php
/**
 * Ce fichier ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_layout_jquerymobile.class.php 2926 2014-10-13 13:20:24Z fmichon $
 */

class layout_jquerymobile extends layout_base {

    /**
     *
     */
    var $layout = "jquerymobile";

    /**
     *
     */
    var $html_head_css = array(
        10 => array(
        ),
        20 => array(
            "../css/layout_jquerymobile_before.css",
            "../lib/jquery-mobile/jquery.mobile.min.css",
            "../css/layout_jquerymobile_after.css",
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
            "../lib/jquery/jquery.min.js",
        ),
        20 => array(
            "../js/layout_jquerymobile_before.js",
            "../lib/jquery-mobile/jquery.mobile.min.js",
            "../js/layout_jquerymobile_after.js",
        ),
        30 => array(
            "../app/js/script.js",
        )
    );

    // {{{ STRUCTURE GENERALE DE LA PAGE

    /**
     *
     */
    public function display() {
        //
        if ($this->get_parameter("flag") != "nohtml") {


          $this->display_html_header();
          echo "<!-- ########## START PAGE ########## -->\n";
          //
          echo "<div data-role=\"page\" data-theme=\"c\">\n";
          echo "<!-- ########## START ACTIONS/MENU ########## -->\n";
          //
          $this->display_menu();
          //
          echo "<!-- ########## END ACTIONS/MENU ########## -->\n";
           //
          if ($this->get_parameter("flag") != "htmlonly"
             && $this->get_parameter("flag") != "htmlonly_nodoctype") {
             //
             $this->display_header();
          }
          //
           $this->display_messages();
          // Content
          $this->display_content_start();
          // Titre
          $this->display_page_title();
          // Marqueur : le header est affiche
          $this->header_displayed = true;
          //
           
        }
    }
    
    /**
     *
     */
    protected function display_html_header_extrametas() {
        //
        echo "\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
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
            echo " data-role=\"header\"";
            echo " data-position=\"fixed\" data-theme=\"c\"";
            echo ">\n";
            // Logo
            $this->display_logo();
            // Fin du header
            echo "</div>\n";
            echo "<!-- ########## END HEADER ########## -->\n";
            /*// Content
            $this->display_content_start();
            // Titre
            $this->display_page_title();
            // Marqueur : le header est affiche
            $this->header_displayed = true;*/
        }
    }

    /**
     * Cette méthode permet d'afficher le début de la section contenu
     */
    public function display_content_start() {
        //
        echo "<!-- ########## START CONTENT ########## -->\n";
        echo "<div id=\"content\"";
        echo " data-role=\"content\"";
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
            echo " data-role=\"footer\" data-theme=\"c\"";
            echo ">\n";
            

            // Version de l'application
            echo "\t";
            echo "<h5>";
            echo $this->get_parameter('application');
            if ($this->get_parameter('version') != NULL) {
                echo " "._("Version")." ".$this->get_parameter('version');
            }
            //
            echo "</h5>";
            echo "\n";

            echo "\t<div data-role=\"navbar\" class=\"ui-body-b ui-body\">\n";
            echo "\t\t<ul>\n";
            //
            foreach ($this->get_parameter("actions_globales") as $link) {
                //
                echo "\t\t\t<li>\n";
                //
                echo "\t\t\t\t";
                //
                if (isset($link['href'])) {
                    echo "<a href=\"".$link['href']."\"";
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
                //
                echo "\t\t\t</li>\n";
            }

            // Fin du footer
            echo "\t\t</ul>\n";
            echo "\t</div>\n";
            
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
            echo "</div>\n";
            //
            echo "<!-- ########## END PAGE ########## -->\n";
            // Footer HTML
            echo "\n</body>\n";
            echo "</html>";
            // Marqueur : le footer HTML est affiche
            $this->html_footer_displayed = true;
        }
    }

    // }}} STRUCTURE GENERALE DE LA PAGE - END

    function display_logo() {
            //
            if (count($this->get_parameter("menu")) != 0)  {
               echo "<a href=\"#panneau\"  data-theme=\"b\" data-role=\"button\" data-icon=\"grid\">MENU</a>";
            }
            //
            echo "<a data-role=\"button\" data-icon=\"home\" ";
            echo " data-iconpos=\"notext\" ";
            echo " href=\"".$this->get_parameter("url_dashboard")."\"  ";
            echo " class=\"ui-btn-right\"";
            echo ">";
            echo _("Tableau de bord");
            echo "</a>";
                        
            //
            echo "<h1>";
            echo $this->get_parameter("application");
            echo "</h1>";
    }
        function display_shortlinks() {

        //
        if (count($this->get_parameter("raccourcis")) == 0) {
            return;
        }
        echo "<div data-role=\"collapsible\" data-collapsed=\"true\" data-mini=\"true\" data-theme=\"c\"  data-content-theme=\"c\">\n";
        //
        echo "<h3>"._("Raccourcis")."</h3>";
        //
        echo "\t\t<div class=\"shortlinks-list\">\n";
        //
        foreach ($this->get_parameter("raccourcis") as $key => $value) {
            //
            echo "\t\t\t<div class=\"shortlinks-list-elem";
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
                //display_page_title
                echo "<a data-role=\"button\" data-theme=\"b\" data-corners=\"true\" data-icon=\"star\" data-mini=\"true\" href=\"".$value['href']."\"";
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
            echo "</div>\n";
        }
        //
     echo "\t\t</div>\n";
     //
     echo "\t</div>\n";
     return;

    }
    public function display_actions() {

        //
        if (count($this->get_parameter("actions_personnelles")) == 0) {
            return;
        }
        //
        echo "<div data-role=\"collapsible\" data-collapsed=\"true\" data-mini=\"true\" data-theme=\"e\"  data-content-theme=\"c\" >\n";
            echo "<h3>"._("Personnel")."</h3>";
        //
        echo "\t\t<div class=\"actions-list\">\n";
        //
        $this->display_action_login();
        $this->display_action_collectivite();
        $this->display_action_extras();
        //
        foreach ($this->get_parameter("actions_personnelles") as $key => $value) {
            //
            echo "\t\t\t<div class=\"actions-list-elem";
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
                echo "<a data-role=\"button\" data-corners=\"true\" data-theme=\"b\" data-icon=\"star\" data-mini=\"true\" href=\"".$value['href']."\"";
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
            echo "</div>\n";
        }
     //
     echo "\t\t</div>\n";
     //
     echo "\t</div>\n";
     //
     return;
    }
    public function display_action_login() {
        echo "\t\t\t<div class=\"action-login-mobile\">";
        echo $_SESSION['login']."&nbsp;";
        //echo "</div>\n";
    }

    public function display_action_collectivite() {
        //echo "\t\t\t<div class=\"action-collectivite-mobile\">";
        $collectivite = $this->get_parameter("collectivite");
        if (isset($collectivite["ville"])) {
            echo $collectivite["ville"];
        }
        echo "</div>\n";
    }
    /**
     * Affichage du menu
     */
    public function display_menu() {
        //
        echo "<!-- ########## START MENU ########## -->\n";
        echo "<div data-role=\"panel\" id=\"panneau\" data-position=\"left\" data-display=\"push\" data-dismissible=\"true\">";
        echo "<center>";
        echo "<a id=\"close-menu\" href=\"#header\"  data-rel=\"close\" data-role=\"button\" data-theme=\"b\" data-icon=\"grid\" data-iconpos=\"notext\" data-mini=\"false\" data-inline=\"true\">Fermer MENU;</a>";
        echo "<a id=\"close-menu\" href=\"#header\"  data-rel=\"close\" data-role=\"button\" data-theme=\"b\" data-icon=\"delete\" data-iconpos=\"notext\" data-mini=\"false\" data-inline=\"true\">Fermer MENU</a>";
        echo "</center>";
        //
        echo "<div data-role=\"collapsible-set\"  data-mini=\"true\" data-theme=\"c\" >\n";
        foreach ($this->get_parameter("menu") as $m => $rubrik) {
            //Affichage de la rubrique
            echo "<div data-role=\"collapsible\" data-collapsed=\"true\" data-content-theme=\"c\">\n";
            //Affichage de la rubrique
            echo "<h3>\t<div  class=\"rubrik";
            if (isset($rubrik["selected"])) {
            echo " selected";
            }
            echo "\"";
            echo ">";
            // Titre de la rubrique
            //echo "\t\t<h3";
            //if (isset($rubrik['description']) and $rubrik['description'] != "") {
            //    echo " title=\"".$rubrik['description']."\"";
            //}
            //echo ">";
            //
            if (isset($rubrik['href']) and $rubrik['href'] != "") {
                echo "<a  data-role=\"button\" data-theme=\"b\" data-icon=\"arrow-r\" data-iconpos=\"right\" data-mini=\"true\"  href=\"";
                echo $rubrik['href'];
                echo "\"";
            } else {
                echo "<span";
            }
            //
            if (isset($rubrik['class']) and $rubrik['class'] != "") {
                echo " class=\"".$rubrik['class']."-20\"";
            }
            echo ">";
            //
            echo $rubrik['title'];
            if (isset($rubrik['href']) and $rubrik['href'] != "") {
                echo "</a>";
            } else {
                echo "</span>";
            }
            //
            //echo "</h3>\n";
            // Fermeture de le rubrique
            echo "\t</div></h3>\n";
            //
            if (count($rubrik['links']) != 0) {
                // Contenu de la rubrique
                //echo "\t\t<div class=\"rubrik\">\n";
                //echo "\t\t\t<ul class=\"rubrik\" data-role=\"listview\">\n";
                // Boucle sur les entrees de menu
                foreach ($rubrik['links'] as $link) {
                    // Entree de menu
                    echo "\t\t\t\t";
                    if (trim($link['title']) != "<hr />"
                        && trim($link['title']) != "<hr/>"
                        && trim($link['title']) != "<hr>") {
                        //
                        echo "<div class=\"elem";
                        if (isset($link["selected"])) {
                            echo " selected";
                        }
                        if (isset($link['class'])) {
                            echo " ".$link['class']."";
                        }
                        echo "\">";
                        //
                        if (isset($link["href"])) {
                            echo "<a data-role=\"button\" data-theme=\"b\" data-icon=\"arrow-r\" data-iconpos=\"right\" data-mini=\"true\"  ";
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
                        echo "</div>";
                    } else {
                        //echo "<li class=\"elem hr\"><!-- --></li>";
                    }
                    echo "\n";
                }
                // Fin de la rubrique
                //echo "\t\t\t</ul>\n";
                //echo "\t\t</div>\n";

            }
          echo "</div>\n";
        }
        $this->display_actions();
         // Raccourcis
        $this->display_shortlinks();
        //
        echo "</div>\n";
        
        // Fin du menu
        echo "</div>\n";
        //
        echo "<!-- ########## END MENU ########## -->\n";
    }

    /**
     *
     * @return void
     */
    public function display_message($class = "", $message = "") {

        echo "\n<div class=\"message ui-bar";
        if ($class == "error") {
            echo " ui-bar-o";
        } elseif ($class == "ok") {
            echo " ui-bar-f";
        } else {
            echo " ui-bar-e";
        }
        echo "\">\n";
        echo "<p>\n";
        echo "\t<span class=\"ui-icon ui-icon-info\"><!-- --></span> \n\t";
        echo "<span class=\"text\">";
        echo $message;
        echo "</span>";
        echo "\n</p>\n";
        echo "</div>\n";
    }
    // 
    public function display_table_start($param) {
        // Affichage de la table
        // data-mode=\"columntoggle\"
        // ou data-mode=\"reflow\" bug
        $class=$param['class'];
        $idcolumntoggle=$param['idcolumntoggle'];
        $order   = array("\r\n", "\n", "\r","public.");
        $replace = '';
        // Traitement du premier \r\n, ils ne seront pas convertis deux fois.
        $newidcolumntoggle = str_replace($order, $replace, $idcolumntoggle);
        echo "<!-- tab-tab -->\n";
        echo "<table id=\"table-".$newidcolumntoggle."\" data-role=\"table\" data-mode=\"columntoggle\" data-column-btn-text=\"Choix d'affichage\" data-column-btn-theme=\"e\" class=\"ui-responsive table-stroke ui-table ui-table-columntoggle\">\n";
        
    }
    public function display_table_start_class_default($param) {
        // Affichage de la table
        // data-mode=\"columntoggle\"
        // ou data-mode=\"reflow\" bug
        $idcolumntoggle=$param['idcolumntoggle'];
        $order   = array("\r\n", "\n", "\r","public.");
        $replace = '';
        // Traitement du premier \r\n, ils ne seront pas convertis deux fois.
        $newidcolumntoggle = str_replace($order, $replace, $idcolumntoggle);
        echo "<!-- tab-tab -->\n";
        echo "<table id=\"table-".$newidcolumntoggle."\" data-role=\"table\" data-mode=\"columntoggle\" data-column-btn-text=\"Choix d'affichage\" data-column-btn-theme=\"e\" class=\"ui-responsive table-stroke ui-table ui-table-columntoggle\">\n";
        
    }
    


    function display_page_title($page_title = "") {
        //
        if ($page_title == "") {
            $page_title = $this->get_parameter("page_title");
        }
        //
        if (!is_null($page_title) && $page_title != "") {
            //
            echo "<div id=\"title\"  ";//class=\"";
            //echo $this->get_parameter("style_title");
            echo "\">\n";
            // echo "<h2>\n";
            // Remplacement les caracteres -> par une image de fleche
            //$ent = str_replace("->", "- ", $page_title);
            $ent = str_replace("->", " <img src='../img/arrow-right-16' vertical-align='middle'> ", $page_title);
            // Afichage du titre
            echo "\t".$ent."\n";
            // Fin du title
            // echo "</h2>\n";
            echo "</div>\n\n";
        }
    }

    // {{{ FORM

    /**
     * Cette méthode permet d'afficher le bouton de validation du formulaire
     */
    public function display_form_button($params) {
        //
        echo "<input";
        echo " data-role=\"button\"";
        echo " data-theme=\"a\"";
        echo " data-icon=\"check\"";
        echo " data-mini=\"true\"";
        echo " type=\"submit\"";
        echo " value=\"".$params["value"]."\" ";
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
        // Spécificité jquerymobile
        echo " data-role=\"button\"";
        echo " data-icon=\"back\"";
        echo " data-inline=\"true\"";
        echo " data-theme=\"c\"";
        echo " data-mini=\"true\"";
        // Attribut href
        echo " href=";
        echo "\"";
        if (isset($params["href"]) && $params["href"] != "") {
            echo $params["href"];
        }
        echo "\"";
        // Attribut onclick
        if (isset($params["onclick"]) && $params["onclick"] != "") {
            echo " onclick=";
            echo "\"";
            echo $params["onclick"];
            echo "\"";
        }
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
        echo "<div  data-role=\"collapsible\"  data-mini=\"true\" data-theme=\"d\" data-content-theme=\"b\" >\n";
        echo "<h3>".$params["action1"]."</h3>";
        // Ouverture du conteneur interne du fieldset
        echo "        <div>\n";
	}
	/**
     * Cette methode permet de fermer un fieldset
    */
	public function display_formulaire_finFieldset($params) {
		// Fermeture du fieldset
        echo "          <div class=\"visualClear\"><!-- --></div>\n";
        echo "        </div>\n";
        echo "      </div>\n";
	}
    /**
     * Permet d'afficher le portlet d'actions contextuelles.
     */
    public function display_formulaire_portlet_start($params = array()) {
		// affichage du portlet d'actions contextuelles
        // theme herite ou class ui-body-a ui-body-b ....
        echo "<div data-role=\"navbar\" class=\"ui-body-c ui-body\"  >";
        echo "<ul>";
	}
    public function display_icon($icon, $title, $content) {
         echo "<img src='../img/".$icon.".png' alt='".$title."' vertical-align='middle'>";
    }
    public function display_formulaire_conteneur_libelle_widget($type_champ) {
		//ouverture div contenant  libelle champs
        // attribut fieldcontain"  pour grouper libelle champs
        // display_formulaire_conteneur_  champs et ibelle_champs -> pas de span
        //
		 echo "      <div data-role=\"fieldcontain\">\n";
	}
    public function display_formulaire_conteneur_libelle_champs() {
		//ouverture conteneur libelle champs
		// echo "        <span id='form-libelle-mobile' form-class=\"form-libelle\">\n";
	}
    public function display_formulaire_conteneur_champs() {
		//ouverture conteneur libelle champs
		//echo "        <span  class=\"form-content\">\n";
	}
    public function display_formulaire_fin_conteneur_champs() {
		//fin conteneur libelle champs et champs
		//echo "        </span>\n";
	}
    public function display_tab_lien_onglet_un($param) {
		// premier onglet -> pas d'afichage
        // echo "\t<a data-role=\"button\"  data-theme=\"b\" data-icon=\"grid\" data-mini=\"true\" href=\"#tabs-1\">".$param."</a>\n";
	}
    public function display_form_lien_onglet_un($param) {
        //inserer dans le conteneur onglet
		//echo "\t<a href=\"#tabs-1\" id=\"main\" class=\"ui-btn-active\" data-role=\"button\"  data-inline=\"true\"  data-mini=\"true\" >".$param."</a>\n";
        
    }
    
    public function display_form_start_conteneur_onglets_accordion() {
		// ouverture conteneur onglets sous formulaire 
		// affichage en accordeon sous le formulaire
        echo "<div id=\"accordion\" >";
        echo "<div data-role=\"collapsible\" data-collapsed=\"true\" data-content-theme=\"b\" data-theme=\"e\" data-mini=\"true\">\n";
        echo "<h3>SOUS FORMULAIRES</h3>";
        echo "<div  data-role=\"collapsible-set\"  data-mini=\"true\" data-theme=\"a\" >\n";
	} 
    public function display_form_close_conteneur_onglets_accordion() {
        // fermeture conteneur onglets sous formulaire 
		// affichage en accordeon sous le formulaire
        echo  "</div>";
        echo  "</div>";
        echo  "</div>";
    }
   
       
    public function display_form_recherche_sousform($param) {
	  //
	}
    public function display_form_recherche_sousform_accordion($param) {
	  //
      //  Affichage de la recherche pour les sous formulaires 
		//echo "\t\t<li>\n";
		//echo "\t\t\t<span  id=\"recherche_onglet\" style=\"display:none;\">\n";
		echo "\t\t\t\t";
		echo "<input data-role=\"none\" type=\"text\" name=\"recherchedyn\" id=\"recherchedyn\" ";
		echo "value=\"\" class=\"champFormulaire\" ";
		echo "onkeyup=\"recherche('".$param["link"]."');\" />";
		echo "\n";
		//echo "\t\t\t</span>\n";
		//echo "\t\t</li>\n";
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
            // Remplacement les caracteres -> par une image de fleche
			$ent = str_replace("->", " <img src='../img/arrow-right-16' vertical-align='middle'> ", $page_subtitle);
            // Afichage du titre
            echo "\t".$ent."\n";
            // Fin du title
            echo "</div>\n\n";
        }
    }
    public function display_formulaire_select_personnalise($params) {
		//
        //  menu selection personnalise  -> data-native-menu=\"false\"
		//  data-overlay-theme=\"b\" a voir
		echo "<select ";
        echo "  data-theme=\"a\" data-inline=\"false\"  data-native-menu=\"false\"  data-mini=\"true\" ";
        echo " name='".$params["champ"]."' ";
        echo " id=\"".$params["champ"]."\" ";
        echo " size='1' ";
        echo " >\n";
       // 
	}
        
    public function display_formulaire_lien_vupload_upload($champ, $obj, $id, $contraintes = null) {
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
        echo " <a  data-role=\"button\" href=\"javascript:vupload_mobile('".$champ."', '".$obj."', '".$id."', ".$contraintes.");\"  data-theme=\"b\"   data-mini=\"true\"  data-inline=\"true\" />"._("Telecharger")."</a>\n";
        //
	}
    public function display_formulaire_lien_voir_upload($champ, $obj, $id) {
		//  
		//  fonction upload -> -> lien  voir
		// 
        echo " <a  data-role=\"button\" href=\"javascript:voir_mobile('".$champ."', '".$obj."', '".$id."');\"  data-theme=\"b\"   data-mini=\"true\"  data-inline=\"true\" />"._("Voir")."</a>\n";
        //
    }

    /**
     * Function permettant de vider le champ de formulaire
     * @param  string $champ champ sur lequel ajouter le bouton
     */
    public function display_formulaire_lien_supprimer_upload($champ) {
        //  
        //  fonction upload -> -> lien  voir
        // 
        echo " <a  data-role=\"button\" href=\"javascript:supprimerUpload('".$champ."');\"  data-theme=\"b\"   data-mini=\"true\"  data-inline=\"true\" />"._("Supprimer")."</a>\n";
        //
    }
    function display_link_js_close_window($js_function_close = "") {
        //
        if ($js_function_close == "") {
            $js_function_close = "window.close();";
        }
        //
        //echo "\n<p class=\"linkjsclosewindow\">";
        echo "<center><a data-role=\"button\" data-theme=\"e\"   data-mini=\"true\"  data-inline=\"true\" class=\"linkjsclosewindow\" href=\"#\" ";
        echo "onclick=\"".$js_function_close."\">";
        echo _("Fermer");
        echo "</a></center>";
        //echo "</p>\n";
    }
  
    public function display_formulaire_localisation_lien($params) {
		//  
		//  localisation 
		//
		echo "<a data-role=\"button\"  data-theme=\"a\"  data-mini=\"true\" data-inline=\"true\" ";
        //
        echo " class=\"localisation ui-state-default ui-corner-all\" href=\"javascript:localisation('".$params["champ"]."','".$params["plan"]."','".$params["positionx"]."');\">";
        //
        echo _("Localisation");
		echo "</a>";
        //
	}
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
        echo "<fieldset class=\"ui-grid-a\"><div class=\"ui-block-a\">";
        echo "\t\t&nbsp;&nbsp;&nbsp;<input data-role=\"none\" type=\"text\" name=\"recherche\" ";
        echo "value=\"".$params["search"]."\" ";
        echo "class=\"champFormulaire-recherche\" /></div>\n";
        // Affichage du champ select permettant de choisir le champ sur lequel
        // doit agir la recherche
        echo "\t\t<div class=\"ui-block-b\"><select  data-theme=\"c\" data-inline=\"false\"  data-native-menu=\"false\"  data-mini=\"true\" name=\"selectioncol\" >\n";
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
        echo "\t\t</select></div></fieldset>\n";
        // Affichage du bouton de soumission du formulaire
        echo "\t\t<button  data-theme=\"b\" data-inline=\"false\"  data-mini=\"true\" type=\"submit\" name=\"s1\">";
        echo _("Recherche");
        echo "</button>\n";
        // Fermeture de la balise formulaire
        echo "\t</form>\n";
        // Fermeture de la balise table
        echo "</div>\n";
    }
    public function display_table_pagination($params) {
        //

        echo "<div class=\"".$params["style"]."-pagination ui-state-default ui-corner-top ui-tabs-selected ui-state-active\">\n";
        echo "<fieldset class=\"ui-grid-a\">";
        echo "<div class=\"ui-block-a\">";
        echo "<div class=\"pagination-nb\">";

        // Si il y a une page precedente
        if ($params["previous"] != "") {
            // Affichage du lien vers la page precedente
            echo " <a  data-role=\"button\" data-icon=\"arrow-l\" data-iconpos=\"notext\" data-theme=\"c\" data-inline=\"true\"  data-mini=\"false\" href=\"";
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
            echo "<div>"._("< Page")."</div>";
            // Fermeture de la balise lien
            echo "</a> ";
        }

        // Affichage du conteneur de message de pagination
        echo "<span class=\"pagination-text\">";
        // Construction du message de pagination
        echo $params["first"]."-".$params["last"]." ";
        //echo _("enregistrement(s)/")." ".$params["total"];
        echo _(" / ")." ".$params["total"];
        // Affichage du message de pagination
        if ($params["search"] != "") {
            echo " = [".$params["search"]."] ";
        }
        // Fermeture de la balise span
        echo "</span>";

        // Si il y a une page suivante
        if ($params["next"] != "") {
            // Affichage du lien vers la page suivante
            echo " <a  data-role=\"button\" data-icon=\"arrow-r\" data-iconpos=\"notext\" data-theme=\"c\" data-inline=\"true\"  data-mini=\"false\" href=\"";
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
            echo "<div>"._("Page >")."</div>";
            // Fermeture de la balise lien
            echo "</a> ";
        }
        //
        echo "</div></div>";
        //
        if ($params["pagination_select"]["active"] == true) {
            //
            echo "<div class=\"ui-block-b\">";
            // Affichage du formulaire
            echo "<form action=\"\">\n";
            // Affichage du mot Pagedisplay_formulaire_text
            //echo "&nbsp;"._('Page')."&nbsp;</div>";
            // Affichage de la liste de choix
            echo "<select  data-theme=\"d\" data-inline=\"true\"  data-native-menu=\"false\"  data-mini=\"true\" name=\"page\"  ";
            if ($params["onglet"]) {
                echo "onchange=\"ajaxIt('".$params["obj"]."', '";
                echo $params["pagination_select"]["link"];
                echo "');";
                echo "\"";
            } else {
                echo "onchange=\"allerpage(this);\"";
            }
            echo " >";
            // Boucle sur le nombre de page pour l'affichage de chaque item de
            // la liste
            echo "&nbsp;"._('Page')."&nbsp";
            for ($i = 1; $i <= $params["pagination_select"]["page_number"]; $i++) {
                // Affichage de l'item selectionne sur la page en cours
                if (($i - 1) * $params["pagination_select"]["serie"] == $params["pagination_select"]["premier"]) {
                    echo "<option value=\"".($i - 1) * $params["pagination_select"]["serie"]."\" selected=\"selected\">";
                    echo "&nbsp;"._('Page')."&nbsp;".$i." / ".$params["pagination_select"]["page_number"];
                    echo "</option>";
                } else {
                    echo "<option value=\"".($i - 1) * $params["pagination_select"]["serie"]."\">";
                    echo "&nbsp;"._('Page')."&nbsp;".$i." / ".$params["pagination_select"]["page_number"];
                    echo "</option>";
                }
            }
            // Fermeture de la balise liste de choix
            echo "</select>";
            // Fonction javascript allerpage()
            echo "<script type=\"text/javascript\">";
            echo "function allerpage(select) { ";
            echo "var str=\"";
            echo $params["pagination_select"]["link"];
            echo "\"; ";
            echo "location=str.replace(/&amp;/g, \"&\")";
            echo "} ";
            echo "</script>";
            // Fermeture de la balise formulaire
            echo "</form>\n";

            //
            echo "</div>";
        }

        //
        echo "\t<div class=\"visualClear\"><!-- --></div>\n";
        echo "</fieldset>\n";
        echo "</div>\n";
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
            $tmp="";
            $tmp=str_replace("<a ","<a data-role=\"button\" data-icon=\"arrow-r\" data-iconpos=\"right\"  data-mini=\"true\" data-theme=\"e\" data-inline=\"true\" ",$page_description);
            echo "\t\t".$tmp."\n";
            echo "\t</p>\n";
            echo "</div>\n";
        }
    }
    public function display_password_input_submit() {
		//  
		//   bouton  valider password
		//
         echo "<input ";
         echo " data-role=\"button\"  data-mini=\"true\" data-theme=\"a\" data-inline=\"false\" ";
         echo " type=\"submit\" name=\"submit-change-password\" value=\""._("Valider")."\" class=\"boutonFormulaire\" />";
	    //
	}
    public function display_start_fieldset($params = array()) {
        // Rétro-compatibilité
        if (count($params) == 0) {
            //
            echo "<div  data-role=\"collapsible\"  data-mini=\"true\" data-theme=\"d\" data-content-theme=\"b\" >\n";
            //
            echo "<h3>";
            //
            return;
        }
        //
        echo "<div data-role=\"collapsible\" data-mini=\"true\" data-theme=\"d\" data-content-theme=\"b\"";
        if (isset($params["fieldset_class"])) {
            echo " class=\"".$params["fieldset_class"]."\"";
        }
        echo ">";
        echo "<h3";
        if (isset($params["legend_class"])) {
            echo " class=\"".$params["legend_class"]."\"";
        }
        echo ">";
        if (isset($params["legend_content"])) {
            echo $params["legend_content"];
        } else {
            echo "...";
        }
        echo "</h3>";
    }

	public function display_stop_fieldset() {
		//  
		//   fiedset 
		//
		echo "</div>\n";
	    //
	}
	public function display_stop_legend_fieldset() {
		//  
		//   fiedset 
		//
		echo "</h3>\n";
	    //
	}
    public function display_lien($param) {
		//  
		//   lien 
		//class=\"ui-body-b ui-body\"
		$tmp="";
        $tmp=str_replace("<a ","<a data-role=\"button\"   data-mini=\"true\" data-theme=\"e\" data-inline=\"true\" ",$param['lien']);
	    echo $tmp;
        //
	}
    /**
     * Affiche le lien retour présent sur beaucoup de pages de l'applicatif.
     *
     * @param $param mixed Tableau de paramètres.
     */
    public function display_lien_retour($param) {
        // Rétro compatibilité
        if (isset($param["lien"])) {
            $tmp = "";
            $tmp = str_replace(
                "<a ",
                "<a data-role=\"button\" data-mini=\"true\" data-theme=\"d\" data-inline=\"true\" ",
                $param['lien']
            );
            echo $tmp;
            return;
        }
        //
        echo "<a";
        echo " href=\"";
        echo $param["href"];
        echo "\"";
        echo " class=\"retour\"";
        echo " data-role=\"button\" data-mini=\"true\"";
        echo " data-theme=\"d\" data-inline=\"true\"";
        echo ">"._("Retour")."</a>";
        //
	}
    public function display_input($param) {
		//  
		//   lien 
		//
		$tmp="";
        $tmp=str_replace("<input ","<input data-role=\"button\"  data-mini=\"true\" data-theme=\"b\" data-inline=\"true\" ",$param['input']);
	    echo $tmp;
        //
	}
         
	    //

    public function display_start_liste_responsive() {
		//  
		//   liste responsive mobile	- general
		//
        echo "   <div class=\"ui-grid-c ui-responsive\" my-breakpoint>";
       //
	}
    public function display_start_block_liste_responsive($nbr_elements) {
		//  
		//   block liste responsive mobile	- general
		//
        $code_block="0abcdabcdabcdabcdabcd";
        
        $nom_block=substr($code_block,$nbr_elements,1);
        echo "<div class=\"ui-block-".$nom_block."\"><div class=\"ui-body ui-body-e\">";//remettre  e
	    //
	}
     public function display_start_block_liste_responsive_theme_c($nbr_elements) {
		//  
		//   block liste responsive mobile	- general
		//
        $code_block="0abcdabcdabcdabcdabcd";
        
        $nom_block=substr($code_block,$nbr_elements,1);
        echo "<div class=\"ui-block-".$nom_block."\"><div class=\"ui-body ui-body-c\">";//remettre  e
	    //
	}
    public function display_close_block_liste_responsive() {
		//  
		//   close block liste responsive mobile	- general
		//
        echo "</div>\n";
        echo "</div>\n";
		//
	}
	public function display_close_liste_responsive() {
		//  
		//   close liste responsive mobile	- general
		//
        echo "</div>\n";
	    //
	}
    public function display_start_navbar() {
		//  
		//   barre navigation -  mobile
		echo "<div data-role=\"navbar\" class=\"ui-body-titre-navbar\"  >";
        
	    //
	}
	public function display_stop_navbar() {
		//  
		//   barre navigation -  mobile
		 echo "</div>";
	    //
	}
    public function display_start_conteneur_grille() {
		//  
		//   grille de mise en page(colonne) -  mobile
		//
        echo "<div class=\"ui-grid-a\">";
	    //
	}
	public function display_start_conteneur_block() {
		//  
		//   grille de mise en page(block) -  mobile
		//
        echo "<div class=\"ui-block-a\">";
	    //
	}
	public function display_close_conteneur_block() {
		//  
		//   grille de mise en page(block) -  mobile
		//
        echo "</div>";
	    //
	}
	public function display_close_conteneur_grille() {
		//  
		//   grille de mise en page(colonne) -  mobile
		//
        echo "</div>";
	    //
	}
   
    public function display_formulaire_css() {
		//  
		//   class formulaire
		// 
		//
	}
    public function display_table_lien_data_colonne_une($params) {
		//  
		//   tableau :  lien colonne une -> consulter,.....
		//
        echo "<a  data-role=\"none\" ";
		if ($params["onglet"] == false or $params["no_ajax"] == true) {
	
			echo "href=\"".$params["lien"].urlencode($params["row"]).
				 (isset($params["id"]) ? $params["id"] : "")."\"";
            echo "id=\"".$params["identifier"]."\"";
			// Gestion de l'attribut target
			if (isset($params["target"])
				and $params["target"] == '_blank') {
				echo " target=\"_blank\" ";
			}
	
		// En visualisation par onglet ..
		} else {
	
			// Gestion de l'attribut targetdisplay_dbform_lien_retour_sousformulaire
			if (isset($params["target"])
				and $params["target"] == "_blank") {
	
				echo "href=\"".$params["lien"].
					 urlencode($params["row"]).$params["id"]."\"";
				echo " target=\"_blank\" ";
	
			// Sans target, rechargement du bloc en ajax
			} else {
				echo "href=\"";
				echo "#";
				echo "\" ";
				echo " onclick=\"ajaxIt('";
				echo $params["obj"]."','";
				echo $params["lien"].urlencode($params["row"]);
				echo $params["id"];
				echo "');\"";
			}
		}
	
		echo ">";
		//
        $tmppos = strpos(strtoupper($params["lib"]), 'CONSULTER');
        if ($tmppos  !== false) {
           echo "<img src='../img/consulter.png'>";
        }else{
           $tmppos = strpos(strtoupper($params["lib"]), 'COPIER');
           if ($tmppos !== false) {
             echo "<img src='../img/dupliquer.png'>";
           }else{
             echo "[".strtoupper($params["lib"])."]";
           }
       }
     
        echo "</a>";
		echo "&nbsp;&nbsp;";
	    //
	}
    public function display_table_lien_entete_colonne_une($params) {
		//  
		//   tableau :  entete -> lien colonne une ->creation...
		//
		echo "<a data-role=\"none\" href=\"";
		if ($params["onglet"]) {
			echo "#";
			echo "\" ";
			echo " onclick=\"ajaxIt('".$params["obj"]."','";
			echo $params["lien"].$params["id"];
			echo "');";
		} else {
			echo $params["lien"].$params["id"];
		}

		echo "\"";
        echo " id=\"".$params["identifier"]."\"";
		echo ">";
		//echo $params["lib"];
         // a faire si ajouter -> img ajouter ect ...
		echo "<img src=\"../img/plus_ajax.png\">";
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
        //  table en mode Column toggle
        //  ajout les priorites d'affichage des colonnes du tableau
        // et fermeture balise th -  entete table 
        if($param["key"]<7)
             $keycol=$param["key"];
        else
             $keycol=6;
        echo "\" data-priority=".$keycol;
       // fermeture balise th -  entete table 
		echo ">";
	    //
	}
    public function display_dbform_lien_retour_sousformulaire($params) {
		//  
		//  lien retour sous formulaire
		//
		echo "\n<a  data-role=\"button\" data-icon=\"back\" data-inline=\"true\" data-theme=\"d\" data-mini=\"true\"  ";
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
     public function display_form_lien_onglet_accordion($params) {
		//  
		//  lien onglets accordion
		//
        echo "<h3>";/* <-  IMPORTANT*/
        echo "<a  data-role=\"button\" data-mini=\"true\" data-inline=\"true\" data-theme=\"b\"";
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
        echo "</h3>";/* <-  IMPORTANT*/
        //
	}
   public function display_form_start_conteneur_chaque_onglet_accordion() {
		//  
		//  lien onglets accordion
		//
		 echo "<div data-role=\"collapsible\"  data-collapsed=\"true\" data-content-theme=\"e\" data-theme=\"d\">\n";
	    //
	}
    public function display_form_close_conteneur_chaque_onglet_accordion() {
		//  
		//  conteneur de chaque onglet  accordion
		//
		 echo  "</div>";
         //
	}
    public function display_formulaire_text($params) {
		//  
		//  formulaire champs texte
		//
	    echo "<input data-role=\"none\" ";
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
		//  formulaire champs  upload
		//
	 echo "<input data-role=\"none\" type=\"text\"";
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

    // }}}

    // {{{ NEW

    /**
     *
     */
    function display_list($params) {
        //
        printf(
            '<ul 
                data-role="listview" 
                data-filter="true" 
                data-filter-placeholder="%s"
                data-inset="true" 
                data-divider-theme="a"
                >',
                (isset($params["title"]) ? $params["title"] : "")
        );
        //
        // if (isset($params["title"])) {
        //     //
        //     printf(
        //         '<li data-role="list-divider">%s</li>',
        //         $params["title"]
        //     );
        // }
        //
        foreach($params["list"] as $key => $value) {
            //
            echo "<li>";
            $this->display_link($value);
            //
            if (isset($value["links"]) && is_array($value["links"]) && count($value["links"]) > 0) {
                //
                foreach ($value["links"] as $link) {
                    //
                    $this->display_link($link);
                }
            }
            echo "</li>";
        }
        //
        printf(
            '</ul>'
        );
    }

    // }}}

}

?>
