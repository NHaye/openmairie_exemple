<?php
/**
 * Ce fichier permet de declarer la classe formulaire.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_formulaire.class.php 3032 2015-02-04 12:15:08Z fmichon $
 */

/**
 *
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
require_once PATH_OPENMAIRIE."om_debug.inc.php";
(defined("DEBUG") ? "" : define("DEBUG", PRODUCTION_MODE));
require_once PATH_OPENMAIRIE."om_logger.class.php";
require_once PATH_OPENMAIRIE."om_message.class.php";

/**
 * Cette classe a pour objet la construction des champs du formulaire suivant :
 * - $type    (tableau) : type de champ
 * - $val     (tableau) : valeur du champ
 * - $taille  (tableau) : taille du champ
 * - $max     (tableau) : saisie maximale autorisee pour le champ
 * - $lib     (tableau) : libelle de saisie
 * - $select  (tableau) : valeur des controles liste
 *                        [0] value
 *                        [1] libelle (<option>libelle</option>)
 * - $groupe  (tableau) : regroupement de champ par ligne
 * - $regroupe (tableau) : fieldset
 * - $enteteTab (string) : entete du formulaire
 */

class formulaire {

    /**
     * Entete
     * @var
     */
    var $enteteTab = "";

    /**
     * Valeur par defaut du champ
     * @var
     */
    var $val;

    /**
     * Type de champ
     * @var
     */
    var $type;

    /**
     * Taille du champ
     * @var
     */
    var $taille;

    /**
     * Nombre de caracteres maximum a saisir
     * @var
     */
    var $max;

    /**
     * Libelle du champ
     * @var
     */
    var $lib;

    /**
     * Regroupement
     * @var
     */
    var $groupe = array();

    /**
     * Valeur des listes
     * @var
     */
    var $select;

    /**
     * Javascript en cas de changement
     * @var
     */
    var $onchange;

    /**
     * Javascript en cas de keyup
     * @var
     */
    var $onkeyup;

    /**
     * Javascript en cas de clic
     * @var
     */
    var $onclick;

    /**
     * Fieldset
     * @var
     */
    var $regroupe = array();

    /**
     * Tableau des champs lies a l'ouverture et fermeture des blocs div
     * ainsi que les valeurs
     * @var
     */
    var $bloc = array();

    /**
     * Tableau des champs lies a l'ouverture et fermeture des blocs div
     * ainsi que les valeurs
     * @var
     */
    var $layout = array();

    /**
     *
     * @var
     */
    var $correct;

    /**
     * Caractere du champ obligatoire
     * @var
     */
    var $required_tag='<span class="not-null-tag">*</span>';

    /**
     * Liste des champs obligatoires
     * @var
     */
    var $required_field = array();

    /**
     *  Liste des types cachés
     * @var
     */
    var $hidden_type_list = array("nodisplay");

    /**
     * Constructeur
     *
     * Initialisation des tableaux de parametrage du formulaire
     * - valeur par defaut
     *   en modification et suppression = initialiser par la valeur des champs
     *   en ajout = initialisation vide
     * - type par defaut
     *   text pour ajout et modification
     *   static pour suppression
     *
     * @param NULL $unused Parametre inutilise
     * @param integer $validation
     * @param integer $maj
     * @param array $champs
     * @param array $val
     * @param array $max
     * @return void
     */
    function formulaire($unused = NULL, $validation, $maj, $champs = array(),
                        $val = array(), $max = array()) {
        //
        if (isset($GLOBALS["f"])) {
            $this->f = $GLOBALS["f"];
        }
        // valeur par defaut et type
        if ($maj == 0) { // ajouter
            for ($i = 0; $i < count($champs); $i++) {
                $this->val[$champs[$i]] = "";
                $this->type[$champs[$i]] = "text";
            }
        }
        if ($maj == 1) { // modifier
            if ($validation == 0) {
                $i = 0;
                foreach ($val as $elem){
                    $this->val[$champs[$i]] = strtr($elem, chr(34), "'");
                    $i++;
                }
            }
            for ($i = 0; $i < count($champs); $i++) {
                $this->type[$champs[$i]] = "text";
                if ($validation != 0) {
                    $this->val[$champs[$i]] = "";
                }
            }
        }
        if ($maj == 2) { // supprimer
            if ($validation == 0) {
                $i = 0;
                foreach ($val as $elem) {
                    $this->val[$champs[$i]] = strtr($elem, chr(34), "'");
                    $i++;
                }
            }
            for ($i = 0; $i < count($champs); $i++) {
                $this->type[$champs[$i]] = "static";
                if ($validation != 0) {
                    $this->val[$champs[$i]] = "";
                }
            }
        }
        if ($maj >= 3) { // consulter
            $i = 0;
            foreach ($val as $elem){
                $this->val[$champs[$i]] = strtr($elem, chr(34), "'");
                $i++;
            }
        }
        // taille et longueur maximum
        $i = 0;
        foreach ($max as $elem) {
            $this->taille[$champs[$i]] = $elem;
            $this->max[$champs[$i]] = $elem;
            $i++;
        }
        // libelle, group, select, onchange
        for ($i = 0; $i<count($champs); $i++) {
            $this->lib[$champs[$i]] = $champs[$i];
            $this->select[$champs[$i]][0] = "";
            $this->select[$champs[$i]][1] = "";
            $this->onchange[$champs[$i]] = "";
            $this->onkeyup[$champs[$i]] = "";
            $this->onclick[$champs[$i]] = "";
        }

    }

    // {{{ Methodes permettant l'affichage de la table contenant le formulaire

    /**
     * Entete
     *
     * @return void
     */
    function entete() {

        echo "\n<!-- ########## START FORMULAIRE ########## -->\n";
        echo "<div class=\"formEntete ui-corner-all\">\n";

    }

    /**
     * Enpied
     *
     * @return void
     */
    function enpied() {
        echo "\n</div>\n";
        echo "<!-- ########## END FORMULAIRE ########## -->\n";

    }


    /**
     * Cette methode permet d'afficher un champ dans une hierarchie de div
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     * @return void
     */
    function afficherChamp($champ, $validation, $DEBUG = false) {
        
        //
        if (isset($this->type[$champ])) {
            $type_champ = $this->type[$champ];
        } else {
            $type_champ = "statiq";
        }

        //Ouverture du div contenant le champ (libelle et widget)
        //
        $this->f->layout->display_formulaire_conteneur_libelle_widget($type_champ);
        //Ajout du label en lien avec l'id du champ correspondant si le champ
        //n'est pas hidden
        if(!in_array($type_champ, $this->hidden_type_list)) {
            // Ouverture du conteneur du libelle du champ
            $this->f->layout->display_formulaire_conteneur_libelle_champs();
            echo "          <label for=\"".$champ."\" class=\"libelle-".$champ.
            "\" id=\"lib-".$champ."\">\n";
            echo "            ".$this->lib[$champ].
            (in_array($champ, $this->required_field)? " ".$this->required_tag:"")."\n";
            echo "          </label>\n";

            $this->f->layout->display_formulaire_fin_conteneur_champs();
        }
        // Ouverture du conteneur de champ
        $this->f->layout->display_formulaire_conteneur_champs();
        // Affichage du champ en fonction de son type
        $fonction = $type_champ;
        if ($fonction == "static") {
            $fonction = "statiq";
        }
        if (method_exists($this, $fonction)) {
            $this->$fonction($champ, $validation);
        } else {
            $this->statiq($champ, $validation);
        }
        $this->f->layout->display_formulaire_fin_conteneur_champs();
        // Fermeture du conteneur de champ (libelle et widget)
        echo "\n";
        echo "      </div>\n";

    }

    /**
     * Cette methode permet d'ouvrir un fieldset
     *
     * @param string $champ Identifiant du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     * @return void
     */
    function debutFieldset($action, $validation, $DEBUG = false) {
        /*
        // Ouverture du fieldset
        echo "      <fieldset class=\"cadre ui-corner-all ui-widget-content ".$action[2]."\">\n";
        echo "        <legend 'class=\"ui-corner-all ui-widget-content ui-state-active\">";
        echo $action[1];
        echo "        </legend>\n";
        // Ouverture du conteneur interne du fieldset
        echo "        <div class=\"fieldsetContent\">\n";
        */
        $params = array(
            "action2" => $action[2],
            "action1" => $action[1] 
         );
        //
        if ($this->getParameter("obj") != NULL && $this->getParameter("form_type") != NULL) {
            $params["identifier"] = "fieldset-".$this->getParameter("form_type")."-".$this->getParameter("obj")."-".$this->normalize_string($action[1]);
        }
        $this->f->layout->display_formulaire_debutFieldset($params);
    }

    /**
     * Cette methode permet de fermer un fieldset
     *
     * @param string $champ Identifiant du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     * @return void
     */
    function finFieldset($action, $validation, $DEBUG = false) {
       /*
        // Fermeture du fieldset
        echo "          <div class=\"visualClear\"><!-- --></div>\n";
        echo "        </div>\n";
        echo "      </fieldset>\n";
        */
        $params = array();
        $this->f->layout->display_formulaire_finFieldset($params);
    }

    /**
     * Cette methode permet d'ouvrir un bloc simple (div)
     *
     * @param string $champ Identifiant du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     * @return void
     */
    function debutBloc($action, $validation, $DEBUG = false) {

    // Ouverture d'un bloc si le champ est le premier d'un groupe 'D'
        echo "\n";
        echo "     <div class=\"bloc ".$action[2]."\">\n";
        //Affichage du libelle du groupe
        if($action[1]!="") {
            echo "        <div class=\"bloc-titre\">\n";
            echo "          <span class=\"text\">\n";
            echo "            ".$action[1]."\n";
            echo "          </span>\n";
            echo "        </div>";
        }
    }

    /**
     * Cette methode permet d'ouvrir un bloc simple (div)
     *
     * @param string $champ Identifiant du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     * @return void
     */
    function finBloc($action, $validation, $DEBUG = false) {
        // Fermeture du bloc
            echo "      </div>\n";
        
    }

