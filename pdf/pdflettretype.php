<?php
/**
 * Ce script permet de générer un fichier pdf représentant une édition
 * "Lettre Type" en fonction de paramètres dans la table om_lettretype
 *
 * @package openmairie_exemple
 * @version SVN : $Id: pdflettretype.php 3027 2015-02-02 18:38:24Z fmichon $
 */

require_once "../obj/utils.class.php";
if (!isset($f)) {
    $f = new utils("nohtml");
}

// Paramétrage du filigrane
(isset($_GET['watermark']) && $_GET['watermark'] == 'true') ?
    $watermark = true : $watermark = false;

/**
 *
 */
// L'attribut collectivite de la classe utils est un tableau de paramètres
$collectivite = $f->collectivite;
// Variable permettant de stocker l'identifiant de la collectivité de niveau 2
$niveau = "";
//
if (isset($_GET["obj"]) || isset($_GET['idx'])) {
    // Identifiant de l'édition à générer (champ id de la table om_etat)
    (isset($_GET['obj']) ? $obj = $_GET['obj'] : $obj = "");
    // Identifiant de l'élément concerné par l'édition
    (isset($_GET['idx']) ? $idx = $_GET['idx'] : $idx = "");
} elseif (isset($_POST["obj"]) || isset($_POST['idx'])) {
    //
    (isset($_POST['obj']) ? $obj = $_POST['obj'] : $obj = "");
    // Si c'est un tableau qui est fourni dans le POST alors on le concatène
    // avec des ; pour coller au format attendu
    if (is_array($obj) === true) {
        $obj_str = "";
        foreach ($obj as $value) {
            $obj_str .= $value.";";
        }
        $obj = $obj_str;
    }
    //
    (isset($_POST['idx']) ? $idx = $_POST['idx'] : $idx = "");
    // Si c'est un tableau qui est fourni dans le POST alors on le concatène
    // avec des ; pour coller au format attendu
    if (is_array($idx) === true) {
        $idx_str = "";
        foreach ($obj as $value) {
            $idx_str .= $value.";";
        }
        $idx = $idx_str;
    }
} else {
    //
    $obj = "";
    $idx = "";
}
//
$editions = array_filter(explode(";", $obj));
$elements = array_filter(explode(";", $idx));

// Si un seul élément est fourni alors qu'il y a plusieurs éditions alors on
// suppose que c'est le même élément pour chacune des éditions
if (count($editions) != count($elements) && count($editions) > 1 && 
    count($elements) == 1) {
    foreach ($editions as $edition) {
        $elements[] = $elements[0];
    }
} elseif (count($editions) != count($elements) && count($editions) == 1 && 
    count($elements) > 1) {
    // Si une seule édition est fourni alors qu'il y a plusieurs éléments alors on
    // suppose que c'est la même édition pour chacun des éléments
    $tmp_edition = $editions[0];
    $editions = array();
    foreach ($elements as $element) {
        $editions[] = $tmp_edition;
    }
    unset($tmp_edition);
}

/**
 * Ces paramètres sont ici pour une raison de rétro-compatibilité
 * @todo Vérifier qu'il n'est pas possible de les supprimer de ce fichier et de
 *       les gérer dans dyn/varlettretypepdf.inc ce qui est déjà en partie le
 *       cas
 */
//
$destinataire = "";
//
$datecourrier = date('d/m/Y');
//
$complement = "<-Ici le complement->";

/**
 *
 */
//
set_time_limit(180);
//
require_once PATH_OPENMAIRIE."fpdf_etat.php";

/**
 * Multi impression
 */
