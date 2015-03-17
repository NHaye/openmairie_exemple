<?php
/**
 * Ce fichier permet de declarer la classe dbForm.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_dbform.class.php 3095 2015-03-12 17:29:34Z vpihour $
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
 *
 */
require_once PATH_OPENMAIRIE."om_formulaire.class.php";

/**
 * Cette classe permet de gerer une interface entre un objet metier et sa
 * representation dans la base de donnees.
 *
 * @abstract
 */
class dbForm {

    /**
     * Nom de la classe formulaire
     */
    var $om_formulaire = "formulaire";

    /**
     *
     * @var array Informations DB nom de chaque champ
     */
    var $champs = array();

    /**
     *
     * @var array Informations DB type de chaque champ
     */
    var $type = array();

    /**
     *
     * @var array Informations DB taille de chaque champ
     */
    var $longueurMax = array();

    /**
     *
     * @var array ??? Informations DB flag de chaque champ
     */
    var $flags = array();

    /**
     *
     * @var array Valeur des champs requete selection
     */
    var $val = array();

    /**
     *
     * @var array Valeur des champs retournes pour saisie et maj
     */
    var $valF = array();

    /**
     *
     * @var string Message retourne au formulaire de saisie
     */
    var $msg = "";

    /**
     *
     * @var boolean Flag pour validation des donnees
     */
    var $correct;

    /**
     * @deprecated
     */
    var $selectioncol = "";

    /**
     * @deprecated
     */
    var $recherche = "";

    /**
     *
     * @var object Objet formulaire
     */
    var $form = NULL;

    /**
     *
     * @var object Objet de connexion DB
     */
    var $db = NULL;

    /**
     *
     * @var array Valeurs de tous les parametres
     */
    var $parameters = array();

    /**
     *
     * @var object Objet instance de utils
     */
    var $f = NULL;

    /**
     * Actions du portlet supplementaires provenant des fichiers .form.inc.php
     */
    var $actions_sup = array();

    /**
     * Actions par defaut dans openMairie
     * @var array
     */
    var $class_actions = array();

    /**
     * Liste des champs uniques
     */
    var $unique_key = array();

    /**
     * Liste des champs not null
     */
    var $required_field = array();

    /**
     * Marqueur permettant de déterminer si l'action sur laquelle on se trouve
     * est disponible sur l'objet instancié et dans le contexte.
     */
    var $_is_action_available = null;

    /**
     * Liste des métadonnées communes à l'ensemble des fichiers de l'application
     * @var array
     */
    var $metadata_global = array();

    /**
     * Ce tableau récupère les messages d'erreurs
     * @var array Valeurs de toutes les erreurs
     */
    var $errors = array();

    /**
     * Tableau permettant de stocker les fichiers en cours de modification
     * dans le cas ou la suite de la transaction ne se déroule pas bien.
     * @var array au format retourné pas le storage
     */
    var $tmpFile = array();

    /**
     * Flag permettant de définir si on setrouve en sousformulaire.
     * @var boolean
     */
    var $sousform;

    /**
     * Attribut permettant de stocker le paramètre du retourformulaire
     * (objet lié du formulaire principal appelé également contexte) uniquement
     * valable dans le cas d'un sous formulaire
     * @var mixed
     */
    var $retourformulaire;

    /**
     * Liste des clés étrangères avec la liste des éventuelles surcharges
     * de leur classe.
     * $foreign_keys_extended = array(
     *    "<foreign_key1_table1>" => array("<classe_surcharge_1_de_table1>", ),
     *    "<foreign_key2_table2>" => array("<classe_surcharge_1_de_table2>", ),
     * );
     * @var mixed
     */
    var $foreign_keys_extended = array();

    /**
     * Constructeur.
     *
     * @param string $id
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function constructeur($id, &$dnu1 = null, $dnu2 = null) {

        //
        if (isset($GLOBALS["f"])) {
            $this->f = $GLOBALS["f"];
        }
        //
        $this->addToLog(__METHOD__."()", VERBOSE_MODE);

        // @deprecated A supprimer
        // Ce raccourci rend la réalité du code difficilement lisible, il est 
        // préférable de ne pas l'utiliser
        $this->db = $this->f->db;

        // Inclusion du fichier de parametre de la table pour recuperer les
        // trois parametres permettant de construire la requete de selection
        // $champs - clause select
        // $tableSelect - clause from
        // $selection - clause where
        // *** custom
        if (file_exists('../dyn/custom.inc.php')) {
            include '../dyn/custom.inc.php';
        }
        if(isset($custom['form'][$this->table])and file_exists($custom['form'][$this->table])){
            include $custom['form'][$this->table];
        }else{ // fin custom
            $fichier = "../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php";
            if (file_exists($fichier)) {
                include $fichier;
            } else {
                $fichier = "../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc";
                if (file_exists($fichier)) {
                    include $fichier;
                }
            }
        }

        // Si la nouvelle gestion des actions est activée on fusionne les actions
        // de base avec celles de la surcharges, sinon ancien fonctionnement
        if ($this->is_option_class_action_activated()===true) {
            // Appel de la méthode de définition des tableaux d'actions.
            $this->init_class_actions();
        } else {
            // XXX Ancienne gestion des actions
            // Sauvegarde des actions contextuelles supplementaires
            if (isset($portlet_actions)) {
                $this->actions_sup = $portlet_actions;
            }
        }

        // Concatenation des champs pour constitution de la clause select
        $listeChamp = "";
        foreach ($champs as $elem) {
            $listeChamp .= $elem.",";
        }
        // Suppresion de la derniere virgule
        $listeChamp = substr($listeChamp, 0, strlen($listeChamp)-1);
        // Initialisation de la variable selection
        if (!isset($selection)) {
            $selection = "";
        }
        // Concatenation de la requete de selection
        $sql = " select ".$listeChamp." from ".$tableSelect." ";
        // Si mode ajout
        if ($id == "]") {
            // Remplacement du 'and' par 'where' dans la varibale $selection
            $selection = ltrim($selection);
            if (strtolower(substr($selection, 0, 3)) == "and") {
                $selection = " where ".substr($selection, 4, strlen($selection));
            }
        } else { // Si mode modification ou suppression
            //
            $sql .= "where ".$this->getCle($id);
        }
        $sql .= " ".$selection." ";
        // Execution de la requete
        $res = $this->f->db->limitquery($sql, 0, 1);
        // Logger
        $this->addToLog(__METHOD__."(): db->limitquery(\"".str_replace(",",", ",$sql)."\", 0, 1);", VERBOSE_MODE);
        // Si une erreur survient
        if (database::isError($res, true)) {
            // Appel de la methode de recuperation des erreurs
            $this->erreur_db($res->getDebugInfo(), $res->getMessage(), $tableSelect);
        } else {
            // Recuperation des informations sur la structure de la table
            // ??? compatibilite POSTGRESQL (len = -1, type vide, flags vide)
            $info = $res->tableInfo();
            // Initialisation de la cle a 0
            $i = 0;
            // Recuperation du nom de chaque champ dans l'attribut 'champs'
            foreach ($info as $elem) {
                $this->champs[$i++] = $elem['name'];
            }
            $i = 0;
            // ??? Le $i devrait etre initialises a 0 pour chaque attribut suivant
            // Recuperation de la taille de chaque champ dans l'attibut 'longueurMax'
            foreach ($info as $elem) {
                $this->longueurMax[$i++] = $elem['len'];
            }
            $i = 0;
            // Recuperation du type de chaque champ dans l'attribut 'type'
            // ??? Non utilise
            foreach ($info as $elem) {
                $this->type[$i++] = $elem['type'];
            }
            $i = 0;
            // Recuperation du flag de chaque champ dans l'attribut 'flags'
            // ??? Non utilise
            foreach ($info as $elem) {
                $this->flags[$i++] = $elem['flags'];
            }
            // Recuperation de l'enregistrement resultat de la requete
            while ($row =& $res->fetchRow()) {
                // Initialisation de la cle a 0
                $i = 0;
                // Recuperation de la valeur de chaque champ dans l'attribut 'val'
                foreach ($row as $elem) {
                    $this->val[$i++] = $elem;
                }
            }
        }
    }
    
    function get_class_custom() {
        // $this->classe remplace get_class($this) en enlevant custom
        $classe=str_replace('_custom','', get_class($this));
        return $classe;
    }

    /**
     * TREATMENT - ajouter.
     * 
     * Cette methode permet d'executer l'ajout (MODE 'insert') de l'objet dans
     * la base de donnees.
     *
     * @param array $val
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     *
     * @return boolean
     */
    function ajouter($val = array(), &$dnu1 = null, $dnu2 = null) {
        // Logger
        $this->addToLog(__METHOD__."() - begin", EXTRA_VERBOSE_MODE);
        // Mutateur de valF
        $this->setValF($val);
        // Mutateur de valF specifique a l'ajout
        $this->setValFAjout($val);
        // Verification de la validite des donnees
        $this->verifier($val, $this->f->db, null);
        // Verification specifique au MODE 'insert' de la validite des donnees
        $this->verifierAjout($val, $this->f->db);
        // Verification du verrou
        $this->testverrou();
        // Si les verifications precedentes sont correctes, on procede a
        // l'ajout, sinon on ne fait rien et on affiche un message d'echec
        if ($this->correct) {
            // Appel au mutateur pour le calcul de la cle primaire (si la cle
            // est automatique) specifique au MODE 'insert'
            $this->setId($this->f->db);
            // Execution du trigger 'before' specifique au MODE 'insert'
            // Le premier parametre est vide car en MODE 'insert'
            // l'enregistrement n'existe pas encore donc il n'a pas
            // d'identifiant
            if($this->triggerajouter("", $this->f->db, $val, null) === false) {
                $this->correct = false;
                $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                return false;
            }
            //Traitement des fichiers uploadé
            $retTraitementFichier = $this->traitementFichierUploadAjoutModification();
            if($retTraitementFichier !== true) {
                $this->correct = false;
                $this->addToErrors("", $retTraitementFichier, $retTraitementFichier);
                return false;
            }
            // Execution de la requete d'insertion des donnees de l'attribut
            // valF de l'objet dans l'attribut table de l'objet
            $res = $this->f->db->autoExecute(DB_PREFIXE.$this->table, $this->valF, DB_AUTOQUERY_INSERT);
            // Logger
            $this->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE.$this->table."\", ".print_r($this->valF, true).", DB_AUTOQUERY_INSERT);", VERBOSE_MODE);
            // Si une erreur survient
            if (database::isError($res, true)) {
                // Appel de la methode de recuperation des erreurs
                $this->erreur_db($res->getDebugInfo(), $res->getMessage(), '');
                $this->correct = false;
                return false;
            } else {
                //
                $main_res_affected_rows = $this->f->db->affectedRows();
                // Log
                $this->addToLog(__METHOD__."(): "._("Requete executee"), VERBOSE_MODE);
                // Mise en place du verrou pour ne pas valider plusieurs fois
                // le meme formulaire
                $this->verrouille();
                // Execution du trigger 'after' specifique au MODE 'insert'
                // Le premier parametre est vide car en MODE 'insert'
                // l'enregistrement n'existe pas encore donc il n'a pas
                // d'identifiant
                if($this->triggerajouterapres($this->valF[$this->clePrimaire], $this->f->db, $val, null) === false) {
                    $this->correct = false;
                    $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                    return false;
                }
                $message = _("Enregistrement")."&nbsp;".$this->valF[$this->clePrimaire]."&nbsp;";
                $message .= _("de la table")."&nbsp;\"".$this->table."\"&nbsp;";
                $message .= "[&nbsp;".$main_res_affected_rows."&nbsp;";
                $message .= _("enregistrement(s) ajoute(s)")."&nbsp;]";
                $this->addToLog(__METHOD__."(): ".$message, VERBOSE_MODE);
                // Message de validation
                $this->addToMessage(_("Vos modifications ont bien ete enregistrees.")."<br/>");
            }
        } else {
            // Message d'echec (saut d'une ligne supplementaire avant le
            // message pour qu'il soit mis en evidence)
            $this->addToMessage("<br/>"._("SAISIE NON ENREGISTREE")."<br/>");
            $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
            return false;
        }
        // Logger
        $this->addToLog(__METHOD__."() - end", EXTRA_VERBOSE_MODE);

