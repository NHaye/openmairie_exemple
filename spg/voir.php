<?php
/**
 * Ce script permet d'offrir un visualisation web d'un fichier. Soit le fichier
 * est une image et il est alors affiché à l'écran soit c'est autre type de
 * fichier et un lien est présenté pour télécharger le fichier.s
 *
 * @package openmairie_exemple
 * @version SVN : $Id: voir.php 2919 2014-10-09 16:50:28Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

/**
 * Définition d'une fonction pour rendre la taille du fichier lisible 
 */
function filesize_format($size) {
    $units = array('o', 'Ko', 'Mo', 'Go', 'To',);
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

/**
 * Affichage de la structure HTML
 */
if ($f->isAjaxRequest()) {
    header("Content-type: text/html; charset=".HTTPCHARSET."");
} else {
    //
    $f->setFlag("htmlonly");
    $f->display();
}

/**
 * Récupération des paramètres
 */
//
(isset($_GET['fic']) ? $fic = $_GET['fic'] : $fic = "");

//
(isset($_GET['mode']) ? $mode = $_GET['mode'] : $mode = "filestorage");

//
(isset($_GET['obj']) ? $obj = $_GET['obj'] : $obj = "");
(isset($_GET['champ']) ? $champ = $_GET['champ'] : $champ = "");
(isset($_GET['id']) ? $id = $_GET['id'] : $id = "");

/**
 * Vérifications des paramètres
 */
// Si les paramètres nécessaires ne sont pas correctement fournis
if (($obj == "" || $champ == "" || $id == "") && $fic == "") {
    // Retour à l'accueil + affichage de l'erreur
    $f->displayMessage("error", _("Les parametres transmis ne sont pas corrects."));
    die();
    
}

/**
 * Cas n°1 - Récupération du fichier en passant par son objet
 */
//
if ($obj != "" && $champ != "" && $id != "") {

    // On vérifie que l'utilisateur a bien le droit de télécharger le champ
    // fichier de l'objet
    if (!($f->isAccredited($obj) || $f->isAccredited($obj.'_'.$champ.'_telecharger'))) {
        // Envoi message de retour
        $f->displayMessage("error", _("Droits insuffisants. Vous n'avez pas suffisamment de droits pour acceder a cette page."));
        die();
    }

    // On vérifie que l'objet existe
    if(file_exists("../obj/".$obj.".class.php")) {

        // Instanciation de l'objet pour récupérer l'uid du fichier
        require_once "../obj/".$obj.".class.php";
        $object = new $obj($id, $f->db, false);
        $fic = $object->getVal($champ);
        
    } else {
        // Si pas d'objet envoi message de retour
        $f->displayMessage("error", _("Objet inexistant."));
        die();
    }
}


/**
 * Affiche le contenu du fichier
 */

// Si le mode de stockage est le mode temporaire alors on récupère le fichier
// depuis ce mode se stockage sinon depuis le sotckage standard
if ($mode == 'temporary') {
    //
    $file = $f->storage->get_temporary($fic);
} else {
    //
    $file = $f->storage->get($fic);
}    
    
/**
 *
 */
if (is_null($file)) {
    //
    $f->displayMessage("error", _("Le fichier n'existe pas ou n'est pas accessible."));
    die();
}

/**
 *
 */
//
$f->displayStartContent();
//
$f->setTitle(_("Voir")." -> [&nbsp;".$file['metadata']['filename']."&nbsp;]");
$f->displayTitle();

/**
 *
 */
//
$f->layout->display_start_conteneur_grille();
$f->layout->display_start_conteneur_block();
//
echo "<div id=\"voir\">\n";
// On compose la classe css du lien en fonction du mimetype du fichier, il est
// nécessaire de remplacer les caractères qui ne sont pas autorisés dans une
// classe css
$searchReplaceArray = array('.' => '-', '/' => '-', '+' => '-', );
$file_mimetype_class = "mimetype-".str_replace(
    array_keys($searchReplaceArray), 
    array_values($searchReplaceArray), 
    $file['metadata']['mimetype']
);
// On compose le lien de téléchargement du fichier
$file_download_link = "../spg/file.php?";
if ($obj != "" && $champ != "" && $id != "") {
    $file_download_link .= "obj=".$obj."&amp;champ=".$champ."&amp;id=".$id;
} else {
    $file_download_link .= "uid=".$fic;
}
if ($mode != "filestorage") {
    $file_download_link .= "&mode=".$mode;
}
// On affiche le bloc d'informations du fichier avec le lien de téléchargement
$file_infos_block  = 
"
<p class=\"file-infos-block\">
    <span>
        <a href=\"%s\" target=\"_blank\" class=\"om-prev-icon file-download %s\">
            %s
        </a>
        <span class=\"discreet\">
            —
            %s, %s
        </span>
    </span>
</p>
";
printf($file_infos_block,
       $file_download_link,
       $file_mimetype_class,
       $file['metadata']['filename'],
       $file['metadata']['mimetype'],
       filesize_format($file['metadata']['size']));
// Si le fichier est une image alors on affiche l'image
if ($file['metadata']['mimetype'] == "image/jpeg"
    || $file['metadata']['mimetype'] == "image/png"
    || $file['metadata']['mimetype'] == "image/gif") {
    //
    $base64 = chunk_split(base64_encode($file['file_content']));
    //
    echo "\n<center>";
    echo "<img src=\"data:".$file['metadata']['mimetype'].";base64,".$base64."\" alt=\"".$file['metadata']['filename']."\" id='img-voir'/>";
    echo "</center>\n";
    //
}
//
echo "\n</div>\n";
//
$f->layout->display_close_conteneur_block();
$f->layout->display_close_conteneur_grille();
//
$f->displayLinkJsCloseWindow();

/**
 *
 */
//
$f->displayEndContent();

?>