//
$counter = 0;
//
foreach ($editions as $key => $value) {
    //
    $counter++;
    //
    $obj = $value;
    // Compatibilité antérieure : dans le cas où le remplacement des variables dans
    // le fichier de remplacement dyn/var<EDITION>pdf.inc se base sur la variable
    // $_GET au lieu de la variable $idx
    $_GET['idx'] = "-1";
    if (isset($elements[$key])) {
        if (is_integer($elements[$key])) {
            $_GET['idx'] = $elements[$key];
        } else {
            $_GET['idx'] = $f->db->escapeSimple($elements[$key]);
        }
    }
    //
    $idx = "-1";
    if (isset($elements[$key])) {
        if (is_integer($elements[$key])) {
            $idx = $elements[$key];
        } else {
            $idx = $f->db->escapeSimple($elements[$key]);
        }
    }

    /**
     * Gestion de la sélection des paramètres de l'édition à générer
     * en fonction du paramètre actif et/ou du niveau de la collectivité
     */
    // On récupère l'enregistrement 'om_lettretype' de la collectivité en cours dans
    // l'état 'actif'
    $sql = " select * from ".DB_PREFIXE."om_lettretype ";
    $sql .= " LEFT JOIN ".DB_PREFIXE."om_logo ";
    $sql .= " ON om_lettretype.logo=om_logo.id ";
    $sql .= " LEFT JOIN ".DB_PREFIXE."om_requete ";
    $sql .= " ON om_lettretype.om_sql=om_requete.om_requete ";
    $sql .= " where om_lettretype.id='".$f->db->escapeSimple($obj)."' ";
    $sql .= " and om_lettretype.actif IS TRUE ";
    $sql .= " and om_lettretype.om_collectivite='".$_SESSION['collectivite']."' ";
    $res1 = $f->db->query($sql);
    $f->addToLog("pdflettretype.php: db->query(\"".$sql."\");", VERBOSE_MODE);
    $f->isDatabaseError($res1);
    // Si on obtient aucun résultat
    if ($res1->numrows() == 0) {
        // On libère le résultat de la requête précédente
        $res1->free();
        // On récupère l'identifiant de la collectivité de niveau 2
        $sql = " select om_collectivite from ".DB_PREFIXE."om_collectivite ";
        $sql .= " where niveau='2' ";
        $niveau = $f->db->getone($sql);
        $f->addToLog("pdflettretype.php: db->getone(\"".$sql."\");", VERBOSE_MODE);
        $f->isDatabaseError($niveau);
        // On récupère l'enregistrement 'om_lettretype' de la collectivité de niveau
        // 2 dans l'état 'actif'
        $sql = " select * from ".DB_PREFIXE."om_lettretype ";
        $sql .= " LEFT JOIN ".DB_PREFIXE."om_logo ";
        $sql .= " ON om_lettretype.logo=om_logo.id ";
        $sql .= " LEFT JOIN ".DB_PREFIXE."om_requete ";
        $sql .= " ON om_lettretype.om_sql=om_requete.om_requete ";
        $sql .= " where om_lettretype.id='".$f->db->escapeSimple($obj)."' ";
        $sql .= " and om_lettretype.actif IS TRUE ";
        $sql .= " and om_lettretype.om_collectivite='".($niveau == "" ? -1 : $niveau)."' ";
        $res1 = $f->db->query($sql);
        $f->addToLog("pdflettretype.php: db->query(\"".$sql."\");", VERBOSE_MODE);
        $f->isDatabaseError($res1);
        // Si on obtient aucun résultat
        if ($res1->numrows() == 0) {
            // On libère le résultat de la requête précédente
            $res1->free();
            // On récupère l'enregistrement 'om_lettretype' de la collectivité de
            // niveau 2 dans n'importe quel état
            $sql = " select * from ".DB_PREFIXE."om_lettretype ";
            $sql .= " LEFT JOIN ".DB_PREFIXE."om_logo ";
            $sql .= " ON om_lettretype.logo=om_logo.id ";
            $sql .= " LEFT JOIN ".DB_PREFIXE."om_requete ";
            $sql .= " ON om_lettretype.om_sql=om_requete.om_requete ";
            $sql .= " where om_lettretype.id='".$f->db->escapeSimple($obj)."' ";
            $sql .= " and om_lettretype.om_collectivite='".($niveau == "" ? -1 : $niveau)."' ";
            $res1 = $f->db->query($sql);
            $f->addToLog("pdflettretype.php: db->query(\"".$sql."\");", VERBOSE_MODE);
            $f->isDatabaseError($res1);
        }
    }

    /**
     *
     */
    //
    while ($edition =& $res1->fetchRow(DB_FETCHMODE_ASSOC)) {

        // Instantiation du pdf si un pdf ou 1er de la liste de fusion
        if ($key == 0) {
            //
            $pdf = new PDF(
                $edition["orientation"],
                "mm",
                $edition["format"],
                true,
                'HTML-ENTITIES'
            );
            // Si le filigrane "DOCUMENT DE TRAVAIL" est paramétré
            if ($watermark == true) {
                // On l'ajoute sur chaque page
                $pdf->setWatermark();
            }
        }
        // Définit les marges du document
        if($edition["margeleft"] == "") {
            $edition["margeleft"] = PDF_MARGIN_LEFT;
        }
        if($edition["margetop"] == "") {
            $edition["margetop"] = PDF_MARGIN_TOP;
        }
        if($edition["margeright"] == "") {
            $edition["margeright"] = PDF_MARGIN_RIGHT;
        }
        if($edition["margebottom"] == "") {
            $edition["margebottom"] = PDF_MARGIN_BOTTOM;
        }
        // set margins
        $pdf->setMargins(
            $edition["margeleft"],$edition["margetop"], $edition["margeright"]);
        $pdf->SetHeaderMargin($edition["margetop"]);
        $pdf->SetFooterMargin($edition["margebottom"]);
        // set auto page breaks
        $pdf->SetAutoPageBreak(true, $edition["margebottom"]);
        // définition du padding haut et bas des balises p span et table
        $tagvs = array(
            'p' => array(
                0 => array('h' => 0, 'n' => 0),
                1 => array('h' => 0, 'n' => 0)
            ),
            'div' => array(
                0 => array('h' => 0, 'n' => 0),
                1 => array('h' => 0, 'n' => 0)
            ),
            'span' => array(
                0 => array('h' => 0, 'n' => 0),
                1 => array('h' => 0, 'n' => 0)
            ),
            'table' => array(
                0 => array('h' => 0, 'n' => 0),
                1 => array('h' => 0, 'n' => 0)
            ),
        );
        $pdf->setHtmlVSpace($tagvs);

        // Ajoute une nouvelle page à l'édition
        $pdf->AddPage();

        /**
         * Affichage du logo
         */
        // Récupération du path du logo à afficher
        $path_logo = $f->storage->getPath($edition['fichier']);
        $type = str_ireplace("image/", "", $f->storage->getMimetype($edition['fichier']));
        // Placement d'une image 
        if (file_exists($path_logo) && !is_dir($path_logo)) {
            //
            if ($edition["resolution"] != "") {
                // récupération de la taille de l'image en pixels
                $size = getimagesize($path_logo);
                // résolution explicite
                $pdf->Image($path_logo,
                            $edition["logoleft"],
                            $edition["logotop"],
                            ($size[0]/($edition["resolution"]/25.4)),
                            ($size[1]/($edition["resolution"]/25.4)),
                            $type);
            } else {
                // aucune dimension explicite
                $pdf->Image($path_logo,
                            $edition["logoleft"],
                            $edition["logotop"],
                            0,
                            0,
                            $type);
            }
        }
        // Définition du css pour la transformation Minuscule/Majuscule
        $css = "<style>
        .mce_maj {
            text-transform: uppercase;
        }
        .mce_min {
            text-transform: lowercase;
        }
        </style>";

        // Remise en forme du html pour être interprété par TCPDF
        $titre = html_entity_decode($edition["titre_om_htmletat"]);
        $corps = html_entity_decode($edition["corps_om_htmletatex"]);
        // Suppression des balises TCPDF pour éviter toutes intrusions
        $titre = preg_replace('#<\s*tcpdf[^>]+>#','',$titre);
        $corps = preg_replace('#<\s*tcpdf[^>]+>#','',$corps);
        // Remplacement des paramètres dans le fichier ../dyn/varlettretypepdf.inc
        if (file_exists("../dyn/varlettretypepdf.inc")) {
            include "../dyn/varlettretypepdf.inc";
        }

        /**
         * Remplacement des champs de fusion par leurs valeurs
         */
        
        // Instanciation de la requête
        require_once "../obj/om_requete.class.php";
        $om_requete = new om_requete($edition['om_sql']);
        // Récupération de son type
        $type_requete = $om_requete->getVal('type');
        // Initialisation du tableau de champs de fusion
        $values = array();
        // Cas requête SQL
        if ($type_requete == 'sql') {
            // récupération de la requête SQL
            $sql = $om_requete->getVal('requete');
            // remplacement d'idx par sa valeur
            $sql = str_replace('&idx', $idx, $sql);
            // définition du schéma
            $sql = str_replace('&DB_PREFIXE', DB_PREFIXE, $sql);
            // exécution de la requête
            $res = $f->db->query($sql);
            $f->addToLog("pdflettretype.php: db->query(\"".$sql."\");", VERBOSE_MODE);
            $f->isDatabaseError($res);
            // création du tableau des champs de fusion
            $values = &$res->fetchRow(DB_FETCHMODE_ASSOC);
        }
        // Cas requête objet
        if ($type_requete == 'objet') {
            // récupération du(des) objet(s) et pour l'unique(premier)
            // son éventuelle méthode
            $classes = $om_requete->getVal('classe');
            $methode = $om_requete->getVal('methode');
            $classes = explode(';', $classes);
            $nb_classes = count($classes);
            $next_value = "";
            foreach ($classes as $key => $classe) {
                $classe = $classes[$key];
                require_once "../obj/".$classe.".class.php";
                // si unique(premier) objet
                if ($key == 0) {
                    $sql_object = new $classe($idx);
                    // Si on récupère un paramètre spécifique de surcharge 
                    // alors on le passe en paramètre à l'objet instancié
                    if (isset($_GET["specific"])) {
                        $sql_object->setParameter(
                            "edition_params_specific", 
                            $_GET["specific"]
                        );
                    }
                    // Si l'objet instancié ne correspond à aucun enregistrement
                    // et qu'il n'y a aucun paramètre spécifique de surcharge
                    // on renvoi un tableau vide de valeurs pour afficher les 
                    // libellés entre crochets
                    if ($sql_object->getVal($sql_object->clePrimaire) == null
                        && !isset($_GET["specific"])) {
                        $values = array();
                        continue;
                    }
                    // si une méthode custom existe on récupère ses valeurs
                    if ($methode != null && $methode != ''
                        && method_exists($sql_object, $methode)) {
                        $custom = $sql_object->$methode('values');
                        $values = array_merge($values, $custom);
                    }
                    // on récupère également les libellés par défaut
                    $default = $sql_object->get_merge_fields('values');
                    $values = array_merge($values, $default);
                }
                // sinon traitement des éventuels objet supplémentaires
                else {
                    // si la valeur de la clé étrangère est valide
                    if ($next_value != null && $next_value != '') {
                        require_once "../obj/".$classe.".class.php";
                        $sql_object = new $classe($next_value);
                        // on ne récupère que les libellés par défaut
                        $default = $sql_object->get_merge_fields('values');
                        $values = array_merge($values, $default);
                    }
                    // sinon valeurs nulles pour supprimer l'appel
                    // aux champs de fusion dans l'édition
                    else {
                        require_once "../obj/".$classe.".class.php";
                        $sql_object = new $classe("]");
                        $nuls = array();
                        $sql_object_table = $sql_object->table;
                        foreach ($sql_object->champs as $key => $champ) {
                            $nuls[$sql_object_table.".".$champ] ="";
                        }
                        $values = array_merge($values, $nuls);
                    }
                }
                // on récupère la valeur de liaison s'il y a encore un objet derrière
                if ($key < ($nb_classes - 1)) {
                    $j = $key + 1;
                    $next_objet = $classes[$j];
                    require_once "../obj/".$next_objet.".class.php";
                    $next_objet = new $next_objet("]");
                    // récupération de la clé primaire
                    $nextClePrimaire = $next_objet->clePrimaire;
                    // récupération de sa valeur
                    $next_value = $sql_object->getVal($nextClePrimaire);
                }
            }
        }

        //////
        // TITRE
        //////

        // Explosion des champs à récupérer depuis la requête
        $temp = explode("[", $titre);
        //
        for ($i = 1; $i < sizeof($temp); $i++) {
            //
            $temp1 = explode("]", $temp[$i]);
            //
            if (isset($values[$temp1[0]])) {
                $titre = str_replace("[".$temp1[0]."]", $values[$temp1[0]], $titre);
            }
            //
            $temp1[0] = "";
        }
        $titre = "<meta charset='UTF-8'><div id=\"dom_etat_content\">".$titre."</div>";

        /**
         * Code barre avec tcpdf
         */
        $titre = $pdf->code_barres_tcpdf($titre);
        $titre=$css.$titre;
        //
        if (trim($titre) != "") {
            // Affichage du titre si non vide
            $pdf->writeHTMLCell(
                $edition["titrelargeur"],
                0,
                $edition["titreleft"],
                $edition["titretop"],
                $titre,
                $edition["titrebordure"],
                0,
                false,
                true,
                '',
                true);
        }

        //////
        // CORPS
        //////
        $pdf->ln();
        // Explosion des champs à récupérer depuis la requête
        $temp = explode("[", $corps);
        //
        for ($i = 1; $i < sizeof($temp); $i++) {
            //
            $temp1 = explode("]", $temp[$i]);
            //
            if (isset($values[$temp1[0]])) {
                $corps = str_replace("[".$temp1[0]."]", $values[$temp1[0]], $corps);
            }
            //
            $temp1[0] = "";
        }
        $corps = "<meta charset='UTF-8'><div id=\"dom_etat_content\">".$corps."</div>";
        //Code barres    
        $corps = $pdf->code_barres_tcpdf($corps);
        // Sous etat
        $corps = $pdf->initSousEtats($f,$edition,$corps, $niveau);
        $corps=$css.$corps;
        //
        if (trim($corps) != "") {
            // Affichage du corps si non vide
            $pdf->writeHTML($corps, true, false, true, false);
        }
    }
}

