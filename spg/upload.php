<?php
/**
 * Ce script permet d'afficher un formulaire pour gérer l'upload de fichier
 * dans le dossier trs.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: upload.php 2377 2013-06-11 09:44:45Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

/**
 * Initialisation des parametres
 */
//
(isset($_GET['form']) ? $form = $_GET['form'] : $form = "f1");
//
(isset($_GET['origine']) ? $origine = $_GET['origine'] : $origine = "");

/**
 * Verification des parametres
 */
if ($origine == "") {
    //
    if ($f->isAjaxRequest() == false) {
        $f->setFlag(NULL);
        $f->display();
    }
    $class = "error";
    $message = _("L'objet est invalide.");
    $f->displayMessage($class, $message);
    die();
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
//
$f->displayStartContent();
//
$f->setTitle(_("Upload"));
$f->displayTitle();
//
$description = _("Cliquer sur 'Parcourir' pour selectionner le fichier a ".
                 "telecharger depuis votre poste de travail puis cliquer sur ".
                 "le bouton 'Envoyer' pour valider votre telechargement.");
$f->displayDescription($description);

/**
 * 
 */
//
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
require_once PATH_OPENMAIRIE."upload.class.php";
//
$Upload = new Upload($f);

/**
 * Gestion des erreurs
 */
//
$error = false;
// Verification du post vide
if (isset($_POST['submited'])
    and (!isset($_FILES['userfile'])
         or $_FILES['userfile']['name'][0] == "")) {
    //
    $error = true;
    $f->displayMessage("error", _("Vous devez selectionner un fichier."));
}

/**
 * Formulaire soumis et valide
 */
if (isset($_POST['submited']) and $error == false) {
    
    // Gestion des extensions de fichier
    if (isset($_GET['origine'])) {
        $tmp = "";
        $tmp = $_GET['origine'].'_extension';
    }
    if (isset(${$tmp})) {
        $Upload->Extension = ${$tmp};
    } else {
        if ( isset($_GET['extension'])&&$_GET['extension']!="" && 
            isset($f->config['upload_extension']) ){
                
            $Upload->Extension = $_GET['extension'];
            
            //Liste des extensions génériques possibles
            $extensionPossibleGen = explode(';', $f->config['upload_extension']);
            array_pop($extensionPossibleGen);
            //Liste des extensions spécifiques possibles
            $extensionPossibleSpe = explode(';', $_GET['extension']);
            
            foreach ($extensionPossibleSpe as $value) {
                
                // Si une seule des extensions spécifiques n'est pas une des 
                // extensions génériques possibles, on utilise la configuration
                // générique
                if ( !in_array($value, $extensionPossibleGen)){
                    $Upload->Extension = $f->config['upload_extension'];
                    break;
                }
            } 
        } elseif (isset($_GET['extension'])&&$_GET['extension']!=""){
            $Upload->Extension = $_GET['extension'];
        }elseif (isset($f->config['upload_extension'])) {
            $Upload->Extension = $f->config['upload_extension'];
        } else {
            $Upload->Extension = '.gif;.jpg;.jpeg;.png;.txt;.pdf;.csv';
        }
    }

    // On lance la procedure d'upload
    $Upload->Execute();
    
    // Gestion erreur / succes
    if ($UploadError) {
        $error = true;
        // (XXX - Le foreach est inutile on traite sur un seul champ fichier)
        foreach ($Upload->GetError() as $elem) {
            foreach($elem as $key => $elem1) {
                $f->displayMessage("error", $elem1);
            }
        }
    } else {
        // (XXX - Le foreach est inutile on traite sur un seul champ fichier)
        foreach ($Upload->GetSummary() as $elem) {
            $nom = $elem['nom'];
            $filename = $elem['nom_originel'];
            // Controle de la longueur du nom de fichier
            if (strlen($filename) > 50) {
                $error = true;
                $f->displayMessage("error", $filename." "._("contient trop de caracteres.")." "._("Autorise(s) : 50 caractere(s)."));
                continue;
            }
            //
            if ($f->isAjaxRequest()) {
                echo "<script type=\"text/javascript\">";
                echo "upload_return('".$form."', '".$_GET['origine']."', 'tmp|".$nom."', '".addslashes($filename)."')";
                echo "</script>";
            } else {
            ?>
            <script type="text/javascript">
                parent.opener.document.<?php echo $form?>.<?php echo $_GET['origine']?>.value='<?php echo "tmp|".$nom?>';
                parent.opener.document.<?php echo $form?>.<?php echo $_GET['origine']."_upload"?>.value='<?php echo $filename?>';
                parent.close();
            </script>
            <?php
            }
        }
    }
}

/**
 * Formulaire non soumis ou non valide
 */
if (!isset($_POST['submited']) or $error == true) {
    // Pour limiter la taille d'un fichier (exprimee en ko)
    if ( isset($_GET['taille_max'])&&$_GET['taille_max']!="" && 
        isset($f->config['upload_taille_max']) && 
        $_GET['taille_max'] > $f->config['upload_taille_max'] ){
        $Upload->MaxFilesize = $f->config['upload_taille_max'];
    } elseif (isset($_GET['taille_max'])&&$_GET['taille_max']!=""){
        $Upload->MaxFilesize = $_GET['taille_max'] * 1024 ;
    }elseif (isset($f->config['upload_taille_max'])) {
        $Upload->MaxFilesize = $f->config['upload_taille_max'];
    } else {
        $Upload->MaxFilesize = '10000';
    }
    
    // Pour ajouter des attributs aux champs de type file
    $Upload->FieldOptions = 'class="champFormulaire"';
    // Pour indiquer le nombre de champs desire
    $Upload->Fields = 2;
    // Initialisation du formulaire
    $Upload->InitForm();
    // Ouverture de la balise form
    echo "<form method=\"post\" enctype=\"multipart/form-data\" ";
    echo "id=\"upload-form\" name=\"upload-form\" ";
    echo "action=\"../spg/upload.php?origine=".(isset($_GET['origine']) ? $_GET['origine'] : "").
        "&amp;form=".$form."&amp;taille_max=".(isset($_GET['taille_max']) ? $_GET['taille_max'] : "").
        "&amp;extension=".(isset($_GET['extension']) ? $_GET['extension'] : "")."\" ";
    echo ">\n";
    // Affichage du champ MAX_FILE_SIZE
    print $Upload->Field[0];
    // Affichage du premier champ de type FILE
    print $Upload->Field[1];
    //
    echo "<br/>\n";
    echo "<br/>\n";
    //
    echo "<input type=\"hidden\" value=\"1\" name=\"submited\" />\n";
    echo "<input type=\"submit\" value=\""._("Envoyer")."\" name=\"submit\" />\n";
    //
    $f->displayLinkJsCloseWindow();
    // Fermeture de la balise form
    echo "</form>\n";
}

/**
 * Affichage de la structure HTML
 */
//
$f->displayEndContent();

?>