    /**
     * Cette methode permet d'ordoner l'affichage des div, fieldset et champs
     *
     * @param array $champs Liste des identifiants des champs
     * @param integer $validation - 0 1er passage
     *                            - > 0 passage suivant suite validation
     * @param boolean $DEBUG Parametre inutilise
     * @param boolean $correct
     */
    function afficher($champs, $validation, $DEBUG = false, $correct) {

        $this->correct = $correct;

        // Affichage du conteneur des champs du formulaire
        echo '<div id="form-content"';
        // Ajout d'une classe si le formulaire a été validé
        if ($this->correct == true) {
            echo ' class="form-is-valid"';
        }
        echo '>';
        
        //Prise en compte de la mise en page setGroupe/setRegroupe
        $this->transformGroupAndRegroupeToLayout($champs);

        // Il est nécessaire d'effectuer une première boucle sur les champs
        // pour savoir lesquels sont hidden et donc les blocs qui ne contiennent
        // que des champs hidden pour ne pas les afficher

        // Niveau d'arborescence en cours
        $level = 0;
        // Tableaux de travail
        $block_to_hide = array();
        // Boucle sur la liste des champs
        for ($i = 0; $i < count($champs); $i++) {
            // Test l'ouverture ou fermeture de bloc ou fieldset sur le champ en cour
            if (isset($this->layout[$champs[$i]])) {
                // Boucle sur les blocs et fieldset du champ
                foreach ($this->layout[$champs[$i]] as $key => $action) {
                    // Test si ouverture de bloc ou fieldset
                    if ($action[0]=="D") {
                        // Appel de la méthode de vérification des champs affichés
                        $retourAffBloc = $this->isBlocPrintable($champs[$i], $key, $champs);
                        // Vérification du retour
                        if(isset($retourAffBloc) AND $retourAffBloc !== true AND $retourAffBloc !== false) {
                            // Ajout des retour au tableau des bloc à cacher
                            foreach($retourAffBloc as $champHidden) {
                                $block_to_hide[$champHidden[0]][] = $champHidden[1];
                            }
                        }
                        
                    } elseif ($action[0]=="DF" AND $this->type[$champs[$i]] == "hidden"){
                        // Gestion du champ caché si un bloc DF est appliqué sur celui-ci
                        $block_to_hide[$champs[$i]][] = $key;
                    }
                }
            }
        }
        // Pour chaque champs
        for ($i = 0; $i < count($champs); $i++) {
            // On verifie qu'un bloc s'ouvre
            if (isset($this->layout[$champs[$i]])) {
                $tabLength=count($this->layout[$champs[$i]]);
                // Pour chaque action sur chaque champ
                foreach ($this->layout[$champs[$i]] as $key=>$action) {
                    // Si le bloc n'est pas dans la liste des blocs à cacher
                    // on affiche l'ouverture du bloc
                    if(!isset($block_to_hide[$champs[$i]]) OR array_search($key, $block_to_hide[$champs[$i]]) === false) {
                        $methode = "debut".$action[3];
                        if($action[0]=="D" OR $action[0]=="DF") {
                                $this->$methode($action,$validation,$DEBUG);
                        }
                    }
                }
                // On affiche le champ
                $this->afficherChamp($champs[$i], $validation, $DEBUG );
                // Pour chaque action sur chaque champ
                foreach ($this->layout[$champs[$i]] as $key=>$action) {
                    // Si le bloc n'est pas dans la liste des blocs à cacher
                    // on affiche la fermeture du bloc
                    if(!isset($block_to_hide[$champs[$i]]) OR array_search($key, $block_to_hide[$champs[$i]]) === false) {
                        $methode = "fin".$action[3];
                        if($action[0]=="F" OR $action[0]=="DF") {
                                $this->$methode($action,$validation,$DEBUG);
                        }
                    }
                }
            } else {
                // On affiche le champ
                $this->afficherChamp($champs[$i], $validation, $DEBUG );
            }
        }

        // Fermeture du div form-content
        echo '</div>';
    }

    /**
     * Permet de définir si la balise passée en paramètre doit être afficher
     * selon les champs affichée entre celle-ci et sa balise fermante
     * @param  array  $bloc        [description]
     * @param  string  $champ_debut [description]
     * @return boolean              [description]
     */
    function isBlocPrintable($champ_debut, $id_bloc, $champs) {
        // Initialisation du niveau hierarchique des blocs à -1 pour
        // commencer pas traiter le bloc courant et pas uniquement
        // les blocs imbriqués, dans la suite des traitements
        $level = -1;
        // Récupération du type de bloc ouvrant pour chercher le bloc fermant correspondant
        $type_bloc = $this->layout[$champ_debut][$id_bloc][3];
        // Récupération de l'index de debut d'itération
        $index_champ = array_search($champ_debut, $champs);
        // Si le champ_debut n'est pas trouvé dans la liste des champs on retourne False
        if( $index_champ === false) {
            return false;
        }

        // Parcours séquentiel sur la liste des champs, en allant au maximum jusqu'au dernier champ.
        // Si le bloc/fieldset se ferme avant le dernier champ
        // on sortira sur le bloc/fieldset fermant.
        for ($index_champ ; $index_champ < count($champs); $index_champ++) {
            // Test de la présence ou pas du champ courant dans la liste des champs comportant 
            // des blocs ou fieldsets :
            // - si le champ figure dans la liste des blocs on va boucler sur les blocs inclus
            //   et on va voir s'ils comportent des champs affichés
            // - sinon on a affaire à un bloc comportant uniquement des champs et on va en 
            //   tester l'affichage
            if (isset($this->layout[$champs[$index_champ]])) {
                // traitement des blocs inclus ( on commence par le bloc courant )
                // 
                // récupération de la liste des blocs ou fieldsets arrachés au champ courant
                $champ_layout = $this->layout[$champs[$index_champ]];
                //
                // Initialisation de l'index du sousbloc (niveau hiérarchique de bloc traité)
                // On ne prend l'index que sur les blocs rattachés au même champ
                // que le bloc courant.
                // Si le cloc n'est pas rattaché au champ courant on positionne l'index à 0.
                // Exemple : 0 si le bloc traité est le premier bloc du champ
                // 
                if($champs[$index_champ] == $champ_debut) {
                    $index_sousbloc = $id_bloc;
                } else {
                    $index_sousbloc = 0;
                }

                // Boucle de traitement des champs de type bloc rattachés au champ courant :
                // Pour tous les champs suivant on vérifie si une balise fermante correspond
                // à celle ouverte afin de stoper la boucle
                while (isset($champ_layout[$index_sousbloc])) {
                    //
                    // Si un champ doit être affiché :
                    // test du type
                    if( (   isset($this->type[$champs[$index_champ]]) 
                            AND $this->type[$champs[$index_champ]] != "hidden")
                        || !isset($this->type[$champs[$index_champ]])) {
                        // Le champ est affiché : le bloc doit être affiché 
                        return true;
                    }
                    // Test si la fin du bloc a été trouvé (level 0) et que aucun champ n'est affiché ;
                    // sinon on gère le niveau de sousbloc (level)
                    if ($level == 0 
                        AND $type_bloc == $champ_layout[$index_sousbloc][3] 
                        AND $champ_layout[$index_sousbloc][0] == "F") {
                        return array(array($champ_debut, $id_bloc),array($champs[$index_champ], $index_sousbloc));
                    } elseif ($level > 0 
                                AND $type_bloc == $champ_layout[$index_sousbloc][3] 
                                AND $champ_layout[$index_sousbloc][0] == "F") {
                        // Un bloc du même type est fermé
                        $level --;
                    } elseif ($type_bloc == $champ_layout[$index_sousbloc][3] 
                                AND $champ_layout[$index_sousbloc][0] == "D") {
                        // Un bloc du même type est ouvert
                        $level ++;
                    }
                    // on boucle sur le sousbloc suivant
                    $index_sousbloc++;
                }

            } else {
                // On a un champ normal à tester, sans sous-bloc : est-ce qu'il est affiché ?
                if((isset($this->type[$champs[$index_champ]]) 
                    AND $this->type[$champs[$index_champ]] != "hidden")
                    || !isset($this->type[$champs[$index_champ]])) {
                    return true;
                }
            }
        }
        // aucun champ n'est affiché
        return false;
    }

    /**
     * Permet d'afficher le portlet d'actions contextuelles.
     *
     * @param string $idx      Identifiant de l'objet en question.
     * @param array  $actions  Tableau d'actions à afficher.
     * @param sting  $sousform Objet correspondant au sous-formulaire ou null.
     *
     * @return void
     */
    function afficher_portlet($idx, $actions = array(), $sousform = null) {
        // affichage du portlet d'actions contextuelles
        $this->f->layout->display_formulaire_portlet_start();
        // boucle sur les actions ordonnees
        foreach ($actions as $key => $action) {
            //
            $action_href = "#";
            if (isset($action["href"])) {
                $action_href = $action["href"];
            }
            //
            $action_id = "";
            if (isset($action["id"]) && trim($action["id"]) != "") {
                $action_id = " id=\"".trim($action["id"])."\"";
            }
            //
            $action_target = "";
            if (isset($action["target"]) && trim($action["target"]) != "" ) {
                $action_target = " target=\"".trim($action["target"])."\"";
            }
            //
            $action_class = "";
            if (isset($action["class"]) && trim($action["class"]) != "") {
                $action_class = " class=\"".trim($action["class"])."\"";
            }
            //
            $action_libelle = $action["libelle"];
            //
            echo sprintf(
                '<li><a href="%s"%s%s%s>%s</a></li>',
                $action_href,
                $action_id,
                $action_class,
                $action_target,
                $action_libelle
            );
        }
        // fermeture du portlet
        $this->f->layout->display_formulaire_portlet_end();
    }

    // }}}

    /**
     * Cette meethode permet d'unifier la nouvelle facon d'afficher avec l'ancienne :
     * les tableaux regroupe et groupe sont inseres dans layout
     *
     * @param array $champs Liste des identifiants des champs
     */
    function transformGroupAndRegroupeToLayout($champs) {
        for ($i = 0; $i < count($champs); $i++) {
            if(isset($this->regroupe[$champs[$i]]) AND $this->regroupe[$champs[$i]][0]!="G") {
                if(!isset($this->layout[$champs[$i]])) {
                    $this->layout[$champs[$i]]=array();
                }
                if($this->regroupe[$champs[$i]][0]=="D") {
                    //Ajout du regroupe en debut du tableau $this->layout
                    array_push($this->layout[$champs[$i]], array($this->regroupe[$champs[$i]][0],$this->regroupe[$champs[$i]][1],$this->regroupe[$champs[$i]][2],"Fieldset"));
                }
                if ($this->regroupe[$champs[$i]][0]=="F" OR $this->regroupe[$champs[$i]][0]=="DF") {
                    //Ajout du regroupe en fin du tableau $this->layout
                    array_unshift($this->layout[$champs[$i]], array($this->regroupe[$champs[$i]][0],$this->regroupe[$champs[$i]][1],$this->regroupe[$champs[$i]][2],"Fieldset"));
                }
            }
            if(isset($this->groupe[$champs[$i]]) AND $this->groupe[$champs[$i]][0]!="G") {
                if(!isset($this->layout[$champs[$i]])) {
                    $this->layout[$champs[$i]]=array();
                }
                if($this->groupe[$champs[$i]][0]=="D" OR $this->groupe[$champs[$i]][0]=="DF") {
                    //Ajout du groupe en debut du tableau $this->layout
                    array_push($this->layout[$champs[$i]], array($this->groupe[$champs[$i]][0],$this->groupe[$champs[$i]][1],$this->groupe[$champs[$i]][2]." group","Bloc"));
                }
                if($this->groupe[$champs[$i]][0]=="F") {
                    //Ajout du groupe en fin du tableau $this->layout
                    array_unshift($this->layout[$champs[$i]], array($this->groupe[$champs[$i]][0],$this->groupe[$champs[$i]][1],$this->groupe[$champs[$i]][2]." group","Bloc"));
                }
            }
        }
    }

    /**
     * Recuperation des variables sous formulaires
     *
     * @param string $champ Libelle des champs a afficher
     * @param integer $validation - 0 1er passage
     *                            - > 0 passage suivant suite validation
     * @param boolean $postVar
     * @param boolean $DEBUG Parametre inutilise
     */
    function recupererPostvarsousform($champs, $validation, $postVar, $DEBUG = false) {
        //
        $this->addToLog(__METHOD__."(): \$this->val = ".print_r($this->val, true), EXTRA_VERBOSE_MODE);
        //
        $this->addToLog(__METHOD__."(): \$postVar = ".print_r($postVar, true), EXTRA_VERBOSE_MODE);
        //
        for ($i = 0; $i < count($champs); $i++) {
            if ($validation > 0) {
                // magic_quotes_gpc est initialise dans php.ini
                // mise automatique de quote quand il y a un ", \ , '.
                if ($this->type[$champs[$i]] == "textdisabled"
                    or $this->type[$champs[$i]] == "static") {
                    $this->val[$champs[$i]] = "";
                } elseif ($this->type[$champs[$i]] == "checkbox_multiple"
                        || $this->type[$champs[$i]] == "select_multiple") {
                    // cas de checkbox/select multiple : les valeurs renvoyees
                    // dans le post sont dans un tableau donc ici les valeurs
                    // sont linearises dans une chaine avec separateur ;
                    if (isset($postVar[$champs[$i]])) {
                        if (!get_magic_quotes_gpc()) { // magic_quotes_gpc = Off
                            $this->val[$champs[$i]] = (
                                DBCHARSET == "UTF8" ? 
                                utf8_encode(implode(";",$postVar[$champs[$i]])) : implode(";",$postVar[$champs[$i]])
                            );
                        } else { // magic_quotes_gpc = On
                            $this->val[$champs[$i]] = (
                                DBCHARSET == "UTF8" ? 
                                utf8_encode(stripslashes(implode(";",$postVar[$champs[$i]]))) : stripslashes(implode(";",$postVar[$champs[$i]]))
                            );
                        }
                    } else {
                        $this->val[$champs[$i]] = "";
                    }
                } elseif (isset($postVar[$champs[$i]])) {
                    // cas standard
                    if (!get_magic_quotes_gpc()) { // magic_quotes_gpc = Off
                        $this->val[$champs[$i]] = (
                            DBCHARSET == "UTF8" ? 
                            utf8_encode(strtr($postVar[$champs[$i]],chr(34),"'")) : strtr($postVar[$champs[$i]],chr(34),"'")
                        );
                    } else { // magic_quotes_gpc = On
                        $this->val[$champs[$i]] = (
                            DBCHARSET == "UTF8" ? 
                            utf8_encode(strtr(stripslashes($postVar[$champs[$i]]),chr(34),"'")) : strtr(stripslashes($postVar[$champs[$i]]),chr(34),"'")
                        );
                    }
                } else {
                    $this->val[$champs[$i]] = "";
                }
            }
        }
        //
        $this->addToLog(__METHOD__."(): \$this->val = ".print_r($this->val, true), EXTRA_VERBOSE_MODE);
    }

