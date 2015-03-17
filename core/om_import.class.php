<?php
/**
 * Ce script permet de déclarer la classe import.
 *
 * @package openmairie_exemple
 * @version SVN : $Id$
 */

/**
 * Définition de la classe import.
 *
 * Cette classe gère le module d'import du framework. Ce module permet 
 * l'intégration de données dans l'applicatif depuis des fichiers CSV.
 * Chaque import disponible est paramétré dans un fichier de configuration
 * qui peut être composé manuellement ou généré depuis le générateur.
 */
class import {

    /**
     * Instance de la classe utils
     * @var resource 
     */
    var $f = null;

    /**
     *
     */
    var $import_list = null;

    /**
     *
     */
    var $script_path = "../scr/import.php";

    /**
     * Constructeur.
     */
    function __construct() {
        // 
        if (isset($GLOBALS["f"])) {
            $this->f = $GLOBALS["f"];
        }
    }

    /**
     * Récupération de la liste des imports disponibles.
     *
     * Ces imports correspondent aux requêtes mémorisées paramétrées dans des 
     * scripts <import>.import.inc.php. Ces scripts sont généralement présents dans
     * le répertoire sql/<db_type>/ de l'application mais peuvent également être 
     * présents dans le répertoire CUSTOM prévu à cet effet.
     *
     * @return void
     */
    function compute_import_list() {
        // On définit le répertoire STANDARD où se trouvent les scripts des imports
        $dir = getcwd();
        $dir = substr($dir, 0, strlen($dir) - 4)."/sql/".OM_DB_PHPTYPE."/";
        // On récupère la liste des imports disponibles dans ce répertoire STANDARD
        $import_list = $this->get_import_list_in_folder($dir);
        //
        if ($this->f->get_custom("path", ".import.inc.php") != null) {
            // On définit le répertoire CUSTOM où se trouvent les scripts des imports
            $dir = $this->f->get_custom("path", ".import.inc.php");
            // On récupère la liste des imports disponibles dans ce répertoire CUSTOM
            $import_list = $this->get_import_list_in_folder($dir, $import_list);
        }
        // On tri la liste des imports disponibles par ordre alphabétique
        uasort($import_list, array($this, "sort_by_lower_title"));
        //
        $this->import_list = $import_list;
    }

    /**
     * Accesseur.
     *
     * @return array
     */
    function get_import_list() {
        //
        if (is_null($this->import_list)) {
            $this->compute_import_list();
        }
        //
        return $this->import_list;
    }

    /**
     * Affichage de la liste des imports disponibles.
     *
     * @return void
     */
    function display_import_list() {
        //
        echo "\n<div id=\"import-list\">\n";
        // Composition de la liste de liens vers les imports disponibles.
        // En partant de la liste des imports disponibles, on compose une liste 
        // d'éléments composés d'une URL, d'un libellé, et de tous les paramètres 
        // permettant l'affichage de l'élément comme un élément de liste.
        $list = array();
        foreach ($this->get_import_list() as $key => $value) {
            //
            $list[] = array(
                "href" => "../scr/import.php?obj=".$key,
                "title" => $value["title"],
                "class" => "om-prev-icon import-16",
            );
        }
        //
        $this->f->layout->display_list(
            array(
                "title" => _("choix de l'import"),
                "list" => $list,
            )
        );
        //
        echo "</div>\n";
    }

