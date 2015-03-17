<?php
/**
 * Ce script permet d'effectuer une correlation entre deux champs d'apres la
 * saisie d'une valeur dans un champ d'origine correle au travers d'une table
 * un autre champ
 *
 * @package openmairie_exemple
 * @version SVN : $Id: combo.php 2925 2014-10-10 16:58:40Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

/**
 * Affichage de la structure HTML
 */
//
$f->setFlag("htmlonly");
$f->display();
//
$f->displayStartContent();

/**
 * Parametres
 */
//
$DEBUG = 0;
//
$nbligne = 0;
// debut = 0 recherche sur la chaine / debut = 1 recherche sur le debut de la chaine
$debut = 0 ;
//
$longueurRecherche = 1;
//
$sql = "";
$z='';
// table sur laquelle se fait la correlation / table sur lequel s effectue la recherche
(isset($_GET['table']) ? $table = $_GET['table'] : $table = "");
// zone d'origine de la correlation / champ de recherche sur la table
(isset($_GET['zorigine']) ? $zoneOrigine = $_GET['zorigine'] : $zoneOrigine = "");
// zone qui sera mise à jour par la correlation / champ en relation
(isset($_GET['zcorrel']) ? $zoneCorrel = $_GET['zcorrel'] : $zoneCorrel = "");
// caracteres saisis dans la zone d'origine / valeur du champ origine a rechercher
(isset($_GET['recherche']) ? $recherche = $_GET['recherche'] : $recherche = "");
// valeur affectée à la zone d'origine / champ d origine => affectation de la valeur validee
(isset($_GET['origine']) ? $champOrigine = $_GET['origine'] : $champOrigine = "");
// valeur affectée à la zone correllée / champ a affecter la valeur validee
(isset($_GET['correl']) ? $champCorrel = $_GET['correl'] : $champCorrel = "");
// parametres de selection / champ de selection (clause where)
(isset($_GET['correl2']) ? $champCorrel2 = $_GET['correl2'] : $champCorrel2 = "");
// parametres de selection / valeur du champ de selection (clause where)
(isset($_GET['zcorrel2']) ? $zoneCorrel2 = $_GET['zcorrel2'] : $zoneCorrel2 = "");
//
(isset($_GET['form']) ? $form = $_GET['form'] : $form = "f1");

/**
 * Vérification des paramètres : table - zorigine - correl2
 */
//
$error = false;
// On instancie l'utilitaire de génération
require_once PATH_OPENMAIRIE."om_gen.class.php";
$g = new gen();
// On récupère la liste de toutes les tables de la base de données
$tables = $g->get_all_tables_from_database();
// On vérifie que la table passée en paramètre existe
if (!in_array($table, $tables)) {
    $error = true;
}
if ($error == false) {
    // On récupère la liste de tous les champs de la table
    $fields = $g->get_fields_list_from_table($table);
    //
    if (!in_array($zoneOrigine, $fields)) {
        $error = true;
    }
    //
    if ($zoneCorrel2 != "" && !in_array($champCorrel2, $fields)) {
        $error = true;
    }
}
//
if ($error == true) {
    $message = _("Erreur de parametres.");
    $f->displayMessage("error", $message);
    $f->displayEndContent();
    die();
}

// parametrage de $sql = requete de recherche specifique
// $longueurRecherche  = longueur autorise en recherche
// $debut = rrecherche au debut de zone ou compris dans la zone
if (file_exists("../dyn/comboparametre.inc.php")) {
    include "../dyn/comboparametre.inc.php";
}

// Log
$debug_infos = array(
    "champOrigine" => $champOrigine,
    "recherche" => $recherche,
    "table" => $table,
    "zoneOrigine" => $zoneOrigine,
    "zoneCorrel" => $zoneCorrel,
    "champCorrel" => $champCorrel,
    "zoneCorrel2" => $zoneCorrel2,
    "champCorrel2" => $champCorrel2
);
$f->addToLog("spg/combo.php: ".print_r($debug_infos, true), EXTRA_VERBOSE_MODE);

