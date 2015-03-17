<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: genaff.php 1080 2012-02-25 15:33:52Z atreal $
 */

require_once "../obj/utils.class.php";
$f = new utils("htmlonly", "gen");

/**
 *
 */
//
(isset($_GET['file']) ? $nomfichier = $_GET['file'] : $nomfichier = "");

/**
 * Affichage de la structure HTML
 */
//
$f->displayStartContent();
//
$f->setTitle(_("Code viewer")." ".$nomfichier);
$f->displayTitle();

/**
 *
 */
//
if (strpos($nomfichier, "database.inc.php") === false
    and strpos($nomfichier, "dyn") === false
    and strpos($nomfichier, "../../") === false
    and (strpos($nomfichier, ".inc.php") > 1
         or strpos($nomfichier, ".inc") > 1
         or strpos($nomfichier, ".class.php") > 1)) {
    
    if (file_exists($nomfichier)) {
        
        fopen($nomfichier,"r");
        $contenu = file($nomfichier);
        $commentaire=0;
        $texte=0;
        for($i = 1; $i < count($contenu)-1; $i++){
        // supp des blancs
        $contenu[$i]=trim($contenu[$i]);
        // compteur de lignes
        $cpt=str_pad($i,4,"0", STR_PAD_LEFT);
        echo "<font style='background-color:#000000;color:#FFFFFF;font-size:8pt'>&nbsp;".
             $cpt."&nbsp;</font> ";
        // extraction -> type
        $type='';
        $partiel=0;
        // commentaire
        if(substr($contenu[$i],0,2)=='/*') $commentaire=1;
        if(substr($contenu[$i],0,2)=='*/') $commentaire=0;
        if(preg_match("@//@",$contenu[$i])){
                $type = "commentaire_partiel";
                $partiel=1;
                $temp=explode('//',$contenu[$i]);
        }
        if(substr($contenu[$i],0,2)=='//' 
                or substr($contenu[$i],0,2)=='/*' 
                or substr($contenu[$i],0,1)=='*'
                or substr($contenu[$i],0,2)=='//'
                or substr($contenu[$i],0,2)=='*/')
                $type = "commentaire_entier";
        // class
        if(substr($contenu[$i],0,8)=='function')
                $type="gras";
        if(substr($contenu[$i],0,8)=='parent::'
            or substr($contenu[$i],0,5)=='class'
            or substr($contenu[$i],0,7)=='require')
                $type="rouge";
        if(substr($contenu[$i],0,7)=='$this->'
           or substr($contenu[$i],0,3)=='var')
                $type="bleu";
        if(substr($contenu[$i],0,7)=='$form->')
                $type="gris";
        // inc
        if(substr($nomfichier, strlen($nomfichier)-3,3)=="inc"){ 
        if(substr($contenu[$i],0,1)=="\$")
            $type="bleu";
        if(substr($contenu[$i],0,13)=="\$reqmo['sql']"
               or substr($contenu[$i],0,12)=="\$etat['sql']"
               or substr($contenu[$i],0,17)=="\$sousetat['sql']"
               or substr($contenu[$i],0,5)=="\$sql=")
                $type="rouge";
        if(substr($contenu[$i],0,14)=="\$etat['corps']"
               or substr($contenu[$i],0,18)=="\$sousetat['titre']"
               or substr($contenu[$i],0,18)=="\$sousetat['corps']"
               or substr($contenu[$i],0,14)=="\$etat['titre']"){
                $type="normal";
                $texte=1;       
        }
        
        }//inc
        if($commentaire==1) $type='commentaire_entier';
        // affichage des types
        switch ($type) {  
              case "commentaire_entier" :    
                echo "<font style='color:#ffffff;background-color:#6dc699;font-size:8pt'>".
                     $contenu[$i]."</font><br/>";
                break;
              case "gras" :
                if($partiel==1){
                  $temp=explode('//',$contenu[$i]);
                  echo "<font style='color:#000000;font-size:8pt;font-weight:bold'>".$temp[0].
                     "</font>"; 
                  echo "<font style='color:#ffffff;background-color:#6dc699;font-size:8pt'> ".
                     $temp[1]."</font><br/>";       
                }else
                  echo "<font style='color:#000000;font-size:8pt;font-weight:bold'> ".
                     $contenu[$i]."</font><br/>";
                break;
              case "gras" :
                if($partiel==1){
                  $temp=explode('//',$contenu[$i]);
                  echo "<font style='color:#000000;font-size:8pt'>".$temp[0].
                     "</font>"; 
                  echo "<font style='color:#ffffff;background-color:#6dc699;font-size:8pt'> ".
                     $temp[1]."</font><br/>";       
                }else
                  echo "<font style='color:#000000;font-size:8pt'> ".
                     $contenu[$i]."</font><br/>";
                break;
              case "rouge" :
                if($partiel==1){
                  $temp=explode('//',$contenu[$i]);
                  echo "<font style='color:red;font-size:8pt'>".$temp[0].
                     "</font>"; 
                  echo "<font style='color:#ffffff;background-color:#6dc699;font-size:8pt'> ".
                     $temp[1]."</font><br/>";       
                }else    
                echo "<font style='color:red;font-size:8pt'>".
                     $contenu[$i]."</font><br/>";
                break;
              case "bleu" :
                 if($partiel==1){
                  $temp=explode('//',$contenu[$i]);
                  echo "<font style='color:blue;font-size:8pt'>".$temp[0].
                     "</font>"; 
                  echo "<font style='color:#ffffff;background-color:#6dc699;font-size:8pt'> ".
                     $temp[1]."</font><br/>";       
                }else 
                echo "<font style='color:blue;font-size:8pt'>".
                     $contenu[$i]."</font><br/>";
                break;
              case "gris" :
                if($partiel==1){
                  $temp=explode('//',$contenu[$i]);
                  echo "<font style='color:#4D4F52;font-size:8pt'>".$temp[0].
                     "</font>"; 
                  echo "<font style='color:#ffffff;background-color:#6dc699;font-size:8pt'> ".
                     $temp[1]."</font><br/>";       
                }else    
                echo "<font style='color:#4D4F52;font-size:8pt'>".
                     $contenu[$i]."</font><br/>";
                break;
              case "commentaire_partiel" :
                  $temp=explode('//',$contenu[$i]);
                  echo "<font style='color:#000000;font-size:8pt'>".$temp[0].
                     "</font>"; 
                  echo "<font style='color:#ffffff;background-color:#6dc699;font-size:8pt'> ".$temp[1].
                     "</font><br/>";  
                break;      
              default:
                echo "<font style='color:#000000;font-size:8pt'>".
                    $contenu[$i]."<br/>";
        }// fin du switch     
        } // boucle for

    } else {
        echo $f->displayMessage("error", $nomfichier." "._(": le fichier n'existe pas."));
    }
} else {
    echo $f->displayMessage("error", $nomfichier." "._("l'acces a ce fichier n'est pas autorise."));
}

/**
 * Affichage de la structure HTML
 */
echo "<br/>";
//
$f->displayLinkJsCloseWindow();
//
$f->displayEndContent();

?>