    /**
     * Recuperation des variables formulaires
     * ajout test si variable post existe
     * avant affectation a?$this->val[$champs[$i]]
     *
     * @param string $champ Libelle des champs a afficher
     * @param integer $validation - 0 1er passage
     *                            - > 0 passage suivant suite validation
     * @param boolean $postVar
     * @param boolean $DEBUG Parametre inutilise
     */
    function recupererPostvar($champs, $validation, $postVar, $DEBUG = false) {
        //
        $this->addToLog(__METHOD__."(): \$this->val = ".print_r($this->val, true), EXTRA_VERBOSE_MODE);
        //
        $this->addToLog(__METHOD__."(): \$postVar = ".print_r($postVar, true), EXTRA_VERBOSE_MODE);
        //
        for ($i = 0; $i < count($champs); $i++) {
            if ($validation > 0) {
                // magic_quotes_gpc est initialise dans php.ini
                // mise automatique de quote quand il y a un ", \ , '.
                if ($this->type[$champs[$i]] == "textdisabled"
                    or $this->type[$champs[$i]] == "static") {
                    $this->val[$champs[$i]] = "";
                } elseif ($this->type[$champs[$i]] == "checkbox_multiple"
                        || $this->type[$champs[$i]] == "select_multiple") {
                    // cas de checkbox/select multiple : les valeurs renvoyees
                    // dans le post sont dans un tableau donc ici les valeurs
                    // sont linearises dans une chaine avec separateur ;
                    if (isset($postVar[$champs[$i]])) {
                        if (!get_magic_quotes_gpc()) { // magic_quotes_gpc = Off
                            $this->val[$champs[$i]] = implode(";",$postVar[$champs[$i]]);
                        } else { // magic_quotes_gpc = On
                            $this->val[$champs[$i]] = stripslashes(implode(";",$postVar[$champs[$i]]));
                        }
                    } else {
                        $this->val[$champs[$i]] = "";
                    }
                } elseif (isset($postVar[$champs[$i]])) {
                    // cas standard
                    if (!get_magic_quotes_gpc()) { // magic_quotes_gpc = Off
                        $this->val[$champs[$i]] = strtr($postVar[$champs[$i]],chr(34),"'");
                    } else { // magic_quotes_gpc = On
                        $this->val[$champs[$i]] = strtr(stripslashes($postVar[$champs[$i]]),chr(34),"'");
                    }
                } else {
                    $this->val[$champs[$i]] = "";
                }
            }
        }
        //
        $this->addToLog(__METHOD__."(): \$this->val = ".print_r($this->val, true), EXTRA_VERBOSE_MODE);
    }

    // {{{ Accesseurs et mutateurs

    /**
     * Methode permettant de definir la liste des champs obligatoires
     *
     * @param array champs
     */
    function setRequired($champ) {
        $this->required_field[] = $champ;
    }

    /**
     *
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setVal($champ, $contenu) {

        $this->val[$champ] = $contenu;

    }

    /**
     *
     *
     * @param string $champ
     * @param string $contenu Type de champ :
     *                        - 'text' : saisie texte alpha numerique
     *                        - 'hidden' : le champ est cache, le parametre est
     *                        passe
     *                        - 'password' : saisie masquee
     *                        - 'static' : champ uniquement affiche
     *                        - 'date' : saisie de date
     *                        - 'hiddenstatic' : champ affiche et passage du
     *                        parametre
     *                        - 'select' : zone de selection soit sur une autre
     *                        table, soit par rapport a un tableau
     * @return void
     */
    function setType($champ, $contenu) {

        $this->type[$champ] = $contenu;

    }

