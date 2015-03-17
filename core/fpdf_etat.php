<?php
/**
 * Ce fichier permet de declarer la classe PDF.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: fpdf_etat.php 3010 2015-01-16 08:53:16Z nmeucci $
 */

/**
 *
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
require_once PATH_OPENMAIRIE."om_debug.inc.php";
(defined("DEBUG") ? "" : define("DEBUG", PRODUCTION_MODE));
require_once PATH_OPENMAIRIE."om_logger.class.php";


/**
 * Inclusion de la classe TCPDF qui permet de generer des fichiers PDF.
 */
require_once('../php/tcpdf_include.php');

/**
 * Cette classe surcharge la classe standard TCPDF pour permettre la gestion
 * d'états et de sous-états de résultats de requêtes dans la base de donnnées.
 */
class PDF extends TCPDF {
    
    /**
     * Font du texte de pied de page.
     */
    var $footerfont;
    
    /**
     * Filigrane actif ou non
     */
    var $watermark;

    /**
     * 
     */
    var $footerattribut;
    
    /**
     * Taille du texte de pied de page.
     */
    var $footertaille;
    
    /**
     * Variables utilisées pour la génération des code barres.
     */
    // Tableau des codes 128
    var $T128;
    // Jeu de caractères éligibles au code 128
    var $ABCset="";
    // Set A du jeu de caractères éligibles
    var $Aset="";
    // Set B du jeu de caractères éligibles
    var $Bset="";
    // Set C du jeu de caractères éligibles
    var $Cset="";
    //Convertisseur source des jeux vers le tableau
    var $SetFrom;
    // Convertisseur destination des jeux vers le tableau
    var $SetTo;
    // Caractères de sélection de jeu au début du code 128
    var $JStart = array("A"=>103, "B"=>104, "C"=>105);
    // Caractères de changement de jeu
    var $JSwap = array("A"=>101, "B"=>100, "C"=>99);

    /**
     * Méthode d'affichage du pied de page.
     */
    function Footer() {
        // surcharge fpdf
        //Pied de page
        //Positionnement a 1,5 cm du bas
        $this->SetY(-15);
        //Police Arial italique 8
        $this->SetFont('helvetica', 'I', 8);
        //Numero de page
        $this->Cell(
            0,
            10,
            'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(),
            0,
            0,
            'C'
        );
    }

    /**
     * Méthode d'affichage de l'entête de page
     */
    function Header() {
        // Si le paramétrage du filigrane est actif
        if ($this->getWatermark() == true) {
            // On l'ajoute sur la page en cours
            $this->displayWatermark();
        }
    }

    /**
     * Affiche un filigrane "DOCUMENT DE TRAVAIL" en fond si appelé depuis
     * le header. Il est composé de deux lignes obliques qui barrent la page.
     */
    function displayWatermark() {
        // Police (police, style, taille)
        $this->SetFont('courier', 'B', 40);
        // Couleur (niveau de gris)
        $this->SetTextColor(200);
        // Position, texte et angle de rotation
        $text1 = "IL - DOCUMENT DE TRAVAIL - DOCUMENT";
        $text2 = "DE TRAVAIL - DOCUMENT DE TRAVAIL - DOCUMENT DE";
        $this->TextWithRotation(0,180,$text1, 45);
        $this->TextWithRotation(0,340,$text2, 45);
    }