        return true;
    }

    /**
     *
     * @param array $val
     */
    function setValFAjout($val = array()) {

        // initialisation valF pour la cle primaire (si pas de cle automatique)
        // [value primary key to database - not automatic primary key]
        $this->valF[$this->clePrimaire] = trim($val[$this->clePrimaire]);

    }

    /**
     *
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     */
    function setId(&$dnu1 = null) {

        // initialisation valF pour la cle primaire (si  cle automatique)
        // [value primary key to database - automatic primary key]
        // id automatique method nextid
        // automatic id with dbpear method nextid

    }

    /**
     *
     * @param array $val
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     */
    function verifierAjout($val = array(), &$dnu1 = null) {

        // Verifier [verify]
        // la cle primaire est obligatoire
        // [primary key is compulsory]
        if ($this->valF[$this->clePrimaire] == "") {
            //
            $this->correct = false;
            //
            $this->addToMessage("<br/>");
            $this->addToMessage( _("L'\"identifiant\" est obligatoire")."&nbsp;");
            $this->addToMessage("[&nbsp;"._($this->clePrimaire)."&nbsp;]");
        }
        if ($this->typeCle == "A") {
            $sql = "select count(*) from ".DB_PREFIXE.$this->table." ";    
            $sql .= "where ".$this->clePrimaire."='".$this->valF[$this->clePrimaire]."' ";
            // Exécution de la requête
            $nb = $this->f->db->getone($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($nb);
            //
            if ($nb > 0) {
                $this->correct = false;
                $this->addToMessage($nb." ");
                $this->addToMessage( _("cle primaire existante"));
                $this->addToMessage(" ".$this->table."<br />");
            }
        }
    }

    /**
     * TREATMENT - modifier.
     * 
     * Cette methode permet d'executer la modification (MODE 'update') de
     * l'objet dans la base de donnees.
     *
     * @param array $val
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     *
     * @return boolean
     */
    function modifier($val = array(), &$dnu1 = null, $dnu2 = null) {
        // Logger
        $this->addToLog(__METHOD__."() - begin", EXTRA_VERBOSE_MODE);
        // Recuperation de la valeur de la cle primaire de l'objet
        if(isset($val[$this->clePrimaire])) {// ***
            $id = $val[$this->clePrimaire];
        } elseif(isset($this->valF[$this->clePrimaire])) {// ***
            $id = $this->valF[$this->clePrimaire];
        } else {
            $id=$this->id;
        }
        // Appel au mutateur de l'attribut valF de l'objet
        $this->setValF($val);
        // Verification de la validite des donnees
        $this->verifier($val, $this->f->db, null);
        // Verification du verrou
        $this->testverrou();
        // Si les verifications precedentes sont correctes, on procede a
        // la modification, sinon on ne fait rien et on affiche un message
        // d'echec
        if ($this->correct) {
            // Execution du trigger 'before' specifique au MODE 'update'
            if($this->triggermodifier($id, $this->f->db, $val, null) === false) {
                $this->correct = false;
                $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                return false;
            }
            //Traitement des fichiers uploadé
            $retTraitementFichier = $this->traitementFichierUploadAjoutModification();
            if($retTraitementFichier !== true) {
                $this->correct = false;
                $this->addToErrors("", $retTraitementFichier, $retTraitementFichier);
                return false;
            }
            // Execution de la requête de modification des donnees de l'attribut
            // valF de l'objet dans l'attribut table de l'objet
            $res = $this->f->db->autoExecute(DB_PREFIXE.$this->table, $this->valF, DB_AUTOQUERY_UPDATE, $this->getCle($id));
            // Logger
            $this->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE.$this->table."\", ".print_r($this->valF, true).", DB_AUTOQUERY_UPDATE, \"".$this->getCle($id)."\")", VERBOSE_MODE);
            // Si une erreur survient
            if (database::isError($res, true)) {
                // Appel de la methode de recuperation des erreurs
                $this->erreur_db($res->getDebugInfo(), $res->getMessage(), '');
                $this->correct = false;
                return false;
            } else {
                //
                $main_res_affected_rows = $this->f->db->affectedRows();
                // Mise en place du verrou pour ne pas valider plusieurs fois
                // le meme formulaire
                $this->verrouille();
                // Execution du trigger 'after' specifique au MODE 'update'
                if($this->triggermodifierapres($id, $this->f->db, $val, null) === false) {
                    $this->correct = false;
                    $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                    return false;
                }
                $retTraitementFichier = $this->traitementFichierUploadSuppression();
                if($retTraitementFichier !== true) {
                    $this->correct = false;
                    $this->addToErrors("", $retTraitementFichier, $retTraitementFichier);
                    return false;
                }
                // Log
                $this->addToLog(__METHOD__."(): "._("Requete executee"), VERBOSE_MODE);
                
                // Log
                $message = _("Enregistrement")."&nbsp;".$id."&nbsp;";
                $message .= _("de la table")."&nbsp;\"".$this->table."\"&nbsp;";
                $message .= "[&nbsp;".$main_res_affected_rows."&nbsp;";
                $message .= _("enregistrement(s) mis a jour")."&nbsp;]";
                $this->addToLog(__METHOD__."(): ".$message, VERBOSE_MODE);
                // Message de validation
                if ($main_res_affected_rows == 0) {
                    $this->addToMessage(_("Attention vous n'avez fait aucune modification.")."<br/>");
                } else {
                    $this->addToMessage(_("Vos modifications ont bien ete enregistrees.")."<br/>");
                }
            }
        } else {
            // Message d'echec (saut d'une ligne supplementaire avant le
            // message pour qu'il soit mis en evidence)
            $this->addToMessage("<br/>"._("SAISIE NON ENREGISTREE")."<br/>");
            $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
            return false;
        }
        // Logger
        $this->addToLog(__METHOD__."() - end", EXTRA_VERBOSE_MODE);

        return true;
    }

    /**
     * TREATMENT - supprimer.
     * 
     * Cette methode permet d'executer la suppression (MODE 'delete') de
     * l'objet dans la base de donnees.
     *
     * @param array $val
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     *
     * @return boolean
     */
    function supprimer($val = array(), &$dnu1 = null, $dnu2 = null) {
        // Logger
        $this->addToLog(__METHOD__."() - begin", EXTRA_VERBOSE_MODE);
        // Recuperation de la valeur de la cle primaire de l'objet
        if(isset($val[$this->clePrimaire])) {// ***
            $id = $val[$this->clePrimaire];
        } elseif(isset($this->valF[$this->clePrimaire])) {// ***
            $id = $this->valF[$this->clePrimaire];
        } else {
            $id=$this->id;
        }
        // Verification des contraintes d'integrite specifique au MODE 'delete'
        $this->correct=true;
        $this->cleSecondaire($id, $this->f->db, $val, null);
        // Verification du verrou
        $this->testverrou();
        // Si les verifications precedentes sont correctes, on procede a
        // la suppression, sinon on ne fait rien et on affiche un message
        // d'echec
        if ($this->correct) {
            // Execution du trigger 'before' specifique au MODE 'delete'
            if($this->triggersupprimer($id, $this->f->db, $val, null) === false) {
                $this->correct = false;
                $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                return false;
            }
            // Construction de la requete de suppression de l'objet dans
            // l'attribut table de l'objet
            $sql = "delete from ".DB_PREFIXE.$this->table." where ".$this->getCle($id);
            // Execution de la requete de suppression de l'objet
            $res = $this->f->db->query($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // Si une erreur survient
            if (database::isError($res)) {
                // Appel de la methode de recuperation des erreurs
                $this->erreur_db($res->getDebugInfo(), $res->getMessage(), '');
                $this->correct = false;
                return false;
            } else {
                //
                $main_res_affected_rows = $this->f->db->affectedRows();
                // Mise en place du verrou pour ne pas valider plusieurs fois
                // le meme formulaire
                $this->verrouille();
                // Execution du trigger 'after' specifique au MODE 'delete'
                if($this->triggersupprimerapres($id, $this->f->db, $val, null) === false) {
                    $this->correct = false;
                    $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
                    return false;
                }
                //Traitement des fichiers uploadé
                $retTraitementFichier = $this->traitementFichierUploadSuppression();
                if($retTraitementFichier !== true) {
                    $this->correct = false;
                    $this->addToErrors("", $retTraitementFichier, $retTraitementFichier);
                    return false;
                }
                // Log
                $message = _("Enregistrement")."&nbsp;".$id."&nbsp;";
                $message .= _("de la table")."&nbsp;\"".$this->table."\"&nbsp;";
                $message .= "[&nbsp;".$main_res_affected_rows."&nbsp;";
                $message .= _("enregistrement(s) supprime(s)")."&nbsp;]";
                $this->addToLog(__METHOD__."(): ".$message, VERBOSE_MODE);
                // Message de validation
                $this->addToMessage(_("La suppression a ete correctement effectuee.")."<br/>");
            }
        } else {
            // Message d'echec (saut d'une ligne supplementaire avant le
            // message pour qu'il soit mis en evidence)
            $this->addToMessage("<br/>"._("SUPPRESSION NON EFFECTUEE")."<br/>");
            $this->addToLog(__METHOD__."(): ERROR", DEBUG_MODE);
            return false;
        }
        // Logger
        $this->addToLog(__METHOD__."() - end", EXTRA_VERBOSE_MODE);
        return true;
    }

    /**
     *
     * @param array $val
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function verifier($val = array(), &$dnu1 = null, $dnu2 = null) {

        // Initialisation du marqueur d'erreur
        $this->correct = true;

        // Vérification des champs requis
        $this->checkRequired();

        // Si aucune erreur constatée, alors vérification des clés uniques
        if ($this->correct == true) {
            //
            $this->checkUniqueKey();
        }
    }

    /**
     * Cette methode est appelee lors de la suppression d'un objet, elle permet
     * d'effectuer des tests pour verifier si l'objet supprime n'est pas cle
     * secondaire dans une autre table pour en empecher la suppression.
     *
     * @param string $id Identifiant (cle primaire) de l'objet dans la base
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param array $val Tableau associatif representant les valeurs du
     *                   formulaire
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     *
     * @return void
     */
    function cleSecondaire($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        // Initialisation de l'attribut correct a true
        $this->correct = true;
    }

    /**
     * Methode de verification de l'unicite d'une valeur pour chaque elements du tableau unique_key,
     * ainsi que l'unicite de la cle multiple unique_multiple_key.
     */
    function checkUniqueKey() {
        $unique=true;
        //Verification des cles uniques
        if(!empty($this->unique_key)) {
            foreach ($this->unique_key as $constraint) {
                if(!is_array($constraint)) {
                    if(!is_null ($this->valF[$constraint])) {
                        if(!$this->isUnique($constraint,$this->valF[$constraint])) {
                            $this->addToMessage( _("La valeur saisie dans le champ")." <span class=\"bold\">".$this->getLibFromField($constraint)."</span> "._("existe deja, veuillez saisir une nouvelle valeur."));
                            $unique=false;
                        }
                    }
                } else {
                    //Verification du groupe de champs uniques
                    $oneIsNull=false;
                    if(!empty($constraint)) {
                        $valueMultiple=array();
                        foreach($constraint as $field) {
                            $valueMultiple[]=$this->valF[$field];
                            if(is_null($this->valF[$field])) {
                                $oneIsNull=true;
                            }
                        }
                        if(!$oneIsNull) {
                            if(!$this->isUnique($constraint,$valueMultiple)) {
                                foreach($constraint as $field) {
                                    $temp[]=$this->getLibFromField($field);
                                }
                                $this->addToMessage( _("Les valeurs saisies dans les champs")." <span class=\"bold\">".implode("</span>, <span class=\"bold\">",$temp)."</span> "._("existent deja, veuillez saisir de nouvelles valeurs."));
                                $unique=false;
                            }
                        }
                    }
                }
            }
        }
        if(!$unique) {
            $this->correct = false;
        }
    }

    /**
     * Methode permettant de requeter la base afin de definir la validite du champ unique
     *
     * @param string $champ nom du champ unique
     * @param string $value valeur à inserer dans la colonne
     */
    function isUnique($champ, $value) {
        //Test sur un groupe de champs
        if(is_array($champ) and is_array($value)) {
            $sql = 'SELECT count(*) FROM '.DB_PREFIXE.$this->table." WHERE ".implode(" = ? AND ",$champ)." = ?"; 
        } else {
        //Test sur un champ
            $sql = 'SELECT count(*) FROM '.DB_PREFIXE.$this->table." WHERE ".$champ." = ?";
        }
        if($this->getParameter('maj')) {
            $sql .= " AND ".$this->clePrimaire." \!= ".$this->valF[$this->clePrimaire];
        }
        // Exécution de la requête
        $nb = $this->f->db->getone($sql, $value);
        // Logger
        $this->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($nb);
        //Si superieur a 0, pas unique
        if ($nb > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Methode de verification des contraintes not null,
     * affiche une erreur si nul.
     */
    function checkRequired() {
        foreach($this->required_field as $field) {
            //Ne test la cle primaire car n'a pas de valeur a l'ajout

            // la cle primaire est automatiquement cree
            if ($field == $this->clePrimaire) {
                continue;
            }

            $error = false;

            /* En ajout - verification des requis

               Fonctionnement formel de la condition:

                SI le champ n'existe pas (est 'unset')
                OU le champ est vide

                ALORS le formulaire n'est pas correct

                SINON le formulaire est correct

              Explication:

                Les champs verifies sont les champs requis. S'ils n'existent
                pas en ajout ou qu'ils sont vide, un message apparait a l'ecran
                avertissant l'utilisateur que certains champs doivent etre
                remplis.

            */
            if ($this->getParameter('maj') == 0 
                && (!isset($this->valF[$field]) || $this->valF[$field] === '')) {

                $error = true;
                $this->correct = false;

            /* En modification - verification des requis

               Fonctionnement formel de la condition:

                SI le champ existe (est 'set')
                ET le champ est vide

                ALORS le formulaire n'est pas correct

                SINON le formulaire est correct

              Explication:

                Les champs verifies sont les champs requis. S'ils existent
                et qu'ils sont vides alors un message apparait a l'ecran
                avertissant l'utilisateur que certains champs doivent etre
                remplis. Si ces champs sont tous saisis, le formulaire est
                correctement soumis. Par contre, si l'un des champs requis
                n'existe pas au moment de verification (il aurait ete 'unset'),
                il ne sera pas verifie, n'entrainera pas de formulaire incorrect
                et ne sera pas insere dans la base de donnees.
                
                Faire un 'unset' permet de ne pas mettre a jour certaines
                donnees sensibles en base a chaque soumission de formulaire.
                
                Faire un 'unset' permet egalement d'outre passer cette condition
                en mode de modification. On suppose qu'a l'ajout une valeur
                a ete inseree dans un champ, et qu'il n'est plus necessaire
                de verifier si ce champ est vide puisque sa valeur ne sera
                pas modifiee en base. Elle sera donc conservee.

            */
            } elseif ($this->getParameter('maj') == 1
                      && isset($this->valF[$field])
                      && $this->valF[$field] === '') {

                $error = true;
                $this->correct = false;
            }

            // ajout du message d'erreur
            if ($error == true) {
                $this->addToMessage( _('Le champ').' <span class="bold">'.$this->getLibFromField($field).'</span> '._('est obligatoire'));
            }
        }
    }

    /**
     * Méthode permettant de retourner le nom d'un champ que le formulaire
     * soit instancié ou non
     * @param  string $champ nom du champ
     * @return string libellé
     */
    function getLibFromField($champ) {
        if(isset($this->form->lib[$champ]) AND $this->form->lib[$champ] != "") {
            return $this->form->lib[$champ];
        } else {
            return _($champ);
        }
    }

    /**
     *
     * @param array $val
     */
    function setvalF($val = array()) {

        // recuperation automatique [automatic recovery]
        foreach(array_keys($val) as $elem){
             $this->valF[$elem] =$val[$elem];
        }

    }
    
    /**
     *
     * @param string $id
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param array $val
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function triggerajouter($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        //
    }

    /**
     *
     * @param string $id
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param array $val
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function triggermodifier($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        //
    }

    /**
     *
     * @param string $id
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param array $val
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function triggersupprimer($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        //
    }

    /**
     *
     * @param string $id
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param array $val
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function triggerajouterapres($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        //
    }

    /**
     *
     * @param string $id
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param array $val
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function triggermodifierapres($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        //
    }

    /**
     *
     * @param string $id
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param array $val
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function triggersupprimerapres($id, &$dnu1 = null, $val = array(), $dnu2 = null) {
        //
    }

    // {{{ Gestion des parametres

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

    /**
     * Permet de récupérer la valeur d'un paramètre ou de sa surcharge.
     *
     * @param string $parameter Clé du paramètre.
     * @param mixed  $override  Tableau de paramètre permettant de surcharger 
     *                          certaines valeurs récupérées de manière standard
     *                          si ce n'est pas le cas.
     * 
     * @return mixed
     */
    function get_parameter_or_override($parameter = "", $override = array()) {
        //
        if (array_key_exists($parameter, $override)) {
            return $override[$parameter];
        } else {
            return $this->getParameter($parameter);
        }
    }

    /**
     * Permet de composer l'url vers les script 'formulaire' standards.
     *
     * @param string $case     Mode dans lequel l'url doit être construite.
     * @param mixed  $override Tableau de paramètre permettant de surcharger 
     *                         certaines valeurs récupérées de manière standard
     *                         si ce n'est pas le cas.
     *
     * @return string
     */
    function compose_form_url($case = "form", $override = array()) {
        //
        $out = "";
        //
        if ($case == "form") {

            //
            $out = "";
            $out .= "../scr/form.php";
            $out .= "?";
            $out .= "obj=".$this->get_class_custom();
            //
            $validation = $this->get_parameter_or_override("validation", $override);
            $out .= ($validation != null ? "&amp;validation=".$validation : "");
            //
            $idx = $this->get_parameter_or_override("idx", $override);
            if ($idx != "]") {
                //
                $maj = $this->get_parameter_or_override("maj", $override);
                $out .= ($maj != null ? "&amp;action=".$maj : "");
                //
                $out .= ($idx != null ? "&amp;idx=".$idx : "");
                //
                $idz = $this->get_parameter_or_override("idz", $override);
                $out .= ($idz != null ? "&amp;idz=".$idz : "");
            }
            //
            $premier = $this->get_parameter_or_override("premier", $override);
            $out .= ($premier != null ? "&amp;premier=".$premier : "");
            //
            $recherche = $this->get_parameter_or_override("recherche", $override);
            $out .= ($recherche != null ? "&amp;recherche=".$recherche : "");
            //
            $tricol = $this->get_parameter_or_override("tricol", $override);
            $out .= ($tricol != null ? "&amp;tricol=".$tricol : "");
            //
            $selectioncol = $this->get_parameter_or_override("selectioncol", $override);
            $out .= ($selectioncol != null ? "&amp;selectioncol=".$selectioncol : "");
            //
            $advs_id = $this->get_parameter_or_override("advs_id", $override);
            $out .= ($advs_id != null ? "&amp;advs_id=".$advs_id : "");
            //
            $valide = $this->get_parameter_or_override("valide", $override);
            $out .= ($valide != null ? "&amp;valide=".$valide : "");
            //
            $retour = $this->get_parameter_or_override("retour", $override);
            $out .= ($retour != null ? "&amp;retour=".$retour : "");

        } elseif ($case == "sousform") {

            //
            $out = "";
            $out .= "../scr/sousform.php";
            $out .= "?";
            $out .= "obj=".$this->get_class_custom();
            //
            $validation = $this->get_parameter_or_override("validation", $override);
            $out .= ($validation != null ? "&amp;validation=".$validation : "");
            //
            //
            $idx = $this->get_parameter_or_override("idx", $override);
            if ($idx != "]") {
                //
                $maj = $this->get_parameter_or_override("maj", $override);
                $out .= ($maj != null ? "&amp;action=".$maj : "");
                //
                $out .= ($idx != null ? "&amp;idx=".$idx : "");
            }
            //
            $premiersf = $this->get_parameter_or_override("premiersf", $override);
            $out .= ($premiersf != null ? "&amp;premiersf=".$premiersf : "");
            //
            $retourformulaire = $this->get_parameter_or_override("retourformulaire", $override);
            $out .= ($retourformulaire != null ? "&amp;retourformulaire=".$retourformulaire : "");
            //
            $trisf = $this->get_parameter_or_override("trisf", $override);
            $out .= ($trisf != null ? "&amp;trisf=".$trisf : "");
            //
            $idxformulaire = $this->get_parameter_or_override("idxformulaire", $override);
            $out .= ($idxformulaire != null ? "&amp;idxformulaire=".$idxformulaire : "");
            //
            $retour = $this->get_parameter_or_override("retour", $override);
            $out .= ($retour != null ? "&amp;retour=".$retour : "");

        }
        //
        return $out;
    }


    /**
     * Methode permettant aux objets metiers de surcharger facilement
     * la methode formulaire et de passer facilement des variables
     * supplementaires en parametre. Cette methode retourne une chaine
     * representant l'attribut action du formulaire.
     *
     * @return string Attribut action du form
     */
    function getDataSubmit() {
        //
        return $this->compose_form_url("form");
    }

    /**
     * Methode permettant aux objets metiers de surcharger facilement
     * la methode sousformulaire et de passer facilement des variables
     * supplementaires en parametre. Cette methode retourne une chaine
     * representant l'attribut action du formulaire.
     *
     * @return string Attribut action du form
     */
    function getDataSubmitSousForm() {
        //
        return $this->compose_form_url("sousform");
    }

    /**
     * Méthode permettant de calculer les métadonnées autres que celle définies
     * lors de l'upload
     *
     * @param string $champ champ sur lequel on récupère les métadonnées
     * @return array tableau contenant les métadonnées
     */
    function getMetadata($champ) {
        // Initialisation du tableau de retour
        $tab_retour = array();
        // Définition des métadonnées globales
        if(isset($this->metadata_global) AND !empty($this->metadata_global)) {
            // Pour chaque clé on récupère la valeur avec la méthode associée
            foreach ($this->metadata_global as $key => $methode) {
                if(method_exists($this, $methode)) {
                    $tab_retour[$key] = $this->$methode();
                }
            }
        }

        // Définition des métadonnées spécifiques à chaque champ
        if(isset($this->metadata[$champ]) AND !empty($this->metadata[$champ])) {
            // Pour chaque clé on récupère la valeur avec la méthode associée
            foreach ($this->metadata[$champ] as $key => $methode) {
                if(method_exists($this, $methode)) {
                    $tab_retour[$key] = $this->$methode();
                }
            }
        }
        return $tab_retour;
    }

    /**
     * Méthode de traitement de fichier uploadé : récupération du fichier temporaire,
     * pour l'ajout et la modification, la suppression se fait dans un 2nd temps.
     * 
     * @return string/boolean retourne true ou un message d'erreur
     */
    function traitementFichierUploadAjoutModification() {

        $type_list = array();
        // Récupération du tableau abstract_type si il existe sinon on utilise
        // les type de champs définis dans le formulaire
        if (isset($this->abstract_type)) {
            $type_list = $this->abstract_type;
        } elseif (isset($this->form->type)) {
            $type_list = $this->form->type;
        }
        // Pour chaque champs configurés avec les widgets upload, upload2 ou filestatic
        // ou chaque champs de type abstrait file défini dans le tableau abstract_type
        foreach ($type_list as $champ => $type) {
            //
            if ($type == "upload" OR $type == "upload2" OR $type == "filestatic"
                OR (isset($this->abstract_type) AND $type == "file")) {

                // Message d'erreur
                $msg = "";

                // Cas d'un ajout de fichier
                // Condition : si la valeur existante en base est vide ou que
                // nous sommes en mode 'AJOUT' ET qu'une valeur est postée pour
                // le champ fichier
                if (($this->getVal($champ) == ""
                     OR $this->getParameter("maj") == 0)
                    AND isset($this->valF[$champ])
                    AND $this->valF[$champ] != "") {

                    // Si la valeur du champ contient le marqueur 'temporary'
                    $temporary_test = explode("|", $this->valF[$champ]);
                    //
                    if (isset($temporary_test[0]) && $temporary_test[0] == "tmp") {
                        //
                        if (!isset($temporary_test[1])) {
                            //
                            $msg = _("Erreur lors de la creation du fichier sur le champ").
                            " \"".$this->table.".".$champ."\". ";
                            $this->addToLog(__METHOD__."(): ".$msg, DEBUG_MODE);
                            return $msg._("Veuillez contacter votre administrateur.");
                        }
                        // Récupération des métadonnées calculées après validation
                        $metadata = $this->getMetadata($champ);
                        //
                        $this->valF[$champ] = $this->f->storage->create($temporary_test[1], $metadata, "from_temporary");
                        // Si le fichier est vérouillé
                        if ($this->valF[$champ] === false) {
                            //
                            $msg =  _("Le fichier sur le champ")." ".$this->table.".".$champ." ".
                            _("est verouille. ");
                            $this->addToLog(__METHOD__."(): ".$msg, DEBUG_MODE);
                            return $msg._("Veuillez revalider le formulaire");
                        }
                        // Gestion du retour d'erreur
                        if ($this->valF[$champ] == OP_FAILURE) {
                            //
                            $msg = _("Erreur lors de la creation du fichier sur le champ").
                            " \"".$this->table.".".$champ."\". ";
                            $this->addToLog(__METHOD__."(): ".$msg, DEBUG_MODE);
                            return  $msg._("Veuillez contacter votre administrateur.");
                        }
                    }
                }

                // Cas d'une modification de fichier
                // Condition : si nous ne sommes pas en mode 'AJOUT' ET si la
                // valeur existante en base n'est pas vide ET qu'une valeur est
                // postée pour le champ fichier ET que la valeur postée est
                // différente de la valeur présente en base
                if ($this->getParameter("maj") != 0
                    AND $this->getVal($champ) != ""
                    AND isset($this->valF[$champ])
                    AND $this->valF[$champ] != ""
                    AND $this->getVal($champ) != $this->valF[$champ]) {

                    // Si la valeur du champ contient le marqueur 'temporary'
                    $temporary_test = explode("|", $this->valF[$champ]);
                    //
                    if (isset($temporary_test[0]) && $temporary_test[0] == "tmp") {
                        //
                        if (!isset($temporary_test[1])) {
                            //
                            $msg = _("Erreur lors de la mise a jour du fichier sur le champ").
                            " \"".$this->table.".".$champ."\". ";
                            $this->addToLog(__METHOD__."(): ".$msg._("id")." = ".$this->valF[$this->clePrimaire]." - "._("uid fichier")." = ".$this->getVal($champ), DEBUG_MODE);
                            return $msg._("Veuillez contacter votre administrateur.");
                        }

                        // Sauvegarde de l'ancien fichier
                        $this->tmpFile[$champ] = $this->f->storage->get($this->getVal($champ));
                        // Récupération des métadonnées calculées après validation
                        $metadata = $this->getMetadata($champ);
                        //
                        $this->valF[$champ] = $this->f->storage->update($this->getVal($champ), $temporary_test[1], $metadata, "from_temporary");
                        // Si le fichier est vérouillé
                        if ($this->valF[$champ] === false) {
                            //
                            $msg = _("Le fichier sur le champ")." ".$this->table.".".$champ." ".
                            _("est verouille. ");
                            $this->addToLog(__METHOD__."(): ".$msg._("id")." = ".$this->valF[$this->clePrimaire]." - "._("uid fichier")." = ".$this->getVal($champ), DEBUG_MODE);
                            return $msg._("Veuillez revalider le formulaire");
                        }
                        // Gestion du retour d'erreur
                        if ($this->valF[$champ] == OP_FAILURE) {
                            //
                            $msg = _("Erreur lors de la mise a jour du fichier sur le champ").
                            " \"".$this->table.".".$champ."\". ";
                            $this->addToLog(__METHOD__."(): ".$msg._("id")." = ".$this->valF[$this->clePrimaire]." - "._("uid fichier")." = ".$this->getVal($champ), DEBUG_MODE);
                            return $msg._("Veuillez contacter votre administrateur.");
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Méthode de traitement de fichier uploadé : récupération du fichier temporaire,
     * pour la suppression.
     * 
     * @return string/boolean retourne true ou un message d'erreur
     */
    function traitementFichierUploadSuppression() {

        $type_list = array();
        // Récupération du tableau abstract_type si il existe sinon on utilise
        // les type de champs définis dans le formulaire
        if (isset($this->abstract_type)) {
            $type_list = $this->abstract_type;
        } elseif (isset($this->form->type)) {
            $type_list = $this->form->type;
        }
        // Pour chaque champs configurés avec les widgets upload, upload2 ou filestatic
        // ou chaque champs de type abstrait file défini dans le tableau abstract_type
        foreach ($type_list as $champ => $type) {
            //
            if ($type == "upload" OR $type == "upload2" OR $type == "filestatic"
                OR (isset($this->abstract_type) AND $type == "file")) {

                // Cas d'une suppression de fichier
                // Condition : si nous sommes en mode 'SUPPRESSION' OU si nous
                // ne sommes pas en mode 'AJOUT' ET si la valeur existante en
                // base n'est pas vide ET qu'une valeur est postée pour le
                // champ fichier ET que cette valeur postée est vide
                if (($this->getParameter("maj") == 2 
                        AND $this->getVal($champ) != "")
                    OR ($this->getParameter("maj") != 0
                        AND $this->getVal($champ) != ""
                        AND isset($this->valF[$champ])
                        AND $this->valF[$champ] == "")) {
                    // Sauvegarde de l'ancien fichier
                    $this->tmpFile[$champ] = $this->f->storage->get($this->getVal($champ));
                    $res = $this->f->storage->delete($this->getVal($champ));
                    // Si le fichier est vérouillé
                    if ($res === false) {
                        //
                        $msg = _("Le fichier sur le champ")." ".$this->table.".".$champ." ".
                        _("est verouille. ");
                        $this->addToLog(__METHOD__."(): ".$msg._("id")." = ".$this->getVal($this->clePrimaire)." - "._("uid fichier")." = ".$this->getVal($champ), DEBUG_MODE);
                        return $msg._("Veuillez revalider le formulaire");
                    }
                    if ($res == OP_FAILURE) {
                        //
                        $msg = _("Erreur lors de la suppression du fichier sur le champ").
                        " \"".$this->table.".".$champ."\". ";
                        $this->addToLog(__METHOD__."(): ".$msg._("id")." = ".$this->getVal($this->clePrimaire)." - "._("uid fichier")." = ".$this->getVal($champ), DEBUG_MODE);
                        return $msg._("Veuillez contacter votre administrateur.");
                    }

                }

            }
        }
        return true;
    }

    /**
     * Permet d'annuler le traitement effectué sur les fichiers du formulaire
     * si une erreur lors de l'enregistrement survient.
     * @return void
     */
    private function undoFileTransaction() {

        $type_list = array();
        // Récupération du tableau abstract_type si il existe sinon on utilise
        // les type de champs définis dans le formulaire
        if (isset($this->abstract_type)) {
            $type_list = $this->abstract_type;
        } elseif (isset($this->form->type)) {
            $type_list = $this->form->type;
        }
        // Pour chaque champs configurés avec les widgets upload, upload2 ou filestatic
        // ou chaque champs de type abstrait file défini dans le tableau abstract_type
                foreach ($type_list as $champ => $type) {
            //
            if ($type == "upload" OR $type == "upload2" OR $type == "filestatic"
                OR (isset($this->abstract_type) AND $type == "file")) {

                // Cas d'un ajout de fichier
                // Condition : si la valeur existante en base est vide ou que
                // nous sommes en mode 'AJOUT' ET qu'une valeur est postée pour
                // le champ fichier
                if (($this->getVal($champ) == ""
                     OR $this->getParameter("maj") == 0)
                    AND isset($this->valF[$champ])
                    AND $this->valF[$champ] != "") {

                    // suppression du fichier ajouté au début du traitement
                    if($this->f->storage->delete($this->valF[$champ]) == OP_FAILURE) {
                        $this->addToMessage(_("L'etat de l'enregistrement n'a pas pu etre réinitialisé"));
                    }
                }

                // Cas d'une modification de fichier
                // Condition : si nous ne sommes pas en mode 'AJOUT' ET si la
                // valeur existante en base n'est pas vide ET qu'une valeur est
                // postée pour le champ fichier ET que la valeur postée est
                // différente de la valeur présente en base
                if ($this->getParameter("maj") != 0
                    AND $this->getVal($champ) != ""
                    AND isset($this->valF[$champ])
                    AND $this->valF[$champ] != ""
                    AND $this->getVal($champ) != $this->valF[$champ]) {

                    // Annulation de la modification des fichiers
                    if(isset($this->tmpFile[$champ])) {
                        if($this->f->storage->update($this->valF[$champ],
                           $this->tmpFile[$champ]["file_content"],
                           $this->tmpFile[$champ]["file_content"]) == OP_FAILURE) {
                            $this->addToMessage(_("L'etat de l'enregistrement n'a pas pu etre réinitialisé"));
                        }
                    }
                    
                }
                // Cas d'une suppression de fichier
                // Condition : si nous sommes en mode 'SUPPRESSION' OU si nous
                // ne sommes pas en mode 'AJOUT' ET si la valeur existante en
                // base n'est pas vide ET qu'une valeur est postée pour le
                // champ fichier ET que cette valeur postée est vide
                if ($this->getParameter("maj") == 2
                    OR ($this->getParameter("maj") != 0
                        AND $this->getVal($champ) != ""
                        AND isset($this->valF[$champ])
                        AND $this->valF[$champ] == "")) {
                    // Annulation de la suppression des fichiers
                    if(isset($this->tmpFile[$champ])) {
                        if($this->f->storage->update($this->valF[$champ],
                           $this->tmpFile[$champ]["file_content"],
                           $this->tmpFile[$champ]["file_content"]) == OP_FAILURE) {
                            $this->addToMessage(_("L'etat de l'enregistrement n'a pas pu etre réinitialisé"));
                        }
                    }
                }
            }
        }
    }

    /**
     * Permet d'annuler toutes modifications effectuées sur le formulaire
     */
    function undoValidation() {
        $this->correct = false;
        $this->f->db->rollback();
        $this->undoFileTransaction();
        if(!empty($this->errors)) {
            $this->addToMessage(_("Une erreur s'est produite. Contactez votre administrateur."));
        }
    }
    /**
     * VIEW - formulaire.
     *
     * @todo Changer l'attribut name du formulaire pour optimiser la gestion
     * des formulaires
     *
     * @return void
     */
    function formulaire() {
        //
        $datasubmit = $this->getDataSubmit();
        // Ouverture de la balise form si pas en consultation
        if ($this->getParameter("maj") != 3) {
            echo "\n<!-- ########## START DBFORM ########## -->\n";
            echo "<form";
            echo " method=\"post\"";
            echo " name=\"f1\"";
            echo " action=\"";
            echo $datasubmit;
            echo "\"";
            echo ">\n";
        }
        // Compatibilite anterieure - On decremente la variable validation
        $this->setParameter("validation", $this->getParameter("validation") - 1);
        // Instanciation de l'objet formulaire
        $this->form = new $this->om_formulaire(
            "", $this->getParameter("validation"), $this->getParameter("maj"), 
            $this->champs, $this->val, $this->longueurMax
        );
        //
        $this->form->setParameter("obj", get_class($this));
        $this->form->setParameter("idx", $this->getParameter("idx"));
        $this->form->setParameter("form_type", "form");
        // Valorisation des variables formulaires
        $this->setVal(
            $this->form, $this->getParameter("maj"),
            $this->getParameter("validation"),
            $this->f->db, null
        );
        $this->setType($this->form, $this->getParameter("maj"));
        $this->setLib($this->form, $this->getParameter("maj"));
        $this->setTaille($this->form, $this->getParameter("maj"));
        $this->setMax($this->form, $this->getParameter("maj"));
        $this->setSelect($this->form, $this->getParameter("maj"), $this->f->db, null);
        $this->setOnchange($this->form, $this->getParameter("maj"));
        $this->setOnkeyup($this->form, $this->getParameter("maj"));
        $this->setOnclick($this->form, $this->getParameter("maj"));
        $this->setGroupe($this->form, $this->getParameter("maj"));
        $this->setRegroupe($this->form, $this->getParameter("maj"));
        $this->setLayout($this->form, $this->getParameter("maj"));
        $this->setRequired($this->form, $this->getParameter("maj"));
        $this->set_form_specificity($this->form, $this->getParameter("maj"));
        //
        $this->form->recupererPostvar(
            $this->champs, $this->getParameter("validation"),
            $this->getParameter("postvar"), null
        );
        // Verification de l'accessibilité sur l'élément
        // Si l'utilisateur n'a pas accès à l'élément dans le contexte actuel
        // on arrête l'exécution du script
        $this->checkAccessibility();
        // Si le formulaire a été validé alors on exécute le traitement souhaitée
        if ($this->getParameter("validation") > 0) {
            // Appel des methodes en fonction du mode pour inserer, modifier,
            // supprimer ou une autre action sur l'objet de/dans la base de
            // donnees.
            switch ($this->getParameter("maj")) {
                // (MODE 'insert')
                case 0 :
                    $this->f->db->autoCommit(false);
                    if( $this->ajouter($this->form->val, $this->f->db, null) ) {
                        $this->f->db->commit(); // Validation des transactions
                    } else {
                        $this->undoValidation(); // Annulation des transactions
                    }
                    break;
                // (MODE 'update')
                case 1 :
                    $this->f->db->autoCommit(false);
                    if( $this->modifier($this->form->val, $this->f->db, null) ) {
                        $this->f->db->commit(); // Validation des transactions
                    } else {
                        $this->undoValidation(); // Annulation des transactions
                    }
                    break;
                // (MODE 'delete')
                case 2 :
                    $this->f->db->autoCommit(false);
                    if( $this->supprimer($this->form->val, $this->f->db, null) ) {
                        $this->f->db->commit(); // Validation des transactions
                    } else {
                        $this->undoValidation(); // Annulation des transactions
                    }
                    break;
                // Autres actions
                default:
                    // Vérification de l'existance de la méthode
                    if ($this->is_action_defined($this->getParameter("maj")) != null
                        && $this->get_action_param($this->getParameter("maj"), "method") != null) {
                        // Désactivation de l'autocommit
                        $this->f->db->autoCommit(false);
                        // Execution de l'action specifique
                        $treatment = $this->get_action_param($this->getParameter("maj"), "method");
                        if(method_exists($this, $treatment)) {
                            if($this->$treatment($this->form->val, $this->f->db, null)) {
                                $this->f->db->commit(); // Validation des transactions
                            } else {
                                $this->undoValidation(); // Annulation des transactions
                            }
                        }
                    }
                    break;
            }
        }
        // Desactivation du verrou
        $this->deverrouille($this->getParameter("validation"));
        // Affichage du message avant d'afficher le formulaire
        $this->message();
        // Affichage du bouton retour
        $this->retour(
            $this->getParameter("premier"),
            $this->getParameter("recherche"),
            $this->getParameter("tricol")
        );
        // Ouverture du conteneur de formulaire
        $this->form->entete();
        // Composition du tableau d'action à afficher dans le portlet
        $this->compose_portlet_actions();
        // Affichage du portlet d'actions s'il existe des actions
        if (!empty($this->user_actions)) {
            $this->form->afficher_portlet(
                $this->getParameter("idx"),
                $this->user_actions
            );
        }
        // Affichage du contenu du formulaire
        $this->form->afficher(
            $this->champs,
            $this->getParameter("validation"),
            null,
            $this->correct
        );
        // Point d'entrée dans le formulaire pour ajout d'éléments spécifiques
        $this->formSpecificContent($this->getParameter("maj"));
        // Fermeture du conteneur de formulaire
        $this->form->enpied();
        // Affichage du bouton et du bouton retour
        echo "\n<!-- ########## START FORMCONTROLS ########## -->\n";
        echo "<div class=\"formControls\">\n";
        if ($this->getParameter("maj") != 3) {
            $this->bouton(
                $this->getParameter("maj")
            );
        }
        $this->retour(
            $this->getParameter("premier"),
            $this->getParameter("recherche"),
            $this->getParameter("tricol")
        );
        echo "</div>\n";
        echo "<!-- ########## END FORMCONTROLS ########## -->\n";
        // Fermeture de la balise form
        if ($this->getParameter("maj") != 3) {
            echo "</form>\n";
            echo "<!-- ########## END DBFORM ########## -->\n";
        }
        // Point d'entrée en dessous du formulaire pour ajout d'éléments spécifiques
        $this->afterFormSpecificContent();
    }

    /**
     * Méthode de comparaison pour réorganisation du tableau des actions.
     * @param array $a Élément à comparer n°1
     * @param array $b Élément à comparer n°2
     * 
     * @return integer 1 ou -1 selon si a.order est > ou < à b.order
     */
    function cmp_class_actions($a, $b) {
        // Si order n'est pas défini on ne fait rien
        if(!isset($a["order"]) or !isset($b["order"])) {
            return 0;
        }
        // Si même ordre on test avec le numéro d'action
        if ($a["order"] == $b["order"]) {
            if(!isset($a["action"]) or !isset($b["action"])) {
                return 0;
            } else {
                return ($a["action"] < $b["action"]) ? -1 : 1;
            }
        }
        return ($a["order"] < $b["order"]) ? -1 : 1;
    }

    /**
     * Méthode permettant de vérifier l'existance d'une action de portlet dans 
     * une action.
     * @param integer $action clé de l'action
     * 
     * @return boolean         true si le portlet est défini
     */
    function is_portlet_action_defined($action) {
        if(isset($this->class_actions[$action]["portlet"]) and
            !empty($this->class_actions[$action]["portlet"])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Méthode permettant de vérifier l'existance d'une action.
     * @param integer $action clé de l'action
     * 
     * @return boolean         true si l'action est défini
     */
    function is_action_defined($action) {
        if(isset($this->class_actions[$action]) and
            !empty($this->class_actions[$action])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @return boolean
     */
    function is_action_condition_satisfied($action) {
        //
        $this->addToLog(__METHOD__."(): start - action ".$action, EXTRA_VERBOSE_MODE);
        // On initialise la valeur de retour à 'true' car par défaut si il n'y
        // a pas de condition, l'action est disponible
        $condition_satisfied = true;
        // On récupère le paramètre de condition sur l'action en cours
        $condition_parameter = $this->get_action_param($action, "condition");
        // Il est possible que le paramètre de condition soit au format 
        // 'string' (une seule méthode) ou au format 'array' (plusieurs 
        // méthodes). Si le format n'est pas 'array' alors on reformate
        // le paramètre.
        if ($condition_parameter == null) {
            $condition_parameter = array();
        } elseif (!is_array($condition_parameter)) {
            $condition_parameter = array($condition_parameter, );
        }
        // On boucle sur la liste des méthodes à vérifier pour que la condition
        // soit satisfaite.
        foreach ($condition_parameter as $condition_method) {
            // Si la méthode existe
            if (method_exists($this, $condition_method)) {
                // Alors on appelle la méthode et on réalise un et logique
                // avec la valeur actuelle de la condition. 
                // TRUE  && TRUE  => TRUE
                // TRUE  && FALSE => FALSE
                // FALSE && FALSE => FALSE
                // FALSE && TRUE  => FALSE
                $condition_satisfied = ($condition_satisfied && $this->$condition_method());
            }
        }
        $this->addToLog(__METHOD__."(): return ".var_export($condition_satisfied, true).";", EXTRA_VERBOSE_MODE);
        $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
        // On retourne la valeur calculée
        return $condition_satisfied;
    }

    /**
     * Méthode permettant de récupérer le tableau complet des action.
     * 
     * @return array         tableau d'action
     */
    function get_class_actions() {
        return $this->class_actions;
    }

    /**
     * Permet de renvoyer la clé de l'action à partir de son identifiant texte.
     *
     * L'identifiant texte correspond à l'attribut "identifier" de l'action, il
     * est sensé être unique et doit avoir une signification fonctionnelle en 
     * opposition à la clé qui est un entier qui n'a aucune signification
     * fonctionnelle.
     *
     * @return integer
     */
    function get_action_key_for_identifier($identifier) {
        //
        foreach ($this->get_class_actions() as $key => $value) {
            //
            if ($value["identifier"] == $identifier) {
                //
                return $key;
            }
        }
        //
        return null;
    }

    /**
     * Définition des actions disponibles sur la classe.
     *
     * @return void
     */
    function init_class_actions() {

        // Initialisation de l'attribut
        $this->class_actions = array();

        // ACTION - 000 - ajouter
        //
        $this->class_actions[0] = array(
            "identifier" => "ajouter",
            "view" => "formulaire",
            "method" => "ajouter",
            "button" => _("ajouter"),
            "permission_suffix" => "ajouter",
        );

        // ACTION - 001 - modifier
        //
        $this->class_actions[1] = array(
            "identifier" => "modifier",
            "portlet" => array(
                "type" => "action-self",
                "libelle" => _("modifier"),
                "class" => "edit-16",
                "order" => 10,
                ),
            "view" => "formulaire",
            "method" => "modifier",
            "button" => _("modifier"),
            "permission_suffix" => "modifier",
        );

        // ACTION - 002 - supprimer
        //
        $this->class_actions[2] = array(
            "identifier" => "supprimer",
            "portlet" => array(
                "type" => "action-self",
                "libelle"=>_("supprimer"),
                "class" => "delete-16",
                "order"=>20,
                ),
            "view" => "formulaire",
            "method" => "supprimer",
            "button" => _("supprimer"),
            "permission_suffix" => "supprimer",
        );

        // ACTION - 003 - consulter
        //
        $this->class_actions[3] = array(
            "identifier" => "consulter",
            "view" => "formulaire",
            "method" => "consulter",
            "button" => _("consulter"),
            "permission_suffix" => "consulter",
        );

    }

    /**
     * Méthode permettant de récupérer une valeur de l'action passée en paramètre.
     * @param integer $action clé de l'action
     * @param string  $param  paramètre à récupérer
     * 
     * @return string         valeur du paramètre
     */
    function get_action_param($action, $param) {
        switch($param) {
            // Représente l'identifiant de l'action soit une chaine de
            // caractères sans espaces ni accents permettant d'identifier
            // l'action (exemple : "modfier" ou "archiver"). Soit 'identifier'
            // est présent dans la configuration et on retourne la valeur soit
            // on renvoi l'identifiant numérique de l'action.
            case "identifier" :
                if (isset($this->class_actions[$action]["identifier"])) {
                    return $this->class_actions[$action]["identifier"];
                } else {
                    return $action;
                }
                break;
            case "method" :
                if(isset($this->class_actions[$action]["method"])) {
                    return $this->class_actions[$action]["method"];
                }
                break;
            case "button" :
                if(isset($this->class_actions[$action]["button"])) {
                    return $this->class_actions[$action]["button"];
                }
                break;
            case "permission_suffix" :
                if(isset($this->class_actions[$action]["permission_suffix"])) {
                    return $this->class_actions[$action]["permission_suffix"];
                }
                break;
            case "condition" :
                if(isset($this->class_actions[$action]["condition"])) {
                    return $this->class_actions[$action]["condition"];
                }
                break;
            case "view" :
                if(isset($this->class_actions[$action]["view"])) {
                    return $this->class_actions[$action]["view"];
                }
                break;
            case "portlet" :
                if(isset($this->class_actions[$action]["portlet"])) {
                    return $this->class_actions[$action]["portlet"];
                }
                break;
            case "portlet_libelle" :
                if(isset($this->class_actions[$action]["portlet"]["libelle"])) {
                    return $this->class_actions[$action]["portlet"]["libelle"];
                }
                break;
            case "portlet_type" :
                if(isset($this->class_actions[$action]["portlet"]["type"])) {
                    return $this->class_actions[$action]["portlet"]["type"];
                }
                break;
            case "portlet_class" :
                if(isset($this->class_actions[$action]["portlet"]["class"])) {
                    return $this->class_actions[$action]["portlet"]["class"];
                }
                break;
            case "portlet_order" :
                if(isset($this->class_actions[$action]["portlet"]["order"])) {
                    return $this->class_actions[$action]["portlet"]["order"];
                }
                break;
            case "portlet_url" :
                if(isset($this->class_actions[$action]["portlet"]["url"])) {
                    return $this->class_actions[$action]["portlet"]["url"];
                }
                break;
            default :
                return null;

        }
    }

    /**
     * Permet de composer un tableau des actions composant le portlet.
     *
     * Ce tableau sera directement interprété par la méthode d'affichage du portlet 
     * (formulaire::afficher_portlet).
     * Une action est composée des éléments suivant : 
     * - href,
     * - target,
     * - class,
     * - onclick,
     * - id,
     * - libelle.
     *
     * @return void
     */
    function compose_portlet_actions() {
 
        // On compose le portlet d'actions uniquement en mode CONSULTER
        // Si on ne se trouve pas dans ce cas alors on sort de la méthode
        $maj = $this->getParameter("maj");
        if ($maj != 3) {
            return;
        }

        // On retient seulement les actions disponibles pour l'utilisateur
        // c'est-à-dire les actions pour lesquelles il a les permissions
        // et/ou qui sont valides dans le contexte en question.
        // On initialise donc le tableau résultat
        $this->user_actions = array();

        // On prépare les variables à utiliser dans la boucle
        $idx = $this->getParameter("idx");
        $retourformulaire = $this->getParameter("retourformulaire");

        // On teste quelle mode de gestion des actions est configuré
        if ($this->is_option_class_action_activated() === false) {

            // ANCIENNE GESTION DES ACTIONS
            // Les actions sont définies par les fichiers de configuration 
            // sql/<OM_DB_PHPTYPE>/*.form.inc.php et des scripts scr/form.php
            // et scr/sousform.php

            // On récupère la définition des actions depuis le paramètre
            // actions et éventuellement l'attribut actions_sup
            // Si aucune action n'est présente alors on sort de la méthode
            $actions = array_merge($this->getParameter("actions"), $this->actions_sup);
            if (empty($actions)) {
                return;
            }

            // On boucle sur les actions définies
            foreach ($actions as $key => $conf) {

                /**
                 * Vérifications sur la validité de l'action
                 */
                // Vérification des droits
                // Si des droits sont requis sur l'action et que l'utilisateur 
                // n'est pas autorisé alors on passe à l'itération suivante.
                if (isset($conf['rights']) && !$this->f->isAccredited(
                        $conf['rights']['list'],
                        $conf['rights']['operator'])) {
                    continue;
                }
                // Vérification du lien
                // Si l'action est configurée dans lien ou avec un lien #
                // alors on passe à l'itération suivante.
                if (empty($conf['lien']) || $conf['lien'] == '#') {
                    continue;
                }

                /**
                 * Composition de l'action
                 */
                // On détermine l'identifiant de l'action.
                $action_identifier = $key;
                // On détermine le type de l'action.
                $action_type = "";
                if (isset($conf["target"]) && $conf["target"] == "_blank") {
                    // Si l'action est paramétrée pour ouvrir le lien dans une
                    // nouvelle fenêtre alors le type de l'action est 'action-blank'
                    // peu importe les autres paramètres de l'action.
                    $action_type = "action-blank";
                } elseif ($retourformulaire != "" 
                    && (!isset($conf['ajax']) || $conf['ajax'] == true)) {
                    // Si l'action est paramétrée pour s'ouvrir en ajax, c'est-à-dire
                    // pour être ouverte en lieu et place du formulaire actuel
                    // (valable pour un souform).
                    $action_type = "action-self";
                }
                // Préparation du tri
                $action_order = $key;
                if (isset($conf['ordre']) and !empty($conf['ordre'])) {
                    $action_order = $conf['ordre'];
                }
                // On compose l'attribut id de l'action. Il s'agit d'un identifiant
                // 'unique' pour l'action composé de la chaine 'action', du type du
                // formulaire, de l'objet du formulaire, du nom de l'action
                $action_id = "action";
                $action_id .= ($retourformulaire != "" ? "-sousform" : "-form");
                $action_id .= "-".get_class($this)."-".$key;
                // On compose l'attribut class de l'action. Il est composé de la 
                // classe 'action' et éventuellement du type de l'action.
                $action_class = sprintf(" action %s ", $action_type);
                //
                $action_target = ($action_type == "action-blank" ? "_blank" : "");
                //
                $action_libelle = $conf["lib"];
                // On compose l'attribut href de l'action. Il est possible que 
                // cet attribut contienne un 'trick' qui consiste en la fermeture
                // de la déclaration de l'attribut href (\") pour ouvrir un 
                // attribut onclick par exemple.
                $action_href = $conf["lien"].$idx.$conf["id"];
                //
                $action = array(
                    "action" => $action_identifier,
                    "order" => $action_order,
                    "id" => $action_id,
                    "class" => $action_class,
                    "target" => $action_target,
                    "libelle" => $action_libelle,
                    "href" => $action_href,
                );

                /**
                 *
                 */
                // On ajoute l'action dans le tableau résultat
                $this->user_actions[$key] = $action;
            }
        } else {

            // NOUVELLE GESTION DES ACTIONS
            // Gestion des actions définies dans les attributs de classe

            // Les actions sont définies dans un attribut de la classe
            // Si aucune action n'y est présente alors on sort de la méthode
            $actions = $this->get_class_actions();
            if (empty($actions)) {
                return;
            }

            // On boucle sur les actions définies
            foreach ($actions as $key => $conf) {

                /**
                 * Vérifications sur la validité de l'action
                 */
                // Vérification de l'existence de l'action portlet
                // On récupère uniquement les actions qui sont à afficher dans
                // le portlet. Si ce n'est pas le cas, on passe à l'itération
                // suivante.
                if ($this->is_portlet_action_defined($key) !== true) {
                    continue;
                }
                // Vérification de la condition
                // Si une condition est définie sur l'action et que la condition
                // n'est pas vérifiée dans le contexte, alors on passe à 
                // l'itération suivante.
                if ($this->is_action_condition_satisfied($key) !== true) {
                    continue;
                }
                // Verification des droits
                // Si des droits sont requis sur l'action et que l'utilisateur 
                // n'est pas autorisé, alors on passe à l'itération suivante.
                $specific_right = "";
                $permission_suffix = $this->get_action_param($key, "permission_suffix");
                if ($permission_suffix != null) {
                    $specific_right = get_class($this)."_".$permission_suffix;
                }
                if (!$this->f->isAccredited(
                        array(get_class($this), $specific_right, ),
                        "OR")) {
                    continue;
                }

                /**
                 * Composition de l'action
                 */
                // On détermine le type de l'action.
                $action_type = "";
                if ($this->get_action_param($key, "portlet_type") != null) {
                    $action_type = $this->get_action_param($key, "portlet_type");
                }
                // On détermine l'identifiant de l'action
                $action_identifier = $this->get_action_param($key, "identifier");
                // Préparation du tri
                $action_order = $key;
                $portlet_order = $this->get_action_param($key, "portlet_order");
                if ($portlet_order!=null and is_integer($portlet_order)) {
                    $action_order = $portlet_order;
                }
                // On compose l'attribut id de l'action. Il s'agit d'un identifiant
                // 'unique' pour l'action composé de la chaine 'action', du type du
                // formulaire, de l'objet du formulaire, du nom de l'action
                $action_id = "action";
                $action_id .= ($retourformulaire != "" ? "-sousform" : "-form");
                $action_id .= "-".get_class($this)."-".$action_identifier;
                // On compose l'attribut class de l'action. Il est composé de la 
                // classe 'action' et éventuellement du type de l'action.
                switch ($action_type) {
                    case "action-direct": 
                        $class_tmp = "action-direct"; 
                        break;
                    case "action-direct-with-confirmation": 
                        $class_tmp = "action-direct action-with-confirmation"; 
                        break;
                    case "action-blank": 
                        $class_tmp = "action-blank"; 
                        break;
                    case "action-self": 
                        $class_tmp = "action-self"; 
                        break;
                    case "action-overlay": 
                        $class_tmp = "action-overlay"; 
                        break;
                    default: 
                        $class_tmp = $action_type;
                }
                $action_class = sprintf(" action %s ", $class_tmp);
                //
                $action_target = ($action_type == "action-blank" ? "_blank" : "");
                // On compose le libellé de l'action.
                $libelle_title = $this->get_action_param($key, "portlet_libelle");
                if ($libelle_title == null) {
                    $libelle_title = $action_identifier;
                }
                $libelle_class = $this->get_action_param($key, "portlet_class");
                if ($libelle_class == null) {
                    $libelle_class = "";
                }
                $action_libelle = sprintf(
                    "<span title=\"%s\" class=\"om-prev-icon om-icon-16 %s\">%s</span>",
                    $libelle_title,
                    ($libelle_class == "" ? "" : " ".$libelle_class),
                    $libelle_title
                );
                // On compose l'attribut href de l'action.
                if ($this->get_action_param($key, "portlet_url") != null) {
                    $url = $this->get_action_param($key, "portlet_url");
                    $action_href = $url.$idx;
                } else {
                    //
                    $override = array(
                        "validation" => null,
                        "maj" => $key,
                        "retour" => "form",
                    );
                    // Si en sousform appel de sousform.php sinon form.php
                    if($this->getParameter("retourformulaire") != "") {
                        $action_href = $this->compose_form_url("sousform", $override);
                    } else {
                        $action_href = $this->compose_form_url("form", $override);
                    }
                }
                //
                $action = array(
                    "action" => $action_identifier,
                    "order" => $action_order,
                    "id" => $action_id,
                    "class" => $action_class,
                    "target" => $action_target,
                    "libelle" => $action_libelle,
                    "href" => $action_href,
                );

                /**
                 *
                 */
                // On ajoute l'action dans le tableau résultat
                $this->user_actions[$key] = $action;
            }
        }

        // Tri du tableau résultat
        uasort($this->user_actions, array($this, 'cmp_class_actions'));

    }

    // {{{ POINTS D'ENTREE DANS LES VUES - formulaire et sousformulaire

    /**
     * Point d'entrée permettant d'afficher des informations spécifiques.
     *
     * Cette méthode à surcharger permet d'afficher des informations 
     * spécifiques en fin de formulaire.
     *
     * @param integer $maj Identifiant de l'action en cours d'exécution.
     *
     * @return void
     */
    function formSpecificContent($maj) { }

    /**
     * Point d'entrée permettant d'afficher des informations spécifiques.
     *
     * Cette méthode à surcharger permet d'afficher des informations 
     * spécifiques en fin de sousformulaire.
     *
     * @param integer $maj Identifiant de l'action en cours d'exécution.
     *
     * @return void
     */
    function sousFormSpecificContent($maj) { }

    /**
     * Point d'entrée permettant d'afficher des informations spécifiques.
     *
     * Cette méthode à surcharger permet d'afficher des informations 
     * spécifiques après le formulaire.
     *
     * @return void
     */
    function afterFormSpecificContent() { }

    /**
     * Point d'entrée permettant d'afficher des informations spécifiques.
     *
     * Cette méthode à surcharger permet d'afficher des informations 
     * spécifiques après le sousformulaire.
     *
     * @return void
     */
    function afterSousFormSpecificContent() { }

    // }}}

    // {{{ GESTION DU VERROU

    // Le principe du verrou est d'empêcher l'utilisateur de valider plusieurs
    // fois le même formulaire en cliquant deux fois sur le bouton ou en
    // cliquant sur le bouton actualiser du navigateur...
    //
    // Un paramètre $verrou permet dans le fichier dyn/var.inc de configurer
    // l'utilisation du verrou ou non de manière générale dans l'applicatif.
    // $verrou = 0; => le verrou n'est pas utilisé
    // $verrou = 1; => le verrou est utilisé
    // Par défaut , c'est-à-dire si la variable n'est pas définie, alors le
    // paramètre est considéré comme égal à 1.
    //
    // L'information de verrou est stockée dans la variable de session et est
    // associée au nom de l'objet qui vient d'être validé.

    /**
     * Cette méthode permet de déverrouiller la validation.
     *
     * Elle est appelée juste avant l'affichage du formulaire ou du
     * sous-formulaire et déverrouille la validation si le paramètre $validation
     * passé est égal à 0. C'est à dire au chargement du formulaire.
     * 
     * @return void
     */
    function deverrouille($validation) {
        // Si le tableau de stockage des verrous dans la session n'existe pas
        // alors on l'initialise
        if (!isset($_SESSION['verrou']) or !is_array($_SESSION['verrou'])) {
            $_SESSION['verrou'] = array();
        }
        // Si la variable $validation est égale à 0 alors on déverrouille
        // en positionnant le marqueur du verrou à 0 sur l'objet qui vient
        // d'être validé
        if ($validation == 0) {
            $_SESSION['verrou'][get_class($this)] = 0;
        }
    }

    /**
     * Cette méthode permet de verrouiller la validation.
     *
     * Elle est appelée lorsque l'enregistrement est effectuée de manière
     * effective dans la base de données. Par exemple, dans la méthode
     * ajouter(), cette méthode est appelée juste après la fonction
     * autoexecute(). La méthode a pour objectif de stocker l'information
     * du verrou pour permettre de la tester en cas de nouveau post du même
     * élément.
     *
     * @return void
     */
    function verrouille() {
        // Si le tableau de stockage des verrous dans la session n'existe pas
        // alors on l'initialise
        if (!isset($_SESSION['verrou']) or !is_array($_SESSION['verrou'])) {
            $_SESSION['verrou'] = array();
        }
        // On verrouille en ajoutant une entrée avec le nom de la classe
        // de l'objet qui vient d'être validé
        $_SESSION['verrou'][get_class($this)] = 1;
    }

    /**
     * Cette méthode permer de vérifier si il existe un verrou sur l'élément
     * en cours de validation.
     *
     * Elle est appelée lors des vérifications préalables à l'enregistrement.
     * Si le paramètre de verrou est activé et que l'élément en train d'être
     * validé est verrouillé. Alors on positionne l'attribut correct a false
     * et on ajoute un message clair pour l'utilisateur dans le tableau des
     * messages. Si le paramètre de verrou n'est pas activé ou que l'élément
     * n'a pas la validation verrouillé alors la méthode ne fait rien.
     * 
     * @return void
     */
    function testverrou() {
        // Inclusion du fichier de paramétrage dyn/var.inc pour récupérer le
        // paramètre $verrou
        if (file_exists("../dyn/var.inc")) {
            include "../dyn/var.inc";
        }
        // Si le tableau de stockage des verrous dans la session n'existe pas
        // alors on l'initialise
        if (!isset($_SESSION['verrou']) or !is_array($_SESSION['verrou'])) {
            $_SESSION['verrou'] = array();
        }
        // Si la variable n'est pas définie alors on l'initialise à 1, ce qui
        // active la gestion du verrou.
        if (!isset($verrou)) {
            // On positionne la variable à 1 par défaut
            $verrou = 1;
            // XXX Pourquoi faire cette initialisation ? N'est-ce pas une erreur
            // d'initialiser le verrou à non verrouillé si le paramètre n'existe
            // pas dans dyn/var.inc ? La méthode testverrou() renverra dans tous
            // les cas de figure que l'élément n'est pas verrouillé.
            $_SESSION['verrou'][get_class($this)] = 0;
        }
        // Si la gestion du verrou est activée dans l'application et que le
        // verrou est activé sur l'élément en train d'être validé
        // alors on positionne l'attribut correct à false et on ajoute un
        // message pour l'utilisateur
        if ($verrou == 1 && isset($_SESSION['verrou'][get_class($this)])
            && $_SESSION['verrou'][get_class($this)] == 1) {
            // On positionne l'attribut correct à false pour ne pas permettre
            // la validation de l'élément
            $this->correct = false;
            // On ajoute un message pour l'utilisateur
            $this->addToMessage(_("Il y a actuellement un verrou sur cet element a cause d'une procedure anormale."));
        }
        // Logger
        $this->addToLog(__METHOD__."(): "._("Le parametre de verrou dans le fichier var.inc est :")." ".($verrou?"true":"false"), EXTRA_VERBOSE_MODE);
        // Logger
        $this->addToLog(__METHOD__."(): "._("Le verrou sur cet element est :")." ".(isset($_SESSION['verrou'][get_class($this)]) && $_SESSION['verrou'][get_class($this)]?_("active"):_("desactive")), EXTRA_VERBOSE_MODE);

    }

    // }}}

    /**
     *
     */
    function message() {

        if ($this->msg != "") {

            //
            if ($this->correct) {
                $class = "valid";
            } else {
                $class = "error";
            }
            $this->f->layout->display_message($class, $this->msg);

        }

    }

    /**
     * Cette méthode permet de composer le lien retour et de l'afficher
     *
     * @param integer $premier DEPRECATED
     * @param string $recherche DEPRECATED
     * @param string $tricol DEPRECATED
     */
    function retour($premier = NULL, $recherche = NULL, $tricol = NULL) {

        /**
         * Composition du lien retour
         */
        // Les scripts tab.php et form.php se trouvent dans le dossier "../scr/"
        // il est important de faire apparaître le dossier parent dans le lien
        // pour que l'on puisse utiliser la classe depuis un script qui se
        // trouve dans un autre dossier
        $href = "../scr/";
        // Le comportement pour le lien retour est le suivant :
        // XXX à compléter
        if ($this->getParameter("retour") == "form"
            && !($this->getParameter("validation") > 0
                 && $this->getParameter("maj") == 2
                 && $this->correct == true)) {
            $href .= "form.php?";
        } else {
            $href .= "tab.php?";
        }
        //        
        $href .= "obj=".$this->get_class_custom();

        //
        if($this->getParameter("retour")=="form") {
            $href .= "&amp;idx=".$this->getParameter("idx");
            $href .= "&amp;idz=".$this->getParameter("idz");
            $href .= "&amp;action=3";
        }
        //
        $href .= "&amp;premier=".$this->getParameter("premier");
        $href .= "&amp;tricol=".$this->getParameter("tricol");
        $href .= "&amp;recherche=".$this->getParameter("recherche");
        $href .= "&amp;selectioncol=".$this->getParameter("selectioncol");
        $href .= "&amp;advs_id=".$this->getParameter("advs_id");
        $href .= "&amp;valide=".$this->getParameter("valide");

        /**
         * Affichage du lien retour
         */
        // Composition du tableau de paramètres
        $params = array(
            "href" => $href,
        );
        // Appel de la méthode d'affichage du lien retour par le layout
        $this->f->layout->display_form_retour($params);

    }

    /**
     * Cette methode permet d'afficher le bouton de validation du formulaire
     *
     * @param integer $maj Mode de mise a jour
     * @return void
     */
    function bouton($maj) {
        if (!$this->correct
            && $this->checkActionAvailability() == true) {
            //
            switch($maj) {
                case 0 :
                    $bouton = _("Ajouter");
                    break;
                case 1 :
                    $bouton = _("Modifier");
                    break;
                case 2 :
                    $bouton = _("Supprimer");
                    break;
                default :
                    // Actions specifiques
                    if ($this->get_action_param($maj, "button") != null) {
                        //
                        $bouton = $this->get_action_param($maj, "button");
                    } else {
                        //
                        $bouton = _("Valider");
                    }
                    break;
            }
            //
            $bouton .= "&nbsp;"._("l'enregistrement de la table")."&nbsp;:";
            $bouton .= "&nbsp;'"._($this->table)."'";
            //
            $params = array(
                "value" => $bouton,
                "name" => "submit",
            );
            //
            $this->f->layout->display_form_button($params);
        }

    }

    /**
     *
     */
    function boutonsousformulaire($datasubmit, $maj, $val=null) {
        //
        $this->bouton($maj);
    }

    /**
     *
     */
    function setRequired(&$form, $maj) {
        if( $maj<2 ) {
            foreach($this->required_field as $field) {
                $form->setRequired($field);
            }
        }
    }

    /**
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function setVal(&$form, $maj, $validation, &$dnu1 = null, $dnu2 = null) {

        $this->set_form_default_values($form, $maj, $validation);
    }

    /**
     *  Permet de pré-remplir les valeurs des formulaires.
     *  
     * @param [object]   $form        formulaire
     * @param [integer]  $maj         mode
     * @param [integer]  $validation  validation
     */
    function set_form_default_values(&$form, $maj, $validation) {

        //
    }

    /**
     * Méthode permettant de remplir valF avant validation du formulaire
     */
    function setValFFromVal() {
        foreach ($this->champs as $champ) {
            $this->valF[$champ] = $this->getVal($champ);
        }
    }

    /**
     *
     */
    function setType(&$form, $maj) {

        //

    }

    /**
     *
     */
    function setLib(&$form, $maj) {

        // libelle automatique
        //[automatic wording]
            foreach(array_keys($form->val) as $elem){
                 $form->setLib($elem,_($elem));
            }

    }

    /**
     *
     */
    function setTaille(&$form, $maj) {

        //

    }

    /**
     *
     */
    function setMax(&$form, $maj) {

        //

    }

    /**
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        //

    }

    /**
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     */
    function getSelectOldValue(&$form, $maj, &$dnu1 = null, &$contenu, $sql_by_id, $table, $val = null) {

        if ($val == null) {
            $val = $this->form->val[$table];
        }
        // Recuperation de la valeur depuis la base de donnes.
        $sql_by_id = str_replace('<idx>', $val, $sql_by_id);
        // Exécution de la requête
        $res = $this->f->db->query($sql_by_id);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql_by_id."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        //
        while ($row =& $res->fetchRow()) {
            // Si première entrée nulle
            if ($contenu[0][0] == '') {
                // On insère l'ancienne valeur en deuxième position
                // Valeurs
                $contenu[0] = array_merge(array($contenu[0][0]),
                                    array($row[0]),
                                    array_slice($contenu[0], 1));
                // Libellés
                $contenu[1] = array_merge(array($contenu[1][0]),
                                    array($row[1]),
                                    array_slice($contenu[1], 1));
            }
            // Sinon on l'insère en premier
            else {
                // Valeurs
                $contenu[0] = array_merge(array($row[0]),
                                          $contenu[0]);
                // Libellés
                $contenu[1] = array_merge(array($row[1]),
                                          $contenu[1]);
            }
        }
    }

    /**
     *
     */
    function setOnchange(&$form, $maj) {

        //

    }

    /**
     *
     */
    function setOnkeyup(&$form, $maj) {

        //

    }

    /**
     *
     */
    function setOnclick(&$form, $maj) {

        //

    }

    /**
     *
     */
    function setGroupe(&$form, $maj) {

        //

    }

    /**
     *
     */
    function setRegroupe(&$form, $maj) {

        //

    }

    /**
     *
     */
    function setBloc(&$form, $maj) {

        //

    }

    /**
     *
     */
    function setFieldset(&$form, $maj) {

        //

    }

    /**
     *
     */
    function setLayout(&$form, $maj) {

    }

    /**
     * Mutateur.
     *
     * Permet d'effectuer des appels aux mutateurs spécifiques sur le formulaire
     * de manière fonctionnelle et non en fonction du mutateur. Exemple : au lieu 
     * de gérer le champ service dans les méthodes setType, setSelect, le setLib, 
     * ... Nous allons les gérer dans cette méthode et appeler tous les mutateurs
     * à la suite.
     * 
     * @param resource $form Instance formulaire.
     * @param integer $maj Clé de l'action.
     *
     * @return void
     */
    function set_form_specificity(&$form, $maj) {

        //

    }

    /**
     * Permet de modifier le fil d'Ariane depuis l'objet pour un formulaire
     * @param string    $ent    Fil d'Ariane récupéréré 
     * @return                  Fil d'Ariane
     */
    function getFormTitle($ent) {

        return $ent;
    }

    /**
     * Permet de modifier le fil d'Ariane depuis l'objet pour un sous-formulaire
     * @param string    $subEnt Fil d'Ariane récupéréré 
     * @return                  Fil d'Ariane
     */
    function getSubFormTitle($subEnt) {   

        return $subEnt;
    }

    /**
     * VIEW - sousformulaire.
     *
     * @return void
     */
    function sousformulaire() {
        //
        $datasubmit = $this->getDataSubmitSousForm();
        // Ouverture de la balise form si pas en consultation
        if ($this->getParameter("maj") != 3) {
            echo "\n<!-- ########## START DBFORM ########## -->\n";
            echo "<form";
            echo " method=\"post\"";
            echo " name=\"f2\"";
            echo " action=\"\"";
            echo " onsubmit=\"affichersform('".$this->getParameter("objsf")."', '".$datasubmit."', this);return false;\"";
            echo ">\n";
        }
        // Compatibilite anterieure - On decremente la variable validation
        $this->setParameter("validation", $this->getParameter("validation") - 1);
        // Instanciation de l'objet formulaire
        $this->form = new $this->om_formulaire(
            "", $this->getParameter("validation"), $this->getParameter("maj"), 
            $this->champs, $this->val, $this->longueurMax
        );
        //
        $this->form->setParameter("obj", get_class($this));
        $this->form->setParameter("idx", $this->getParameter("idx"));
        $this->form->setParameter("form_type", "sousform");
        // Valorisation des variables formulaires
        $this->setValsousformulaire(
            $this->form, $this->getParameter("maj"),
            $this->getParameter("validation"),
            $this->getParameter("idxformulaire"),
            $this->getParameter("retourformulaire"),
            $this->getParameter("typeformulaire"),
            $this->f->db, null
        );
        $this->setType($this->form, $this->getParameter("maj"));
        $this->setLib($this->form, $this->getParameter("maj"));
        $this->setTaille($this->form, $this->getParameter("maj"));
        $this->setMax($this->form, $this->getParameter("maj"));
        $this->setSelect($this->form, $this->getParameter("maj"), $this->f->db, null);
        $this->setOnchange($this->form, $this->getParameter("maj"));
        $this->setOnkeyup($this->form, $this->getParameter("maj"));
        $this->setOnclick($this->form, $this->getParameter("maj"));
        $this->setGroupe($this->form, $this->getParameter("maj"));
        $this->setRegroupe($this->form, $this->getParameter("maj"));
        $this->setLayout($this->form, $this->getParameter("maj"));
        $this->setRequired($this->form, $this->getParameter("maj"));
        $this->set_form_specificity($this->form, $this->getParameter("maj"));
        //
        $this->form->recupererPostvarsousform(
            $this->champs, $this->getParameter("validation"),
            $this->getParameter("postvar"), null
        );
        // Verification de l'accessibilité sur l'élément
        // Si l'utilisateur n'a pas accès à l'élément dans le contexte actuel
        // on arrête l'exécution du script
        $this->checkAccessibility();
        // Si le formulaire a été validé alors on exécute le traitement souhaitée
        if ($this->getParameter("validation") > 0) {
            // Appel des methodes en fonction du mode pour inserer, modifier,
            // supprimer ou une autre action sur l'objet de/dans la base de
            // donnees.
            switch ($this->getParameter("maj")) {
                // (MODE 'insert')
                case 0 :
                    $this->f->db->autoCommit(false);
                    if( $this->ajouter($this->form->val, $this->f->db, null) ) {
                        $this->f->db->commit(); // Validation des transactions
                    } else {
                        $this->undoValidation(); // Annulation des transactions
                    }
                    break;
                // (MODE 'update')
                case 1 :
                    $this->f->db->autoCommit(false);
                    if( $this->modifier($this->form->val, $this->f->db, null) ) {
                        $this->f->db->commit(); // Validation des transactions
                    } else {
                        $this->undoValidation(); // Annulation des transactions
                    }
                    break;
                // (MODE 'delete')
                case 2 :
                    $this->f->db->autoCommit(false);
                    if( $this->supprimer($this->form->val, $this->f->db, null) ) {
                        $this->f->db->commit(); // Validation des transactions
                    } else {
                        $this->undoValidation(); // Annulation des transactions
                    }
                    break;
                // Autres actions
                default:
                    // Vérification de l'existance de la méthode
                    if ($this->is_action_defined($this->getParameter("maj")) != null
                        && $this->get_action_param($this->getParameter("maj"), "method") != null) {
                        // Désactivation de l'autocommit
                        $this->f->db->autoCommit(false);
                        // Execution de l'action specifique
                        $treatment = $this->get_action_param($this->getParameter("maj"), "method");
                        if(method_exists($this, $treatment)) {
                            if($this->$treatment($this->form->val, $this->f->db, null)) {
                                $this->f->db->commit(); // Validation des transactions
                            } else {
                                $this->undoValidation(); // Annulation des transactions
                            }
                        }
                    }
                    break;
            }
        }
        // Desactivation du verrou
        $this->deverrouille($this->getParameter("validation"));
        // Affichage du message avant d'afficher le formulaire
        $this->message();
        // Affichage du bouton retour
        $this->retoursousformulaire(
            $this->getParameter("idxformulaire"),
            $this->getParameter("retourformulaire"),
            $this->form->val,
            $this->getParameter("objsf"),
            $this->getParameter("premiersf"),
            $this->getParameter("tricolsf"), 
            $this->getParameter("validation"), 
            $this->getParameter("idx"), 
            $this->getParameter("maj"), 
            $this->getParameter("retour")
        );
        // Ouverture du conteneur de formulaire
        $this->form->entete();
        // Composition du tableau d'action à afficher dans le portlet
        $this->compose_portlet_actions();
        // Affichage du portlet d'actions s'il existe des actions
        if (!empty($this->user_actions)) {
            $this->form->afficher_portlet(
                $this->getParameter("idx"),
                $this->user_actions,
                $this->getParameter("objsf")
            );
        }
        // Affichage du contenu du formulaire
        $this->form->afficher(
            $this->champs,
            $this->getParameter("validation"),
            null,
            $this->correct
        );
        // Point d'entrée dans le formulaire pour ajout d'éléments spécifiques
        $this->sousFormSpecificContent($this->getParameter("maj"));
        // Fermeture du conteneur de formulaire
        $this->form->enpied();
        // Affichage du bouton et du bouton retour
        echo "\n<!-- ########## START FORMCONTROLS ########## -->\n";
        echo "<div class=\"formControls\">\n";
        if ($this->getParameter("maj") != 3) {
            $this->boutonsousformulaire(
                $datasubmit,
                $this->getParameter("maj"),
                $this->form->val
            );
        }
        $this->retoursousformulaire(
            $this->getParameter("idxformulaire"),
            $this->getParameter("retourformulaire"),
            $this->form->val,
            $this->getParameter("objsf"),
            $this->getParameter("premiersf"),
            $this->getParameter("tricolsf"),
            $this->getParameter("validation"),
            $this->getParameter("idx"),
            $this->getParameter("maj"),
            $this->getParameter("retour")
        );
        echo "</div>\n";
        echo "<!-- ########## END FORMCONTROLS ########## -->\n";
        // Fermeture de la balise form
        if ($this->getParameter("maj") != 3) {
            echo "</form>\n";
            echo "<!-- ########## END DBFORM ########## -->\n";
        }
        // Point d'entrée en dessous du formulaire pour ajout d'éléments spécifiques
        $this->afterSousFormSpecificContent();
    }

    /**
     *
     */
    function retoursousformulaire($idxformulaire, $retourformulaire, $val, $objsf, $premiersf, $tricolsf, $validation, $idx, $maj, $retour) {
        //
        $params = array(
                "objsf" => $objsf,
                "retour" => $retour,
                "validation" =>$validation,
                "maj" => $maj,
                "correct" => $this->correct,
                "idx" =>$idx,
                "retourformulaire" => $retourformulaire,
                "idxformulaire" => $idxformulaire,
                "premiersf" => $premiersf,
                "tricolsf" => $tricolsf,
        );
        $this->f->layout->display_dbform_lien_retour_sousformulaire($params);
        //
    }

    /**
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     */
    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {

        if ($validation==0) {
          if ($maj == 0){

            $form->setVal($retourformulaire, $idxformulaire);
        }}

        $this->set_form_default_values($form, $maj, $validation);
    }

    /**
     * Cette methode permet d'obtenir une chaine representant la clause where
     * pour une requete de selection sur la cle primaire.
     *
     * @param string $id Valeur de la cle primaire
     * @return string Clause where
     */
    function getCle($id = "") {
        //
        $cle = " ".$this->table.".".$this->clePrimaire." = ";
        // Clause where en fonction du type de la cle primaire
        if ($this->typeCle == "A") {
            $cle .= " '".$this->f->db->escapeSimple($id)."' ";
        } else {
            $cle .= " ".intval($id)." ";
        }
        //
        return $cle;
    }

    /**
     * Cette methode permet de faire les verifications necessaires lors de
     * l'ajout de messages, et d'obtenir une coherence dans l'attribut message
     * de l'objet pour l'affichage.
     *
     * @param string $message
     */
    function addToMessage($message = "") {
        //
        if (!isset($this->msg)) {
            $this->msg = "";
        } else {
            if ($this->msg != "") {
                $this->msg .= "<br />";
            }
        }
        //
        $this->msg .= $message;
    }

    /**
     * Cette methode ne doit plus etre appelee, c'est 'message::isError($res)'
     * qui s'occupe d'afficher le message d'erreur et de faire le 'die()'.
     *
     * @deprecated
     */
    function erreur_db($debuginfo, $messageDB, $table) {
        $this->addToErrors(
            $debuginfo,
            $messageDB,
            _("Erreur de base de donnees. Contactez votre administrateur.")
        );
    }

    /**
     * Cette methode remplace erreur_db, et permet de remplir le tableau d'erreur
     *
     */
    function addToErrors($debuginfo, $messageDB, $msg) {
            $this->errors['db_debuginfo'] = $debuginfo;
            $this->errors['db_message'] = $messageDB;
            $this->addToLog(__METHOD__."(): ".$msg, VERBOSE_MODE);
    }

    /**
     * Cette methode vide les valeurs des erreurs du tableau errors.
     */
    function clearErrors() {
        foreach (array_keys($this->errors) as $key) {
            $this->errors[$key] = '';
        }
    }



    /**
     * Cette methode permet de rechercher le nombre d'enregistrements
     * ayant le champ 'field' correspondant a la valeur 'id' dans la table
     * 'table'. Si il y a des enregistrements, alors l'attribut 'correct' de
     * l'objet est passe a la valeur false et un message supplementaire est
     * ajoute a l'attribut msg de l'objet.
     *
     * Cette methode est principalement destinee a etre appellee depuis la
     * methode cleSecondaire.
     *
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param string $table
     * @param string $field
     * @param string $id
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     * @param string $selection
     */
    function rechercheTable(&$dnu1 = null, $table, $field, $id, $dnu2 = null, $selection = "") {

        //
        $sql = "select count(*) from ".DB_PREFIXE.$table." ";
        if ($this->typeCle == "A") {
            $sql .= "where ".$field."='".$id."' ";
        } else {
            $sql .= "where ".$field."=".$id." ";
        }
        $sql .= $selection;

        // Exécution de la requête
        $nb = $this->f->db->getone($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->getone(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($nb);

        //
        if ($nb > 0) {
            $this->correct = false;
            $this->msg .= $nb." ";
            $this->msg .= _("enregistrement(s) lie(s) a cet enregistrement dans la table");
            $this->msg .= " ".$table."<br />";
        }

    }
    
    /**
     * Initialisation des valeurs des champs HTML <select>
     *
     * @param formulaire $form formulaire
     * @param null &$dnu1 @deprecated Ancienne ressource de base de données.
     * @param int $maj type d action (0:ajouter, 1:modifier, etc.)
     * @param null $dnu2 @deprecated Ancien marqueur de débogage.
     * @param string $field nom du champ <select> a initialiser
     * @param string $sql requete de selection des valeurs du <select>
     * @param string $sql_by_id requete de selection valeur par identifiant
     * @param string $om_validite permet de définir si l'objet lié est affecté par une date de validité
     * @param string $multiple permet d'utiliser cette méthode pour configurer l'affichage de select_multiple (widget)
     */
    
    function init_select(&$form = null, &$dnu1 = null, $maj, $dnu2 = null, $field, $sql,
                         $sql_by_id = "", $om_validite = false, $multiple = false) {

        // MODE AJOUTER et MODE MODIFIER
        if ($maj < 2) {
            // Exécution de la requête
            $res = $this->f->db->query($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // Vérification d'une éventuelle erreur de base de données
            $this->f->isDatabaseError($res);
            // Initialisation du select
            $contenu = array();
            $contenu[0][0] = '';
            $contenu[1][0] = _('choisir')."&nbsp;"._($field);
            //
            $k=1;
            while($row =& $res->fetchRow()){
                $contenu[0][$k] = $row[0];
                $contenu[1][$k] = $row[1];
                $k++;
            }

            // Si en mode "modifier" et si la gestion des dates de validité est activée
            if ($maj == 1 && $om_validite == true) {
                $field_values = array();
                // Dans le cas d'un select_multiple
                if ($multiple == true) {
                    $field_values = explode(";", $this->form->val[$field]);
                }
                // Dans le cas d'un select simple
                else {
                    $field_values = array($this->form->val[$field],);
                }
                // S'il y a une ou plusieurs valeurs
                if (!empty($field_values) && $field_values[0] != '') {
                    // pour chacune d'entre elles
                    foreach ($field_values as $field_value) {
                        // si elle manque au contenu du select
                        if (!in_array($field_value, $contenu[0])) {
                            // on l'ajoute
                            $this->getSelectOldValue($form, $maj, $this->f->db, $contenu,
                                                     $sql_by_id, $field, $field_value);
                        }
                    }
                }
                // S'il n'y a pas de valeur c'est que soit :
                // - aucune valeur n'est présaisie en première validation,
                // - le formulaire a été validé en erreur.
                // C'est ce dernier cas qui nous intéresse afin de ne pas perdre
                // dans le contenu une valeur invalide pourtant sélectionnée.
                // Si elle n'a pas été sélectionnée elle est dans tous les cas
                // perdue, il faut recharger le formulaire pour la récupérer.
                else {
                    // On vérifie si le formulaire est vide : si oui
                    // cela signifie que le formulaire a été validé en erreur
                    $empty = true;
                    foreach ($this->form->val as $f => $value) {
                        if (!empty($value)) {
                            $empty = false;
                        }
                    }
                    // Déclaration des valeurs postées
                    $field_posted_values = array();
                    // Dans le cas d'un select_multiple avec des valeurs postées
                    if ($multiple == true && isset($_POST[$field])) {
                        $field_posted_values = $_POST[$field];
                    }
                    // Dans le cas d'un select simple avec une valeur postée
                    elseif (isset($_POST[$field])) {
                        $field_posted_values = array($_POST[$field],);
                    }
                    // S'il y a une ou plusieurs valeurs postées
                    // et que le formulaire a déjà été validé
                    if ($empty == true && !empty($field_posted_values) && $field_posted_values[0] != '') {
                        // pour chacune d'entre elles
                        foreach ($field_posted_values as $field_posted_value) {
                            // si elle manque au contenu du select
                            if (!in_array($field_posted_value, $contenu[0])) {
                                // on l'ajoute
                                $this->getSelectOldValue($form, $maj, $this->f->db, $contenu,
                                                         $sql_by_id, $field, $field_posted_value);
                            }
                        }
                    }
                }
            }
            // Initialisation des options du select dans le formulaire
            $form->setSelect($field, $contenu);
            // Logger
            $this->addToLog(__METHOD__."(): form->setSelect(\"".$field."\", ".print_r($contenu, true).");", EXTRA_VERBOSE_MODE);
        }

        // MODE SUPPRIMER et MODE CONSULTER
        if ($maj >= 2) {
            // Initialisation du select
            $contenu[0][0] = '';
            $contenu[1][0] = '';

            if (isset($this->form->val[$field]) and
                !empty($this->form->val[$field]) and $sql_by_id) {
                // Dans le cas d'un select_multiple
                if ($multiple == true) {
                    // Permet de gérer le cas ou les clés primaires sont alphanumériques
                    $val_field = "'".str_replace(";", "','",$this->form->val[$field])."'";
                } else {
                    $val_field = $this->form->val[$field];
                }
                // ajout de l'identifiant recherche a la requete
                $sql_by_id = str_replace('<idx>', $val_field, $sql_by_id);
                // Exécution de la requête
                $res = $this->f->db->query($sql_by_id);
                // Logger
                $this->addToLog(__METHOD__."(): db->query(".$sql_by_id.");", VERBOSE_MODE);
                // Vérification d'une éventuelle erreur de base de données
                $this->f->isDatabaseError($res);
                // Affichage de la première ligne d'aide à la saisie
                $row =& $res->fetchRow();
                $contenu[0][0] = $row[0];
                $contenu[1][0] = $row[1];
                //
                $k=1;
                while($row =& $res->fetchRow()){
                    $contenu[0][$k] = $row[0];
                    $contenu[1][$k] = $row[1];
                    $k++;
                }
            }

            $form->setSelect($field, $contenu);
            // Logger
            $this->addToLog(__METHOD__."(): form->setSelect(\"".$field."\", ".print_r($contenu, true).");", EXTRA_VERBOSE_MODE);
        }
    }
    
    /**
     * Cette methode est à surcharger elle permet de tester dans chaque classe
     * des droits des droits spécifiques en fonction des données
     */
    function canAccess() {
        return true;
    }
    
    /**
     * Appelle la méthode canAccess() et affiche ou non une erreur
     */
    function checkAccessibility() {
        //Test les droits d'accès à l'élément.
        if(!$this->canAccess()
            || !$this->checkActionAvailability()) {
            //
            $this->addToLog(__METHOD__."(): acces non autorise", EXTRA_VERBOSE_MODE);
            //
            if ($this->f->isAjaxRequest() == false) {
                $this->f->setFlag(NULL);
                $this->f->display();
            }
            //
            $message_class = "error";
            $message = _("Droits insuffisants. Vous n'avez pas suffisamment de ".
                    "droits pour acceder a cette page.");
            $this->f->displayMessage($message_class, $message);
            // Arrêt du script
            die();
        }
    }

    /**
     * Méthode permettant de récupérer la valeur d'un élément de $this->val
     * en lui passant en paramètre le nom du champ
     **/
    function getVal($champ) {
        if(isset($this->val[array_search($champ,$this->champs)])) {
            return $this->val[array_search($champ,$this->champs)];
        }
        return "";
    }
    
    /**
     * Vérification de la disponibilité de l'action sur l'objet.
     *
     * Le postulat est que les actions ajouter, modifier, supprimer et
     * consulter sont disponibles sur tous les objets. La disponibilité des
     * autres actions est vérifiée si la valeur de l'action existe comme clé
     * dans l'attribut actions de l'objet.
     *
     * @return boolean
     */
    function checkActionAvailability() {

        // Test si l'action à déjà été défini
        if ($this->_is_action_available != null) {
            // Si oui on retourne la valeur précédement définie
            return $this->_is_action_available;
        }

        // Vérification de l'existance d'une action définie dans les attributs
        // de l'objet
        if (($this->is_action_defined($this->getParameter("maj")) === false and
            $this->is_option_class_action_activated()===true) or
            $this->is_action_condition_satisfied($this->getParameter("maj")) === false) {
            // Ajout des logs
            $this->addToLog(
                __METHOD__."(): action non disponible",
                EXTRA_VERBOSE_MODE
            );
            // Message d'erreur affiché à l'utilisateur
            $message = _("Cette action n'est pas disponible.");
            $this->addToMessage($message);
            // Message en rouge
            $this->correct = false;
            // Flag action dispo à false
            $this->_is_action_available = false;
        } else {
            // Flag action dispo à true
            $this->_is_action_available = true;
        }
        //
        return $this->_is_action_available;
    }

    /**
     * Indique si on se trouve dans le contexte d'une clé étrangère.
     *
     * Lorsque l'on se trouve dans un sous formulaire, les champs qui sont
     * liés à l'objet du formulaire principal (clé étrangère) doivent avoir 
     * un comportement spécifique. La classe du formulaire principal peut 
     * facilement être surchargée, il est donc nécessaire de modifier tous 
     * ces comportements spécifiques pour y ajouter le nom de la classe qui
     * surcharge l'objet principal. Cette méthode permet de faciliter la 
     * vérification.
     *
     * @param string $foreign_key Table de la clé étrangère.
     * @param string $context     Valeur du contexte (retourformulaire) qui doit 
     *                            être vérifiée.
     *
     * @return bool
     */
    function is_in_context_of_foreign_key($foreign_key = "", $context = "") {
        // Si la liste n'existe pas ou n'est pas un tableau
        // ou si la valeur n'est pas dans la liste
        if (!isset($this->foreign_keys_extended[$foreign_key])
            || !is_array($this->foreign_keys_extended[$foreign_key])
            || !in_array($context, $this->foreign_keys_extended[$foreign_key])) {
            // On ne se trouve pas dans le contexte
            return false;
        } else {
            // Sinon on se trouve dans le contexte
            return true;
        }
    }

    // {{{ Méthodes utilitaires de gestion des dates

    /**
     *
     */
    function dateDB($val) {

        // ======================================================================
        // fonction de traitement de date qui est saisie sous la forme JJ/MM/AAAA
        // dateSyspmePHP renvoie la date systeme au format de la base de donnees
        // dateDB met la date au format de la base de donnees
        // datePHP met la date au format PHP
        // anneePHP, moisPHP, jourPHP controle les dates affichees et transforme
        // en date en format PHP pour les annee mois et jour
        // heureDB controle l heure saisie
        // ======================================================================
        // ============================================================
        // transforme les dates affichees en date pour base de donnees
        // ============================================================
        if($val == "") return "";
        $dateOK=1; // Flag sur longueur de date annee mois jour
        $date = explode("/", $val);
        // annee est sur 2 caractere / 1 caractere
        if ($date==""){
           if  (strlen($date[2]) ==2) $date[2]="20".$date[2] ;
           if  (strlen($date[2]) ==1) $date[2]="200".$date[2] ;
           if  (strlen($date[2]) !=4){
               echo "faux";
               $dateOK=0;
           }
        // mois sur un caractere
           if  (strlen($date[1]) ==1) $date[1]="0".$date[1] ;
           if  (strlen($date[1]) !=2){
               $dateOK=0;
           }
        }
        // Jour sur 1 caractere
        if  (strlen($date[0]) ==1) $date[0]="0".$date[0] ;
        if  (strlen($date[0]) !=2){
           $dateOK=0;
        }
        // Transforme la date suivant parametre date => connexion
        if ($dateOK==1){
           if (FORMATDATE=="AAAA-MM-JJ"){
           // controle de date
              if (sizeof($date) == 3 and (checkdate($date[1],$date[0],$date[2])or $val=="00/00/0000")) {
                 return $date[2]."-".$date[1]."-".$date[0];
              }else{
                  $this->msg= $this->msg."<br>la date ".$val." n'est pas une date";
                  if($this->correct==true) $this->correct=false;
              }
           }
           if (FORMATDATE=="JJ/MM/AAAA"){
           // controle de date
              if (sizeof($date) == 3  and checkdate($date[1],$date[0],$date[2])){
                 return $date[0]."/".$date[1]."/".$date[2];
              }else{
                  $this->msg= $this->msg."<br>la date ".$val." n'est pas une date";
                  $this->correct=false;
              }
           }
        }else{
              // Format de saisie mauvais
              $this->msg= $this->msg."<br>la date ".$val." n'est pas une date";
              $this->correct=false;
        }

    }

    /**
     *
     */
    function heureDB($val) {

        // =====================================================================
        // controle du champs heure saisi 00 ou 00:00 ou 00:00:00
        // =====================================================================
        // pb saisie H et h **************
        $val = str_replace("H",":",$val);
        $val = str_replace("h",":",$val);
        // ================================
        $heure = explode(":", $val);
           if (sizeof($heure) >= 1 or sizeof($heure) <= 3 ) {
              If (sizeof($heure) ==1 and $heure[0]>=0 and $heure[0] <= 23)
                 return $heure[0].":00:00";
              If (sizeof($heure) ==2 and $heure[0]>=0 and $heure[0] <= 23 and $heure[1]>=0 and $heure[1] <= 59)
                 return $heure[0].":".$heure[1].":00";
              If (sizeof($heure) ==3 and $heure[0]>=0 and $heure[0] <= 23 and $heure[1]>=0 and $heure[1] <= 59 and $heure[2]>=0 and $heure[2] <= 59)
                 return $heure[0].":".$heure[1].":".$heure[2];
           }
               $this->msg= $this->msg."<br>l heure ".$val." n'est pas une heure";
               $this->correct=false;

    }

    /**
     *
     */
    function dateSystemeDB() {

        // =======================================================================
        // mise au format base de donnees de la date systeme
        // =======================================================================
        if (FORMATDATE=="AAAA-MM-JJ")
              return date('Ymd');
        if (FORMATDATE=="JJ/MM/AAAA")
            return date('d/m/y');

    }

    /**
     * Cette methode permet de verifier la validite d'une date et de la
     * retourner sous le format 'AAAA-MM-JJ'
     *
     * @param string $val Date saisie au format 'JJ/MM/AAAA'
     * @return mixed
     */
    function datePHP($val) {

        // On explose la date pour en extraire ses trois elements (jour, mois,
        // annee)
        $date = explode("/", $val);
        // Verification de la validite de la date, c'est-a-dire qu'elle
        // comporte trois elements (jour, mois, annee) et qu'elle existe
        // dans le calendrier gregorien
        if (sizeof($date) == 3 and checkdate($date[1], $date[0], $date[2])) {
            // Retour de la date au format 'AAAA-MM-JJ'
            return $date[2]."-".$date[1]."-".$date[0];
        } else {
            // La date n'est pas valide donc on positionne le flag $correct a
            // false et on decrit l'erreur dans $msg
            $this->correct = false;
            $this->msg .= "<br/>";
            $this->msg .= $val;
            $this->msg .= " "._("n'est pas une date valide");
            $this->msg .= " "._("[calcul date php]");
        }

    }

    /**
     * Cette methode permet de verifier la validite d'une date et d'en
     * retourner l'annee
     *
     * @param string $val Date saisie au format 'JJ/MM/AAAA'
     * @return mixed
     */
    function anneePHP($val) {

        // On explose la date pour en extraire ses trois elements (jour, mois,
        // annee)
        $date = explode("/", $val);
        // Verification de la validite de la date, c'est-a-dire qu'elle
        // comporte trois elements (jour, mois, annee) et qu'elle existe
        // dans le calendrier gregorien
        if (sizeof($date) == 3 and checkdate($date[1], $date[0], $date[2])) {
            // Retour de l'annee
           return $date[2];
        } else {
            // La date n'est pas valide donc on positionne le flag $correct a
            // false et on decrit l'erreur dans $msg
            $this->correct = false;
            $this->msg .= "<br/>";
            $this->msg .= $val;
            $this->msg .= " "._("n'est pas une date valide");
            $this->msg .= " "._("[calcul annee php]");
        }

    }

    /**
     * Cette methode permet de verifier la validite d'une date et d'en
     * retourner le mois
     *
     * @param string $val Date saisie au format 'JJ/MM/AAAA'
     * @return mixed
     */
    function moisPHP($val) {

        // On explose la date pour en extraire ses trois elements (jour, mois,
        // annee)
        $date = explode("/", $val);
        // Verification de la validite de la date, c'est-a-dire qu'elle
        // comporte trois elements (jour, mois, annee) et qu'elle existe
        // dans le calendrier gregorien
        if (sizeof($date) == 3 and checkdate($date[1], $date[0], $date[2])) {
            // Retour du mois
           return $date[0];
        } else {
            // La date n'est pas valide donc on positionne le flag $correct a
            // false et on decrit l'erreur dans $msg
            $this->correct = false;
            $this->msg .= "<br/>";
            $this->msg .= $val;
            $this->msg .= " "._("n'est pas une date valide");
            $this->msg .= " "._("[calcul mois php]");
        }

    }

    /**
     * Cette methode permet de verifier la validite d'une date et d'en
     * retourner le jour
     *
     * @param string $val Date saisie au format 'JJ/MM/AAAA'
     * @return mixed
     */
    function jourPHP($val) {

        // On explose la date pour en extraire ses trois elements (jour, mois,
        // annee)
        $date = explode("/", $val);
        // Verification de la validite de la date, c'est-a-dire qu'elle
        // comporte trois elements (jour, mois, annee) et qu'elle existe
        // dans le calendrier gregorien
        if (sizeof($date) == 3 and checkdate($date[1], $date[0], $date[2])) {
            // Retour du jour
           return $date[1];
        } else {
            // La date n'est pas valide donc on positionne le flag $correct a
            // false et on decrit l'erreur dans $msg
            $this->correct = false;
            $this->msg .= "<br/>";
            $this->msg .= $val;
            $this->msg .= " "._("n'est pas une date valide");
            $this->msg .= " "._("[calcul jour php]");
        }

    }

    /**
     * Méthode pour convertir une date Y-m-d en d/m/Y
     */
    function dateDBToForm($date) {
        if($date == "") {
            return "";
        }
        $dateFormat = new DateTime($date);
        return $dateFormat->format('d/m/Y');
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

    /**
     *
     */
    function __destruct() {
        // Logger
        $this->addToLog(__METHOD__."()", VERBOSE_MODE);
    }

    // EDITIONS

    /**
     * Récupération des champs de fusion pour l'édition ou l'aide à la saisie
     *
     * @param  type  integer valeurs ou libellés
     * @return array         tableau associatif
     */
    function get_merge_fields($type) {
        // selon que l'on souhaite récupérer les valeurs ou les libellés
        switch ($type) {
            case 'values':
                return $this->get_values_merge_fields();
                break;
            case 'labels':
                return $this->get_labels_merge_fields();
                break;
            default:
                return array();
                break;
        }
    }

    /**
     * Récupération des valeurs des champs de fusion
     * 
     * @return array         tableau associatif
     */
    function get_values_merge_fields() {
        // récupération de la table de la classe instanciée
        $table = $this->table;
        $classe = get_class($this);
        // récupération des clés étrangères
        $foreign_keys = array();
        foreach ($this->foreign_keys_extended as $foreign_key => $values) {
            $foreign_keys[] = $foreign_key;
        }
        // Inclusion du fichier de requêtes
        if (file_exists("../sql/".OM_DB_PHPTYPE."/".$classe.".form.inc.php")) {
            include "../sql/".OM_DB_PHPTYPE."/".$classe.".form.inc.php";
        } elseif (file_exists("../sql/".OM_DB_PHPTYPE."/".$table.".form.inc")) {
            include "../sql/".OM_DB_PHPTYPE."/".$table.".form.inc";
        }
        // initialisation du tableau de valeurs
        $values = array();
        // pour chaque champ de l'objet on crée un champ de fusion
        foreach ($this->champs as $key => $champ) {
            // récupération de la valeur
            $value = $this->getVal($champ);
            // si c'est un booléen on remplace par oui/non
            if ($this->type[$key] == 'bool') {
                switch ($value) {
                    case 't':
                    case 'true':
                    case 1:
                        $value = _("oui");
                        break;
                    case 'f':
                    case 'false':
                    case 0:
                        $value = _("non");
                        break;
                }
            }
            // si c'est une date anglosaxonne on la formate en FR
            if (DateTime::createFromFormat('Y-m-d', $value) !== FALSE) {
                $dateFormat = new DateTime($value);
                $value = $dateFormat->format('d/m/Y');
            }
            // si c'est une clé étrangère avec une valeur valide
            // on remplace par le libellé
            if (in_array($champ, $foreign_keys)
                && $value != null && $value != '') {
                // construction variable sql
                $var_sql = "sql_".$champ."_by_id";
                // si la variable existe
                if (isset($$var_sql)) {
                    // remplacement de l'id par sa valeur dans la condition
                    $sql = str_replace('<idx>', $value, $$var_sql);
                    // exécution requete
                    $res = $this->f->db->query($sql);
                    $this->f->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
                    // Si la récupération de la description de l'avis échoue
                    if ($this->f->isDatabaseError($res, true)) {
                        // Appel de la methode de recuperation des erreurs
                        $this->erreur_db($res->getDebugInfo(), $res->getMessage(), '');
                        $this->correct = false;
                        return false;
                    }
                    $row = &$res->fetchRow();
                    // récupération libellé
                    $value = $row[1];
                }
            }
            $values[$table.".".$champ] = $value;
        }
        return $values;
    }

    /**
     * Récupération des libellés des champs de fusion
     * 
     * @return array         tableau associatif
     */
    function get_labels_merge_fields() {
        // récupération de la table de la classe instanciée
        $table = $this->table;
        // récupération du nom de la clé primaire
        $clePrimaire = _($this->clePrimaire);
        // initialisation du tableau de libellés
        $labels = array();
        // pour chaque champ de l'objet on crée un champ de fusion
        foreach ($this->champs as $key => $champ) {
            $labels[$clePrimaire][$table.".".$champ] = _($champ);
        }
        return $labels;
    }
    
    /**
     * 
     * @return boolean
     */
    function is_option_class_action_activated() {
        
        // Option activée, le !== false est nécessaire pour que l'option soit activée
        // même si le paramètre global n'est pas défini
        if($this->f->getParameter("activate_class_action") !== false){
            return true;
        }
        // Permet de pouvoir utiliser les nouvelles actions que sur certains objets
        if(isset($this->activate_class_action) && $this->activate_class_action === true){
            return true;
        }
        return false;
    }
}

?>