    /**
     * Libelle
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setLib($champ, $contenu) {

        $this->lib[$champ] = $contenu;

    }

    /**
     * Maximum autorise a la saisie
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setMax($champ, $contenu) {

        $this->max[$champ] = $contenu;

    }

    /**
     * Taille du controle
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setTaille($champ, $contenu) {

        $this->taille[$champ] = $contenu;

    }

    /**
     *
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setSelect($champ, $contenu) {

        /*
        GESTION DES TABLES ET PASSAGE DE PARAMETRES
        valeur de $select ============================================================
        TABLE ------------------------------------------------------------------------
        select['nomduchamp'][0]= value de l option
        $select['nomduchamp'][1]= affichage
        COMBO (recherche de correspondance entre table importante)--------------------
        $select['code_departement_naissance'][0][0]="departement";// table
        $select['code_departement_naissance'][0][1]="code"; // zone origine
        $select['code_departement_naissance'][1][0]="libelle_departement"; // zone correl
        $select['code_departement_naissance'][1][1]="libelle_departement_naissance"; // champ correl
        (facultatif)
        $select['code_departement_naissance'][2][0]="code_departement"; // champ pour le where
        $select['code_departement_naissance'][2][1]="code_departement_naissance"; // zone du formulaire concerne
        TEXTAREAMULTI ----------------------------------------------------------------
        select['nomduchamp'][0]=  nom du champ origine pour recuperer la valeur
        -------------------------------------------------------------------------------
        */
        $this->select[$champ] = $contenu;

    }

    /**
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setOnchange($champ, $contenu) {

        $this->onchange[$champ] = $contenu;

    }

    /**
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setOnkeyup($champ, $contenu) {

        $this->onkeyup[$champ] = $contenu;

    }

    /**
     *
     * @param string $champ
     * @param string $contenu
     * @return void
     */
    function setOnclick($champ, $contenu) {

        $this->onclick[$champ] = $contenu;

    }

    /**
     *
     * @param string $champ
     * @param string $contenu Position du champ dans le groupe :
     *                        - 'D' premier champ du groupe
     *                        - 'F' dernier champ du groupe
     *                        - 'DF' premier et dernier (champ seul)
     * @param string $libelle libelle positionne au debut du groupe de champs
     * @param string $style   classes separees par un espace
     * @return void
     */
    function setGroupe($champ, $contenu, $libelle = "", $style = "") {

        $this->groupe[$champ][0] = $contenu;
        $this->groupe[$champ][1] = $libelle;
        $this->groupe[$champ][2] = $style;

    }

    /**
     *
     * @param string $champ
     * @param string $contenu Position du champ dans le groupe :
     *                        - 'D' premier champ du groupe
     *                        - 'F' dernier champ du groupe
     *                        - 'DF' premier et dernier (champ seul)
     * @param string $libelle libelle positionne au debut du groupe de champs
     * @param string $style   classes separees par un espace
     * @return void
     */
    function setRegroupe($champ, $contenu, $libelle, $style = "") {

        $this->regroupe[$champ][0] = $contenu;
        $this->regroupe[$champ][1] = $libelle;
        $this->regroupe[$champ][2] = $style;

    }

    /**
     *
     * @param string $champ
     * @param string $contenu Position du champ dans le groupe :
     *                        - 'D' premier champ du groupe
     *                        - 'F' dernier champ du groupe
     *                        - 'DF' premier et dernier (champ seul)
     * @param string $libelle libelle positionne au debut du groupe de champs
     * @param string $style   classes separees par un espace
     * @return void
     */
    function setBloc($champ, $contenu, $libelle = "", $style = "") {
        $this->layout[$champ][]=array($contenu, $libelle, $style, 'Bloc');
    }

    /**
     *
     * @param string $champ
     * @param string $contenu Position du champ dans le groupe :
     *                        - 'D' premier champ du groupe
     *                        - 'F' dernier champ du groupe
     *                        - 'DF' premier et dernier (champ seul)
     * @param string $libelle libelle positionne au debut du groupe de champs
     * @param string $style   classes separees par un espace
     * @return void
     */
    function setFieldset($champ, $contenu, $libelle = "", $style = "") {
        $this->layout[$champ][]=array($contenu, $libelle, $style, 'Fieldset');
    }

    /**
     *
     * @var array Valeurs de tous les parametres
     */
    var $parameters = array();

    /**
     *
     */
    function setParameters($parameters = array()) {
        //
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     *
     */
    function setParameter($parameter = "", $value = "") {
        //
        $this->parameters[$parameter] = $value;
    }

    /**
     *
     */
    function getParameter($parameter = "") {
        //
        if (isset($this->parameters[$parameter])) {
            return $this->parameters[$parameter];
        } else {
            return NULL;
        }
    }

    // }}}

    // {{{ Méthodes utilitaires

    /**
     * Affichage de la date suivant le format de la base de donnees
     *
     * @param string $val
     * @return string
     */
    function dateAff($val) {

        if (FORMATDATE == "AAAA-MM-JJ") {
            $valTemp = explode("-", $val);
            if( count($valTemp) == 3 ) {
                return $valTemp[2]."/".$valTemp[1]."/".$valTemp[0];
            }else{
                return $val;
            }
        }
        //
        if (FOMATDATE == "JJ/MM/AAAA") {
            return $val;
        }

    }

    /**
     * Cette méthode permet de transformer une chaine de caractère standard
     * en une chaine utilisable comme sélecteur css. Le principe est de
     * supprimer les espaces, les caractères spéciaux et les accents.
     * 
     * @param string $string La chaine de caractère à normaliser
     * 
     * @return string La chaine de caractère normalisée
     */
    function normalize_string($string = "") {
        //
        $invalid = array('Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 
            'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c', 'À'=>'A', 'Á'=>'A', 
            'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 
            'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 
            'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 
            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 
            'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e',  'ë'=>'e', 'ì'=>'i', 'í'=>'i',
            'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 
            'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y',  
            'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', "`" => "", "´" => "", 
            "„" => "", "`" => "", "´" => "'", "“" => "\"", "”" => "\"", 
            "´" => "'", "&acirc;€™" => "'", "{" => "", "~" => "", "–" => "-", 
            "’" => "",  "(" => "",  ")" => "", " " => "-", "/"=>"-", "'"=>"_",
        );
         
        $string = str_replace(array_keys($invalid), array_values($invalid), $string);
        $string = strtolower($string);
        return $string;
    }

    // }}}

    // {{{ Gestion des messages de debug

    /**
     *
     */
    function addToLog($message, $type = DEBUG_MODE) {
        //
        if (isset($this->f) && method_exists($this->f, "elapsedtime")) {
            logger::instance()->log(
                $this->f->elapsedtime()." : class ".get_class($this)." - ".$message,
                $type
            );
        } else {
            logger::instance()->log(
                "X.XXX : class ".get_class($this)." - ".$message,
                $type
            );
        }
    }

    // }}}

    // {{{ WIDGET_FORM - START

    /**
     * WIDGET_FORM - affichepdf.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function affichepdf($champ, $validation, $DEBUG = false) {

        //
        $scan_pdf = $this->val[$champ];
        //
        echo "<object data='".$scan_pdf."' name='".$champ."' value=\"".
        $scan_pdf."\" type='application/pdf' width='650' height='200'>";
        echo "</object>";

    }


    /**
     * WIDGET_FORM - checkbox.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function checkbox($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] == 1 || $this->val[$champ] == "t"
            || $this->val[$champ] == "Oui") {
            $value = "Oui";
            $checked = " checked=\"checked\"";
        } else {
            $value = "";
            $checked = "";
        }
        //
        echo "<input";
        echo " type=\"".$this->type[$champ]."\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$value."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo $checked;
        if (!$this->correct) {
            echo " onchange=\"changevaluecheckbox(this);";
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo "".$this->onchange[$champ]."";
            }
            echo "\"";
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

    }

    /**
     * WIDGET_FORM - checkboxdisabled.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function checkboxdisabled($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] == 1 || $this->val[$champ] == "t"
            || $this->val[$champ] == "Oui") {
            $value = "Oui";
            $checked = " checked=\"checked\"";
        } else {
            $value = "";
            $checked = "";
        }
        //
        echo "<input";
        echo " type=\"checkbox\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$value."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo " disabled=\"disabled\"";
        echo $checked;
        if (!$this->correct) {
            echo " onchange=\"changevaluecheckbox(this);";
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo "".$this->onchange[$champ]."";
            }
            echo "\"";
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        }
        echo " />\n";
    }

    /**
     * WIDGET_FORM - checkboxnum.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function checkboxnum($champ, $validation, $DEBUG = false) {

        //
        if($this->val[$champ] == 1) {
            $value = 1;
            $checked = "checked ";
        } else {
            $value = 0;
            $checked = "";
        }
        //
        if (!$this->correct) {
            if ($this->onchange != "") {
                echo "<input type='checkbox' ";
                echo "name='".$champ."' ";
                echo "value=\"$value\" ";
                echo " id=\"".$champ."\" ";
                echo "size=".$this->taille[$champ]." ";
                echo "maxlength=".$this->max[$champ]." ";
                echo "onchange=\"changevaluecheckboxnum(this);".$this->onchange[$champ]."\" ";
                echo "class='champFormulaire' ";
                echo $checked;
                echo ">\n";
            } else {
                echo "<input type='checkbox' ";
                echo "name='".$champ."' ";
                echo "value=\"$value\" ";
                echo " id=\"".$champ."\" ";
                echo "size=".$this->taille[$champ]." ";
                echo "maxlength=".$this->max[$champ]." ";
                echo "onchange=\"changevaluecheckboxnum(this)\" ";
                echo "class='champFormulaire' ";
                echo $checked;
                echo ">\n";
            }
        } else {
            echo "<input type='checkbox' ";
            echo "name='".$champ."' ";
            echo "value=\"$value\" ";
            echo " id=\"".$champ."\" ";
            echo "size=".$this->taille[$champ]." ";
            echo "maxlength=".$this->max[$champ]." ";
            echo "onchange=\"changevaluecheckboxnum(this)\" ";
            echo "class='champFormulaire' ";
            echo "disabled=\"disabled\" ";
            echo $checked;
            echo ">\n";
        }

    }

    /**
     * WIDGET_FORM - checkboxstatic.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function checkboxstatic($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] == 1 || $this->val[$champ] == "t"
            || $this->val[$champ] == "Oui") {
            $value = "Oui";
        } else {
            $value = "Non";
        }
        echo "<span id=\"".$champ."\" class=\"field_value\">$value</span>";
    }

    /**
     * WIDGET_FORM - checkbox_multiple.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function checkbox_multiple($champ, $validation, $DEBUG = false) {

        // ***************************************************************************
        // CHECKBOX_MULTIPLE
        //select['nomduchamp'][0]= value de l option
        //select['nomduchamp'][1]= affichage
        // ****************************************************************************
        // Delinearisation
        $selected_values = explode(";", $this->val[$champ]);
        //
        $k = 0;
        foreach ($this->select[$champ] as $elem) {
            while ($k <count($elem)) {
                //
                //
                echo "<input";
                echo " type=\"checkbox\"";
                echo " name=\"".$champ."[]\"";
                echo " value=\"".$this->select[$champ][0][$k]."\"";
                echo " class=\"champFormulaire\"";
                if (in_array($this->select[$champ][0][$k], $selected_values)) {
                    echo " checked=\"checked\"";
                }
                if (!$this->correct) {
                    if (isset($this->onchange) and $this->onchange[$champ] != "") {
                        echo " onchange=\"".$this->onchange[$champ]."\"";
                    }
                    if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                        echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
                    }
                    if (isset($this->onclick) and $this->onclick[$champ] != "") {
                        echo " onclick=\"".$this->onclick[$champ]."\"";
                    }
                } else {
                    echo " disabled=\"disabled\"";
                }
                echo " />\n";
                echo $this->select[$champ][1][$k];
                echo "<br/>";
                $k++;
                //
            }
        }

    }

    /**
     * WIDGET_FORM - comboC.
     *
     * combo en cascade ex: voie -> zone -> cimetiere
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function comboC($champ, $validation, $DEBUG = false) {

        $tab=$this->select[$champ][0][0];
        $zorigine=$this->select[$champ][0][1];
        $zcorrel=$this->select[$champ][1][0];
        $correl=$this->select[$champ][1][1];
        if(isset($this->select[$champ][2][0])){
        $zcorrel2=$this->select[$champ][2][1];
        $correl2=$this->select[$champ][2][0];
        }else{
        $zcorrel2="s1"; // valeur du champ submit (sinon pb dans js)
        $correl2="";
        }
        $params="&amp;table=".$tab."&amp;correl=".$correl."&amp;zorigine=".$zorigine."&amp;zcorrel=".$zcorrel."&amp;correl2=".$correl2;

        echo "<a class=\"combog ui-state-default ui-corner-all\" href=\"javascript:vcorrel('".$champ."','".$zcorrel2."','".$params."');\">";
        echo "<span class=\"ui-icon ui-icon-circle-arrow-w\" ";
        echo "title=\""._("Cliquer ici pour correler")."\">";
        echo "<- "._("Correler");
        echo "</span>";
        echo "</a>\n";

        echo "<input ";
        echo " type='text' ";
        echo " name='".$champ."' ";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\" ";
        echo " autocomplete=\"off\" ";
        echo " size='".$this->taille[$champ]."' ";
        echo " maxlength='".$this->max[$champ]."' ";
        echo " onchange=\"".$this->onchange[$champ]."\" ";
        echo " class='champFormulaire comboc' />\n";

        echo "<a class=\"comboc ui-state-default ui-corner-all\" href=\"javascript:vcorrel3('".$tab."');\">";
        echo "<span class=\"ui-icon ui-icon-extlink\" ";
        echo "title=\""._("Cliquer ici pour correler")."\">";
        echo "<- "._("Correler");
        echo "</span>";
        echo "</a>\n";

    }

    /**
     * WIDGET_FORM - comboD.
     *
     * Combo droit (recherche de correspondance entre table importante)
     * $select['code_departement_naissance'][0][0]="departement";// table
     * $select['code_departement_naissance'][0][1]="code"; // zone origine
     * $select['code_departement_naissance'][1][0]="libelle_departement"; // zone correl
     * $select['code_departement_naissance'][1][1]="libelle_departement_naissance"; // champ correl
     * (facultatif)
     * $select['code_departement_naissance'][2][0]="code_departement"; // champ pour le where
     * $select['code_departement_naissance'][2][1]="code_departement_naissance"; // zone du formulaire concern?
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function comboD($champ, $validation, $DEBUG = false) {

        echo "<input ";
        echo "type=\"text\" ";
        //echo "type=\"".$this->type[$champ]."\" ";
        echo "name=\"".$champ."\" ";
        echo " id=\"".$champ."\" ";
        echo "value=\"".$this->val[$champ]."\" ";
        echo " autocomplete=\"off\" ";
        echo "size=\"".$this->taille[$champ]."\" ";
        echo "maxlength=\"".$this->max[$champ]."\" ";
        if (!$this->correct) {
            if ($this->onchange != "") {
                echo "onchange=\"".$this->onchange[$champ]."\" ";
            }
        } else {
            echo "disabled=\"disabled\" ";
        }
        echo "class=\"champFormulaire combod\" ";
        echo "/>\n";

        if (!$this->correct) {
            //
            $tab = $this->select[$champ][0][0];
            $zorigine = $this->select[$champ][0][1];
            $zcorrel = $this->select[$champ][1][0];
            $correl = $this->select[$champ][1][1];
            if (isset($this->select[$champ][2][0])) {
                $zcorrel2 = $this->select[$champ][2][1];
                $correl2 = $this->select[$champ][2][0];
            } else {
                $zcorrel2 = "s1";  // valeur du champ submit (sinon pb dans js)
                $correl2 = "";
            }
            $params = "&amp;table=".$tab."&amp;correl=".$correl."&amp;zorigine=".$zorigine."&amp;zcorrel=".$zcorrel."&amp;correl2=".$correl2;
            echo "<a class=\"combod ui-state-default ui-corner-all\" href=\"javascript:vcorrel('".$champ."','".$zcorrel2."','".$params."');\">\n";
            echo "<span class=\"ui-icon ui-icon-circle-arrow-e\" ";
            echo "title=\""._("Cliquer ici pour correler")."\">";
            echo "-> "._("Correler");
            echo "</span>";
            echo "</a>\n";
        }

    }

    /**
     * WIDGET_FORM - comboD2.
     *
     * combo D2 pour F2 (sousformdyn)
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function comboD2($champ, $validation, $DEBUG = false) {

        if ($this->correct) {
            echo "<input type='".$this->type[$champ]."' ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo "size='".$this->taille[$champ]."' ";
            echo "maxlength='".$this->max[$champ]."' ";
            echo "class='champFormulaire combod' ";
            echo "disabled=\"disabled\" ";
            echo ">\n";
        } else {
            echo "<input type='".$this->type[$champ]."' ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo " autocomplete=\"off\" ";
            echo "size='".$this->taille[$champ]."' ";
            echo "maxlength='".$this->max[$champ]."' ";
            echo "onchange=\"".$this->onchange[$champ]."\" ";
            echo "class='champFormulaire combod' ";
            echo ">\n";
            //
            $tab = $this->select[$champ][0][0];
            $zorigine = $this->select[$champ][0][1];
            $zcorrel = $this->select[$champ][1][0];
            $correl = $this->select[$champ][1][1];
            if (isset($this->select[$champ][2][0])) {
                $zcorrel2 = $this->select[$champ][2][1];
                $correl2 = $this->select[$champ][2][0];
            } else {
                $zcorrel2 = "s1";  // valeur du champ submit (sinon pb dans js)
                $correl2 = "";
            }
            $params = "&table=".$tab."&correl=".$correl."&zorigine=".$zorigine."&zcorrel=".$zcorrel."&correl2=".$correl2;
            // appel vcorrel2
            echo "<a class=\"combod ui-state-default ui-corner-all\" href=\"javascript:vcorrel2('".$champ."','".$zcorrel2."','".$params."');\">";
            echo "<span class=\"ui-icon ui-icon-circle-arrow-e\" ";
            echo "title=\""._("Cliquer ici pour correler")."\">";
            echo "-> "._("Correler");
            echo "</span>";
            echo "</a>\n";
        }

    }

    /**
     * WIDGET_FORM - comboG.
     *
     * Combo gauche
     * (recherche de correspondance entre table importante)
     * $select['code_departement_naissance'][0][0]="departement";// table
     * $select['code_departement_naissance'][0][1]="code"; // zone origine
     * $select['code_departement_naissance'][1][0]="libelle_departement"; // zone correl
     * $select['code_departement_naissance'][1][1]="libelle_departement_naissance"; // champ correl
     * (facultatif)
     * $select['code_departement_naissance'][2][0]="code_departement"; // champ pour le where
     * $select['code_departement_naissance'][2][1]="code_departement_naissance"; // zone du formulaire concernee
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function comboG($champ, $validation, $DEBUG = false) {

        if (!$this->correct) {
            // zone libelle
            $tab = $this->select[$champ][0][0];
            $zorigine = $this->select[$champ][0][1];
            $zcorrel = $this->select[$champ][1][0];
            $correl = $this->select[$champ][1][1];
            if (isset($this->select[$champ][2][0])) {
                $zcorrel2 = $this->select[$champ][2][1];
                $correl2 = $this->select[$champ][2][0];
            } else {
                $zcorrel2 = "s1"; // valeur du champ submit (sinon pb dans js)
                $correl2 = "";
            }
            $params = "&amp;table=".$tab."&amp;correl=".$correl."&amp;zorigine=".$zorigine."&amp;zcorrel=".$zcorrel."&amp;correl2=".$correl2;
            //
            echo "<a class=\"combog ui-state-default ui-corner-all\" href=\"javascript:vcorrel('".$champ."','".$zcorrel2."','".$params."');\">\n";
            echo "<span class=\"ui-icon ui-icon-circle-arrow-w\" ";
            echo "title=\""._("Cliquer ici pour correler")."\">";
            echo "<- "._("Correler");
            echo "</span>";
            echo "</a>\n";
        }

        echo "<input ";
        echo "type=\"text\" ";
        //echo "type=\"".$this->type[$champ]."\" ";
        echo " autocomplete=\"off\" ";
        echo "name=\"".$champ."\" ";
        echo " id=\"".$champ."\" ";
        echo "value=\"".$this->val[$champ]."\" ";
        echo "size=\"".$this->taille[$champ]."\" ";
        echo "maxlength=\"".$this->max[$champ]."\" ";
        if (!$this->correct) {
            if ($this->onchange != "") {
                echo "onchange=\"".$this->onchange[$champ]."\" ";
            }
        } else {
            echo "disabled=\"disabled\" ";
        }
        echo "class=\"champFormulaire combog\" ";
        echo "/>\n";

    }

    /**
     * WIDGET_FORM - comboG2.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function comboG2($champ, $validation, $DEBUG = false) {

        if (!$this->correct) {
            // zone libelle
            $tab = $this->select[$champ][0][0];
            $zorigine = $this->select[$champ][0][1];
            $zcorrel = $this->select[$champ][1][0];
            $correl = $this->select[$champ][1][1];
            if (isset($this->select[$champ][2][0])) {
                $zcorrel2 = $this->select[$champ][2][1];
                $correl2 = $this->select[$champ][2][0];
            } else {
                $zcorrel2 = "s1"; // valeur du champ submit (sinon pb dans js)
                $correl2 = "";
            }
            $params="&table=".$tab."&correl=".$correl."&zorigine=".$zorigine."&zcorrel=".$zcorrel."&correl2=".$correl2;
            // appel vcorrel2
            echo "<a class=\"combog ui-state-default ui-corner-all\" href=\"javascript:vcorrel2('".$champ."','".$zcorrel2."','".$params."');\">";
            echo "<span class=\"ui-icon ui-icon-circle-arrow-w\" ";
            echo "title=\""._("Cliquer ici pour correler")."\">";
            echo "<- "._("Correler");
            echo "</span>";
            echo "</a>";
            //
            echo "<input type=".$this->type[$champ]." ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo " autocomplete=\"off\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo "size=".$this->taille[$champ]." ";
            echo "maxlength=".$this->max[$champ]." ";
            echo "onchange=\"".$this->onchange[$champ]."\" ";
            echo "class='champFormulaire combog' ";
            echo ">\n";
        } else {
            echo "<input type=".$this->type[$champ]." ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo "size=".$this->taille[$champ]." ";
            echo "maxlength=".$this->max[$champ]." ";
            echo "class='champFormulaire combog' ";
            echo "disabled=\"disabled\"";
            echo ">\n";
        }

    }

    /**
     * WIDGET_FORM - date.
     *
     * La date est saisie ou affichee sous le format JJ/MM/AAAA, un calendrier
     * s affiche en js
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function date($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] != "" and $validation == 0) {
            $this->val[$champ] = $this->dateAff($this->val[$champ]);
        }
        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"10\"";
        if (!$this->correct) {
            echo " class=\"champFormulaire datepicker\"";
            if ($this->onchange != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if ($this->onkeyup != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if ($this->onclick != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " class=\"champFormulaire\"";
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

    }

    /**
     * WIDGET_FORM - date2.
     *
     * Date en Full Onglet, la date est saisie ou affichee sous le format
     * JJ/MM/AAAA, un calendrier s affiche en js
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function date2($champ, $validation, $DEBUG = false) {

        //
        $this->date($champ, $validation);

    }

    /**
     * WIDGET_FORM - datedisabled.
     *
     * Champs date disabled
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function datedisabled($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] != "" and $validation == 0) {
            $defautDate = $this->dateAff($this->val[$champ]);
        } else {
            $defautDate = $this->val[$champ];
        }
        //
        if (!$this->correct) {
            echo "<input type='text' ";
            echo "name='".$champ."' ";
            echo "id=\"".$champ."\" ";
            echo "value=\"".$defautDate."\" ";
            echo "class='champFormulaire' disabled=\"disabled\" />\n";
        } else {
            echo $this->val[$champ]."\n";
        }

    }

    /**
     * WIDGET_FORM - datestatic.
     *
     * Date static formatee
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function datestatic($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] != "") {
            $defautDate = $this->dateAff($this->val[$champ]);
        } else {
            $defautDate = $this->val[$champ];
        }
        //
        echo "<span id=\"".$champ."\" class=\"field_value\">";
            echo $defautDate."";
        echo "</span>";

    }

    /**
     * WIDGET_FORM - file.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function file($champ, $validation, $DEBUG = false) {
        // Si le storage n'est pas configuré, alors on affiche un message
        // d'erreur clair pour l'utilisateur
        echo "<div id=\"".$champ."\">";
        if ($this->f->storage == NULL) {
            // Message d'erreur
            echo _("Le syteme de stockage n'est pas accessible. Erreur de ".
                   "parametrage. Contactez votre administrateur.");
            echo "</div>";
            // On sort de la méthode
            return -1;
        }
        //
        if ($this->f->storage->getFilename($this->val[$champ]) != ""
            AND $this->val[$champ] != "") {
            //
            echo $this->f->storage->getFilename($this->val[$champ]);
            //
            $link = "../spg/voir.php?obj=".$this->getParameter("obj")."&amp;champ=".$champ.
                    "&amp;id=".$this->getParameter("idx");
            //
            echo "<span class=\"om-prev-icon consult-16\" title=\""._("Ouvrir le fichier")."\">";
            echo "<a href=\"javascript:load_form_in_modal('".$link."');\" >";
            echo _("Visualiser");
            echo "</a>";
            echo "</span>";
            //
            echo "<span class=\"om-prev-icon reqmo-16\" title=\""._("Enregistrer le fichier")."\">";
            echo "<a href=\"../spg/file.php?obj=".$this->getParameter("obj")."&amp;champ=".$champ.
                    "&amp;id=".$this->getParameter("idx")."\" target=\"_blank\">";
            echo _("Telecharger");
            echo "</a>";
            echo "</span>";
        } elseif ($this->val[$champ] != "") {
            //
            echo _("Le fichier n'existe pas ou n'est pas accessible.");
        }
        echo "</div>";
    }

    /**
     * WIDGET_FORM - filestatic.
     *
     * Affichage du nom du fichier ou d'une erreur si le fichier est inaccessible
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function filestatic($champ, $validation, $DEBUG = false) {
        // Si le storage n'est pas configuré, alors on affiche un message
        // d'erreur clair pour l'utilisateur
        echo "<div id=\"".$champ."\">";
        if ($this->f->storage == NULL) {
            // Message d'erreur
            echo _("Le syteme de stockage n'est pas accessible. Erreur de ".
                   "parametrage. Contactez votre administrateur.");
            echo "</div>";
            // On sort de la méthode
            return -1;
        }
        if ($this->f->storage->getFilename($this->val[$champ]) != ""
            AND $this->val[$champ] != "") {
            //
            echo $this->f->storage->getFilename($this->val[$champ]);
        } elseif ($this->val[$champ] != "") {
            //
            echo _("Le fichier n'existe pas ou n'est pas accessible.");
        }
        echo "</div>";
    }

    /**
     * WIDGET_FORM - geom.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function geom($champ, $validation, $DEBUG = false) {
        if (file_exists("../dyn/var.inc")) {
            include "../dyn/var.inc";
        }
        if (!isset($siglien)) {
            $siglien = "../scr/tab_sig.php?idx=";
        }
        if (isset($this->select[$champ][0][0])
            && isset($this->select[$champ][0][1])
            && isset($this->select[$champ][0][2])) {
            //
            $obj = $this->select[$champ][0][0];
            $idx = $this->select[$champ][0][1];
            $seli = $this->select[$champ][0][2];
            //
            echo "<a class=\"localisation ui-state-default ui-corner-all\" href=\"javascript:localisation_sig('".$siglien."','".$idx."','".$obj."','".$seli."');\">";
            echo "<span class=\"ui-icon sig-16\" ";
            echo "title=\""._("Cliquer ici pour positionner l'element")."\">";
            echo _("Localisation");
            echo "</span>";
            echo "</a>";
        }
    }

    /**
     * WIDGET_FORM - hidden.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function hidden($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"".$this->type[$champ]."\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        //echo " size=\"".$this->taille[$champ]."\"";
        //echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

    }

    /**
     * WIDGET_FORM - hiddendate.
     *
     * Type hidden sur les champs dates.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function hiddendate($champ, $validation, $DEBUG = false) {

        if ($this->val[$champ] != "" and $validation == 0) {
            $this->val[$champ] = $this->dateAff($this->val[$champ]);
        }

        echo "<input";
        echo " type=\"hidden\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"10\"";
        if (!$this->correct) {
            echo " class=\"champFormulaire datepicker\"";
            if ($this->onchange != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if ($this->onkeyup != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if ($this->onclick != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " class=\"champFormulaire\"";
            echo " disabled=\"disabled\"";
        }
        echo " />\n";
    }

    /**
     * WIDGET_FORM - hiddenstatic.
     * 
     * La valeur du champ est passe par le controle hidden
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function hiddenstatic($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"hidden\"";
        echo " id=\"".$champ."\"";
        echo " name=\"".$champ."\"";
        echo " value=\"".$this->val[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo " />\n";
        echo $this->val[$champ]."\n";

    }

    /**
     * WIDGET_FORM - hiddenstaticdate.
     *
     * La valeur du champ est passe par le controle hidden
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function hiddenstaticdate($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] != "" and $validation == 0) {
            $defautDate = $this->dateAff($this->val[$champ]);
        } else {
            $defautDate = $this->val[$champ];
        }
        //
        if (!$this->correct) {
            echo "<input type='hidden' ";
            echo "name='".$champ."' ";
            echo "id=\"".$champ."\" ";
            echo "value=\"".$defautDate."\" ";
            echo "class='champFormulaire' />\n";
            echo $defautDate."";
        } else {
            echo $this->val[$champ]."\n";
        }

    }

    /**
     * WIDGET_FORM - hiddenstaticnum.
     *
     * La valeur du champ est passe par le controle hidden
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function hiddenstaticnum($champ, $validation, $DEBUG = false) {

        echo "<input type='hidden' ";
        echo "name='".$champ."' ";
        echo "id=\"".$champ."\" ";
        echo "value=\"".$this->val[$champ]."\" ";
        echo "class='champFormulaire' >\n";
        echo "<p align='right'>".$this->val[$champ]."</p>\n";

    }

    /**
     * WIDGET_FORM - html.
     *
     * Méthode d'affichage de tinyMCE sur textarea
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function html($champ, $validation, $DEBUG = false) {
        if(!isset($this->select[$champ]['class'])) {
            $this->select[$champ]['class'] = "";
        }
        if (!$this->correct) {
            $this->select[$champ]['class'] .= " html";
            $this->textarea($champ, $validation, $DEBUG);
        } else {
            $this->htmlstatic($champ, $validation, $DEBUG);
        }
    }

    /**
     * WIDGET_FORM - htmlEtat.
     *
     * Méthode d'affichage de tinyMCE simplifié pour titre om_etat
     * et om_lettretype
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function htmlEtat($champ, $validation, $DEBUG = false) {
        if(!isset($this->select[$champ]['class'])) {
            $this->select[$champ]['class'] = "";
        }
        if (!$this->correct) {
            $this->select[$champ]['class'] .= " htmletat";
            $this->textarea($champ, $validation, $DEBUG);
        } else {
            $this->htmlstatic($champ, $validation, $DEBUG);
        }
    }

    /**
     * WIDGET_FORM - htmlEtatEx.
     *
     * Méthode d'affichage de tinyMCE extended sur textarea pour
     * corps d'om_etat et om_lettretype
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function htmlEtatEx($champ, $validation, $DEBUG = false) {
        if(!isset($this->select[$champ]['class'])) {
            $this->select[$champ]['class'] = "";
        }
        if (!$this->correct) {
            $this->select[$champ]['class'] .= " htmletatex";
            $this->textarea($champ, $validation, $DEBUG);
        } else {
            $this->htmlstatic($champ, $validation, $DEBUG);
        }
    }

    /**
     * WIDGET_FORM - htmlstatic.
     *
     * Méthode d'affichage du html interprété sur textarea
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function htmlstatic($champ, $validation, $DEBUG = false) {
        echo "<div id='".$champ."'>".$this->val[$champ]."</div>";
    }

    /**
     * WIDGET_FORM - http.
     *
     * lien http en formulaire - passage d argument sur une
     * application tierce
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function http($champ, $validation, $DEBUG = false) {

        //
        if (isset($this->select[$champ][0])) {
            $aff = $this->select[$champ][0];
        } else {
            $aff = $champ;
        }
        //
        echo "<a href=\"".$this->val[$champ]."\" target=\"_blank\">";
        echo $aff;
        echo "</a>\n";

    }

    /**
     * WIDGET_FORM - httpclick.
     *
     * lien http en formulaire - passage d argument sur une
     * application tierce
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function httpclick($champ, $validation, $DEBUG = false) {

        //
        if (isset($this->select[$champ][0])) {
            $aff = $this->select[$champ][0];
        } else {
            $aff = $champ;
        }
        //
        echo "<a href='#' onclick=\"".$this->val[$champ]."; return false;\" >";
        echo $aff;
        echo "</a>\n";

    }

    /**
     * WIDGET_FORM - localisation.
     *
     * - $select['positiony'][0]="plan";// zone plan
     * - $select['positiony'][1]="positionx"; // zone coordonnees X
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function localisation($champ, $validation, $DEBUG = false) {

        //
        echo "<input data-role=\"none\"";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire localisation\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

        //
        if (!$this->correct) {
            // zone libelle
            
            $plan = $this->select[$champ][0][0];  // plan
            $positionx = $this->select[$champ][0][1];
            //
            $params = array(
                    "champ" => $champ,
                    "plan" => $plan,
                    "positionx"  => $positionx
                 );
            $this->f->layout->display_formulaire_localisation_lien($params);
            //
        }

    }

    /**
     * WIDGET_FORM - localisation2.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function localisation2($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire localisation\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

        //
        if (!$this->correct) {
            // zone libelle
            $plan = $this->select[$champ][0][0];  // plan
            $positionx = $this->select[$champ][0][1];
            //
            echo "<a class=\"localisation ui-state-default ui-corner-all\" href=\"javascript:localisation2('".$champ."','".$plan."','".$positionx."');\">";
            echo "<span class=\"ui-icon ui-icon-pin-s\" ";
            echo "title=\""._("Cliquer ici pour positionner l'element")."\">";
            echo _("Localisation");
            echo "</span>";
            echo "</a>";
        }

    }

    /**
     * WIDGET_FORM - localisation_edition.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function localisation_edition($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        //  
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire localisation\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

        //
        if (!$this->correct) {
            //
            $format = (isset($this->select[$champ]["format"]) ? $this->select[$champ]["format"] : "");
            $orientation = (isset($this->select[$champ]["orientation"]) ? $this->select[$champ]["orientation"] : "");
            $x = (isset($this->select[$champ]["x"]) ? $this->select[$champ]["x"] : "");
            $y = (isset($this->select[$champ]["y"]) ? $this->select[$champ]["y"] : "");
            //
            echo "<button class=\"localisation\" type=\"button\" onclick=\"";
            echo "javascript:localisation_edition(form, '".$format."','".$orientation."','".$x."','".$y."');";
            echo "\">";
            echo "<span class=\"ui-icon ui-icon-pin-s\" title=\""._("Cliquer ici pour positionner l'element")."\">";
            echo _("Localisation");
            echo "</span>";
            echo "</button>";
        }

    }

    /**
     * WIDGET_FORM - mail.
     *
     * Envoi avec le logiciel de messagerie
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function mail($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire mail\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";
        //
        $mail = $this->val[$champ];
        //
        echo "<a class=\"mail ui-state-default ui-corner-all\" href='mailto:".$mail."'>";
        echo "<span class=\"ui-icon ui-icon-mail-closed\" ";
        echo "title=\""._("Cliquer ici pour envoyer un mail a cette adresse")."\">";
        echo _("MailTo");
        echo "</span>";
        echo "</a>";

    }

    /**
     * WIDGET_FORM - nodisplay.
     *
     * Widget permettant d'annuler l'affichage d'un champ
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function nodisplay($champ, $validation, $DEBUG = false) {

    }

    /**
     * WIDGET_FORM - pagehtml.
     *
     * Page HTML : les \n => <br> en affichage
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function pagehtml($champ, $validation, $DEBUG = false) {

        //
        if ($this->val[$champ] != "" and $validation == 0) {
            $this->val[$champ] = str_replace("<br>", "\n", $this->val[$champ]);
        }
        //
        if (!$this->correct) {
            echo "<textarea ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "cols=".$this->taille[$champ]." ";
            echo "rows=".$this->max[$champ]." ";
            echo "onchange=\"".$this->onchange[$champ]."\" ";
            echo "class='champFormulaire' >";
            echo $this->val[$champ];
            echo "</textarea>\n";
        } else {
            echo "<textarea ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "cols=".$this->taille[$champ]." ";
            echo "rows=".$this->max[$champ]." ";
            echo "onchange=\"".$this->onchange[$champ]."\" ";
            echo "class='champFormulaire' ";
            echo "disabled=\"disabled\" >";
            echo $this->val[$champ];
            echo "</textarea>\n";
        }

    }

    /**
     * WIDGET_FORM - password.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function password($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"".$this->type[$champ]."\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

    }

    /**
     * WIDGET_FORM - rvb.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function rvb($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire rvb\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

    }

    /**
     * WIDGET_FORM - rvb2.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function rvb2($champ, $validation, $DEBUG = false) {

        //
        $this->rvb($champ, $validation, $DEBUG);

    }

    /**
     * WIDGET_FORM - select.
     *
     * SELECT - Affichage de table
     * - select['nomduchamp'][0]= value de l option
     * - $select['nomduchamp'][1]= affichage
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function select($champ, $validation, $DEBUG = false) {

        //
        if (!$this->correct) {
            if ($this->onchange[$champ] != "") {
                echo "<select ";
                echo "name='".$champ."' ";
                echo " id=\"".$champ."\" ";
                echo "size='1' ";
                echo "onchange=\"".$this->onchange[$champ]."\" ";
                echo " class=\"'champFormulaire\" \n";
                echo " >\n";
            } else {
                $params = array(
                    "champ" => $champ
                );
                $this->f->layout->display_formulaire_select_personnalise($params);
                /*echo "<select ";
                echo "name='".$champ."' ";
                echo " id=\"".$champ."\" ";
                echo "size='1' ";
                echo " class=\"'champFormulaire\" \n";
                echo " >\n";*/
            }
        } else {
            echo "<select ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "size='1' ";
            echo "class='champFormulaire' ";
            echo "disabled=\"disabled\" >\n";
        }
        //
        $k = 0;
        foreach ($this->select[$champ] as $elem) {
            while ($k <count($elem)) {
                if (!$this->correct) {
                    if ($this->val[$champ] == $this->select[$champ][0][$k]) {
                        echo "    <option ";
                        echo "selected=\"selected\" ";
                        echo "value=\"".$this->select[$champ][0][$k]."\">";
                        echo $this->select[$champ][1][$k];
                        echo "</option>\n";
                    } else {
                        echo "    <option ";
                        echo "value=\"".$this->select[$champ][0][$k]."\">";
                        echo $this->select[$champ][1][$k];
                        echo "</option>\n";
                    }
                    $k++;
                } else {
                    if ($this->val[$champ] == $this->select[$champ][0][$k]) {
                        echo "    <option ";
                        echo "selected=\"selected\" ";
                        echo "value=\"".$this->select[$champ][0][$k]."\" >";
                        echo $this->select[$champ][1][$k];
                        echo "</option>\n";
                        $k = count($elem);
                    }
                    $k++;
                }
            }
        }
        //
        echo "</select>";

    }

    /**
     * WIDGET_FORM - selectdisabled.
     *
     * Affichage champ + lien mais pas modification de
     * donnees $val
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selectdisabled($champ, $validation, $DEBUG = false) {

        //
        echo "<select ";
        echo "name='".$champ."' ";
        echo " id=\"".$champ."\" ";
        echo "size='1' ";
        echo "class='champFormulaire' ";
        echo "disabled=\"disabled\">\n";
        //
        $k = 0;
        foreach ($this->select[$champ] as $elem) {
            while ($k < count($elem)) {
                if (!$this->correct) {
                    if ($this->val[$champ] == $this->select[$champ][0][$k]) {
                        echo "    <option ";
                        echo "selected=\"selected\" ";
                        echo "value=\"".$this->select[$champ][0][$k]."\">";
                        echo $this->select[$champ][1][$k];
                        echo "</option>\n";
                    }
                    $k++;
                } else {
                    if ($this->val[$champ] == $this->select[$champ][0][$k]){
                        echo "    <option ";
                        echo "selected=\"selected\" ";
                        echo "value=\"".$this->select[$champ][0][$k]."\">";
                        echo $this->select[$champ][1][$k];
                        echo "</option>\n";
                        $k = count($elem);
                    }
                    $k++;
                }
            }
        }
        //
        echo "</select>\n";
    }

    /**
     * WIDGET_FORM - selecthiddenstatic.
     *
     * Affichage d'un champ lie avec:
     *
     * - libelle statique
     * - valeur en champ cache
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selecthiddenstatic($champ, $validation, $DEBUG = false) {

        // si la valeur existe dans la liste des valeurs
        if (in_array($this->val[$champ], $this->select[$champ][0])) {

            // recherche du libelle associe a la valeur du champ
            $key = array_search($this->val[$champ], $this->select[$champ][0]);

            // affichage du libelle
            echo '<span class="field_value">';
            echo $this->select[$champ][1][$key];
            echo '</span>';

            // affichage du champ cache
            echo "<input";
            echo " type=\"hidden\"";
            echo " id=\"".$champ."\"";
            echo " name=\"".$champ."\"";
            echo " value=\"".$this->val[$champ]."\"";
            echo " class=\"champFormulaire\"";
            echo " />\n";
        }
    }

    /**
     * WIDGET_FORM - selecthiddenstaticlick.
     *
     * selecthiddenstatic amelioré - lien http sur objet correspondant a la cle etrangere
     * soit dans la meme fenetre soit dans un nouvel onglet (2 boutons)
     * application tierce
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selecthiddenstaticlick($champ, $validation, $DEBUG = false) {
        $this->selecthiddenstatic($champ, $validation, $DEBUG);
        echo "<a class=\"upload ui-state-default ui-corner-all\" href=\"./form.php?obj=".$champ."&action=3&idx=".$this->val[$champ]."\">";
        echo "<span class=\"ui-icon ui-icon-extlink\" ";
        echo "title=\""._("Cliquer pour aller a la fiche correspondante")."\">";
        echo _("aller");
        echo "</span>";
        echo "</a>    \n     ";
                echo "<a class=\"upload ui-state-default ui-corner-all\" href=\"./form.php?obj=".$champ."&action=3&idx=".$this->val[$champ]."\"         target=\"_blank\">";
        echo "<span class=\"ui-icon ui-icon-newwin\" ";
        echo "title=\""._("Cliquer pour aller a la fiche correspondante dans une nouvelle fenetre")."\">";
        echo _("aller");
        echo "</span>";
        echo "</a>\n";
    }

    /**
     * WIDGET_FORM - selectliste.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selectliste($champ, $validation, $DEBUG = false) {

        // ***************************************************************************
        // SELECTLISTE (liste)
        // affichage de table
        //select['nomduchamp'][0]= value de l option
        //select['nomduchamp'][1]= affichage
        // ****************************************************************************
        if(!$this->correct) {
        echo "<select name='".$champ."' size='".$this->taille[$champ].
        "' class='champFormulaire' ";
        if($this->onchange[$champ]!="")
        echo "onchange=\"".$this->onchange[$champ]."\" ";
        if($this->onclick[$champ]!="")
        echo "onclick=\"".$this->onclick[$champ]."\" ";
        echo ">";
        }else
        echo "<select name='".$champ."' size='".$this->taille[$champ].
        "' class='champFormulaire' disabled=\"disabled\" >";
        $k=0;
        foreach($this->select[$champ] as $elem)
        //  $nOption++;
        while ($k <count($elem)) {
        if(!$this->correct) {
        if ($this->val[$champ]==$this->select[$champ][0][$k])
        echo "<option selected=\"selected\" value=\"".$this->select[$champ][0][$k].
        "\">".$this->select[$champ][1][$k]."</option>";
        else
        echo "<option value=\"".$this->select[$champ][0][$k].
        "\">".$this->select[$champ][1][$k]."</option>";
        $k++;

        }else{
        if ($this->val[$champ]==$this->select[$champ][0][$k]){
        echo "<option selected=\"selected\" value=\"".$this->select[$champ][0][$k].
        "\" >".$this->select[$champ][1][$k]."</option>";
        $k =count($elem);
        }
        $k++;
        }
        }
        echo "</select>";

    }

    /**
     * WIDGET_FORM - selectlistemulti.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selectlistemulti($champ, $validation, $DEBUG = false) {

        // ***************************************************************************
        // SELECTLISTEMULTI (liste)
        // affichage de table
        //select['nomduchamp'][0]= value de l option
        //select['nomduchamp'][1]= affichage
        //select['nomduchamp'][2]= autre select dont la value peut etre ajoutee
        //select['nomduchamp'][3]= champ cache des values ajoutees ex: 45,12,32
        // ****************************************************************************
        // colones = taille
        // lignes = max
        echo "<table><tr><td>";
        if(!$this->correct) {
        $champ2=$this->select[$champ][2];
        $champ3=$this->select[$champ][3];
        echo "<table border=1 ><tr><td>";
        echo "<input type='button' name='_select$champ' onclick='addlist(\"$champ\",\"$champ2\",\"$champ3\")' value='->' class='boutonmulti'> ";
        echo "</td></tr><tr><td>";
        echo "<input type='button' name='_unselect$champ' onclick='removelist(\"$champ\",\"$champ3\")' value='<-' class='boutonmulti'> ";
        echo "</td></tr><tr><td>";
        echo "<input type='button' name='_unselectall$champ' onclick='removealllist(\"$champ\",\"$champ3\")' value='<<' class='boutonmulti'> ";
        echo "</td></tr></table></td><td>";
        echo "<select name='".$champ."' size='".$this->taille[$champ].
        "' class='champFormulaire' ";
        if($this->onchange[$champ]!="")
        echo "onchange=\"".$this->onchange[$champ]."\" ";
        if($this->onclick[$champ]!="")
        echo "onclick=\"".$this->onclick[$champ]."\" ";
        echo ">";
        }else
        echo "<select name='".$champ."' size='".$this->taille[$champ].
        "' class='champFormulaire' disabled=\"disabled\" >";
        $k=0;
        foreach($this->select[$champ] as $elem)
        //  $nOption++;
        while ($k <count($elem)) {
        if(!$this->correct) {
        if ($this->val[$champ]==$this->select[$champ][0][$k])
        echo "<option selected=\"selected\" value=\"".$this->select[$champ][0][$k].
        "\">".$this->select[$champ][1][$k]."</option>";
        else
        echo "<option value=\"".$this->select[$champ][0][$k].
        "\">".$this->select[$champ][1][$k]."</option>";
        $k++;
        }else{
        if ($this->val[$champ]==$this->select[$champ][0][$k]){
        echo "<option selected=\"selected\" value=\"".$this->select[$champ][0][$k].
        "\" >".$this->select[$champ][1][$k]."</option>";
        $k =count($elem);
        }
        $k++;
        }
        }
        echo "</select>";
        echo "</td></tr></table>";

    }

    /**
     * WIDGET_FORM - selectstatic.
     *
     * Affichage d'un champ lie avec:
     *
     * - libelle statique
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function selectstatic($champ, $validation, $DEBUG = false) {

        // recherche du libelle associe a la valeur du champ
        $key = array_search($this->val[$champ], $this->select[$champ][0]);

        // affichage du libelle
        echo '<span id="'.$champ.'" class="field_value">';
        if ($key !== false) {
            echo $this->select[$champ][1][$key];
        } else {
            echo $this->val[$champ];
        }
        echo '</span>';
    }


    /**
     * WIDGET_FORM - select_multiple.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function select_multiple($champ, $validation, $DEBUG = false) {

        // ***************************************************************************
        // SELECT_MULTIPLE
        //select['nomduchamp'][0]= value de l option
        //select['nomduchamp'][1]= affichage
        // ****************************************************************************
        // Delinearisation
        $selected_values = explode(";", $this->val[$champ]);
        //
        echo "<select";
        echo " name=\"".$champ."[]\"";
        echo " id=\"".$champ."\" ";
        echo " multiple=\"multiple\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " class=\"champFormulaire selectmultiple\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo ">\n";
        //
        $k = 0;
        foreach ($this->select[$champ] as $elem) {
            while ($k <count($elem)) {
                echo "    <option ";
                echo " value=\"".$this->select[$champ][0][$k]."\"";
                if (in_array($this->select[$champ][0][$k], $selected_values)) {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo $this->select[$champ][1][$k];
                echo "</option>\n";
                $k++;
            }
        }
        //
        echo "</select>\n";
    }

    /**
     * WIDGET_FORM - select_multiple_static.
     *
     * Ce widget permet d'afficher une liste statique (html) des valeurs 
     * d'un champ. Cette liste de valeurs provient de la combinaison entre les 
     * valeurs et libellés disponibles dans le paramétrage select de ce champ
     * et entre les valeurs du champ représentées de manière linéaire. 
     * 
     * Deux contraintes sont présentes ici : 
     *  - $this->val[$champ] correspond aux valeurs sélectionnées. Le format 
     *    attendu ici dans la valeur du champ est une chaine de caractère 
     *    représentant la liste des valeurs sélectionnées séparées par des ; 
     *    (points virgules). 
     *    Exemple : $this->val[$champ] = string(5) "4;2;3";
     *  - $this->select[$champ] correspond aux libellés de toutes les valeurs 
     *    disponibles dans cette liste lors de la modification de l'élément.
     *    Exemple : $this->select[$champ] = array(2) {
     *         [0] => array(3) {
     *           [0] => string(1) "2"
     *           [1] => string(1) "3"
     *           [2] => string(1) "4"
     *         }
     *         [1] => array(3) {
     *           [0] => string(5) "Plans"
     *           [1] => string(7) "Visites"
     *           [2] => string(18) "Dossiers à enjeux"
     *         }
     *       }
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function select_multiple_static($champ, $validation, $DEBUG = false) {
        // Si aucune valeur n'est sélectionnée alors on affiche rien
        if ($this->val[$champ] == "") {
            return;
        }
        // On transforme la chaine de caractère en tableau grâce au
        // séparateur ;
        $selected_values = explode(";", $this->val[$champ]);
        // On affiche la liste
        echo "<ul>";
        // On boucle sur la liste de valeurs sélectionnées
        foreach ($selected_values as $value) {
            //
            echo "<li>";
            // On affiche le libellé correspondant à la valeur
            echo $this->select[$champ][1][array_search($value, $this->select[$champ][0])];
            //
            echo "</li>";
        }
        //
        echo "</ul>";
    }

    /**
     * WIDGET_FORM - statiq.
     *
     * La valeur du champ n'est pas conservee
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function statiq($champ, $validation, $DEBUG = false) {
        echo "<span class=\"field_value\" id=\"".$champ."\">";
        echo $this->val[$champ]."\n";
        echo "</span>";
    }

    /**
     * WIDGET_FORM - text.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function text($champ, $validation, $DEBUG = false) {
        $text_onchange="";
        $text_onkeyup="";
        $text_onclick="";
        $text_disabled="";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                $text_onchange=" onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                $text_onkeyup= " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                $text_onclick= " onclick=\"".$this->onclick[$champ]."\"";
            }
        } 
       $params = array(
        "type" => $this->type[$champ],
        "name" => $champ,
        "id" => $champ,
        "value" => $this->val[$champ],
        "size" => $this->taille[$champ],
        "maxlength" => $this->max[$champ],
        "correct" => $this->correct,
        "onchange" =>$text_onchange,
        "onkeyup" =>  $text_onkeyup,
        "onclick" => $text_onclick
        );
        $this->f->layout->display_formulaire_text($params);
        //
        
      /*echo "<input";
        echo " type=\"".$this->type[$champ]."\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
       //
        echo " />\n";*/
    }

    /**
     * WIDGET_FORM - textarea.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textarea($champ, $validation, $DEBUG = false) {

        //
        echo "<textarea";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " cols=\"".$this->taille[$champ]."\"";
        echo " rows=\"".$this->max[$champ]."\"";
        if(!isset($this->select[$champ]['class'])) {
            $this->select[$champ]['class'] = "";
        }
        echo " class=\"champFormulaire ".$this->select[$champ]['class']."\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo ">\n";
        echo $this->val[$champ];
        echo "</textarea>\n";

    }

    /**
     * WIDGET_FORM - textareahiddenstatic.
     *
     * La valeur du champ n est pas passe, affichage du champ en texte
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textareahiddenstatic($champ, $validation, $DEBUG = false) {

        echo "<input type='hidden' ";
        echo "name='".$champ."' ";
        echo "id=\"".$champ."\" ";
        echo "value=\"".$this->val[$champ]."\" ";
        echo "class='champFormulaire' >\n";
        $this->val[$champ] = str_replace("\n","<br>",$this->val[$champ]);
        echo $this->val[$champ]."\n";

    }

    /**
     * WIDGET_FORM - textareamulti.
     *
     * Recuperation d une valeur dans un champ
     * - le champ d origine = $this->select[$champ][0]
     * - le champ d arrive = $champPage HTML : les \n => <br> en affichage
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textareamulti($champ, $validation, $DEBUG = false) {

        if (!$this->correct) {
            //
            echo "<input";
            echo " type=\"button\"";
            echo " onclick=\"selectauto('".$champ."','".$this->select[$champ][0]."')\"";
            echo " value=\"->\" ";
            echo " class=\"boutonmulti\"";
            echo " />\n";
        }
        //
        echo "<textarea";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " cols=\"".$this->taille[$champ]."\"";
        echo " rows=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire champmulti\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo ">\n";
        echo $this->val[$champ];
        echo "</textarea>\n";

    }

    /**
     * WIDGET_FORM - textareastatic.
     *
     * Affichage du contenu d'un champ TEXT en conservant les retours a la ligne
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textareastatic($champ, $validation, $DEBUG = false) {
        echo "<span class=\"field_value pre\" id=\"".$champ."\">";
        echo $this->val[$champ];
        echo "</span>";
    }

    /**
     * WIDGET_FORM - textdisabled.
     *
     * pas de passage de parametre
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textdisabled($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo " disabled=\"disabled\"";
        echo " />\n";

    }

    /**
     * WIDGET_FORM - textreadonly.
     *
     * champ texte non modifiable - pas de passage de parametre
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function textreadonly($champ, $validation, $DEBUG = false) {

        //
        echo "<input";
        echo " type=\"text\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        echo " readonly=\"readonly\"";
        echo " />\n";

    }

    /**
     * WIDGET_FORM - upload.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function upload($champ, $validation, $DEBUG = false) {
        // Si le storage n'est pas configuré, alors on affiche un message
        // d'erreur clair pour l'utilisateur
        if ($this->f->storage == NULL) {
            // Message d'erreur
            echo "<div id=\"".$champ."\">";
            echo _("Le syteme de stockage n'est pas accessible. Erreur de ".
                   "parametrage. Contactez votre administrateur.");
            echo "</div>";
            // On sort de la méthode
            return -1;
        }

        // Explode de la valeur afin de vérifier si l'uid est temporaire
        $temporary_test = explode("|", $this->val[$champ]);
        //
        echo "<input";
        echo " type=\"hidden\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";
        $text_onchange="";
        $text_onkeyup="";
        $text_onclick="";
        $text_disabled="";
        $text_value="";
        // Test si une valeur est présente
        if (isset($this->val[$champ]) AND !empty($this->val[$champ])) {
            // Test si la valeur contient "tmp"
            if (isset($temporary_test[0]) AND $temporary_test[0] == "tmp") {
                // Si la valeur du champ contient effectivement un uid
                if (isset($temporary_test[1])) {
                    $text_value=" value=\"".$this->f->storage->getFilename_temporary($temporary_test[1])."\" ";
                }
            } else {
                // Et si le formulaire à déjà été validé pour afficher le nom du fichier
                $text_value=" value=\"".$this->f->storage->getFilename($this->val[$champ])."\" ";
            }
        } else {
            $text_value=" value=\"\" ";
        }
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                $text_onchange=" onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                $text_onkeyup= " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                $text_onclick= " onclick=\"".$this->onclick[$champ]."\"";
            }
        } 
        $params = array(
        "name" => $champ,
        "id" => $champ,
        "correct" => $this->correct,
        "onchange" =>$text_onchange,
        "onkeyup" =>  $text_onkeyup,
        "onclick" => $text_onclick,
        "value" => $text_value
        );
        $this->f->layout->display_formulaire_champs_upload($params);
       
        /*echo "<input type=\"text\"";
        echo " name=\"".$champ."_upload\"";
        echo " id=\"".$champ."_upload\" ";
        if (isset($this->val[$champ]) AND !empty($this->val[$champ])) {
            echo " value=\"".$this->f->storage->getFilename($this->val[$champ])."\" ";
        } else {
            echo " value=\"\" ";
        }
        
        echo " class=\"champFormulaire upload\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";*/

        if (!$this->correct) {
            //
            $this->f->layout->display_formulaire_lien_vupload_upload($champ, 
                                                                        $this->getParameter("obj"),
                                                                        $this->getParameter("idx"));
            // On cache les bouton voir et supprimer si pas de valeur
            $this->f->layout->display_formulaire_lien_voir_upload($champ,
                                                                     $this->getParameter("obj"),
                                                                     $this->getParameter("idx")
                                                                    );
            // Bouton permettant de vider le champ de formulaire
            $this->f->layout->display_formulaire_lien_supprimer_upload($champ);

       }
    }

    /**
     * WIDGET_FORM - upload2.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function upload2($champ, $validation, $DEBUG = false) {
        // Si le storage n'est pas configuré, alors on affiche un message
        // d'erreur clair pour l'utilisateur
        if ($this->f->storage == NULL) {
            // Message d'erreur
            echo "<div id=\"".$champ."\">";
            echo _("Le syteme de stockage n'est pas accessible. Erreur de ".
                   "parametrage. Contactez votre administrateur.");
            echo "</div>";

            // On sort de la méthode
            return -1;
        }
        // Explode de la valeur afin de vérifier si l'uid est temporaire
        $temporary_test = explode("|", $this->val[$champ]);
        //
        //   Gestion des contraintes   
        //
        $contraintes = "'',''";
        if ( isset($this->select[$champ]) && is_array($this->select[$champ]) && 
            isset($this->select[$champ]['constraint']) && 
            is_array($this->select[$champ]['constraint'])){
            
            $contraintes = (isset($this->select[$champ]['constraint']['size_max']))? 
                "'".$this->select[$champ]['constraint']['size_max']."',": "'', ";
            $contraintes .= (isset($this->select[$champ]['constraint']['extension']))? 
                "'".$this->select[$champ]['constraint']['extension']."'": "''";
        }
        
        //
        echo "<input";
        echo " type=\"hidden\"";
        echo " name=\"".$champ."\"";
        echo " id=\"".$champ."\" ";
        echo " value=\"".$this->val[$champ]."\"";
        echo " size=\"".$this->taille[$champ]."\"";
        echo " maxlength=\"".$this->max[$champ]."\"";
        echo " class=\"champFormulaire\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";
        
        echo "<input type=\"text\"";
        echo " name=\"".$champ."_upload\"";
        echo " id=\"".$champ."_upload\" ";
        // Test si une valeur est présente
        if (isset($this->val[$champ]) AND !empty($this->val[$champ])) {
            // Test si la valeur contient "tmp"
            if (isset($temporary_test[0]) AND $temporary_test[0] == "tmp") {
                // Si la valeur du champ contient effectivement un uid
                if (isset($temporary_test[1])) {
                    echo " value=\"".$this->f->storage->getFilename_temporary($temporary_test[1])."\" ";
                }
            } else {
                // Et si le formulaire à déjà été validé pour afficher le nom du fichier
                echo " value=\"".$this->f->storage->getFilename($this->val[$champ])."\" ";
            }
        } else {
            echo " value=\"\" ";
        }
        
        echo " class=\"champFormulaire upload\"";
        if (!$this->correct) {
            if (isset($this->onchange) and $this->onchange[$champ] != "") {
                echo " onchange=\"".$this->onchange[$champ]."\"";
            }
            if (isset($this->onkeyup) and $this->onkeyup[$champ] != "") {
                echo " onkeyup=\"".$this->onkeyup[$champ]."\"";
            }
            if (isset($this->onclick) and $this->onclick[$champ] != "") {
                echo " onclick=\"".$this->onclick[$champ]."\"";
            }
        } else {
            echo " disabled=\"disabled\"";
        }
        echo " />\n";

        //
        if (!$this->correct) {
            //
            echo "<a class=\"upload ui-state-default ui-corner-all\" href=\"javascript:vupload2('".$champ."',".$contraintes.");\">";
            echo "<span class=\"ui-icon ui-icon-arrowthickstop-1-s\" ";
            echo "title=\""._("Cliquer ici pour telecharger un fichier depuis votre poste de travail")."\">";
            echo _("Telecharger");
            echo "</span>";
            echo "</a>\n";

            echo "<a class=\"voir ui-state-default ui-corner-all\" href=\"javascript:voir2('".$champ."', '".$this->getParameter("obj")."',
                                                                 '".$this->getParameter("idx")."');\">\n";
            echo "<span class=\"ui-icon ui-icon-newwin\" ";
            echo "title=\""._("Cliquer ici pour voir le fichier")."\">";
            echo _("Voir");
            echo "</span>";
            echo "</a>\n";
            // Bouton permettant de vider le champ de formulaire
            echo "<a class=\"voir ui-state-default ui-corner-all\" href=\"javascript:supprimerUpload2('".$champ."');\">\n";
            echo "<span class=\"ui-icon ui-icon-closethick\" ";
            echo "title=\""._("Cliquer ici pour supprimer le fichier")."\">";
            echo _("Supprimer");
            echo "</span>";
            echo "</a>\n";
        }

    }

    /**
     * WIDGET_FORM - voir.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function voir($champ, $validation, $DEBUG = false) {

        if (!$this->correct) {
            //
            echo "<input type='text' ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo "size=".$this->taille[$champ]." ";
            echo "maxlength=".$this->max[$champ]." ";
            echo "onchange=\"".$this->onchange[$champ]."\" ";
            echo "class='champFormulaire voir' ";
            echo ">";
            //
            echo "<a class=\"voir ui-state-default ui-corner-all\" href=\"javascript:voir('".$champ."',
                                                                                          '".$this->getParameter("obj")."',
                                                                                          '".$this->getParameter("idx")."');\">\n";
            echo "<span class=\"ui-icon ui-icon-newwin\" ";
            echo "title=\""._("Cliquer ici pour voir le fichier")."\">";
            echo _("Voir");
            echo "</span>";
            echo "</a>\n";
        } else {
            //
            echo "<input type='text' ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo "size=".$this->taille[$champ]." ";
            echo "maxlength=".$this->max[$champ]." ";
            echo "class='champFormulaire voir' ";
            echo "disabled=\"disabled\" ";
            echo ">\n";
        }

    }

    /**
     * WIDGET_FORM - voir2.
     *
     * @param string $champ Nom du champ
     * @param integer $validation
     * @param boolean $DEBUG Parametre inutilise
     *
     * @return void
     */
    function voir2($champ, $validation, $DEBUG = false) {

        if (!$this->correct) {
            //
            echo "<input type='hidden' ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "value=\"".$this->val[$champ]."\" ";
            echo "class='champFormulaire voir' ";
            echo ">\n";
            //
            echo "<p align='left'>";
            echo $this->val[$champ];
            echo " </p>\n";
            //
            echo "<a class=\"ui-state-default ui-corner-all\" href=\"javascript:voir2('".$champ."',
                                                                                          '".$this->getParameter("obj")."',
                                                                                          '".$this->getParameter("idx")."');\">\n";
            echo "<span class=\"voir ui-icon ui-icon-newwin\" ";
            echo "title=\""._("Cliquer ici pour voir le fichier")."\">";
            echo _("Voir");
            echo "</span>";
            echo "</a>\n";
        } else {
            //
            echo "<input type='text' ";
            echo "name='".$champ."' ";
            echo " id=\"".$champ."\" ";
            echo "value='".$this->val[$champ]."' ";
            echo "size=".$this->taille[$champ]." ";
            echo "maxlength=".$this->max[$champ]." ";
            echo "class='champFormulaire voir' ";
            echo "disabled=\"disabled\" ";
            echo ">\n";
        }

    }

    // }}} WIDGET_FORM - END

}

?>