/**
 *
 */
//
echo "<form name=\"f3\" method=\"post\" action=\"../spg/combo.php\">";
//
if (strlen($recherche) > $longueurRecherche) {  
    /**
     * Construction de la requete
     */
    //
    if ($sql == "") {
        // Log
        $f->addToLog("spg/combo.php: Construction de la requete standard", EXTRA_VERBOSE_MODE);
        if ($debut == 0) {
            $sql = "select * from ".DB_PREFIXE.$table." where ".$zoneOrigine." like '%".$f->db->escapeSimple($recherche)."%'";
        } else {
            $sql = "select * from ".DB_PREFIXE.$table." where ".$zoneOrigine." like '".$f->db->escapeSimple($recherche)."%'";
        }
    }
    // 
    if ($zoneCorrel2 != "") {
        $sql .= " and ".$champCorrel2." like '".$f->db->escapeSimple($zoneCorrel2)."'";
    }
    //
    if ($DEBUG == 1) {
        echo $sql;
    }
    
    /**
     * Execution de la requete
     */
    //
    $res = $f->db->query($sql);
    $f->addToLog("spg/combo.php: db->query(\"".$sql."\");", VERBOSE_MODE);
    $f->isDatabaseError($res);
    //
    $nbligne = $res->numrows();
    //
    switch($nbligne) {
        case 0 : 
            //
            $message = _("Votre saisie ne donne aucune correspondance");
            $f->displayMessage("error", $message);
            //
            break;
        case 1 :
            //
            while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                // dans la zone correllee
                $x = $row[$zoneCorrel];
                // dans la zone d'origine
                $y = $row[$zoneOrigine];
                // parametrage des retour dans les champs $x et $y
                if (file_exists("../dyn/comboretour.inc.php")) {
                    include "../dyn/comboretour.inc.php";
                }
            }
            // Envoi des donnees dans le formulaire de la fenetre parent
            echo "\n<script type=\"text/javascript\">\n";
            echo "opener.document.".$form.".".$champCorrel.".value = \"".$x."\";\n";
            echo "opener.document.".$form.".".$champOrigine.".value = \"".$y."\";\n";
            if($champCorrel2 != '') {
                echo "if (opener.document.".$form.".".$champCorrel2." != undefined) {\n";
                echo "opener.document.".$form.".".$champCorrel2.".value = \"".$z."\";\n";
                // Simulation d'un event onchange
                echo "el = opener.document.".$form.".".$champCorrel2.";\n";
                echo "if(document.createEvent) {\n"; // if(!IE)
                
                echo "  ev = document.createEvent('Event');\n";
                echo "  ev.initEvent('change', true, false);\n";
                echo "  el.dispatchEvent(ev);\n";
                echo "} else {\n";
                echo "  el.fireEvent( 'onchange');\n";
                echo "}\n";

                echo "}\n";
            }
            // Simulation d'un event onchange
            echo "el = opener.document.".$form.".".$champCorrel.";\n";

            echo "if(document.createEvent) {\n"; // if(!IE)
            echo "  ev = document.createEvent('Event');\n";
            echo "  ev.initEvent('change', true, false);\n";
            echo "  el.dispatchEvent(ev);\n";
            echo "} else {\n";
            echo "  el.fireEvent( 'onchange');\n";
            echo "}\n";
            // Simulation d'un event onchange
            echo "el = opener.document.".$form.".".$champOrigine.";\n";
            echo "if(document.createEvent) {\n"; // if(!IE)
            echo "  ev = document.createEvent('Event');\n";
            echo "  ev.initEvent('change', true, false);\n";
            echo "  el.dispatchEvent(ev);\n";
            echo "} else {\n";
            echo "  el.fireEvent( 'onchange');\n";
            echo "}\n";

            echo "this.close();\n";
            echo "</script>\n";
            //
            break;
        default :
            //
            echo "\n<div class=\"instructions\">\n";
            echo "<p>\n";
            echo _("Selectionner dans la liste ci-dessous la correspondance avec ".
                   "votre recherche")." ".$champOrigine.". ";
            echo _("Puis valider votre choix en cliquant sur le bouton : \"Valider\".");
            echo "</p>\n";
            echo "</div>\n";
            //
            echo "<select size=\"1\" name=\"".$champOrigine."\" class=\"champFormulaire\">\n";
            while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                // dans la zone correllee
                $x = $row[$zoneCorrel];
                // dans la zone d'origine
                $y = $row[$zoneOrigine];
                // affichage
                $aff = $row[$zoneCorrel]." - ".$row[$zoneOrigine];
                // defintion du retour  unique d apres la table select = $retourUnique
                // definition affichage en table = $aff
                if (file_exists("../dyn/comboaffichage.inc.php")) {
                    include "../dyn/comboaffichage.inc.php";
                }
                //
                $opt = "<option value=\"".$x."£".$y."£".$z."\">";
                $opt .= $aff;
                $opt .= "</option>\n";
                //
                echo $opt;
            }
            echo "</select>\n";
            // Envoi des donnees dans le formulaire de la fenetre parent
            echo "\n<script type=\"text/javascript\">\n";
            echo "function recup()\n{\n";
            echo "var s = document.f3.".$champOrigine.".value;\n";
            echo "var x = s.split( \"£\" );\n";
            echo "opener.document.".$form.".".$champOrigine.".value = x[1];\n";
            echo "opener.document.".$form.".".$champCorrel.".value = x[0];\n";
            if($champCorrel2 != '') {
                echo "if (opener.document.".$form.".".$champCorrel2." != undefined) {\n";
                echo "opener.document.".$form.".".$champCorrel2.".value = x[2];\n";

                echo "el = opener.document.".$form.".".$champCorrel2.";\n";
                echo "if(document.createEvent) {\n"; // if(!IE)
                
                echo "  ev = document.createEvent('Event');\n";
                echo "  ev.initEvent('change', true, false);\n";
                echo "  el.dispatchEvent(ev);\n";
                echo "} else {\n";
                echo "  el.fireEvent( 'onchange');\n";
                echo "}\n";

                echo "}\n";
            }
            echo "el = opener.document.".$form.".".$champCorrel.";\n";

            echo "if(document.createEvent) {\n"; // if(!IE)
            echo "  ev = document.createEvent('Event');\n";
            echo "  ev.initEvent('change', true, false);\n";
            echo "  el.dispatchEvent(ev);\n";
            echo "} else {\n";
            echo "  el.fireEvent( 'onchange');\n";
            echo "}\n";
            echo "el = opener.document.".$form.".".$champOrigine.";\n";
            echo "if(document.createEvent) {\n"; // if(!IE)
            echo "  ev = document.createEvent('Event');\n";
            echo "  ev.initEvent('change', true, false);\n";
            echo "  el.dispatchEvent(ev);\n";
            echo "} else {\n";
            echo "  el.fireEvent( 'onchange');\n";
            echo "}\n";

            echo "this.close();\n}\n";
            echo "</script>\n";
            //
            echo "<div class=\"formControls\">\n";
            echo "<input type=\"submit\" tabindex=\"70\" value=\""._("Valider")."\" onclick=\"javascript:recup();\" class=\"boutonFormulaire\" />\n";
            break;
    }
    
} else {
    
    //
    $message = _("Vous devez saisir une valeur d'au moins");
    $message .= " ".($longueurRecherche+1)." ";
    $message .= _("caracteres dans le champ");
    $message .= " ".$champOrigine.".";
    $f->displayMessage("error", $message);
    
}
//
if ($nbligne < 1) {
    echo "<div class=\"formControls\">\n";
}
$f->displayLinkJsCloseWindow();
echo "</div>\n";
//
echo "</form>";

/**
 *
 */
//
$f->displayEndContent();

?>
