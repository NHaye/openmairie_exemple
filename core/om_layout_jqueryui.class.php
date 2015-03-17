<?php
/**
 * Ce fichier ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_layout_jqueryui.class.php 2943 2014-10-27 12:55:04Z fmichon $
 */

class layout_jqueryui extends layout_base {

    /**
     *
     */
    var $layout = "jqueryui";

    /**
     *
     */
    var $html_head_css = array(
        10 => array(
            "../lib/jquery-thirdparty/jquery-minicolors/jquery.minicolors.css",
        ),
        20 => array(
            "../css/layout_jqueryui_before.css",
            "../om-theme/jquery-ui-theme/jquery-ui.custom.css",
            "../om-theme/om.css",
        ),
        30 => array(
            "../app/css/app.css",
        ),
    );

    /**
     *
     */
    var $html_head_js = array();

    public function __construct($layout_type = null) {
        $this->html_head_js = array(
            10 => array(
                "../js/iepngfix_tilebg.js",
                "../lib/jquery/jquery.min.js",
                "../lib/jquery-thirdparty/jquery.form.js",
                "../lib/jquery-thirdparty/jquery.collapsible.js",
                "../lib/jquery-thirdparty/jquery-minicolors/jquery.minicolors.min.js",
                "../lib/jquery-thirdparty/jquery.autosize-min.js",
            ),
            20 => array(
                "../js/layout_jqueryui_before.js",
                "../lib/jquery-ui/jquery.ui.datepicker-fr.min.js",
                "../lib/tinymce/tinymce.min.js",
                "../lib/jquery-ui/jquery-ui.min.js",
                "../js/".LOCALE.".js",
                "../js/layout_jqueryui_after.js",
            ),
            30 => array(
                "../app/js/script.js",
            )
        );
    }

    // {{{ STRUCTURE GENERALE DE LA PAGE

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
            echo " class=\"ui-widget";
            echo " ".$this->get_parameter("style_header");
            echo "\"";
            echo ">\n";
            // Logo
            $this->display_logo();
            // Actions personnelles
            $this->display_actions();
            // Raccourcis
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
        echo " class=\"ui-widget ui-widget-content ui-corner-all";
        // XXX
        if ($this->get_parameter("flag") == "htmlonly"
            || $this->get_parameter("flag") == "htmlonly_nodoctype"
            || $this->get_parameter("flag") == "nohtml"
            || count($this->get_parameter("menu")) == 0) {
             echo " nomenu";
        }
        echo "\"";
        echo ">\n\n";
    }

    // }}} STRUCTURE GENERALE DE LA PAGE - END

    public function display_start_fieldset($params = array()) {
        // XXX Rétro-compatibilité
        if (count($params) == 0) {
            //
            echo "<fieldset class=\"collapsible\">\n";
            //
            echo "\t<legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
            //
            return;
        }
        //
        if (!isset($params["fieldset_class"])) {
            $params["fieldset_class"] = "";
        }
        $params["fieldset_class"] .= " cadre ui-corner-all ui-widget-content";
        //
        if (!isset($params["legend_class"])) {
            $params["legend_class"] = "";
        }
        $params["legend_class"] .= " ui-corner-all ui-widget-content ui-state-active";
        //
        parent::display_start_fieldset($params);
    }

    // {{{ NEW

    /**
     *
     */
    function display_list($params) {
        // Ouverture du fieldset
        $this->display_start_fieldset(array(
            "legend_content" => $params["title"],
            "fieldset_class" => (isset($params["class"]) ? $params["class"] : ""),
        ));
        //
        //echo "<ul>";
        foreach($params["list"] as $key => $value) {
            echo "<div class=\"choice ui-corner-all ui-widget-content\">\n";
            //echo "<li>";
            $this->display_link($value);
            //
            if (isset($value["links"]) && is_array($value["links"]) && count($value["links"]) > 0) {
                //
                foreach ($value["links"] as $link) {
                    //
                    $this->display_link($link);
                }
            }
            //echo "</li>";
            echo "</div>";
        }
        //echo "</ul>";
        // Fermeture du fieldset
        $this->display_stop_fieldset();
    }

    // }}}

}

?>
