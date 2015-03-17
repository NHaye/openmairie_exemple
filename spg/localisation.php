<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: localisation.php 2141 2013-03-10 23:11:06Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

/**
 * Initialisation des parametres
 */
//
(isset($_GET['format']) ? $format = $_GET['format'] : $format = "");
//
(isset($_GET['orientation']) ? $orientation = $_GET['orientation'] : $orientation = "");
//
(isset($_GET['positionx']) ? $positionx = $_GET['positionx'] : $positionx = "");
//
(isset($_GET['positiony']) ? $positiony = $_GET['positiony'] : $positiony = ""); 
//
(isset($_GET['x']) ? $x = $_GET['x'] : $x = 0);
//
(isset($_GET['y']) ? $y = $_GET['y'] : $y = 0);
//
(isset($_GET['form']) ? $form = $_GET['form'] : $form = 'f1');

/**
 * Affichage de la structure HTML
 */
if ($f->isAjaxRequest()) {
    header("Content-type: text/html; charset=".HTTPCHARSET."");
} else {
    //
    $f->addHTMLHeadJs(array("../lib/jquery-ui/jquery-ui.min.js",
                            "../js/localisation.js"));
    //
    $f->setFlag("htmlonly");
    $f->display();
}
//
$f->displayStartContent();
//
$f->setTitle(_("Localisation"));
$f->displayTitle();

/**
 *
 */
$f->layout->display_start_conteneur_grille();
$f->layout->display_start_conteneur_block();
//
echo "<div";
echo " style=\"float:left; border: 1px solid #cdcdcd; margin-bottom: 10px;\"";
echo ">\n";
//
if ($format == "A4" && $orientation == "P") {
    $width = 210;
    $height = 297;
} elseif ($format == "A4" && $orientation == "L") {
    $width = 297;
    $height = 210;
} elseif ($format == "A3" && $orientation == "P") {
    $width = 297;
    $height = 420;
} elseif ($format == "A3" && $orientation == "L") {
    $width = 420;
    $height = 297;
} else {
    $width = 210;
    $height = 297;
}
//
echo "<div";
echo " id=\"localisation-wrapper\"";
echo " style=\"position:relative; float:left; ";
echo " width:".$width."px;";
echo " height:".$height."px;";
echo " background-color: #abcdef;\"";
echo ">\n";
//
echo "<img";
echo " id=\"draggable\"";
echo " class=\"".$form.";".$positionx.";".$positiony.";\"";
echo " src=\"../img/zoneobligatoire.gif\"";
echo " style=\"position:absolute; margin: 0; padding:0; left:".$x."px; top:".$y."px; border-left: 1px solid #999; border-top: 1px solid #999;\"";
echo " />";
//
echo "\n</div>\n";
//
echo "<div class=\"visualClear\"><!-- --></div>\n";
//
echo "\n</div>\n";
//
echo "<div class=\"visualClear\"><!-- --></div>\n";
//
$f->layout->display_close_conteneur_block();
$f->layout->display_close_conteneur_grille();
// 
/**
 *
 */
//
$f->displayLinkJsCloseWindow();

/**
 *
 */
//
$f->displayEndContent();

?>