<?php
/**
 * Cette page permet de lister les différentes éditions pdf présentes dans
 * l'application.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: edition.php 2919 2014-10-09 16:50:28Z fmichon $
 */

require_once "../obj/utils.class.php";
if (!isset($f)) {
    $f = new utils("nohtml");
}

/**
 * Fonction permettant de lister les éditions disponibles dans un répertoire.
 *
 * @param string $folder_path Path vers le répertoire.
 * @param array  $pdf_list    Liste d'éditions (optionnelle).
 *
 * @return array Liste des éditions disponibles.
 */
function get_pdf_list_in_folder($folder_path = "", $pdf_list = array()) {
    // On teste si le répertoire existe
    if (is_dir($folder_path)) {
        // Si le répertoire existe alors l'ouvre
        $folder = opendir($folder_path);
        // On liste le contenu du répertoire
        while ($file = readdir($folder)) {
            // Si le nom du fichier contient la bonne extension
            if (strpos($file, ".pdf.inc.php")) {
                // On récupère la première partie du nom du fichier
                // c'est à dire sans l'extension complète
                $elem = substr($file, 0, strlen($file) - 12);
                // On l'ajoute à la liste des éditions disponibles
                // avec le path complet vers le script et le titre
                $pdf_list[$elem] = array(
                    "title" => _($elem),
                    "path" => $folder_path.$file,
                );
            }
        }
        // On ferme le répertoire
        closedir($folder);
    }
    // On retourne la liste des éditions disponibles
    return $pdf_list;
}

/**
 * Fonction permettant de comparer les valeurs de l'attribut title
 * des deux tableaux passés en paramètre.
 * 
 * @param array $a
 * @param array $b
 *
 * @return bool 
 */
function sort_by_lower_title($a, $b) {
    if (strtolower($a["title"]) == strtolower($b["title"])) {
        return 0;
    }
    return (strtolower($a["title"]) < strtolower($b["title"]) ? -1 : 1);
}

/**
 * Récupération de la liste des éditions disponibles.
 *
 * Ces éditions correspondent aux éditions génériques paramétrées dans des 
 * scripts <edition>.pdf.inc.php. Ces scripts sont généralement présents dans
 * le répertoire sql/<db_type>/ de l'application mais peuvent également être 
 * présents dans le répertoire CUSTOM prévu à cet effet.
 */
// On définit le répertoire STANDARD où se trouvent les scripts des éditions
$dir = getcwd();
$dir = substr($dir, 0, strlen($dir) - 4)."/sql/".OM_DB_PHPTYPE."/";
// On récupère la liste des éditions disponibles dans ce répertoire STANDARD
$pdf_list = get_pdf_list_in_folder($dir);
//
if ($f->get_custom("path", ".pdf.inc.php") != null) {
    // On définit le répertoire CUSTOM où se trouvent les scripts des éditions
    $dir = $f->get_custom("path", ".pdf.inc.php");
    // On récupère la liste des éditions disponibles dans ce répertoire CUSTOM
    $pdf_list = get_pdf_list_in_folder($dir, $pdf_list);
}
// On tri la liste des éditions disponibles par ordre alphabétique
uasort($pdf_list, "sort_by_lower_title");

/**
 *
 */
// Nom de l'objet metier
(isset($_GET['obj']) ? $obj = $_GET['obj'] : $obj = "");
// Vérification de l'existence de l'objet
// XXX Vérifier une permission spécifique ?
if ($obj != "" && !array_key_exists($obj, $pdf_list)) {
    $class = "error";
    $message = _("L'objet est invalide.");
    $f->addToMessage($class, $message);
    $f->setFlag(NULL);
    $f->display();
    die();
}

/**
 *
 */
//
if ($obj == "") {
    //
    $usecase = "pdf-list";
} else {
    //
    $usecase = "pdf-output";
}


/**
 *
 */
if ($usecase == "pdf-output") {

    /**
     * Génération du PDF
     */
    //
    $multiplicateur = 1;
    //
    $collectivite = $f->collectivite;
    //
    include $pdf_list[$obj]["path"];
    //
    set_time_limit(180);
    //
    require_once PATH_OPENMAIRIE."db_fpdf.php";
    //
    $pdf = new PDF($orientation, 'mm', $format);
    $pdf->Open();
    $pdf->SetMargins($margeleft, $margetop, $margeright);
    $pdf->AliasNbPages();
    $pdf->SetDisplayMode('real', 'single');
    $pdf->SetDrawColor($C1border, $C2border, $C3border);
    $pdf->AddPage();
    $pdf->Table($sql, $f->db, $height, $border, $align, $fond, $police, $size,
                $multiplicateur, $flag_entete);
    //
    $pdf->Output();

} elseif ($usecase == "pdf-list") {

    /**
     * Affichage de la structure de la page
     */
    //
    $f->setTitle(_("export")." -> "._("editions"));
    $f->isAuthorized("edition");
    $f->setFlag(NULL);
    //
    $f->display();
    //
    $description = _(
        "Le module 'editions' permet d'acceder aux listings PDF ".
        "de l'application."
    );
    $f->displayDescription($description);

    /**
     * Affichage de la liste des éditions disponibles.
     */
    //
    echo "\n<div id=\"edition\">\n";
    // Composition de la liste de liens vers les éditions disponibles.
    // En partant de la liste d'éditions disponibles, on compose une liste 
    // d'éléments composés d'une URL, d'un libellé, et de tous les paramètres 
    // permettant l'affichage de l'élément comme un élément de liste.
    $list = array();
    foreach ($pdf_list as $key => $value) {
        //
        $list[] = array(
            "href" => "../scr/edition.php?obj=".$key,
            "title" => $value["title"],
            "class" => "om-prev-icon edition-16",
            "target" => "blank",
        );
    }
    //
    $f->layout->display_list(
        array(
            "title" => _("choix de l'edition"),
            "list" => $list,
        )
    );
    //
    echo "</div>\n";

}

?>
