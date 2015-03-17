<?php
/**
 * Ce script permet ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: widgetctl.php 2941 2014-10-24 06:52:31Z fmichon $
 */

require_once "../obj/utils.class.php";
if (!isset($f)) {
    // Si ce script n'est pas appelé en include depuis un autre fichier
    // alors on instancie utils et on vérifie que l'utilisateur a les
    // permissions pour gérer les tableaux de bord
    $f = new utils("nohtml", "om_dashboard");
    $f->disableLog();
    header("Content-type: text/html; charset=".HTTPCHARSET."");
}

/**
 *
 */
function widgetView($f = NULL,
                    $id = NULL,
                    $title = NULL,
                    $content = NULL,
                    $footer = NULL,
                    $type = NULL,
                    $mode_edit = false) {

    //
    if ($type == "file" 
        && !file_exists("../app/widget_".$footer.".php")) {
        //
        return;
    }

    //
    $class_sup = "";

    //
    if ($type == "file") {
        //
        $class_sup = "widget_".$footer;
        //
        $file =  "../app/widget_".$footer.".php";
        $footer = "#";
        // Enclenchement de la tamporisation de sortie
        ob_start();
        //
        include $file;
        //
        $content = ob_get_clean();
        //
        if (isset($widget_is_empty) 
            && $widget_is_empty == true
            && $mode_edit != true) {
            //
            return;
        }
    }

    // Ouverture du conteneur du widget
    echo "<div";
    echo " class=\"widget ui-widget ui-widget-content ui-helper-clearfix ui-corner-all ".$class_sup."\"";
    echo " id=\"widget_".$id."\"";
    echo ">\n";
    
    // Titre du widget
    echo "<div class=\"widget-header ";
    if ($mode_edit == true) {
        echo "widget-header-edit widget-header-move ";
    }
    echo "ui-widget-header ui-corner-all\">";
    echo "<h3>";
    echo $title;
    echo "</h3>";
    echo "</div>\n";
    
    // Ouverture du wrapper : Contenu + Footer
    echo "<div class=\"widget-content-wrapper\">\n";
    
    // Contenu du widget
    echo "<!-- Start Widget Content -->\n";
    echo "<div class=\"widget-content\">\n\n";
    //
    echo $content;
    //
    echo "\n\n</div>\n";
    echo "<!-- End Widget Content -->\n";
    
    // Footer du widget
    if ($footer != "#" && $footer != "" && $footer != NULL) {
        echo "<div class=\"widget-footer\">\n";
        echo "<a href='".$footer."' >";
        if (isset($footer_title)) {
            echo $footer_title;
        } else {
            echo _("Acceder au lien");
        }
        echo "</a>\n";
        echo "</div>\n";
    }
    
    // Fermeture du wrapper : Contenu + Footer
    echo "</div>\n";
    
    // Fermeture du conteneur du widget
    echo "</div>\n";

}


/**
 * UPDATE
 */
if (isset($_GET['action']) && $_GET['action'] == "update") {
    //
    $alldata = array();
    foreach($_GET as $key => $values) {
        // Dans le tableau associatif $_GET il y a la cle 'action' necessaire
        // au fonctionnement du script, donc si c'est le cas on passe a
        // l'iteration suivante
        if ($key == "action") {
            continue;
        }
        //
        $bloc = "C".str_replace("column_", "", $key);
        //
        $widgets = explode("x", $values);
        //
        foreach($widgets as $i => $widget) {
            //
            $position = $i+1;
            //
            $widget = str_replace("widget_", "", $widget);
            // Lorsqu'une colonne est vide, il y a une valeur vide dans le
            // tableau widget, donc si c'est le cas on passe a l'iteration
            // suivante
            if ($widget == "") {
                continue;
            }
            //
            array_push($alldata, array($position, $bloc, $widget));
        }
    }
    
    //
    $sql = "update ".DB_PREFIXE."om_dashboard set ";
    $sql .= " position=?, ";
    $sql .= " bloc=? ";
    $sql .= " where om_dashboard=? ";
    //
    $sth = $f->db->prepare($sql);
    // Vérification d'une éventuelle erreur de base de données
    $f->isDatabaseError($sth);
    // Exécution de la requête
    $res = $f->db->executeMultiple($sth, $alldata);
    // Logger
    $f->addToLog("../spg/widgetctl.php: db->executeMultiple(\"".$sth."\", ".print_r($alldata, true).");", VERBOSE_MODE);
    // Vérification d'une éventuelle erreur de base de données
    $f->isDatabaseError($res);
}

/**
 * DELETE
 */
