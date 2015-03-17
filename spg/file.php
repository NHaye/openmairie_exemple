<?php
/**
 * Ce script permet de visualiser un fichier dont l'uid est passé en paramètre
 *
 * @package openmairie_exemple
 * @version SVN : $Id: file.php 2919 2014-10-09 16:50:28Z fmichon $
 */

require_once "../obj/utils.class.php";
require_once "../core/om_filestorage.class.php";

$f = new utils("nohtml");
$f->disableLog();

/**
 * Initialisation des parametres
 */

//
(isset($_GET['uid']) ? $uid = $_GET['uid'] : $uid = "");
(isset($_GET['dl']) ? $dl = $_GET['dl'] : $dl = "");
(isset($_GET['mode']) ? $mode = $_GET['mode'] : $mode = "filestorage");
(isset($_GET['obj']) ? $obj = $_GET['obj'] : $obj = "");
(isset($_GET['champ']) ? $champ = $_GET['champ'] : $champ = "");
(isset($_GET['id']) ? $id = $_GET['id'] : $id = "");

//Vérification de l'existence des paramètres
if($obj != "" && $champ != "" && $id != "") {

    //Vérification des droits
    if ($f->isAccredited($obj) || $f->isAccredited($obj.'_'.$champ.'_telecharger')) {

        // On vérifie que l'objet existe
        if(file_exists("../obj/".$obj.".class.php")) {

            // Instanciation de l'objet pour récupérer l'uid du fichier
            require_once "../obj/".$obj.".class.php";
            $object = new $obj($id, $f->db, false);
            $uid = $object->getVal($champ);

            //Affichage du fichier
            display_file_content($uid, $dl, $f, $mode);
        } else {
            // Si pas d'objet envoi message de retour
            $f->displayMessage("error", _("Objet inexistant."));
            die();
        }
    }
    else {

        //Envoi message de retour
        $f->displayMessage("error", _("Droits insuffisants. Vous n'avez pas suffisamment de droits pour acceder a cette page."));
    }
//Sinon si l'uid est renseigné
} elseif ($uid != "") {

    //Affichage du fichier
    display_file_content($uid, $dl, $f, $mode);

//Sinon
} else {

    // Retour à l'accueil + affichage de l'erreur
    $f->displayMessage("error", _("Le fichier n'existe pas ou n'est pas accessible."));

}

 /**
 * Affiche le contenu du fichier
 * @param  string $uid Identifiant unique du fichier
 * @param  object $f   Instance de la classe utils
 * @param  string $dl  Téléchargement
 * @param  string $mode Mode permettant de définir l'endroit où se situe le fichier
 */
function display_file_content($uid, $dl, $f, $mode) {

    // Visualisation du fichier
    $file = $f->storage->get($uid);
    //Si mode temporary
    if ($mode == 'temporary'){
        //Visualisation depuis le fichier temporaire
        $file = $f->storage->get_temporary($uid);
    }

    // Affichage du contenu du fichier
    if($file != null) {

        // Headers
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date dans le passé
        header("Content-Type: ".$file['metadata']['mimetype']);
        header("Accept-Ranges: bytes");

        // Vérification pour la valeur de $dl
        if (!in_array($dl, array("download", "inline"))) {
            if ($f->getParameter("edition_output") == "download") {
                $dl="download";
            } else {
                $dl="inline";
            }
        }

        // Vérification si on affiche simplement l'image, sinon envoi un dialogue de sauvegarde
        if ($dl=="download") {
            header("Content-Disposition: attachment; filename=\"".$file['metadata']['filename']."\";" );
         } else {
            header("Content-Disposition: inline; filename=\"".$file['metadata']['filename']."\";" );

         }

        // Rendu du fichier
        echo $file['file_content'];
        
    } else {
        // Retour à l'accueil + affichage de l'erreur
        $f->displayMessage("error", _("Le fichier n'existe pas ou n'est pas accessible."));

    }
}

?>