    /**
     * Affichage du formulaire d'import.
     *
     * @param string $obj Identifiant de l'import.
     *
     * @return void
     */
    function display_import_form($obj) {
        //
        (defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
        require_once PATH_OPENMAIRIE."om_formulaire.class.php";
        //
        echo "\n<div id=\"form-csv-import\" class=\"formulaire\">\n";
        echo "<form ";
        echo " action=\"".$this->script_path."?obj=".$obj."\" ";
        echo " method=\"post\" ";
        echo " name=\"f2\">\n";
        //
        $champs = array("fic1", "separateur");
        //
        $form = new formulaire(null, 0, 0, $champs);
        //
        $form->setLib("fic1", _("Fichier CSV"));
        $form->setType("fic1", "upload2");
        $form->setTaille("fic1", 64);
        $form->setMax("fic1", 30);
        // Restriction sur le champ d'upload
        $params = array(
            "constraint" => array(
                "extension" => ".csv;.txt"
            ),
        );
        $form->setSelect("fic1", $params);
        //
        $form->setLib("separateur", _("Separateur"));
        $form->setType("separateur", "select");
        $separator_list = array(
            0 => array(";", ",", ),
            1 => array("; "._("(point-virgule)"), ", "._("(virgule)")),
        );
        $form->setSelect("separateur", $separator_list);
        //
        $form->entete();
        $form->afficher($champs, 0, false, false);
        $form->enpied();
        //
        echo "\n<!-- ########## START FORMCONTROLS ########## -->\n";
        echo "<div class=\"formControls\">\n";
        echo "<input ";
        echo " type=\"submit\" ";
        echo " name=\"submit-csv-import\" ";
        echo " value=\""._("Importer")."\" ";
        echo " class=\"boutonFormulaire\" />\n";
        // Lien retour
        $this->f->layout->display_lien_retour(array(
            "href" => $this->script_path,
        ));
        echo "</div>\n";
        //
        echo "</form>\n";
        echo "</div>\n";
    }

    /**
     * Affichage de l'assistant d'import.
     *
     * @param string $obj Identifiant de l'import.
     *
     * @return void
     */
    function display_import_helper($obj) {
        // Récupération du fichier de paramétrage de l'import
        // XXX Faire un accesseur pour vérifier l'existence du fichier
        include $this->import_list[$obj]["path"];
        //
        if (isset($zone) && !isset($fields)) {
            return;
        }
        //
        echo "<fieldset class=\"cadre ui-corner-all ui-widget-content\">\n";
        //
        echo "\t<legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
        echo _("Structure du fichier CSV");
        echo "</legend>\n";
        // Lien vers le téléchargement d'un fichier CSV modèle
        echo "<div>";
        $this->f->layout->display_link(array(
            "href" => $this->script_path."?obj=".$obj."&amp;action=template",
            "title" => _("Télécharger le fichier CSV modèle"),
            "class" => "om-prev-icon reqmo-16",
            "target" => "_blank",
        ));
        echo "</div>";
        // Affichage des informations sur l'import
        echo "<table ";
        echo " class=\"table table-condensed table-bordered table-striped\" ";
        echo " id=\"structure_csv\">\n";
        //
        echo "<thead>\n";
        echo "<tr>";
        echo "<th>"._("Ordre")."</th>";
        echo "<th>"._("Champ")."</th>";
        echo "<th>"._("Type")."</th>";
        echo "<th>"._("Obligatoire")."</th>";
        echo "<th>"._("Defaut")."</th>";
        echo "<th>"._("Vocabulaire")."</th>";
        echo "</tr>\n";
        echo "</thead>\n";
        //
        $i = 1;
        //
        echo "<tbody>\n";
        foreach ($fields as $key => $field) {
            // Gestion du caractère obligatoire du champ
            (isset($field["notnull"]) && $field["notnull"] == true) ?
                $needed = true : $needed = false;
            echo "<tr>";
            // Ordre
            echo "<td><b>".$i."</b></td>";
            // Champ
            echo "<td>".$key."</td>";
            // Type
            echo "<td>";
            if (isset($field["type"])) {
                switch ($field["type"]) {
                    case 'blob':
                        echo "text";
                        break;
                    default:
                        echo $field["type"];
                        break;
                }
            }
            // Taille
            if (isset($field["len"])) { 
                if (!in_array($field["type"], array("blob", "geom", ))) {
                    echo " (";
                    echo $field["len"];
                    echo ")";
                }
            }
            echo "</td>";
            // Obligatoire si not null et si aucune valeur par défaut
            echo "<td>";
            if ($needed == true && !isset($field["default"])) { 
                echo "Oui";
            }
            echo "</td>";
            // Défaut
            echo "<td>";
            if (isset($field["default"])) { 
                echo "<i>".$field["default"]."</i>";
            }
            echo "</td>";
            // Vocabulaire
            echo "<td>";
            // Clé étrangère
            if (isset($field["fkey"])) {
                echo _("Cle etrangere vers")." : <a href=\"../scr/tab.php?obj=".$field["fkey"]["foreign_table_name"]."\">";
                echo $field["fkey"]["foreign_table_name"].".".$field["fkey"]["foreign_column_name"];
                echo "</a>";
                if (isset($field["fkey"]["foreign_key_alias"]) 
                    && isset($field["fkey"]["foreign_key_alias"]["fields_list"])) {
                    if (count($field["fkey"]["foreign_key_alias"]["fields_list"]) > 1) {
                        echo "<br/>=> "._("Valeurs alternatives possibles")." : ";
                    } else {
                        echo "<br/>=> "._("Valeur alternative possible")." : ";
                    }
                    echo implode(", ", $field["fkey"]["foreign_key_alias"]["fields_list"]);
                }
            }
            // Dates et booléens
            $field_info = "";
            if (isset($field["type"])) {
                switch ($field["type"]) {
                    case 'date':
                        $field_info = _("Format")." : '"._("YYYY-MM-DD")."'";
                        break;
                    case 'bool':
                        $field_info = _("Format")." :<br/>";
                        if ($needed == false) {
                            $field_info .= "'' "._("pour état null")."<br/>";
                        }
                        $field_info .= "'t', 'true', '1' "._("pour oui");
                        $field_info .= "<br/>";
                        $field_info .= "'f', 'false', '0' "._("pour non");
                        break;
                    default:
                        break;
                }
            }
            echo $field_info;
            echo "</td>";
            //
            echo "</tr>\n";
            $i++;
        }
        //
        echo "</tbody>\n";
        //
        echo "</table>\n";
        //
        echo "</fieldset>\n";
    }

    /**
     * Traitement d'import.
     *
     * @param string $obj Identifiant de l'import.
     *
     * @todo Modifier cette méthode pour la rendre générique et éventuellement
     *       utilisable depuis d'autres contextes que celui de la vue principale
     *       du module import.
     *
     * @return void
     */
    function treatment_import($obj) {

        // On vérifie que le formulaire a bien été validé
        if (!isset($_POST['submit-csv-import'])) {
            //
            return false;
        }

        // On vérifie que le fichier a bien été posté et qu'il n'est pas vide
        if (isset($_POST['fic1']) && $_POST['fic1'] == "") {
            //
            $class = "error";
            $message = _("Vous n'avez pas selectionne de fichier a importer.");
            $this->f->displayMessage($class, $message);
            //
            return false;
        }

        // On enlève le préfixe du fichier temporaire
        $fichier_tmp = str_replace("tmp|", "", $_POST['fic1']);
        // On récupère le chemin vers le fichier
        $path = $this->f->storage->storage->temporary_storage->getPath($fichier_tmp);
        // On vérifie que le fichier peut être récupéré
        if (!file_exists($path)) {
            //
            $class = "error";
            $message = _("Le fichier n'existe pas.");
            $this->f->displayMessage($class, $message);
            //
            return false;
        }

        // Configuration par défaut du fichier de paramétrage de l'import
        //
        $table = "";
        // Clé primaire numérique automatique. Si la table dans laquelle les
        // données vont être importées possède une clé primaire numérique 
        // associée à une séquence automatique, il faut positionner le nom du
        // champ de la clé primaire dans la variable $id. Attention il se peut 
        // que ce paramètre se checauche avec le critère OBLIGATOIRE. Si ce
        // champ est défini dans $zone et qu'il est obligatoire et qu'il est
        // en$id, les valeurs du fichier CSV seront ignorées.
        $id = "";
        // 
        $verrou = 1; // =0 pas de mise a jour de la base / =1 mise a jour
        //
        $fic_rejet = 1; // =0 pas de fichier pour relance / =1 fichier relance traitement
        //
        $ligne1 = 1; // = 1 : 1ere ligne contient nom des champs / o sinon

        // Récupération du fichier de paramétrage de l'import
        // XXX Faire un accesseur pour vérifier l'existence du fichier
        include $this->import_list[$obj]["path"];

        // On ouvre le fichier en lecture
        $fichier = fopen($path, "r");

        // Initialisation des variables
        $cpt = array(
            "total" => 0,
            "rejet" => 0,
            "insert" => 0,
            "firstline" => 0,
            "empty" => 0,
        );
        $rejet = "";

        // Boucle sur chaque ligne du fichier
        while (!feof($fichier)) {
            // Incrementation du compteur de lignes
            $cpt['total']++;
            // Logger
            $this->f->addToLog(__METHOD__."(): LINE ".$cpt['total'], EXTRA_VERBOSE_MODE);
            // On définit si on se trouve sur la ligne titre
            $firstline = ($cpt['total'] == 1 && $ligne1 == 1 ? true : false);
            // On définit le marqueur correct à true
            $correct = true;
            //
            $valF = array();
            $msg = "";

            // Récupération de la ligne suivante dans le fichier
            $input = fgets($fichier, 4096);
            // Définition des encodages sources possibles
            $encodages = array("UTF-8", "ASCII", "Windows-1252", "ISO-8859-15", "ISO-8859-1");
            // Encodage de la ligne en UTF-8
            $input = iconv(mb_detect_encoding($input, $encodages), "UTF-8", $input);
            // Transformation de la chaine de caractère en tableau suite aux informations CSV
            $contenu = str_getcsv($input, $_POST['separateur']);

            //
            $this->f->addToLog(__METHOD__."(): LINE ".$cpt['total']." - contenu = ".print_r($contenu, true), EXTRA_VERBOSE_MODE);
            // Si la ligne a plus d'un champ et que le premier champ n'est pas vide
            if (count($contenu) > 1 && $contenu[0] != "") { // enregistrement vide (RC à la fin)
                //
                if ($firstline == true) {
                    //
                    $cpt['firstline']++;
                    // Logger
                    $this->f->addToLog(__METHOD__."(): LINE ".$cpt['total']." - firstline", EXTRA_VERBOSE_MODE);
                } else {

                    // NG
                    if (isset($fields)) {

                        // On boucle sur chaque champ définit dans le fichier 
                        // de configuration de l'import pour récupérer la 
                        // valeur à importer dans la base pour ce champ
                        foreach ($fields as $key => $field) {

                            //
                            $key_num = array_search($key, array_keys($fields));
                            //
                            if (!isset($contenu[$key_num])) {
                                $valF[$key] = -1;
                                // On rejette l'enregistrement
                                $correct = false;
                                // Raison du rejet à ajouter dans le fichier rejet
                                $msg = _("nombre de colonnes incohérent")." : la colonne ".$key_num." n'existe pas";
                                // Logger
                                $this->f->addToLog(__METHOD__."(): REJET => ".$msg, EXTRA_VERBOSE_MODE);
                            } else {
                                // Suppression des espaces superflus
                                $valF[$key] = trim($contenu[$key_num]);
                                // la chaine de texte 'null' représente la valeur null
                                if (strtolower($valF[$key]) === "null") {
                                    $valF[$key] = null;
                                }
                            }

                            // Logger
                            $this->f->addToLog(__METHOD__."(): LINE ".$cpt['total']." - champ '".$key."' = '".$valF[$key]."'", EXTRA_VERBOSE_MODE);

                            // ----------------------------------------------------
                            // Gestion des valeurs par défaut lors de la 
                            // transmission d'une valeur vide ou nulle
                            // ----------------------------------------------------
                            // Si le paramétrage est défini correctement et que la 
                            // valeur transmise est vide ou nulle
                            if (isset($field["type"]) && isset($field["notnull"])
                                && $valF[$key] !== '0'
                                && (empty($valF[$key]) || is_null($valF[$key]))) {

                                // Si une valeur par défaut est proposée dans la paramétrage
                                if (isset($field["default"])) {
                                    // On affecte la valeur par défaut
                                    $valF[$key] = $field["default"];

                                // Si le champ est de type entier et qu'il n'est pas 
                                // obligatoire
                                } elseif ($field["type"] == "int"
                                          && $field["notnull"] == false) {
                                    // On affecte null afin de ne pas initialiser 
                                    // un champs entier avec une chaine vide ce qui 
                                    // provoquerait une erreur de base de données
                                    $valF[$key] = null;

                                // Si le champ est de type date et qu'il n'est pas 
                                // obligatoire
                                } elseif ($field["type"] == "date"
                                          && $field["notnull"] == false) {
                                    // On affecte null afin de ne pas initialiser 
                                    // un champs date avec une chaine vide ce qui 
                                    // provoquerait une erreur de base de données
                                    $valF[$key] = null;

                                // Si le champ est de type booléen et qu'il n'est pas 
                                // obligatoire
                                } elseif ($field["type"] == "bool"
                                          && $field["notnull"] == false) {
                                    // XXX Vérifier pourquoi la valeur null
                                    // provoque une erreur de base de données
                                    $valF[$key] = null;

                                // Si le champ est de type string et qu'il n'est pas 
                                // obligatoire et que ce champ est une clé étrangère
                                } elseif ($field["type"] == "string"
                                          && $field["notnull"] == false
                                          && isset($field["fkey"])) {
                                    // On affecte null car une chaine vide serait
                                    // considérait comme une clé primaire de l'autre
                                    // table ce qui provoquerait une erreur de base
                                    //  de données
                                    $valF[$key] = null;

                                // Si le champ est de type geom et qu'il n'est pas 
                                // obligatoire
                                } elseif ($field["type"] == "geom"
                                          && $field["notnull"] == false) {
                                    // On enlève le champ de l'enregistrement
                                    unset($valF[$key]);

                                }
                            }

                            // ----------------------------------------------------
                            // Vérification du caractère obligatoire d'un champ
                            // ----------------------------------------------------
                            if (isset($field["notnull"]) && $field["notnull"] == true
                                && (empty($valF[$key]) || is_null($valF[$key]))) {
                                // On rejette l'enregistrement
                                $correct = false;
                                // Raison du rejet à ajouter dans le fichier rejet
                                $msg = _("champ obligatoire vide")." : ".$key." = ".$valF[$key];
                                // Logger
                                $this->f->addToLog(__METHOD__."(): REJET => ".$msg, EXTRA_VERBOSE_MODE);
                            }

                            // ----------------------------------------------------
                            // Vérification de la taille des string
                            // ----------------------------------------------------
                            if (isset($field["type"]) && $field["type"] == "string"
                                && strlen($valF[$key]) > $field["len"]) {
                                // On rejette l'enregistrement
                                $correct = false;
                                // Raison du rejet à ajouter dans le fichier rejet
                                $msg = _("valeur trop longue pour le champ")." : ".$key."(".$field["len"].") = ".$valF[$key];
                                // Logger
                                $this->f->addToLog(__METHOD__."(): REJET => ".$msg, EXTRA_VERBOSE_MODE);
                            }

                            // ----------------------------------------------------
                            // Gestion du critère EXIST
                            // ----------------------------------------------------
                            // Ce critère permet de vérifier si la valeur fournie
                            // existe bien dans la table liée (clé étrangère). 
                            // Exemple du paramétrage de ce critère :
                            //   $exist = array(
                            //     "champ1" => 1, // Ce champ est lié et doit exister
                            //     "champ2" => 0, // Ce champ n'est pas lié
                            //   );
                            // Par défaut si le critère n'est pas paramétré, on 
                            // considère que le champ n'est pas lié.
                            // Comportement : si la valeur du champ en question
                            // dans le fichier CSV est vide alors on rejette 
                            // l'enregistrement
                            // ----------------------------------------------------
                            if (isset($field["fkey"]) && !empty($valF[$key])) {
                                // Logger
                                $this->f->addToLog(__METHOD__."(): LINE ".$cpt['total']." - champ '".$key."' - EXIST", EXTRA_VERBOSE_MODE);
                                // Si le champ recherché est sensé être de type entier et est n'est pas numeric
                                if (isset($field["type"]) && $field["type"] = "int" 
                                    && !is_numeric($valF[$key])
                                    && isset($field["fkey"]["foreign_key_alias"])) {
                                    $sql = str_replace("<SEARCH>", $valF[$key], $field["fkey"]["foreign_key_alias"]["query"]);
                                    $res = $this->f->db->getOne($sql);
                                    if (!is_null($res) && !$this->f->isDatabaseError($res, true)) {
                                        $valF[$key] = $res;
                                    } else {
                                        // On rejette l'enregistrement
                                        $correct = false;
                                        // Raison du rejet à ajouter dans le fichier rejet
                                        $msg = _("cle secondaire inexistante")." : ".$key." = ".$valF[$key]." / ";
                                        // Logger
                                        $this->f->addToLog(__METHOD__."(): REJET => ".$msg, EXTRA_VERBOSE_MODE);
                                    }
                                } else  {

                                    //
                                    $sql = $field["fkey"]["sql_exist"].$valF[$key];
                                    if (strrpos($field["fkey"]["sql_exist"], "'") === strlen($field["fkey"]["sql_exist"])-strlen("'")) {
                                        $sql .= "'";
                                    }
                                    //
                                    $res = $this->f->db->getOne($sql);
                                    // Logger
                                    $this->f->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
                                    // Si le résultat de la requête ne renvoi aucune valeur ou
                                    // une erreur de base de données alors on rejette l'enregistrement
                                    if (is_null($res) || $this->f->isDatabaseError($res, true)) {
                                        // On rejette l'enregistrement
                                        $correct = false;
                                        // Raison du rejet à ajouter dans le fichier rejet
                                        $msg = _("cle secondaire inexistante")." : ".$key." = ".$valF[$key]." / ";
                                        // Logger
                                        $this->f->addToLog(__METHOD__."(): REJET => ".$msg, EXTRA_VERBOSE_MODE);
                                    }
                                }
                            }

                        }

                    // OG
                    } elseif (isset($zone)) {

                        // On boucle sur chaque champ définit dans le fichier 
                        // de configuration de l'import pour récupérer la 
                        // valeur à importer dans la base pour ce champ
                        foreach (array_keys($zone) as $champ) {

                            // Logger
                            $this->f->addToLog(__METHOD__."(): LINE ".$cpt['total']." - champ '".$champ."'", EXTRA_VERBOSE_MODE);

                            // ----------------------------------------------------
                            // Gestion du critère DEFAUT
                            // ----------------------------------------------------
                            if ($zone[$champ] == "") { // valeur par defaut
                                //
                                $valF[$champ] = ""; // eviter le not null value
                                //
                                if (!isset($defaut[$champ])) {
                                    $defaut[$champ] = "";
                                }
                                $valF[$champ] = $defaut[$champ];
                            } else {
                                if (isset($contenu[$zone[$champ]])) {
                                    $valF[$champ] = $contenu[$zone[$champ]];
                                } else  {
                                    // On rejette l'enregistrement
                                    $correct = false;
                                    // Raison du rejet à ajouter dans le fichier rejet
                                    $msg = _("nombre de colonnes incohérent")." : la colonne ".$champ." n'existe pas dans le fichier";
                                    // Logger
                                    $this->f->addToLog(__METHOD__."(): REJET => ".$msg, EXTRA_VERBOSE_MODE);
                                }
                            }

                            // ----------------------------------------------------
                            // Gestion du critère OBLIGATOIRE
                            // ----------------------------------------------------
                            // Ce critère permet de vérifier, avant de faire une 
                            // requête d'insertion, que la valeur fournie est 
                            // correcte. Exemple du paramétrage de ce critère :
                            //   $obligatoire = array(
                            //     "champ1" => 1, // Ce champ est obligatoire
                            //     "champ2" => 0, // Ce champ n'est pas obligatoire
                            //   );
                            // Par défaut si le critère n'est pas paramétré, on 
                            // considère que le champ n'est pas obligatoire.
                            // Comportement : si la valeur du champ en question
                            // dans le fichier CSV est vide ou null alors on 
                            // rejette l'enregistrement.
                            // ----------------------------------------------------
                            if (isset($obligatoire[$champ])
                                && $obligatoire[$champ] == 1
                                && (empty($valF[$champ]) || is_null($valF[$champ]))) {
                                // On rejette l'enregistrement
                                $correct = false;
                                // Raison du rejet à ajouter dans le fichier rejet
                                $msg = _("champ obligatoire vide")." : ".$champ." = ".$valF[$champ];
                                // Logger
                                $this->f->addToLog(__METHOD__."(): REJET => ".$msg, EXTRA_VERBOSE_MODE);
                            } elseif (!isset($obligatoire[$champ])) {
                                // Par défaut on indique que le champ n'est pas obligatoire
                                $obligatoire[$champ] = 0;
                            }

                            // ----------------------------------------------------
                            // Gestion du critère EXIST
                            // ----------------------------------------------------
                            // Ce critère permet de vérifier si la valeur fournie
                            // existe bien dans la table liée (clé étrangère). 
                            // Exemple du paramétrage de ce critère :
                            //   $exist = array(
                            //     "champ1" => 1, // Ce champ est lié et doit exister
                            //     "champ2" => 0, // Ce champ n'est pas lié
                            //   );
                            // Par défaut si le critère n'est pas paramétré, on 
                            // considère que le champ n'est pas lié.
                            // Comportement : si la valeur du champ en question
                            // dans le fichier CSV est vide alors on rejette 
                            // l'enregistrement
                            // ----------------------------------------------------
                            if (isset($exist[$champ]) 
                                && $exist[$champ] == 1
                                && !empty($valF[$champ])) {
                                // Logger
                                $this->f->addToLog(__METHOD__."(): LINE ".$cpt['total']." - champ '".$champ."' - EXIST", EXTRA_VERBOSE_MODE);
                                //
                                $sql = $sql_exist[$champ].$valF[$champ];
                                if (strrpos($sql_exist[$champ], "'") === strlen($sql_exist[$champ])-strlen("'")) {
                                    $sql .= "'";
                                }
                                //
                                $res = $this->f->db->getOne($sql);
                                // Logger
                                $this->f->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
                                // Si le résultat de la requête ne renvoi aucune valeur ou
                                // une erreur de base de données alors on rejette l'enregistrement
                                if (is_null($res) || $this->f->isDatabaseError($res, true)) {
                                    // On rejette l'enregistrement
                                    $correct = false;
                                    // Raison du rejet à ajouter dans le fichier rejet
                                    $msg = _("cle secondaire inexistante")." : ".$champ." = ".$valF[$champ];
                                    // Logger
                                    $this->f->addToLog(__METHOD__."(): REJET => ".$msg, EXTRA_VERBOSE_MODE);
                                }
                            } elseif (!isset($exist[$champ])) {
                                // Par défaut on indique que le champ n'est pas lié
                                $exist[$champ] = 0;
                            }

                        }
                    }

                    // Insertion de la ligne dans la base de donnees
                    if ($correct == true) {
                        // On désactive l'auto-commit
                        $this->f->db->autoCommit(false);
                        // 
                        if ($id != "") {
                            //
                            $res = $this->f->db->nextId($table);
                            // Logger
                            $this->f->addToLog(__METHOD__."(): db->nextId(\"".$table."\");", VERBOSE_MODE);
                            //
                            if ($this->f->isDatabaseError($res, true)) {
                                // On rejette l'enregistrement
                                $correct = false;
                                // Raison du rejet à ajouter dans le fichier rejet
                                $msg = $res->getMessage();
                                // Logger
                                $this->f->addToLog(__METHOD__."(): REJET => ".$msg, EXTRA_VERBOSE_MODE);
                            } else  {
                                $valF[$id] = $res;
                            }
                        }
                        // Exécution de la requête
                        $res = $this->f->db->autoExecute($table, $valF, DB_AUTOQUERY_INSERT);
                        // Logger
                        $this->f->addToLog(__METHOD__."(): db->autoExecute(\"".$table."\", ".print_r($valF, true).", DB_AUTOQUERY_INSERT);", VERBOSE_MODE);
                        //
                        if ($this->f->isDatabaseError($res, true)) {
                            //
                            $this->f->db->rollback();
                            // On rejette l'enregistrement
                            $correct = false;
                            // Raison du rejet à ajouter dans le fichier rejet
                            $msg = $res->getMessage();
                            // Logger
                            $this->f->addToLog(__METHOD__."(): REJET => ".$msg, EXTRA_VERBOSE_MODE);
                        } else {
                            //
                            if ($verrou == 1) {
                                // Commit de la transaction
                                $this->f->db->commit();
                            } else  {
                                $this->f->db->rollback();
                            }
                            //
                            $cpt["insert"]++;
                        }

                    }
                }
                // Si il y a une erreur sur la ligne alors on constitue un fichier
                // de rejet que l'utilisateur corrigera
                if ($correct == false || $firstline == true) {
                    // On recompose la ligne avec les separateurs
                    $ligne = "";
                    foreach ($contenu as $elem) {
                        $ligne .= $elem.$_POST['separateur'];
                    }
                    // On ajoute une colonne erreur sur la premiere ligne
                    if ($firstline == true) {
                        $ligne .= "rejet";
                    } else {
                        $cpt["rejet"]++;
                        $ligne .= $msg;
                    }
                    // Ajout du caractere de fin de ligne
                    $rejet .= $ligne."\n";
                }
            } else {
                $cpt["empty"]++;
            }
        }

        // Fermeture du fichier
        fclose($fichier);

        /**
         * Affichage du message de résultat de l'import
         */
        // Composition du message résultat
        $message = _("Resultat de l'import")."<br/>";
        $message .= $cpt["total"]." "._("ligne(s) dans le fichier dont :")."<br/>";
        $message .= " - ".$cpt["firstline"]." "._("ligne(s) d'entete")."<br/>";
        $message .= " - ".$cpt["insert"]." "._("ligne(s) importee(s)")."<br/>";
        $message .= " - ".$cpt["rejet"]." "._("ligne(s) rejetee(s)")."<br/>";
        $message .= " - ".$cpt["empty"]." "._("ligne(s) vide(s)")."<br/>";
        //
        if ($fic_rejet == 1 && $cpt["rejet"] != 0) {
            //
            $class = "error";
            // Composition du fichier de rejet
            $rejet = substr($rejet, 0, strlen($rejet) - 1);
            $metadata = array(
                "filename" => "import_".$obj."_".date("Ymd_Gis")."_rejet.csv",
                "size" => strlen($rejet),
                "mimetype" => "application/vnd.ms-excel",
            );
            $uid = $this->f->storage->create_temporary($rejet, $metadata);
            // Enclenchement de la tamporisation de sortie
            ob_start();
            //
            $this->f->layout->display_link(array(
                "href" => "../spg/file.php?uid=".$uid."&amp;mode=temporary",
                "title" => _("Télécharger le fichier CSV rejet"),
                "class" => "om-prev-icon reqmo-16",
                "target" => "_blank",
            ));
            // Affecte le contenu courant du tampon de sortie au message puis l'efface
            $message .= ob_get_clean();
        } else {
            //
            $class = "ok";
        }
        //
        $this->f->displayMessage($class, $message);
    }

    /**
     * Vue principale du module import.
     *
     * Cette vue gère l'intégralité du module import :
     *  - le listing des imports disponibles,
     *  - le formulaire d'import d'un objet sélectionné,
     *  - la validation du formulaire d'import,
     *  - l'appel au traitement d'import,
     *  - l'affichage du retour du traitement.
     *
     * @todo Il est nécessaire de gérer la récupération des $_GET et $_POST
     *       dans des méthodes séparées afin de contrôler les données d'entrées
     *       et de génériser le traitement d'import.
     *
     * @return void
     */
    function view_import_main() {
        //
        set_time_limit(3600);
        // Nom de l'objet metier
        (isset($_GET['obj']) ? $obj = $_GET['obj'] : $obj = "");
        // Vérification de l'existence de l'objet
        if ($obj != "" && !array_key_exists($obj, $this->get_import_list())) {
            $class = "error";
            $message = _("L'objet est invalide.");
            $this->f->addToMessage($class, $message);
            $this->f->setFlag(null);
            $this->f->display();
            die();
        }
        //
        if ($obj == "") {
            //
            $this->display_import_list();
        } else {
            // XXX Accesseur
            $this->f->displaySubTitle("-> ".$this->import_list[$obj]["title"]);
            //
            if (isset($_POST["submit-csv-import"])) {
                $this->treatment_import($obj);
            }
            //
            $this->display_import_form($obj);
            //
            $this->display_import_helper($obj);
        }
    }

    /**
     * Fichier CSV modèle.
     *
     * @return void
     */
    function view_import_csv_template() {
        //
        $this->f->disableLog();
        // Nom de l'objet metier
        (isset($_GET['obj']) ? $obj = $_GET['obj'] : $obj = "");
        // Vérification de l'existence de l'objet
        if (($obj != ""
             && !array_key_exists($obj, $this->get_import_list()))
            || $obj == "") {
            $class = "error";
            $message = _("L'objet est invalide.");
            $this->f->addToMessage($class, $message);
            $this->f->setFlag(null);
            $this->f->display();
            die();
        }
        //
        header("Content-type:application/vnd.ms-excel");
        header("Content-disposition:filename=import-".$obj."-template.csv");
        // Configuration par défaut du fichier de paramétrage de l'import
        // Liste de champs
        $fields = array();
        // Récupération du fichier de paramétrage de l'import
        // XXX Faire un accesseur pour vérifier l'existence du fichier
        include $this->import_list[$obj]["path"];
        //
        $csv_template = "";
        foreach ($fields as $key => $field) {
            $csv_template .= $key.";";
        }
        //
        echo $csv_template;
    }

    /**
     * Sélecteur de vue.
     *
     * @return void
     */
    function view_import() {
        //
        if (isset($_GET["action"]) && $_GET["action"] == "template") {
            //
            $this->view_import_csv_template();
        } else {
            //
            $this->f->setFlag(null);
            $this->f->display();
            //
            $description = _("Ce module permet l'intégration de données dans l'applicatif depuis des fichiers CSV.");
            $this->f->displayDescription($description);
            //
            $this->view_import_main();
        }
    }

    /**
     * Méthode utilitaire.
     *
     * Cette fonction permet de lister les imports disponibles dans un 
     * répertoire.
     *
     * @param string $folder_path Path vers le répertoire.
     * @param array  $import_list Liste des imports (optionnelle).
     *
     * @return array Liste des imports disponibles.
     */
    function get_import_list_in_folder($folder_path = "", $import_list = array()) {
        // On teste si le répertoire existe
        if (is_dir($folder_path)) {
            // Si le répertoire existe alors l'ouvre
            $folder = opendir($folder_path);
            // On liste le contenu du répertoire
            while ($file = readdir($folder)) {
                // Si le nom du fichier contient la bonne extension
                if (strpos($file, ".import.inc.php")) {
                    // On récupère la première partie du nom du fichier
                    // c'est à dire sans l'extension complète
                    $elem = substr($file, 0, strlen($file) - 15);
                    // On l'ajoute à la liste des imports disponibles
                    // avec le path complet vers le script et le titre
                    $import_list[$elem] = array(
                        "title" => _($elem),
                        "path" => $folder_path.$file,
                    );
                }
            }
            // On ferme le répertoire
            closedir($folder);
        }
        // On retourne la liste des imports disponibles
        return $import_list;
    }

    /**
     * Méthode utilitaire.
     *
     * Cette fonction permet de comparer (ordre alphabétique) les valeurs de 
     * l'attribut title des deux tableaux passés en paramètre.
     * 
     * @param array $a Premier tableau.
     * @param array $b Second tableau.
     *
     * @return boolean
     */
    function sort_by_lower_title($a, $b) {
        if (strtolower($a["title"]) == strtolower($b["title"])) {
            return 0;
        }
        return (strtolower($a["title"]) < strtolower($b["title"]) ? -1 : 1);
    }

}

?>