if (isset($_GET['action']) && $_GET['action'] == "delete") {
    //
    if (isset($_GET['widget']) && $_GET['widget'] != "") {
        //
        $widget = str_replace("widget_", "", $_GET['widget']);
        // Suppression du widget
        $sql = "delete from ".DB_PREFIXE."om_dashboard where om_dashboard = ".intval($widget);
        // Exécution de la requête
        $res = $f->db->query($sql);
        // Logger
        $f->addToLog("../spg/widgetctl.php: db->query(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $f->isDatabaseError($res);
    }
}

/**
 * INSERT
 */
if (isset($_GET['action']) && $_GET['action'] == "insert") {
    //
    $bloc = "C1";
    //
    (isset($_GET["profil"]) ? $profil = str_replace("dashboard_profil_", "", $_GET['profil']) : $profil = 0);
    // Sur la validation du formulaire
    if (isset($_POST['widget_add_form_valid']) && isset($_POST['widget']) && is_numeric($_POST['widget'])) {
        // Ajout du widget dans la base et affichage de ce dernier
        //
        (isset($_POST['widget']) && is_numeric($_POST['widget']) ? $widget = $_POST['widget'] : $widget = 0);
        //
        (isset($_POST['profil']) && is_numeric($_POST['profil']) ? $profil = $_POST['profil'] : $profil = 0);
        //
        $valF = array();
        //
        $valF['om_dashboard'] = $f->db->nextId(DB_PREFIXE."om_dashboard");
        // Logger
        $f->addToLog("../spg/widgetctl.php: db->nextId(\"".DB_PREFIXE."om_dashboard\");", VERBOSE_MODE);
        //
        $valF['om_profil'] = $profil;
        $valF['om_widget'] = $widget;
        $valF['bloc'] = $bloc;
        $valF['position'] = 1;
        // XXX
        $sql = "update ".DB_PREFIXE."om_dashboard set position=position+1 where om_profil = ".intval($profil)." and bloc ='".$f->db->escapeSimple($bloc)."'";
        // Exécution de la requête
        $position = $f->db->query($sql);
        // Logger
        $f->addToLog("../spg/widgetctl.php: db->query(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $f->isDatabaseError($position);
        // Exécution de la requête
        $res = $f->db->autoExecute(DB_PREFIXE."om_dashboard", $valF, DB_AUTOQUERY_INSERT);
        // Logger
        $f->addToLog("../spg/widgetctl.php: db->autoExecute(\"".DB_PREFIXE."om_dashboard\", ".print_r($valF, true).", DB_AUTOQUERY_INSERT);", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $f->isDatabaseError($res);
        // On retourne l'id du widget dans le tableau de bord de l'utilisateur
        // pour l'afficher
        echo $valF['om_dashboard'];
    } elseif (!isset($_POST['widget_add_form_valid'])) {
        // Composition du formulaire
        $content = "";
        // Description du formulaire
        $content .= _("Selectionner le widget a inserer puis cliquer sur ".
                      "le bouton 'Valider' pour valider votre selection.");
        // Ouverture du formulaire
        $content .= "<form";
        $content .= " method=\"post\"";
        $content .= " id=\"widget_add_form\"";
        $content .= " action=\"../tdb/ajouter.php?bloc=".$bloc."\"";
        $content .= ">\n";
        // On recupere la liste des widgets que l'utilisateur peut inserer en
        // fonction de son profil
        $sql = "select om_widget as widget, libelle from ".DB_PREFIXE."om_widget ";
        $sql .= " order by libelle";
        // Exécution de la requête
        $res = $f->db->query($sql);
        // Logger
        $f->addToLog("../spg/widgetctl.php: db->query(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $f->isDatabaseError($res);
        // Liste des widgets que l'utilisateur peut inserer en fonction de son
        // profil
        $content .= "<select name=\"widget\">";
        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $content .= "<option value='".$row['widget']."' >".$row['libelle']."</option>";
        }
        $content .= "</select>\n";
        // Valeur du profil
        $content .= "<input id=\"widget_add_form_profil\" type=\"hidden\" value=\"".$profil."\" name=\"profil\" />\n";
        // Bouton Valider
        $content .= "<input type=\"button\" value=\""._("Valider")."\" name=\"widget.add.form.valid\" onclick=\"widget_add_form_post()\" />\n";
        // Fermeture du formulaire
        $content .= "</form>\n";
        
        // Affichage du widget
        widgetView($f,
                   "",
                   _("Ajouter un nouveau widget"),
                   $content,
                   "",
                   "web",
                   true);

    } else {
        echo "null";
    }
}

/**
 * VIEW
 */
if (isset($_GET['action']) && $_GET['action'] == "view") {
    // Requete de selection du widget
    $sql = " SELECT ";
    $sql .= " om_dashboard.om_dashboard, ";
    $sql .= " om_widget.om_widget as widget, ";
    $sql .= " om_widget.libelle as libelle, ";
    $sql .= " om_widget.lien as lien, ";
    $sql .= " om_widget.texte as texte, ";
    $sql .= " om_widget.type as type, ";
    $sql .= " om_dashboard.position ";
    $sql .= " FROM ".DB_PREFIXE."om_dashboard ";
    $sql .= " INNER JOIN ".DB_PREFIXE."om_widget on om_dashboard.om_widget=om_widget.om_widget ";
    $sql .= " WHERE ";
    $sql .= " om_dashboard.om_dashboard=".intval($_GET['widget'])." ";
    // Exécution de la requête
    $res = $f->db->query($sql);
    // Logger
    $f->addToLog("../spg/widgetctl.php: db->query(\"".$sql."\");", VERBOSE_MODE);
    // Vérification d'une éventuelle erreur de base de données
    $f->isDatabaseError($res);
    //
    $row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
    //
        widgetView($f, $row['om_dashboard'], $row['libelle'], $row['texte'],
                   $row['lien'], $row["type"], true);
}

?>