    /**
     * Méthode de rotation de texte.
     *
     * @param [type]  $xr         [description]
     * @param [type]  $yr         [description]
     * @param [type]  $txtr       [description]
     * @param [type]  $txtr_angle [description]
     * @param integer $font_angle [description]
     */
    function TextWithRotation($xr, $yr, $txtr, $txtr_angle, $font_angle = 0) {
        //nouvelle fonction : ROTATION texte 90 45 .....
        $txtr=str_replace(
            ')',
            '\\)',
            str_replace(
                '(',
                '\\(',
                str_replace(
                    '\\',
                    '\\\\',
                    $txtr
                )
            )
        );
        //
        $font_angle+=90+$txtr_angle;
        $txtr_angle*=M_PI/180;
        $font_angle*=M_PI/180;
        //
        $txtr_dx=cos($txtr_angle);
        $txtr_dy=sin($txtr_angle);
        $font_dx=cos($font_angle);
        $font_dy=sin($font_angle);
        //
        $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',
            $txtr_dx,
            $txtr_dy,
            $font_dx,
            $font_dy,
            $xr*$this->k,
            ($this->h-$yr)*$this->k,
            $txtr
        );
        if ($this->ColorFlag) {
            $s='q '.$this->TextColor.' '.$s.' Q';
        }
        $this->_out($s);
    }

    /**
     * DEPRECATED 4.0.0
     */
    function sousetat(&$db, $etat, $sousetat) {
        $GLOBALS['entete_flag']=$sousetat['entete_flag'];
        // Exécution de la requête
        $res = $db->query($sousetat['sql']);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sousetat['sql']."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        if (database::isError($res)) {
            $this->erreur_db($res->getDebugInfo(),$res->getMessage(),'');
        } else {
            $info=$res->tableInfo();
        }
        $this->SetDrawColor(
            $sousetat['bordure_couleur'][0],
            $sousetat['bordure_couleur'][1],
            $sousetat['bordure_couleur'][2]
        );////couleur du trace
        //intervalle
        $this->ln($sousetat['intervalle_debut']);
        //titre
        $this->SetFillColor(
            $sousetat['titrefondcouleur'][0],
            $sousetat['titrefondcouleur'][1],
            $sousetat['titrefondcouleur'][2]
        );
        $this->SetTextColor(
            $sousetat['titretextecouleur'][0],
            $sousetat['titretextecouleur'][1],
            $sousetat['titretextecouleur'][2]
        );
        $this->SetFont(
            $sousetat["titrefont"],
            $sousetat["titreattribut"],
            $sousetat["titretaille"]
        );
        $this->MultiCell(
            $sousetat['tableau_largeur'],
            $sousetat["titrehauteur"],
            $sousetat["titre"],
            $sousetat["titrebordure"],
            $sousetat["titrealign"],
            $sousetat["titrefond"]
        );
        //
        $nbchamp=count($info);
        //
        $this->SetFont($etat['se_font'], '', $sousetat['tableau_fontaille']);
        // ENTETE
        if ($sousetat['entete_flag']==1) {
            $this->SetFillColor(
                $sousetat['entete_fondcouleur'][0],
                $sousetat['entete_fondcouleur'][1],
                $sousetat['entete_fondcouleur'][2]
            );
            $this->SetTextColor(
                $sousetat['entete_textecouleur'][0],
                $sousetat['entete_textecouleur'][1],
                $sousetat['entete_textecouleur'][2]
            );
            //texte horizontal
            if (!isset($sousetat['entete_orientation'])) {
                for($k=0; $k<$nbchamp; $k++) {
                    $this->Cell(
                        $sousetat['cellule_largeur'][$k],
                        $sousetat['entete_hauteur'],
                        mb_strtoupper($info[$k]['name']),
                        $sousetat['entetecolone_bordure'][$k],
                        0,
                        $sousetat['entetecolone_align'][$k],
                        $sousetat['entete_fond']
                    );
                }
            } else {
                //texte avec angle
                for ($k=0;$k<$nbchamp;$k++) {
                    //texte horizontal si entete_orientation =0
                    if ($sousetat['entete_orientation'][$k] == 0) {
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['entete_hauteur'],
                            mb_strtoupper($info[$k]['name']),
                            $sousetat['entetecolone_bordure'][$k],
                            0,
                            $sousetat['entetecolone_align'][$k],
                            $sousetat['entete_fond']
                        );
                    } else {
                        if ($sousetat['entete_orientation'][$k] > 0) {
                            $this->Cell(
                                $sousetat['cellule_largeur'][$k],
                                $sousetat['entete_hauteur'],
                                '',
                                $sousetat['entetecolone_bordure'][$k],
                                0,
                                $sousetat['entetecolone_align'][$k],
                                $sousetat['entete_fond']
                            );
                            $xd=$this->Getx();
                            $yd=$this->Gety();
                            $xd=$xd-(floor($sousetat['cellule_largeur'][$k]/2));
                            if ($sousetat['entete_orientation'][$k] < 91) {
                                $yd=($yd+$sousetat['entete_hauteur'])-1;
                            } else {
                                $yd=($yd+$sousetat['entete_hauteur'])-5;
                            }
                            $this->TextWithRotation(
                                $xd,
                                $yd,
                                mb_strtoupper($info[$k]['name']),
                                $sousetat['entete_orientation'][$k],
                                0
                            );
                        } else {
                            $this->Cell(
                                $sousetat['cellule_largeur'][$k],
                                $sousetat['entete_hauteur'],
                                '',
                                $sousetat['entetecolone_bordure'][$k],
                                0,
                                $sousetat['entetecolone_align'][$k],
                                $sousetat['entete_fond']
                            );
                            $xd=$this->Getx();
                            $yd=$this->Gety();
                            $xd = $xd -
                                floor((($sousetat['cellule_largeur'][$k]/2))) -
                                floor(strlen ($info[$k]['name']));
                            $yd= ($yd + $sousetat['entete_hauteur']) - 3;
                            $this->TextWithRotation(
                                $xd,
                                $yd,
                                mb_strtoupper($info[$k]['name']),
                                $sousetat['entete_orientation'][$k],
                                0
                            );
                        }
                    }//mo 27 mars 2008 fin else entete_orientation'][$k]different de zero
                }//fin for
            }
            //
            $this->ln();
        }
        //
        $couleur=1;
        $this->SetTextColor(
            $etat['se_couleurtexte'][0],
            $etat['se_couleurtexte'][1],
            $etat['se_couleurtexte'][2]
        );
        //  initialisation
        for($j=0; $j<$nbchamp; $j++) {
            $total[$j]=0;
        }
        $cptenr=0;
        $flagtotal=0;
        $flagmoyenne=0;
        $flagcompteur=0;
        //
        while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            //
            if ($couleur==1){
                $this->SetFillColor(
                    $sousetat['se_fond1'][0],
                    $sousetat['se_fond1'][1],
                    $sousetat['se_fond1'][2]
                );
                $couleur=0;
            } else {
                $this->SetFillColor(
                    $sousetat['se_fond2'][0],
                    $sousetat['se_fond2'][1],
                    $sousetat['se_fond2'][2]
                );
                $couleur=1;
            }
            //
            //preparer multiligne
            $max_ln=1;  
            $multi_height=$sousetat['cellule_hauteur'];
            //Etablir nb lignes necessaires et preparer chaines avec \n
            for ($j=0; $j<$nbchamp; $j++) {
                // A ajouter eventuellement dans .sousetat.inc
                // //a 1 texte organise en multiligne, avec autre valeur texte compresse
                // $sousetat['cellule_multiligne']=array("0","0","1","1","1","0","0","1","1","0");
                // //pourcentage de hauteur utilisee pour 1 ligne d'une cellule multiligne
                // $sousetat['cellule_hautmulti']=1/2; 
                if (isset($sousetat['cellule_multiligne'])) {
                    //si variable definie, valeur a 1 => multiligne 
                    if ($sousetat['cellule_multiligne'][$j] == 1) {
                        $t_ln=$this->PrepareMultiCell(
                            $sousetat['cellule_largeur'][$j],
                            $row[$info[$j]['name']]
                        );
                        if ($t_ln>$max_ln) {
                            $max_ln=$t_ln;
                        }
                    }
                    // sinon compression
                } else {
                    //si variable non definie, multiligne par defaut
                    $t_ln=$this->PrepareMultiCell(
                        $sousetat['cellule_largeur'][$j],
                        $row[$info[$j]['name']]
                    );
                    if ($t_ln>$max_ln) {
                        $max_ln=$t_ln;
                    }
                }
            }
            // fixation de la nouvelle hauteur si plus d'1 ligne selon quota
            // hauteur/nblignesmulti ou pas
            if ($max_ln > 1) {
                //si valeur cellule_hautmulti existe
                if (isset($sousetat['cellule_hautmulti'])) {
                    $multi_height=
                        $max_ln *
                        $sousetat['cellule_hauteur'] *
                        $sousetat['cellule_hautmulti'];
                } else  {
                    //sinon valeur par defaut 1/2
                    $multi_height = $max_ln * $sousetat['cellule_hauteur'] * 1 / 2;
                }
            }
            for ($j=0; $j<$nbchamp; $j++) {
                if (isset($sousetat['cellule_numerique'][$j]) and
                    trim($sousetat['cellule_numerique'][$j])!="") {
                    //champs non numerique = 999 , numerique
                    if ($sousetat['cellule_numerique'][$j] == 999) {
                        // non numerique
                        if ($cptenr==0) {
                            $this->Cell(
                                $sousetat['cellule_largeur'][$j],
                                $multi_height,$row[$info[$j]['name']],
                                $sousetat['cellule_bordure_un'][$j],
                                0,
                                $sousetat['cellule_align'][$j],
                                $sousetat['cellule_fond']
                            );
                        } else {
                            $this->Cell(
                                $sousetat['cellule_largeur'][$j],
                                $multi_height,
                                $row[$info[$j]['name']],
                                $sousetat['cellule_bordure'][$j],
                                0,
                                $sousetat['cellule_align'][$j],
                                $sousetat['cellule_fond']
                            );
                        }
                    } else {
                        // numerique
                        if ($cptenr==0) {
                            $this->Cell(
                                $sousetat['cellule_largeur'][$j],
                                $multi_height,
                                number_format(
                                    $row[$info[$j]['name']],
                                    $sousetat['cellule_numerique'][$j],
                                    ',',
                                    ' '
                                ),
                                $sousetat['cellule_bordure_un'][$j],
                                0,
                                $sousetat['cellule_align'][$j],
                                $sousetat['cellule_fond']
                            );
                        } else {
                            $this->Cell(
                                $sousetat['cellule_largeur'][$j],
                                $multi_height,
                                number_format(
                                    $row[$info[$j]['name']],
                                    $sousetat['cellule_numerique'][$j],
                                    ',',
                                    ' '
                                ),
                                $sousetat['cellule_bordure'][$j],
                                0,
                                $sousetat['cellule_align'][$j],
                                $sousetat['cellule_fond']
                            );
                        }
                        // si total = calcul variable total
                        if ($sousetat['cellule_total'][$j]==1) {
                            $total[$j] = $total[$j]+$row[$info[$j]['name']];
                            $flagtotal = 1;
                        }
                        if ($sousetat['cellule_moyenne'][$j]==1) {
                            if ($flagtotal == 0) {
                                $total[$j] = $total[$j]+$row[$info[$j]['name']];
                            }
                            $flagmoyenne=1;
                        }
                    }
                }
                if ($sousetat['cellule_compteur'][$j]==1) {
                    $flagcompteur=1;
                }
            } // fin for
            $cptenr=$cptenr+1;
            $this->ln();
        } //fin while
        //
        // apres derniere ligne
        if ($sousetat['tableau_bordure'] == "1") {
            $this->Cell($sousetat['tableau_largeur'], 0, '', "T", 1, 'L', 0);
        }
        //affichage total----------------------------------------------------
        if ($flagtotal == 1) {
            for ($k=0; $k<$nbchamp; $k++) {
                if ($sousetat['cellule_total'][$k] == 1) {
                    $this->SetFont(
                        $etat['se_font'],
                        '',
                        $sousetat['cellule_fontaille_total']
                    );
                    $this->SetFillColor(
                        $sousetat['cellule_fondcouleur_total'][0],
                        $sousetat['cellule_fondcouleur_total'][1],
                        $sousetat['cellule_fondcouleur_total'][2]
                    );
                    $this->Cell(
                        $sousetat['cellule_largeur'][$k],
                        $sousetat['cellule_hauteur_total'],
                        number_format(
                            $total[$k],
                            $sousetat['cellule_numerique'][$k],
                            ',',
                            ' '
                        ),
                        $sousetat['cellule_bordure_total'][$k],
                        0,
                        $sousetat['cellule_align_total'][$k],
                        $sousetat['cellule_fond_total']
                    );
                } else {// affichage sur la colone correspondante
                    if ($k==0) {
                        // 1ere colone
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_total']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_total'][0],
                            $sousetat['cellule_fondcouleur_total'][1],
                            $sousetat['cellule_fondcouleur_total'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_total'],
                            'TOTAL',
                            $sousetat['cellule_bordure_total'][$k],
                            0,
                            $sousetat['cellule_align_total'][$k],
                            $sousetat['cellule_fond_total']
                        );
                    } else {
                        // colones suivante
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_total']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_total'][0],
                            $sousetat['cellule_fondcouleur_total'][1],
                            $sousetat['cellule_fondcouleur_total'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_total'],
                            '',
                            $sousetat['cellule_bordure_total'][$k],
                            0,
                            $sousetat['cellule_align_total'][$k],
                            $sousetat['cellule_fond_total']
                        );
                    }
                }
            } //fin for k
            $this->ln();
        }
        //$k=0;
        //affichage moyenne----------------------------------------------------
        if ($flagmoyenne==1) {
            for ($k=0; $k<$nbchamp; $k++) {
                if ($sousetat['cellule_moyenne'][$k] == 1) {
                    $this->SetFont(
                        $etat['se_font'],
                        '',
                        $sousetat['cellule_fontaille_moyenne']
                    );
                    $this->SetFillColor(
                        $sousetat['cellule_fondcouleur_moyenne'][0],
                        $sousetat['cellule_fondcouleur_moyenne'][1],
                        $sousetat['cellule_fondcouleur_moyenne'][2]
                    );
                    $this->Cell(
                        $sousetat['cellule_largeur'][$k],
                        $sousetat['cellule_hauteur_moyenne'],
                        number_format(
                            $total[$k]/$cptenr,
                            $sousetat['cellule_numerique'][$k],
                            ',',
                            ' '
                        ),
                        $sousetat['cellule_bordure_moyenne'][$k],
                        0,
                        $sousetat['cellule_align_moyenne'][$k],
                        $sousetat['cellule_fond_moyenne']
                    );
                } else {
                    // affichage sur la colone correspondante
                    if ($k == 0) {
                        // 1ere colone
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_moyenne']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_moyenne'][0],
                            $sousetat['cellule_fondcouleur_moyenne'][1],
                            $sousetat['cellule_fondcouleur_moyenne'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_moyenne'],
                            'MOYENNE',
                            $sousetat['cellule_bordure_moyenne'][$k],
                            0,
                            $sousetat['cellule_align_moyenne'][$k],
                            $sousetat['cellule_fond_moyenne']
                        );
                    } else {
                        // colones suivante
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_moyenne']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_moyenne'][0],
                            $sousetat['cellule_fondcouleur_moyenne'][1],
                            $sousetat['cellule_fondcouleur_moyenne'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_moyenne'],
                            '',
                            $sousetat['cellule_bordure_moyenne'][$k],
                            0,
                            $sousetat['cellule_align_moyenne'][$k],
                            $sousetat['cellule_fond_moyenne']
                        );
                    }
                }
            } //fin for k
            $this->ln();
        }
        //affichage compteur----------------------------------------------------
        if ($flagcompteur == 1) {
            for ($k=0; $k<$nbchamp; $k++) {
                if ($sousetat['cellule_compteur'][$k] == 1) {
                    $this->SetFont(
                        $etat['se_font'],
                        '',
                        $sousetat['cellule_fontaille_nbr']
                    );
                    $this->SetFillColor(
                        $sousetat['cellule_fondcouleur_nbr'][0],
                        $sousetat['cellule_fondcouleur_nbr'][1],
                        $sousetat['cellule_fondcouleur_nbr'][2]
                    );
                    $this->Cell(
                        $sousetat['cellule_largeur'][$k],
                        $sousetat['cellule_hauteur_nbr'],
                        number_format($cptenr, 0, ',', ' '),
                        $sousetat['cellule_bordure_nbr'][$k],
                        0,
                        $sousetat['cellule_align_nbr'][$k],
                        $sousetat['cellule_fond_nbr']
                    );
                } else {
                    // affichage sur la colone correspondante
                    if ($k==0) {
                        // 1ere colone
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_nbr']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_nbr'][0],
                            $sousetat['cellule_fondcouleur_nbr'][1],
                            $sousetat['cellule_fondcouleur_nbr'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_nbr'],
                            'NOMBRE',
                            $sousetat['cellule_bordure_nbr'][$k],
                            0,
                            $sousetat['cellule_align_nbr'][$k],
                            $sousetat['cellule_fond_nbr']
                        );
                    } else {
                        // colones suivante
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_nbr']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_nbr'][0],
                            $sousetat['cellule_fondcouleur_nbr'][1],
                            $sousetat['cellule_fondcouleur_nbr'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_nbr'],
                            '',
                            $sousetat['cellule_bordure_nbr'][$k],
                            0,
                            $sousetat['cellule_align_nbr'][$k],
                            $sousetat['cellule_fond_nbr']
                        );
                    }
                }
            } //fin for k
            $this->ln();
        }
        if ($cptenr > 0) {
            $this->ln($sousetat['intervalle_fin']);
        }
    }
    
    /**
     * Initialisation des sous-états : ajout d'une balise tcpdf
     *
     * @param utilis  $f      handler om_application
     * @param string  $etat   identifiant de l'état
     * @param string  $corps  corps de l'état
     * @param integer $niveau niveau de l'utilisateur
     *
     * @return string         corps de l'état avec initialisation des se
     */
    function initSousEtats($f, $etat, $corps, $niveau) {
        $this->f=$f;
        // XXX
        // Bug si texte riche inséré dans du texte riche :
        // warning à cause d'une balise P dans P.
        // Solution palliative : désactiver les erreurs.
        libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $dom->loadHTML($corps);
        libxml_clear_errors();
        $xPath = new DOMXPath($dom);
        // Pour chaque sous-état
        foreach($xPath->query("//*[contains(@class, 'mce_sousetat')]") as $node) {
            $nomsousetat = $node->getAttribute("id");
            // On récupère l'enregistrement 'om_sousetat' de la collectivité en
            // cours dans l'état 'actif'
            $sql = " select * from ".DB_PREFIXE."om_sousetat ";
            $sql .= " where id='".trim($nomsousetat)."' ";
            $sql .= " and actif IS TRUE ";
            $sql .= " and om_collectivite='".$_SESSION['collectivite']."' ";
            // Exécution de la requête
            $res2 = $f->db->query($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res2);
            // Si on obtient aucun résultat
            if ($res2->numrows() == 0) {
                // On libère le résultat de la requête précédente
                $res2->free();
                //
                if ($niveau == "") {
                    // On récupère l'identifiant de la collectivité de niveau 2
                    $sql = "select om_collectivite from ".DB_PREFIXE."om_collectivite ";
                    $sql .= " where niveau='2' ";
                    // Exécution de la requête
                    $niveau = $f->db->getone($sql);
                    // Logger
                    $this->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
                    // Vérification d'une éventuelle erreur de base de données
                    $f->isDatabaseError($niveau);
                }
                // On récupère l'enregistrement 'om_sousetat' de la collectivité
                // de niveau 2 dans l'état 'actif'
                $sql = " select * from ".DB_PREFIXE."om_sousetat ";
                $sql .= " where id='".trim($nomsousetat)."'";
                $sql .= " and actif IS TRUE ";
                $sql .= " and om_collectivite='".$niveau."' ";
                // Exécution de la requête
                $res2 = $f->db->query($sql);
                // Logger
                $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
                // Vérification d'une éventuelle erreur de base de données
                $f->isDatabaseError($res2);
                // Si on obtient aucun résultat
                if ($res2->numrows() == 0) {
                    // On libère le résultat de la requête précédente
                    $res2->free();
                    // On récupère l'enregistrement 'om_sousetat' de la collectivité de
                    // niveau 2 dans n'importe quel état
                    $sql = " select * from ".DB_PREFIXE."om_sousetat ";
                    $sql .= " where id='".trim($nomsousetat)."' ";
                    $sql .= " and om_collectivite='".$niveau."' ";
                    // Exécution de la requête
                    $res2 = $f->db->query($sql);
                    // Logger
                    $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
                    // Vérification d'une éventuelle erreur de base de données
                    $f->isDatabaseError($res2);
                }
            }
            // Définition de la valeur $collectivité pour inclure le fichier de
            // substitution varetatpdf.inc
            $collectivite = $this->f->collectivite;

            //
            while ($sousetat =& $res2->fetchRow(DB_FETCHMODE_ASSOC)) {
                //
                $sql = '';
                $titre = '';
                // Variables statiques contenant des paramètres à remplacer
                $sql = $sousetat['om_sql'];
                $titre = $sousetat['titre'];
                // Remplacement des paramètres dans le fichier ../dyn/varetatpdf.inc
                if (file_exists("../dyn/varetatpdf.inc")) {
                    include "../dyn/varetatpdf.inc";
                }
                //
                $sousetat['om_sql'] = $sql;
                $sousetat['titre'] = $titre;

                $params = TCPDF_STATIC::serializeTCPDFtagParameters(
                array(
                    $f->db,
                    $etat,
                    $sousetat)
                );
                $node->nodeValue = '';
                $fragment = $dom->createDocumentFragment();
                $fragment->appendXML('<tcpdf method="sousetatdb" params="'.$params.'" />');
                $node->appendChild($fragment);
            }
        }

        return $dom->saveHTML();
    }

    /**
     * Méthode d'affichage des sous-états.
     *
     * @param array $sousetat tableau de paramétrage du sous-état
     * @param array $info     valeurs à afficher
     */
    function entete_sous_etat($sousetat, $info) {
        //
        $nbchamp=count($info);
        if ($sousetat['entete_flag'] == 1) {
            $this->SetFillColor(
                $sousetat['entete_fondcouleur'][0],
                $sousetat['entete_fondcouleur'][1],
                $sousetat['entete_fondcouleur'][2]
            );
            $this->SetTextColor(
                $sousetat['entete_textecouleur'][0],
                $sousetat['entete_textecouleur'][1],
                $sousetat['entete_textecouleur'][2]
            );
            //texte horizontal
            if (!isset($sousetat['entete_orientation'])) {
                //-------------------------------------------
                for($k=0;$k<$nbchamp;$k++) {
                    $this->Cell(
                        $sousetat['cellule_largeur'][$k],
                        $sousetat['entete_hauteur'],
                        mb_strtoupper(_($info[$k]['name']), "UTF-8"),
                        $sousetat['entetecolone_bordure'][$k],
                        0,
                        $sousetat['entetecolone_align'][$k],
                        $sousetat['entete_fond'],
                        '',
                        1
                    );
                }
                //-------------------------------------------
            } else {
                //texte avec angle
                for ($k=0; $k<$nbchamp; $k++) {
                    //mo 27 mars 2008-------------------------------------------
                    //texte horizontal si entete_orientation =0
                    if ($sousetat['entete_orientation'][$k] == 0) {
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['entete_hauteur'],
                            mb_strtoupper(_($info[$k]['name']), "UTF-8"),
                            $sousetat['entetecolone_bordure'][$k],
                            0,
                            $sousetat['entetecolone_align'][$k],
                            $sousetat['entete_fond'],
                            '',
                            1
                        );
                    } else {
                        //mo 27 mars 2008---------------------------------------
                        if ($sousetat['entete_orientation'][$k] > 0) {
                            $this->Cell(
                                $sousetat['cellule_largeur'][$k],
                                $sousetat['entete_hauteur'],
                                '',
                                $sousetat['entetecolone_bordure'][$k],
                                0,
                                $sousetat['entetecolone_align'][$k],
                                $sousetat['entete_fond']
                            );
                            $xd=$this->Getx();
                            $yd=$this->Gety();
                            $xd=$xd-(floor($sousetat['cellule_largeur'][$k]/2));
                            if ($sousetat['entete_orientation'][$k] < 91) {
                                $yd=($yd+$sousetat['entete_hauteur'])-1;
                            } else {
                                $yd=($yd+$sousetat['entete_hauteur'])-5;
                            }
                            $this->TextWithRotation(
                                $xd,
                                $yd,
                                mb_strtoupper(_($info[$k]['name']), "UTF-8"),
                                $sousetat['entete_orientation'][$k],
                                0
                            );
                        } else {
                            $this->Cell(
                                $sousetat['cellule_largeur'][$k],
                                $sousetat['entete_hauteur'],
                                '',
                                $sousetat['entetecolone_bordure'][$k],
                                0,
                                $sousetat['entetecolone_align'][$k],
                                $sousetat['entete_fond']
                            );
                            $xd=$this->Getx();
                            $yd=$this->Gety();
                            $xd = $xd - 
                                floor((($sousetat['cellule_largeur'][$k]/2))) -
                                floor(strlen (_($info[$k]['name'])));
                            $yd=($yd+$sousetat['entete_hauteur'])-3;
                            $this->TextWithRotation(
                                $xd,
                                $yd,
                                mb_strtoupper(_($info[$k]['name'])),
                                $sousetat['entete_orientation'][$k],
                                0
                            );
                        }
                    }//mo 27 mars 2008 fin else entete_orientation'][$k]different de zero
                }//fin for
            }
            //
            $this->ln();
        }
    }

    /**
     * Méthode d'affichage des sous états depuis la version 4.0.0.
     *
     * @param database $db       handler database
     * @param string   $etat     identifiant de l'état
     * @param array    $sousetat paramétrage du sous-état à afficher
     */
    function sousetatdb($db, $etat, $sousetat) {
        $GLOBALS['entete_flag']=$sousetat['entete_flag'];
        // Exécution de la requête
        $res = $db->query($sousetat['om_sql']);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sousetat['om_sql']."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        if (database::isError($res)) {
            $this->erreur_db($res->getDebugInfo(), $res->getMessage(), '');
        } else {
            $info=$res->tableInfo();
        }
        // {{{
        // transformer les valeurs de la base en tableau
        $sousetat['titrefondcouleur'] = explode(
            "-",
            $sousetat['titrefondcouleur']
        );
        $sousetat['titretextecouleur'] = explode(
            "-",
            $sousetat['titretextecouleur']
        );
        $sousetat['entete_orientation'] = explode(
            "|",
            $sousetat['entete_orientation']
        );
        $sousetat['entetecolone_bordure'] = explode(
            "|",
            $sousetat['entetecolone_bordure']
        );
        $sousetat['entetecolone_align'] = explode(
            "|",
            $sousetat['entetecolone_align']
        );
        $sousetat['entete_fondcouleur'] = explode(
            "-",
            $sousetat['entete_fondcouleur']
        );
        $sousetat['entete_textecouleur'] = explode(
            "-",
            $sousetat['entete_textecouleur']
        );
        $sousetat['bordure_couleur'] = explode(
            "-",
            $sousetat['bordure_couleur']
        );
        $sousetat['se_fond1'] = explode(
            "-",
            $sousetat['se_fond1']
        );
        $sousetat['se_fond2'] = explode(
            "-",
            $sousetat['se_fond2']
        );
        $sousetat['cellule_largeur'] = explode(
            "|",
            $sousetat['cellule_largeur']);    

        $sousetat['cellule_bordure_un'] = explode(
            "|",
            $sousetat['cellule_bordure_un']
        );
        $sousetat['cellule_bordure'] = explode(
            "|",
            $sousetat['cellule_bordure']
        );
        $sousetat['cellule_align'] = explode(
            "|",
            $sousetat['cellule_align']
        );
        $sousetat['cellule_fondcouleur_total'] = explode(
            "-",
            $sousetat['cellule_fondcouleur_total']
        );
        $sousetat['cellule_bordure_total'] = explode(
            "|",
            $sousetat['cellule_bordure_total']
        );
        $sousetat['cellule_align_total'] = explode(
            "|",
            $sousetat['cellule_align_total']
        );
        $sousetat['cellule_fondcouleur_moyenne'] = explode(
            "-",
            $sousetat['cellule_fondcouleur_moyenne']
        );
        $sousetat['cellule_bordure_moyenne'] = explode(
            "|",
            $sousetat['cellule_bordure_moyenne']
        );
        $sousetat['cellule_align_moyenne'] = explode(
            "|",
            $sousetat['cellule_align_moyenne']
        );
        $sousetat['cellule_fondcouleur_nbr'] = explode(
            "-",
            $sousetat['cellule_fondcouleur_nbr']
        );
        $sousetat['cellule_bordure_nbr'] = explode(
            "|",
            $sousetat['cellule_bordure_nbr']
        );
        $sousetat['cellule_align_nbr'] = explode(
            "|",
            $sousetat['cellule_align_nbr']
        );
        $sousetat['cellule_numerique'] = explode(
            "|",
            $sousetat['cellule_numerique']
        );
        $sousetat['cellule_total'] = explode(
            "|",
            $sousetat['cellule_total']
        );
        $sousetat['cellule_moyenne'] = explode(
            "|",
            $sousetat['cellule_moyenne']
        );
        $sousetat['cellule_compteur'] = explode(
            "|",
            $sousetat['cellule_compteur']
        );
        // }}}
        //couleur du tracé
        $this->SetDrawColor(
            $sousetat['bordure_couleur'][0],
            $sousetat['bordure_couleur'][1],
            $sousetat['bordure_couleur'][2]
        );
        //intervalle
        $this->ln($sousetat['intervalle_debut']);
        //titre
        $this->SetFillColor(
            $sousetat['titrefondcouleur'][0],
            $sousetat['titrefondcouleur'][1],
            $sousetat['titrefondcouleur'][2]
        );
        $this->SetTextColor(
            $sousetat['titretextecouleur'][0],
            $sousetat['titretextecouleur'][1],
            $sousetat['titretextecouleur'][2]
        );
        $this->SetFont(
            $sousetat["titrefont"],
            $sousetat["titreattribut"],
            $sousetat["titretaille"]
        );
        $this->MultiCell(
            $sousetat['tableau_largeur'],
            $sousetat["titrehauteur"],
            $sousetat["titre"],
            $sousetat["titrebordure"],
            $sousetat["titrealign"],
            $sousetat["titrefond"]
        );
        //
        $nbchamp=count($info);
        //
        $this->SetFont($etat['se_font'], '', $sousetat['tableau_fontaille']);
        // ENTETE
        $this->entete_sous_etat($sousetat, $info);
        //
        $couleur=1;
        $this->SetTextColor(
            $etat['se_couleurtexte'][0],
            $etat['se_couleurtexte'][1],
            $etat['se_couleurtexte'][2]
        );
        //  initialisation
        for($j=0; $j < $nbchamp; $j++) {
            $total[$j]=0;
        }
        $cptenr=0;
        $flagtotal=0;
        $flagmoyenne=0;
        $flagcompteur=0;
        //
        while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)) {

            //preparer multiligne
            $max_ln=1;  
            $multi_height=$sousetat['cellule_hauteur'];
            //Etablir nb lignes necessaires et preparer chaines avec \n
            for ($j=0; $j<$nbchamp; $j++) {
                // A ajouter eventuellement dans .sousetat.inc
                // //a 1 texte organise en multiligne, avec autre valeur texte compresse
                // $sousetat['cellule_multiligne']=
                //      array("0","0","1","1","1","0","0","1","1","0");
                // //pourcentage de hauteur utilisee pour 1 ligne d'une cellule multiligne
                // $sousetat['cellule_hautmulti']=1/2; 
                if (isset($sousetat['cellule_multiligne'])) {
                    //si variable definie, valeur a 1 => multiligne 
                    if ($sousetat['cellule_multiligne'][$j] == 1) {
                        $t_ln=$this->PrepareMultiCell(
                            $sousetat['cellule_largeur'][$j],
                            $row[$info[$j]['name']]
                        );
                        if ($t_ln>$max_ln) {
                            $max_ln=$t_ln;
                        }
                    }
                    // sinon compression
                } else {
                    //si variable non definie, multiligne par defaut
                    $t_ln=$this->PrepareMultiCell(
                        $sousetat['cellule_largeur'][$j],
                        $row[$info[$j]['name']]
                    );
                    if ($t_ln > $max_ln) {
                        $max_ln = $t_ln;
                    }
                }
            }
            //fixation de la nouvelle hauteur si plus d'1 ligne selon quota
            //hauteur/nblignesmulti ou pas
            if ($max_ln > 1) {
                if (isset($sousetat['cellule_hautmulti'])) { 
                    //si valeur cellule_hautmulti existe
                    $multi_height=
                        $max_ln*
                        $sousetat['cellule_hauteur']*
                        $sousetat['cellule_hautmulti'];
                } else { //sinon valeur par defaut 1/2
                    $multi_height=$max_ln*$sousetat['cellule_hauteur']*1/2;
                }
            }
            // Saut de page si pagebreak atteint
            if($this->checkPageBreak($multi_height)) {
                // ENTETE
                $this->entete_sous_etat($sousetat, $info);
            }  
            // Couleur des cellules
            if ($couleur == 1){
                $this->SetFillColor(
                    $sousetat['se_fond1'][0],
                    $sousetat['se_fond1'][1],
                    $sousetat['se_fond1'][2]
                );
                $couleur=0;
            } else {
                $this->SetFillColor(
                    $sousetat['se_fond2'][0],
                    $sousetat['se_fond2'][1],
                    $sousetat['se_fond2'][2]
                );
                $couleur=1;
            }
            for ($j=0; $j<$nbchamp; $j++) {
                if (isset($sousetat['cellule_numerique'][$j]) and
                    trim($sousetat['cellule_numerique'][$j])!=""){
                    //champs non numerique = 999 , numerique
                    if ($sousetat['cellule_numerique'][$j] == 999) {
                        // non numerique
                        $value = $row[$info[$j]['name']];

                        if ($cptenr==0) {
                            $this->MultiCell(
                                $sousetat['cellule_largeur'][$j],
                                $multi_height,
                                $value,
                                $sousetat['cellule_bordure_un'][$j],
                                $sousetat['cellule_align'][$j],
                                $sousetat['cellule_fond'],
                                0
                            );
                        } else {
                            $this->MultiCell(
                                $sousetat['cellule_largeur'][$j],
                                $multi_height,
                                $value,
                                $sousetat['cellule_bordure'][$j],
                                $sousetat['cellule_align'][$j],
                                $sousetat['cellule_fond'],
                                0
                            );
                        }
                    } else {
                        // numerique
                        if ($cptenr==0) {
                            $this->MultiCell(
                                $sousetat['cellule_largeur'][$j],
                                $multi_height,
                                number_format(
                                    $row[$info[$j]['name']],
                                    $sousetat['cellule_numerique'][$j],
                                    ',',
                                    ' '
                                ),
                                $sousetat['cellule_bordure_un'][$j],
                                $sousetat['cellule_align'][$j],
                                $sousetat['cellule_fond'],
                                0
                            );
                        } else {
                            $this->MultiCell(
                                $sousetat['cellule_largeur'][$j],
                                $multi_height,
                                number_format(
                                    $row[$info[$j]['name']],
                                    $sousetat['cellule_numerique'][$j],
                                    ',',
                                    ' '
                                ),
                                $sousetat['cellule_bordure'][$j],
                                $sousetat['cellule_align'][$j],
                                $sousetat['cellule_fond'],
                                0
                            );
                        }
                        // si total = calcul variable total
                        if ($sousetat['cellule_total'][$j]==1) {
                            $total[$j] = $total[$j]+$row[$info[$j]['name']];
                            $flagtotal=1;
                        }
                        if ($sousetat['cellule_moyenne'][$j]==1) {
                            if ($flagtotal == 0) {
                                $total[$j] = $total[$j]+$row[$info[$j]['name']];
                            }
                            $flagmoyenne=1;
                        }
                    }
                }
                if ($sousetat['cellule_compteur'][$j]==1) {
                    $flagcompteur=1;
                }
            } // fin for
            $cptenr=$cptenr+1;
            $this->ln();
        } //fin while
        //
        // apres derniere ligne
        if ($sousetat['tableau_bordure'] == "1") {
            $this->Cell($sousetat['tableau_largeur'], 0, '', "T", 1, 'L', 0);
        }
        //affichage total----------------------------------------------------
        if ($flagtotal==1) {
            for ($k=0;$k<$nbchamp;$k++) {
                if ($sousetat['cellule_total'][$k]==1) {
                    $this->SetFont(
                        $etat['se_font'],
                        '',
                        $sousetat['cellule_fontaille_total']
                    );
                    $this->SetFillColor(
                        $sousetat['cellule_fondcouleur_total'][0],
                        $sousetat['cellule_fondcouleur_total'][1],
                        $sousetat['cellule_fondcouleur_total'][2]
                    );
                    $this->Cell(
                        $sousetat['cellule_largeur'][$k],
                        $sousetat['cellule_hauteur_total'],
                        number_format(
                            $total[$k],
                            $sousetat['cellule_numerique'][$k],
                            ',',
                            ' '
                        ),
                        $sousetat['cellule_bordure_total'][$k],
                        0,
                        $sousetat['cellule_align_total'][$k],
                        $sousetat['cellule_fond_total']
                    );
                } else {// affichage sur la colone correspondante
                    if ($k==0) {
                        // 1ere colone
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_total']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_total'][0],
                            $sousetat['cellule_fondcouleur_total'][1],
                            $sousetat['cellule_fondcouleur_total'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_total'],
                            'TOTAL',
                            $sousetat['cellule_bordure_total'][$k],
                            0,
                            $sousetat['cellule_align_total'][$k],
                            $sousetat['cellule_fond_total']
                        );
                    } else {
                        // colones suivante
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_total']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_total'][0],
                            $sousetat['cellule_fondcouleur_total'][1],
                            $sousetat['cellule_fondcouleur_total'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_total'],
                            '',
                            $sousetat['cellule_bordure_total'][$k],
                            0,
                            $sousetat['cellule_align_total'][$k],
                            $sousetat['cellule_fond_total']
                        );
                    }
                }
            } //fin for k
            $this->ln();
        }
        //$k=0;
        //affichage moyenne----------------------------------------------------
        if ($flagmoyenne==1) {
            for ($k=0; $k<$nbchamp; $k++) {
                if ($sousetat['cellule_moyenne'][$k] == 1) {
                    $this->SetFont(
                        $etat['se_font'],
                        '',
                        $sousetat['cellule_fontaille_moyenne']
                    );
                    $this->SetFillColor(
                        $sousetat['cellule_fondcouleur_moyenne'][0],
                        $sousetat['cellule_fondcouleur_moyenne'][1],
                        $sousetat['cellule_fondcouleur_moyenne'][2]
                    );
                    $this->Cell(
                        $sousetat['cellule_largeur'][$k],
                        $sousetat['cellule_hauteur_moyenne'],
                        number_format(
                            $total[$k]/$cptenr,
                            $sousetat['cellule_numerique'][$k],
                            ',',
                            ' '
                        ),
                        $sousetat['cellule_bordure_moyenne'][$k],
                        0,
                        $sousetat['cellule_align_moyenne'][$k],
                        $sousetat['cellule_fond_moyenne']
                    );
                } else {
                    // affichage sur la colone correspondante
                    if ($k==0) {
                        // 1ere colone
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_moyenne']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_moyenne'][0],
                            $sousetat['cellule_fondcouleur_moyenne'][1],
                            $sousetat['cellule_fondcouleur_moyenne'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_moyenne'],
                            'MOYENNE',
                            $sousetat['cellule_bordure_moyenne'][$k],
                            0,
                            $sousetat['cellule_align_moyenne'][$k],
                            $sousetat['cellule_fond_moyenne']
                        );
                    } else {
                        // colones suivante
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_moyenne']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_moyenne'][0],
                            $sousetat['cellule_fondcouleur_moyenne'][1],
                            $sousetat['cellule_fondcouleur_moyenne'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_moyenne'],
                            '',
                            $sousetat['cellule_bordure_moyenne'][$k],
                            0,
                            $sousetat['cellule_align_moyenne'][$k],
                            $sousetat['cellule_fond_moyenne']
                        );
                    }
                }
            } //fin for k
                $this->ln();
        }
        //affichage compteur----------------------------------------------------
        if ($flagcompteur==1) {
            for ($k=0;$k<$nbchamp;$k++) {
                if ($sousetat['cellule_compteur'][$k]==1) {
                    $this->SetFont(
                        $etat['se_font'],
                        '',
                        $sousetat['cellule_fontaille_nbr']
                    );
                    $this->SetFillColor(
                        $sousetat['cellule_fondcouleur_nbr'][0],
                        $sousetat['cellule_fondcouleur_nbr'][1],
                        $sousetat['cellule_fondcouleur_nbr'][2]
                    );
                    $this->Cell(
                        $sousetat['cellule_largeur'][$k],
                        $sousetat['cellule_hauteur_nbr'],
                        number_format($cptenr, 0, ',', ' '),
                        $sousetat['cellule_bordure_nbr'][$k],
                        0,
                        $sousetat['cellule_align_nbr'][$k],
                        $sousetat['cellule_fond_nbr']
                    );
                } else {
                    // affichage sur la colone correspondante
                    if ($k==0) {
                        // 1ere colone
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_nbr']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_nbr'][0],
                            $sousetat['cellule_fondcouleur_nbr'][1],
                            $sousetat['cellule_fondcouleur_nbr'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_nbr'],
                            'NOMBRE',
                            $sousetat['cellule_bordure_nbr'][$k],
                            0,
                            $sousetat['cellule_align_nbr'][$k],
                            $sousetat['cellule_fond_nbr']
                        );
                    } else {
                        // colones suivante
                        $this->SetFont(
                            $etat['se_font'],
                            '',
                            $sousetat['cellule_fontaille_nbr']
                        );
                        $this->SetFillColor(
                            $sousetat['cellule_fondcouleur_nbr'][0],
                            $sousetat['cellule_fondcouleur_nbr'][1],
                            $sousetat['cellule_fondcouleur_nbr'][2]
                        );
                        $this->Cell(
                            $sousetat['cellule_largeur'][$k],
                            $sousetat['cellule_hauteur_nbr'],
                            '',
                            $sousetat['cellule_bordure_nbr'][$k],
                            0,
                            $sousetat['cellule_align_nbr'][$k],
                            $sousetat['cellule_fond_nbr']
                        );
                    }
                }
            } //fin for k
            $this->ln();
        }
        if ($cptenr>0) {
            $this->ln($sousetat['intervalle_fin']);
        }
    }
    
    /**
     *
     */
    function PrepareMultiCell($w, &$txt) {
        //prepare un texte passe par reference (en le modifiant) avec ajout \n
        //pour traitement par Cell modifie  
        //et retourne nb ligne necessaire
        //base sur code MultiCell mais pas d'affichage
        $cw=&$this->CurrentFont['cw']; //largeur caractere
        if ($w==0) { //si largeur=0, largeur=largeurcourante-margegauche-positionx
            $w = $this->w-$this->rMargin-$this->x;
        }
        $cellPaddings = $this->getCellPaddings();
        $wmax=($w-2*$cellPaddings['L'])*1000/$this->FontSize;
        $s=str_replace("\r", '', $txt);
        $nb=strlen($s); //longueur texte sans retour chariot
        if ($nb>0 && $s[$nb-1]=="\n") {
            $nb--;      //supp. dernier retour ligne si existe
        }
        $sep=-1;    //espace
        $i=0;       //index boucle
        $j=0;
        $l=0;
        $ns=0;
        $nl=1;
        $nbrc=0; //nb retourcharriot
        while ($i<$nb) {  //boucle sur texte
            //Get next character
            $c=$s{$i};  //caractere courant
            if ($c=="\n") {  //retour ligne
                //Explicit line break
                $i++;     //
                $sep=-1;   //raz espace
                $j=$i;     //debut de ligne
                $l=0;
                $ns=0;
                $nl++;   //nb ligne +1
                continue; // prochain caractere
            }
            if ($c==' ') {  //si espace
                $sep=$i; //position espace
                $ls=$l;
                $ns++;
            }
            $l+=$cw[ord($c)];
            if ($l>$wmax) { //si ligne depasse largeur
                //Automatic line break
                if($sep==-1) { //si aucun espace detecte
                    if($i==$j) {
                        $i++;
                    }
                } else { //espace detecte
                    $i=$sep+1;   //prochain car = car suivant dernier espace
                }  //insertion retour charriot dans texte
                $txt=substr($txt, 0, $i+$nbrc)."\n".substr($txt, $i+$nbrc);
                $nbrc++;
                $sep=-1;
                $j=$i;
                $l=0;
                $ns=0;
                $nl++;   //nb ligne +1 
            } else {
                //ligne < largeur colonne
                $i++;
            }
        }  //fin de texte
        return $nl;
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
    
    /*
     * Initialisation des variables pour la création d'un code barre de type code 128
     * */
    function init_Code128(){
        // Composition des caractères
        $this->T128[] = array(2, 1, 2, 2, 2, 2);    //0 : [ ]
        $this->T128[] = array(2, 2, 2, 1, 2, 2);    //1 : [!]
        $this->T128[] = array(2, 2, 2, 2, 2, 1);    //2 : ["]
        $this->T128[] = array(1, 2, 1, 2, 2, 3);    //3 : [#]
        $this->T128[] = array(1, 2, 1, 3, 2, 2);    //4 : [$]
        $this->T128[] = array(1, 3, 1, 2, 2, 2);    //5 : [%]
        $this->T128[] = array(1, 2, 2, 2, 1, 3);    //6 : [&]
        $this->T128[] = array(1, 2, 2, 3, 1, 2);    //7 : [']
        $this->T128[] = array(1, 3, 2, 2, 1, 2);    //8 : [(]
        $this->T128[] = array(2, 2, 1, 2, 1, 3);    //9 : [)]
        $this->T128[] = array(2, 2, 1, 3, 1, 2);    //10 : [*]
        $this->T128[] = array(2, 3, 1, 2, 1, 2);    //11 : [+]
        $this->T128[] = array(1, 1, 2, 2, 3, 2);    //12 : [,]
        $this->T128[] = array(1, 2, 2, 1, 3, 2);    //13 : [-]
        $this->T128[] = array(1, 2, 2, 2, 3, 1);    //14 : [.]
        $this->T128[] = array(1, 1, 3, 2, 2, 2);    //15 : [/]
        $this->T128[] = array(1, 2, 3, 1, 2, 2);    //16 : [0]
        $this->T128[] = array(1, 2, 3, 2, 2, 1);    //17 : [1]
        $this->T128[] = array(2, 2, 3, 2, 1, 1);    //18 : [2]
        $this->T128[] = array(2, 2, 1, 1, 3, 2);    //19 : [3]
        $this->T128[] = array(2, 2, 1, 2, 3, 1);    //20 : [4]
        $this->T128[] = array(2, 1, 3, 2, 1, 2);    //21 : [5]
        $this->T128[] = array(2, 2, 3, 1, 1, 2);    //22 : [6]
        $this->T128[] = array(3, 1, 2, 1, 3, 1);    //23 : [7]
        $this->T128[] = array(3, 1, 1, 2, 2, 2);    //24 : [8]
        $this->T128[] = array(3, 2, 1, 1, 2, 2);    //25 : [9]
        $this->T128[] = array(3, 2, 1, 2, 2, 1);    //26 : [:]
        $this->T128[] = array(3, 1, 2, 2, 1, 2);    //27 : [;]
        $this->T128[] = array(3, 2, 2, 1, 1, 2);    //28 : [<]
        $this->T128[] = array(3, 2, 2, 2, 1, 1);    //29 : [=]
        $this->T128[] = array(2, 1, 2, 1, 2, 3);    //30 : [>]
        $this->T128[] = array(2, 1, 2, 3, 2, 1);    //31 : [?]
        $this->T128[] = array(2, 3, 2, 1, 2, 1);    //32 : [@]
        $this->T128[] = array(1, 1, 1, 3, 2, 3);    //33 : [A]
        $this->T128[] = array(1, 3, 1, 1, 2, 3);    //34 : [B]
        $this->T128[] = array(1, 3, 1, 3, 2, 1);    //35 : [C]
        $this->T128[] = array(1, 1, 2, 3, 1, 3);    //36 : [D]
        $this->T128[] = array(1, 3, 2, 1, 1, 3);    //37 : [E]
        $this->T128[] = array(1, 3, 2, 3, 1, 1);    //38 : [F]
        $this->T128[] = array(2, 1, 1, 3, 1, 3);    //39 : [G]
        $this->T128[] = array(2, 3, 1, 1, 1, 3);    //40 : [H]
        $this->T128[] = array(2, 3, 1, 3, 1, 1);    //41 : [I]
        $this->T128[] = array(1, 1, 2, 1, 3, 3);    //42 : [J]
        $this->T128[] = array(1, 1, 2, 3, 3, 1);    //43 : [K]
        $this->T128[] = array(1, 3, 2, 1, 3, 1);    //44 : [L]
        $this->T128[] = array(1, 1, 3, 1, 2, 3);    //45 : [M]
        $this->T128[] = array(1, 1, 3, 3, 2, 1);    //46 : [N]
        $this->T128[] = array(1, 3, 3, 1, 2, 1);    //47 : [O]
        $this->T128[] = array(3, 1, 3, 1, 2, 1);    //48 : [P]
        $this->T128[] = array(2, 1, 1, 3, 3, 1);    //49 : [Q]
        $this->T128[] = array(2, 3, 1, 1, 3, 1);    //50 : [R]
        $this->T128[] = array(2, 1, 3, 1, 1, 3);    //51 : [S]
        $this->T128[] = array(2, 1, 3, 3, 1, 1);    //52 : [T]
        $this->T128[] = array(2, 1, 3, 1, 3, 1);    //53 : [U]
        $this->T128[] = array(3, 1, 1, 1, 2, 3);    //54 : [V]
        $this->T128[] = array(3, 1, 1, 3, 2, 1);    //55 : [W]
        $this->T128[] = array(3, 3, 1, 1, 2, 1);    //56 : [X]
        $this->T128[] = array(3, 1, 2, 1, 1, 3);    //57 : [Y]
        $this->T128[] = array(3, 1, 2, 3, 1, 1);    //58 : [Z]
        $this->T128[] = array(3, 3, 2, 1, 1, 1);    //59 : [[]
        $this->T128[] = array(3, 1, 4, 1, 1, 1);    //60 : [\]
        $this->T128[] = array(2, 2, 1, 4, 1, 1);    //61 : []]
        $this->T128[] = array(4, 3, 1, 1, 1, 1);    //62 : [^]
        $this->T128[] = array(1, 1, 1, 2, 2, 4);    //63 : [_]
        $this->T128[] = array(1, 1, 1, 4, 2, 2);    //64 : [`]
        $this->T128[] = array(1, 2, 1, 1, 2, 4);    //65 : [a]
        $this->T128[] = array(1, 2, 1, 4, 2, 1);    //66 : [b]
        $this->T128[] = array(1, 4, 1, 1, 2, 2);    //67 : [c]
        $this->T128[] = array(1, 4, 1, 2, 2, 1);    //68 : [d]
        $this->T128[] = array(1, 1, 2, 2, 1, 4);    //69 : [e]
        $this->T128[] = array(1, 1, 2, 4, 1, 2);    //70 : [f]
        $this->T128[] = array(1, 2, 2, 1, 1, 4);    //71 : [g]
        $this->T128[] = array(1, 2, 2, 4, 1, 1);    //72 : [h]
        $this->T128[] = array(1, 4, 2, 1, 1, 2);    //73 : [i]
        $this->T128[] = array(1, 4, 2, 2, 1, 1);    //74 : [j]
        $this->T128[] = array(2, 4, 1, 2, 1, 1);    //75 : [k]
        $this->T128[] = array(2, 2, 1, 1, 1, 4);    //76 : [l]
        $this->T128[] = array(4, 1, 3, 1, 1, 1);    //77 : [m]
        $this->T128[] = array(2, 4, 1, 1, 1, 2);    //78 : [n]
        $this->T128[] = array(1, 3, 4, 1, 1, 1);    //79 : [o]
        $this->T128[] = array(1, 1, 1, 2, 4, 2);    //80 : [p]
        $this->T128[] = array(1, 2, 1, 1, 4, 2);    //81 : [q]
        $this->T128[] = array(1, 2, 1, 2, 4, 1);    //82 : [r]
        $this->T128[] = array(1, 1, 4, 2, 1, 2);    //83 : [s]
        $this->T128[] = array(1, 2, 4, 1, 1, 2);    //84 : [t]
        $this->T128[] = array(1, 2, 4, 2, 1, 1);    //85 : [u]
        $this->T128[] = array(4, 1, 1, 2, 1, 2);    //86 : [v]
        $this->T128[] = array(4, 2, 1, 1, 1, 2);    //87 : [w]
        $this->T128[] = array(4, 2, 1, 2, 1, 1);    //88 : [x]
        $this->T128[] = array(2, 1, 2, 1, 4, 1);    //89 : [y]
        $this->T128[] = array(2, 1, 4, 1, 2, 1);    //90 : [z]
        $this->T128[] = array(4, 1, 2, 1, 2, 1);    //91 : [{]
        $this->T128[] = array(1, 1, 1, 1, 4, 3);    //92 : [|]
        $this->T128[] = array(1, 1, 1, 3, 4, 1);    //93 : [}]
        $this->T128[] = array(1, 3, 1, 1, 4, 1);    //94 : [~]
        $this->T128[] = array(1, 1, 4, 1, 1, 3);    //95 : [DEL]
        $this->T128[] = array(1, 1, 4, 3, 1, 1);    //96 : [FNC3]
        $this->T128[] = array(4, 1, 1, 1, 1, 3);    //97 : [FNC2]
        $this->T128[] = array(4, 1, 1, 3, 1, 1);    //98 : [SHIFT]
        $this->T128[] = array(1, 1, 3, 1, 4, 1);    //99 : [Cswap]
        $this->T128[] = array(1, 1, 4, 1, 3, 1);    //100 : [Bswap]                
        $this->T128[] = array(3, 1, 1, 1, 4, 1);    //101 : [Aswap]
        $this->T128[] = array(4, 1, 1, 1, 3, 1);    //102 : [FNC1]
        $this->T128[] = array(2, 1, 1, 4, 1, 2);    //103 : [Astart]
        $this->T128[] = array(2, 1, 1, 2, 1, 4);    //104 : [Bstart]
        $this->T128[] = array(2, 1, 1, 2, 3, 2);    //105 : [Cstart]
        $this->T128[] = array(2, 3, 3, 1, 1, 1);    //106 : [STOP]
        $this->T128[] = array(2, 1);                //107 : [END BAR]
    
        //J eux de caractères
        for ($i = 32; $i <= 95; $i++) {  
            $this->ABCset .= chr($i);
        }
        $this->Aset = $this->ABCset;
        $this->Bset = $this->ABCset;
        for ($i = 0; $i <= 31; $i++) {
            $this->ABCset .= chr($i);
            $this->Aset .= chr($i);
        }
        for ($i = 96; $i <= 126; $i++) {
            $this->ABCset .= chr($i);
            $this->Bset .= chr($i);
        }
        $this->Cset="0123456789";
    
        //Convertisseurs des jeux A & B
        for ($i=0; $i<96; $i++) {
            @$this->SetFrom["A"] .= chr($i);
            @$this->SetFrom["B"] .= chr($i + 32);
            @$this->SetTo["A"] .= chr(($i < 32) ? $i+64 : $i-32);
            @$this->SetTo["B"] .= chr($i);
        }
    }
    
    /*
     * Génération d'un code barre de type code 128
     * Script original : http://www.fpdf.org/fr/script/script88.php
     * @param $x, $y angle supérieur code du code barres
     * @param $code le code à créer
     * @param $w largeur du code
     * @param $h hauteur du code
     * */
    function Code128($x, $y, $code, $w, $h) {
    
        // Initialisation des données
        if( $this->T128 == "" || $this->ABCset == "" || $this->Aset == "" || 
            $this->Bset == "" || $this->Cset == "" || $this->SetFrom == "" || 
            $this->SetTo == "" ){
            $this->init_Code128();
        }
        
        // Affiche le numéro sous le code barres
        $this->Text($x, $y+$h+4, $code);
        
        // Création des guides de choix ABC
        $Aguid = "";
        $Bguid = "";
        $Cguid = "";
        for ($i=0; $i < strlen($code); $i++) {
            $needle = substr($code, $i, 1);
            $Aguid .= ((strpos($this->Aset, $needle)===false) ? "N" : "O"); 
            $Bguid .= ((strpos($this->Bset, $needle)===false) ? "N" : "O"); 
            $Cguid .= ((strpos($this->Cset, $needle)===false) ? "N" : "O");
        }
    
        $SminiC = "OOOO";
        $IminiC = 4;
    
        $crypt = "";
        while ($code > "") {
            
            $i = strpos($Cguid, $SminiC);
            // Force le jeu C, si possible
            if ($i!==false) {                           
                $Aguid [$i] = "N";
                $Bguid [$i] = "N";
            }
            
            // Jeu C 
            if (substr($Cguid, 0, $IminiC) == $SminiC) {
                // Début Cstart, sinon Cswap  
                $crypt .= chr(($crypt > "") ? $this->JSwap["C"] : $this->JStart["C"]); 
                // Étendu du set C
                $made = strpos($Cguid, "N");
                if ($made === false) {
                    $made = strlen($Cguid);
                }
                // Seulement un nombre pair
                if (fmod($made, 2)==1) {
                    $made--;
                }
                for ($i=0; $i < $made; $i += 2) {
                    // Conversion 2 par 2
                    $crypt .= chr(strval(substr($code, $i, 2)));
                }
                $jeu = "C";
            } else {
                // Étendu du set A
                $madeA = strpos($Aguid, "N");
                if ($madeA === false) {
                    $madeA = strlen($Aguid);
                }
                // Étendu du set B
                $madeB = strpos($Bguid, "N");
                if ($madeB === false) {
                    $madeB = strlen($Bguid);
                }
                // Étendu traité
                $made = (($madeA < $madeB) ? $madeB : $madeA );
                // Jeu en cours
                $jeu = (($madeA < $madeB) ? "B" : "A" );
    
                //  Début start, sinon swap
                $crypt .= chr(($crypt > "") ? $this->JSwap[$jeu] : $this->JStart[$jeu]);
    
                // Conversion selon jeu
                $crypt .= strtr(
                    substr($code, 0, $made),
                    $this->SetFrom[$jeu],
                    $this->SetTo[$jeu]
                );
    
            }
            // Raccourcir légende et guides de la zone traitée
            $code = substr($code, $made);
            $Aguid = substr($Aguid, $made);
            $Bguid = substr($Bguid, $made);
            $Cguid = substr($Cguid, $made);
        }
    
        // Calcul de la somme de contrôle
        $check = ord($crypt[0]);
        for ($i=0; $i<strlen($crypt); $i++) {
            $check += (ord($crypt[$i]) * $i);
        }
        $check %= 103;
        
        // Chaine cryptée complète
        $crypt .= chr($check) . chr(106) . chr(107);
    
        // Calcul de la largeur du module
        $i = (strlen($crypt) * 11) - 8;
        $modul = $w/$i;
    
        for ($i=0; $i<strlen($crypt); $i++) {
            $c = $this->T128[ord($crypt[$i])];
            for ($j=0; $j<count($c); $j++) {
                $this->Rect($x, $y, $c[$j]*$modul, $h, "F");
                $x += ($c[$j++]+$c[$j])*$modul;
            }
        }
    }

    /**
     * Affiche des code barres en les positionnant correctement grâce à des marqueurs
     * @param   string      $data      le texte à traiter
     * @return  null
     */
    function code_barres_tcpdf($data) {
        // XXX
        // Bug si texte riche inséré dans du texte riche :
        // warning à cause d'une balise P dans P.
        // Solution palliative : désactiver les erreurs.
        libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $dom->loadHTML($data);
        libxml_clear_errors();
        $xPath = new DOMXPath($dom);
        foreach($xPath->query("//*[contains(@class, 'mce_codebarre')]") as $node) {
            $params = TCPDF_STATIC::serializeTCPDFtagParameters(
                array(
                    $node->textContent,
                    'C128',
                    '',
                    '',
                    50,
                    10,
                    0.4,
                    array(
                        'position'=>'S',
                        'border'=>false,
                        'padding'=>0,
                        'fgcolor'=>array(0,0,0),
                        'bgcolor'=>array(255,255,255),
                        'text'=>true,
                        'font'=>$this->getFontFamily(),
                        'fontsize'=>8,
                        'stretchtext'=>4),
                    'N')
                );
            $node->nodeValue = '';
            $fragment = $dom->createDocumentFragment();
            $fragment->appendXML('<tcpdf method="write1DBarcode" params="'.$params.'" />');
            $node->appendChild($fragment);
        }
        return $dom->saveHTML();
    }

    /**
     * Paramètre l'activation du filigrane
     * 
     * @param [boolean]  $state  true si actif
     * @return void
     */
    function setWatermark($state = true) {
        $this->watermark = $state;
    }

    /**
     * Retourne l'état d'activation du filigrane
     * 
     * @return  [boolean]  true si actif
     */
    function getWatermark() {
        return $this->watermark;
    }
}

?>