//
if (!isset($pdf)) {
    return;
}

// Construction du nom du fichier
$filename = date("Ymd-His");
$filename .= "-lettretype";
$filename .= "-".$obj;
$filename .= ".pdf";
//
$output = "";
if (isset($_GET["output"])) {
    $output = $_GET["output"];
}
if (!in_array($output, array("string", "file", "download", "inline", "no"))) {
    if ($f->getParameter("edition_output") == "download") {
        $output = "download";
    } else {
        $output = "inline"; // Valeur par defaut
    }
}
//
if ($output == "string") {
    // S : renvoyer le document sous forme de chaine. name est ignore.
    $pdf_output = $pdf->Output("", "S");
} elseif ($output == "file") {
    // F : sauver dans un fichier local, avec le nom indique dans name
    // (peut inclure un repertoire).
    $pdf->Output($f->getParameter("pdfdir").$filename, "F");
} elseif ($output == "download") {
    // D : envoyer au navigateur en forcant le telechargement, avec le nom
    // indique dans name.
    $pdf->Output($filename, "D");
} elseif ($output == "inline") {
    // I : envoyer en inline au navigateur. Le plug-in est utilise s'il est
    // installe. Le nom indique dans name est utilise lorsque l'on selectionne
    // "Enregistrer sous" sur le lien generant le PDF.
    $pdf->Output($filename, "I");
} elseif ($output == "no") {
    // 
}

?>
