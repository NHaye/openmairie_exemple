<?php
/**
 * Ce fichier permet de declarer la classe PDF.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: db_fpdf.php 2942 2014-10-24 14:47:15Z fmichon $
 */

/**
 *
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
require_once PATH_OPENMAIRIE."om_debug.inc.php";
(defined("DEBUG") ? "" : define("DEBUG", PRODUCTION_MODE));
require_once PATH_OPENMAIRIE."om_logger.class.php";

/**
 * Inclusion de la classe FPDF qui permet de generer des fichiers PDF.
 */
require_once "fpdf.php";

/**
 * Cette methode surcharge la classe standard fpdf pour permettre la gestion
 * d'editions de resultats de requetes dans la base de donnnees.
 */
class PDF extends fpdf {

    /**
     *
     */
    function header() {

        //
        // ---no page
        $this->SetFont($GLOBALS['police'],'','9');
        $this->Cell(0,2,'Page '.$this->PageNo().' / {nb}',0,1,'R');
        // ---
        $this->SetFont($GLOBALS['police'],$GLOBALS['grastitre'],$GLOBALS['sizetitre']);
        $this->SetTextColor($GLOBALS['C1titre'],$GLOBALS['C2titre'],$GLOBALS['C3titre']);
        $this->SetFillColor($GLOBALS['C1titrefond'],$GLOBALS['C2titrefond'],$GLOBALS['C3titrefond']);
        //--------------CELL---------------------------//
        // width (default->0 jusqu'a la marge droite), //
        // height(default->0),                         //
        // texte                                       //
        // border 0 ou 1                               //
        // position 0 -> a droite                      //
        //         1 -> debut                          //
        //         2 ->dessous                         //
        // align L C R                                 //
        // fond cellule 0 (default)-> transparent      //
        //---------------------------------------------//
        if (isset($GLOBALS['flagsessionliste'])){
           if ( $GLOBALS['flagsessionliste']==1) {
              $this->Cell($GLOBALS['widthtableau'],$GLOBALS['heightitre'],$GLOBALS['libtitre']." ".$GLOBALS['nolibliste'],$GLOBALS['bordertitre'],1,$GLOBALS['aligntitre'],$GLOBALS['fondtitre']);
           }else{
              $this->Cell($GLOBALS['widthtableau'],$GLOBALS['heightitre'],$GLOBALS['libtitre'],$GLOBALS['bordertitre'],1,$GLOBALS['aligntitre'],$GLOBALS['fondtitre']);
           }
         }else{
              $this->Cell($GLOBALS['widthtableau'],$GLOBALS['heightitre'],$GLOBALS['libtitre'],$GLOBALS['bordertitre'],1,$GLOBALS['aligntitre'],$GLOBALS['fondtitre']);
         }
        $this->Cell($GLOBALS['widthtableau'],0,' ',$GLOBALS['bt'],1,'L',0);
        $this->ln(0.4);

    }

    /**
     *
     */
    function Footer() {

        $this->ln(0);
        $this->Cell($GLOBALS['widthtableau'],0,' ',$GLOBALS['bt'],1,'L',0);

    }

    /**
     *
     */
    function Table($query, &$db, $height, $border, $align, $fond, $police, $size, $multiplicateur, $flag_entete) {

        // =======================================================================
        // specifique framework spg
        // pdf.php
        // =======================================================================
        // Exécution de la requête
        $res = $db->query($query);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$query."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        if (database::isError($res)) {
           $this->erreur_db($res->getDebugInfo(),$res->getMessage(),'');
         }else{
         $info=$res->tableInfo();
         $GLOBALS['nbchamp']=count($info);
         //
         $this->SetFont($police,'',$size);
        $this->ln();
        if ($flag_entete==1){
            $this->SetFillColor($GLOBALS['C1fondentete'],$GLOBALS['C2fondentete'],$GLOBALS['C3fondentete']);
            $this->SetTextColor($GLOBALS['C1entetetxt'],$GLOBALS['C2entetetxt'],$GLOBALS['C3entetetxt']);
            for($k=0;$k<$GLOBALS['nbchamp'];$k++){
               $this->Cell($GLOBALS['l'.$k],$GLOBALS['heightentete'],strtoupper($info[$k]['name']),$GLOBALS['be'.$k],0,$GLOBALS['ae'.$k],$GLOBALS['fondentete']);
            }
            $this->ln();
        }
        //
        $couleur=1;
        $this->SetTextColor($GLOBALS['C1'],$GLOBALS['C2'],$GLOBALS['C3']);
        //
        $total=array();
        //
        $cptenr=0;
        $flagtot=0;
        $flagmoy=0;
         while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
               //
               if ($couleur==1){
                  $this->SetFillColor($GLOBALS['C1fond1'],$GLOBALS['C2fond1'],$GLOBALS['C3fond1']);
                  $couleur=0;
               }else{
                  $this->SetFillColor($GLOBALS['C1fond2'],$GLOBALS['C2fond2'],$GLOBALS['C3fond2']);
                  $couleur=1;
               }
              //
              for($j=0;$j<$GLOBALS['nbchamp'];$j++){
                 if (isset($GLOBALS['chnd'.$j]) AND TRIM($GLOBALS['chnd'.$j])!=""){
                    //champs non numerique = 999 , numerique
                    if ($GLOBALS['chnd'.$j]==999){
                         $this->Cell($GLOBALS['l'.$j],$height,$row[$info[$j]['name']],$GLOBALS['b'.$j],0,$GLOBALS['a'.$j],$fond);
                    }else{
                         //calcul totaux
                          if (isset($GLOBALS['chtot'.$j]) AND TRIM($GLOBALS['chtot'.$j])!=""){
                             if ($GLOBALS['chtot'.$j]==1 or $GLOBALS['chmoy'.$j]==1){
                                if (!isset($total[$j])) $total[$j]=0;
                                $total[$j] = $total[$j]+$row[$info[$j]['name']];
                                 if ($GLOBALS['chtot'.$j]==1){
                                    if( $flagtot==0){
                                       $flagtot=1;
                                    }
                                 }
                                 if ($GLOBALS['chmoy'.$j]==1){
                                    if( $flagmoy==0){
                                        $flagmoy=1;
                                    }
                                }
                             }
                          }
                         $this->Cell($GLOBALS['l'.$j],$height,number_format($row[$info[$j]['name']], $GLOBALS['chnd'.$j], ',', ' '),$GLOBALS['b'.$j],0,$GLOBALS['a'.$j],$fond);
                    }
                 }else{
                    if (defined("DBCHARSET") && (DBCHARSET == 'UTF8')) {
                        $this->Cell($GLOBALS['l'.$j],$height,utf8_decode($row[$info[$j]['name']]),$GLOBALS['b'.$j],0,$GLOBALS['a'.$j],$fond);
                    } else {
                        $this->Cell($GLOBALS['l'.$j],$height,$row[$info[$j]['name']],$GLOBALS['b'.$j],0,$GLOBALS['a'.$j],$fond);
                    }
                 }
              }// fin for
              $cptenr=$cptenr+1;
              $this->ln();
         }//fin while
         //
         //affichage totaux----------------------------------------------------
         if ($flagtot==1){
             for($k=0;$k<$GLOBALS['nbchamp'];$k++){
                  if ($GLOBALS['chtot'.$k]==1 or $GLOBALS['chtot'.$k]==2){
                      $this->Cell($GLOBALS['l'.$k],$height,number_format($total[$k], $GLOBALS['chnd'.$k], ',', ' '),$border,0,$GLOBALS['a'.$k],$fond);
                  }else{
                     if ($k==0){
                        $this->Cell($GLOBALS['l'.$k],$height,'TOTAL',$border,0,$GLOBALS['a'.$k],$fond);
                     }else{
                        $this->Cell($GLOBALS['l'.$k],$height,'',$border,0,$GLOBALS['a'.$k],$fond);
                     }
                  }
             }//fin for k
             $this->ln();
          }
         //affichage moyenne---------------------------------------------------
          if ($flagmoy==1){
              for($w=0;$w<$GLOBALS['nbchamp'];$w++){
               if ($GLOBALS['chmoy'.$w]==1){
                   $this->Cell($GLOBALS['l'.$w],$height,number_format(($total[$w]/$cptenr), $GLOBALS['chnd'.$w], ',', ' '),$border,0,$GLOBALS['a'.$w],$fond);
               }else{
                  if ($w==0){
                     $this->Cell($GLOBALS['l'.$w],$height,'MOYENNE',$border,0,$GLOBALS['a'.$w],$fond);
                  }else{
                     $this->Cell($GLOBALS['l'.$w],$height,'',$border,0,$GLOBALS['a'.$w],$fond);
                  }
               }
             }//fin for k
         }//fin moyenne
        } // fin error_db

    }

    /**
     *
     */
    function suppglobal() {


        //destruction variables globales-----------------------------------------
        //header
        if (isset($GLOBALS['libtitre'])) unset($GLOBALS['libtitre']);
        if (isset($GLOBALS['police'])) unset($GLOBALS['police']);
        if (isset($GLOBALS['bordertitre'])) unset($GLOBALS['bordertitre']);
        if (isset($GLOBALS['grastitre'])) unset($GLOBALS['grastitre']);
        if (isset($GLOBALS['sizetitre'])) unset($GLOBALS['sizetitre']);
        if (isset($GLOBALS['C1titre'])) unset($GLOBALS['C1titre']);
        if (isset($GLOBALS['C2titre'])) unset($GLOBALS['C2titre']);
        if (isset($GLOBALS['C3titre'])) unset($GLOBALS['C3titre']);
        if (isset($GLOBALS['heightitre'])) unset($GLOBALS['heightitre']);
        if (isset($GLOBALS['C1titrefond'])) unset($GLOBALS['C1titrefond']);
        if (isset($GLOBALS['C2titrefond'])) unset($GLOBALS['C2titrefond']);
        if (isset($GLOBALS['C3titrefond'])) unset($GLOBALS['C3titrefond']);
        if (isset($GLOBALS['fondtitre'])) unset($GLOBALS['fondtitre']);
        if (isset($GLOBALS['aligntitre'])) unset($GLOBALS['aligntitre']);
        if (isset($GLOBALS['flagsessionliste'])) unset($GLOBALS['flagsessionliste']);
        if (isset($GLOBALS['nolibliste'])) unset($GLOBALS['nolibliste']);
        // table
        if (isset($GLOBALS['C1fond1'])) unset($GLOBALS['C1fond1']);
        if (isset($GLOBALS['C2fond1'])) unset($GLOBALS['C2fond1']);
        if (isset($GLOBALS['C3fond1'])) unset($GLOBALS['C3fond1']);
        if (isset($GLOBALS['C1fond2'])) unset($GLOBALS['C1fond2']);
        if (isset($GLOBALS['C2fond2'])) unset($GLOBALS['C2fond2']);
        if (isset($GLOBALS['C3fond2'])) unset($GLOBALS['C3fond2']);
        if (isset($GLOBALS['C1fondentete'])) unset($GLOBALS['C1fondentete']);
        if (isset($GLOBALS['C2fondentete'])) unset($GLOBALS['C2fondentete']);
        if (isset($GLOBALS['C3fondentete'])) unset($GLOBALS['C3fondentete']);
        if (isset($GLOBALS['fondentete'])) unset($GLOBALS['fondentete']);
        if (isset($GLOBALS['heightentete'])) unset($GLOBALS['heightentete']);
        if (isset($GLOBALS['C1entetetxt'])) unset($GLOBALS['C1entetetxt']);
        if (isset($GLOBALS['C2entetetxt'])) unset($GLOBALS['C2entetetxt']);
        if (isset($GLOBALS['C3entetetxt'])) unset($GLOBALS['C3entetetxt']);
        if (isset($GLOBALS['C1'])) unset($GLOBALS['C1']);
        if (isset($GLOBALS['C2'])) unset($GLOBALS['C2']);
        if (isset($GLOBALS['C3'])) unset($GLOBALS['C3']);
        //
        for($z=0;$z<$GLOBALS['nbchamp'];$z++){
          if (isset($GLOBALS['be'.$z])) unset($GLOBALS['be'.$z]);
          if (isset($GLOBALS['ae'.$z])) unset($GLOBALS['ae'.$z]);
          if (isset($GLOBALS['l'.$z])) unset($GLOBALS['l'.$z]);
          if (isset($GLOBALS['a'.$z])) unset($GLOBALS['a'.$z]);
          if (isset($GLOBALS['chnd'.$z])) unset($GLOBALS['chnd'.$z]);
          if (isset($GLOBALS['chtot'.$z])) unset($GLOBALS['chtot'.$z]);
          if (isset($GLOBALS['b'.$z])) unset($GLOBALS['b'.$z]);
          if (isset($GLOBALS['chmoy'.$z])) unset($GLOBALS['chmoy'.$z]);
        }
        if (isset($GLOBALS['nbchamp'])) unset($GLOBALS['nbchamp']);

    }

    // {{{ Gestion des messages de debug

    /**
     *
     */
    function addToLog($message, $type = DEBUG_MODE) {
        //
        logger::instance()->log("class ".get_class($this)." - ".$message, $type);
    }

    // }}}

    /**
     * Cette methode ne doit plus etre appelee, c'est 'message::isError($res)'
     * qui s'occupe d'afficher le message d'erreur et de faire le 'die()'.
     *
     * @deprecated
     */
    function erreur_db($debuginfo, $messageDB, $table) {
        die(_("Erreur de base de donnees. Contactez votre administrateur."));
    }

}

/**
 *
 * @todo Pourquoi cette fonction/methode est en dehors de la classe ?
 */
function suppglobal0() {
    if (isset($GLOBALS['widthtableau'])) unset($GLOBALS['widthtableau']);
    if (isset($GLOBALS['bt'])) unset($GLOBALS['bt']);
}

?>
