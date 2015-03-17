<?php
/**
 * Ce fichier contient ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_application.class.php 3062 2015-02-17 14:26:17Z nhaye $
 */

/**
 *
 */
(defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
require_once PATH_OPENMAIRIE."om_locales.inc.php";
require_once PATH_OPENMAIRIE."om_debug.inc.php";
(defined("DEBUG") ? "" : define("DEBUG", PRODUCTION_MODE));
require_once PATH_OPENMAIRIE."om_logger.class.php";
require_once PATH_OPENMAIRIE."om_message.class.php";
require_once PATH_OPENMAIRIE."om_filestorage.class.php";
require_once PATH_OPENMAIRIE."om_layout.class.php";

/**
 *
 */
class application {

    // {{{ VARs

    // {{{ DATABASE

    /**
     * Cette variable est un tableau associatif. Ce tableau permet de stocker
     * toutes les configurations de bases de donnees presentes dans le fichier
     * de configuration. Chaque connexion est representee par une cle de ce
     * tableau.
     * @var array
     */
    var $database = array();

    /**
     * Cette variable ...
     * @var array
     */
    var $database_config = array();

    /**
     * Cette variable est l'objet renvoye par la connexion a la base de donnees
     * @var resource
     */
    var $db = NULL;

    // }}}

    // {{{ DIRECTORY

    /**
     * Cette variable est un tableau associatif. Ce tableau permet de stocker
     * toutes les configurations d'annauires presentes dans le fichier de
     * configuration. Chaque connexion est representee par une cle de ce
     * tableau.
     * @var array
     */
    var $directory = array();

    /**
     * Cette variable ...
     * @var array
     */
    var $directory_config = array();

    /**
     * Cette variable est l'objet renvoye par la connexion a l'annuaire
     * @var resource
     */
    var $dt = NULL;

    /**
     * Contient le profil par defaut des utilisateurs ajoutes depuis l'annuaire
     * Cette variable peut etre surchargee par le parametrage du fichier
     * dyn/directory.inc.php
     * 
     * @var integer
     */
    var $default_om_profil = 1;

    // }}}

    // {{{ ENVOI DE MAIL

    /**
     * Cette variable est un tableau associatif. Ce tableau permet de stocker
     * toutes les configurations de serveur de mail presentes dans le fichier
     * de configuration. Chaque serveur est represente par une cle de ce
     * tableau.
     * @var array
     */
    var $mail = array();

    var $mail_config = array();

    // }}}

    
    // {{{ CREATION DE FILESTORAGE

    /**
     * Cette variable est un tableau associatif. Ce tableau permet de stocker
     * toutes les configurations de stockage des fichiers présentes dans le fichier
     * de configuration. Chaque configuration est représenté par une clé de ce
     * tableau.
     * @var array
     */
    var $filestorage = array();

    var $filestorage_config = array();

    // }}}


    // {{{ AUTHENTIFICATION ET GESTION DES ACCES AUX PAGES SPECIALES

    /**
     * Cette variable permet de definir la liste des flags
     * speciaux.
     *
     *   login      [OBLIGATOIRE]-> permet de s'authentifier
     *   logout     [OBLIGATOIRE]-> permet de se déconnecter
     *
     * @var array
     */
    var $special_flags = array(
        "login",
        "logout",
    );

    // }}}

    // {{{

    var $table_om_droit = "om_droit";
    var $table_om_droit_field_id = "om_droit";
    var $table_om_droit_field_libelle = "libelle";
    var $table_om_droit_field_om_profil = "om_profil";
    var $table_om_utilisateur = "om_utilisateur";
    var $table_om_utilisateur_field_id = "om_utilisateur";
    var $table_om_utilisateur_field_om_collectivite = "om_collectivite";
    var $table_om_utilisateur_field_om_profil = "om_profil";
    var $table_om_utilisateur_field_om_type = "om_type";
    var $table_om_utilisateur_field_login = "login";
    var $table_om_utilisateur_field_password = "pwd";
    var $table_om_utilisateur_field_nom = "nom";
    var $table_om_utilisateur_field_email = "email";
    var $table_om_profil = "om_profil";
    var $table_om_profil_field_id = "om_profil";
    var $table_om_profil_field_libelle = "libelle";
    var $table_om_profil_field_hierarchie = "hierarchie";
    var $table_om_collectivite = "om_collectivite";
    var $table_om_collectivite_field_id = "om_collectivite";
    var $table_om_collectivite_field_niveau = "niveau";
    var $table_om_password_reset = "om_password_reset";

    // }}}

    /**
     * @var string
     */
    var $authentication_message = "";

    /**
     * @var string
     */
    var $phptype = NULL;

    /**
     * @var string
     */
    var $formatdate = NULL;





    // {{{

    /**
     *
     * @var string
     *  - $flag = NULL; =>
     *  - $flag = "nodoctype"; =>
     *  - $flag = "nohtml"; =>
     *  - $flag = "htmlonly"; =>
     *  - $flag = "htmlonly_nodoctype"; =>
     *  - $flag = "login"; =>
     *  - $flag = "logout"; =>
     *  - $flag = "password_reset" =>
     */
    var $flag = NULL;

    /**
     *
     */
    var $title = NULL;

    /**
     *
     */
    var $right = NULL;

    /**
     *
     */
    var $icon = NULL;

    /**
     *
     */
    var $help = NULL;

    /**
     *
     */
    var $description = "";

    // }}}

    // {{{

    /**
     *
     */
    var $menu = array();

    /**
     *
     */
    var $actions = array();

    /**
     *
     */
    var $shortlinks = array();

    /**
     *
     */
    var $footer = array();

    /**
     *
     * @var array
     */
    var $config = array();

    /**
     *
     * @var array
     */
    var $custom = array();

    /**
     *
     * @var string
     */
    var $version = NULL;

    // }}}

    // {{{

    /**
     *
     * @var array
     */
    var $message = array();

    // }}}

    // {{{

    /**
     * Cet attribut nous permet de stocker le nombre de rubriques dans le menu.
     * L'objectif est d'ajouter une classe css au contenu pour permettre un
     * affichage correct en pleine largeur de la page si il n'y a aucune
     * rubrique dans le menu (égale à 0).
     *
     * @var mixed
     */
    var $nomenu = NULL;


    // }}}

    // {{{

    /**
     *
     */
    var $url_dashboard = "../scr/dashboard.php";

    /**
     *
     */
    var $url_password_reset = "../scr/password_reset.php";

    // }}}

    // {{{

    /**
     *
     * @var boolean
     */
    var $authenticated = false;

    /**
     *
     */
    var $collectivite;

    /**
     *
     */
    var $rights = array();

    // }}}

    var $timestart = NULL;


    // Valeurs postées
    var $submitted_post_value;

    // Valeurs passées à l'url
    var $submitted_get_value;


    // }}}

    // {{{ construct & destruct

    /**
     * Constructeur
     *
     * @param
     * @param
     * @param
     * @param
     * @param
     */
    function __construct($flag = NULL, $right = NULL, $title = NULL, $icon = NULL, $help = NULL) {

        //
        $this->timestart = microtime(true);

        // Logger
        $this->addToLog(__METHOD__."()", VERBOSE_MODE);

        // XXX  Faire la gestion correcte du paramétrage du layout
        $this->layout = new layout("jqueryui");
        if (!is_null($this->layout->error)) {
            echo "error : ".$this->layout->error;
            die();
        }

        //
        $this->setParamsFromFiles();
        $this->checkParams();

        //
        $this->setDefaultValues();

        // Transformation des cinq éléments paramètres en attribut de l'objet
        $this->setFlag($flag);
        $this->setTitle($title);
        $this->setRight($right);
        $this->setHelp($help);
        $this->setIcon($icon);

        // Vérification de l'authentification de l'utilisateur et stockage du
        // résultat en attribut de l'objet
        $this->authenticated = $this->isAuthenticated();

        // XXX  Faire la gestion correcte du paramétrage du layout
        if (isset($_GET["layout"])) {
            $_SESSION["layout"] = $_GET["layout"];
        } elseif (isset($_SESSION["layout"])) {
            $_SESSION["layout"] = $_SESSION["layout"];
        } else {
            $_SESSION["layout"] = "jqueryui";
        }
        $this->layout = new layout($_SESSION["layout"]);
        if (!is_null($this->layout->error)) {
            echo "error : ".$this->layout->error;
            die();
        }

        // Déconnexion de l'utilisateur
        if ($this->flag == "logout") {
            $this->logout();
        }

        // Connexion de l'utilisateur
        if ($this->flag == "login") {
            $this->login();
        }

        // Demande de redéfinition du mot de passe
        if ($this->flag == "password_reset") {
            if ($this->authenticated) {
                $this->redirectAuthenticatedUsers();
            }
        }

        //
        if ($this->authenticated) {
            // Connexion à la base de données si l'utilisateur est authentifié
            $this->connectDatabase();
            // on verifie que l'utilisateur connecté est toujours valide
            $this->checkIfUserIsAlwaysValid();
        }

        // Instanciation du mode de stockage des fichiers
        // Il est important d'appeler cette méthode après la mise en place de
        // la session sinon la méthode ne peut pas trouver le path par défaut
        // et après la méthode connectDatabase sinon on ne trouve pas la
        // configuration de la base sélectionnée
        $this->setFilestorage();

        //
        if (!in_array($this->flag, $this->special_flags)) {

            //
            $this->getAllRights();

            //
            $this->getCollectivite();

            //
            $this->isAuthorized();

        }

        //
        $this->set_submitted_value();

        //
        $this->setMoreParams();

        // Affichage HTML
        $this->display();

    }

    function elapsedtime() {
        return number_format((microtime(true) - $this->timestart), 3);
    }

    /**
     * Desctructeur de la classe, cette methode (appelee automatiquement)
     * permet d'afficher le footer de la page, le footer HTML, et de
     * deconnecter la base de donnees
     *
     * @return void
     */
    function __destruct() {

        // Footer
        $this->displayFooter();

        // Deconnexion SGBD
        $this->disconnectDatabase();

        // Logger
        $this->addToLog(__METHOD__."()", VERBOSE_MODE);

        // Affichage des logs à l'écran
        logger::instance()->displayLog();
        // Écriture des erreurs (log de type DEBUG) dans le fichier d'erreurs
        logger::instance()->writeErrorLogToFile();

        // Footer HTML
        $this->displayHTMLFooter();

    }

    // }}}

    // {{{

    /**
     * Permet de récupérer les différents fichiers de configuration.
     *
     * Cette méthode inclut les différents fichiers de configuration présents 
     * dans le répertoire dyn/ de l'application pour charger le contenu de
     * la configuration dans des attributs de la classe et pouvoir les utiliser
     * à tout moment dans les différentes méthodes de la classe.
     *
     * @return void
     */
    function setParamsFromFiles() {

        //
        if (file_exists("../dyn/custom.inc.php")) {
            include("../dyn/custom.inc.php");
        }
        if (isset($custom)) {
            $this->custom = $custom;
        }

        //
        if (file_exists("../dyn/config.inc.php")) {
            include("../dyn/config.inc.php");
        }
        if (isset($config)) {
            $this->config = $config;
        }

        //
        if (file_exists("../dyn/database.inc.php")) {
            include("../dyn/database.inc.php");
        }

        if (isset($conn)) {
            $this->conn = $conn;
            //
            foreach($this->conn as $key => $conn) {
                $this->database[$key] = array(
                    'title' => $conn[0],
                    'phptype' => $conn[1],
                    'dbsyntax' => $conn[2],
                    'username' => $conn[3],
                    'password' => $conn[4],
                    'protocol' => $conn[5],
                    'hostspec' => $conn[6],
                    'port' => $conn[7],
                    'socket' => $conn[8],
                    'database' => $conn[9],
                    'formatdate' => $conn[10],
                    'schema' => $conn[11],
                    'prefixe' => (isset($conn[12]) ? $conn[12]: ""),
                    'directory' => (isset($conn[13]) ? $conn[13]: ""),
                    'mail' => (isset($conn[14]) ? $conn[14]: ""),
                    'filestorage' => (isset($conn[15]) ? $conn[15]: ""),
                );
            }
        }

        // Trie le tableau
        ksort($this->database);

        //
        if (file_exists("../dyn/directory.inc.php")) {
            include("../dyn/directory.inc.php");
        }
        if (isset($directory)) {
            $this->directory = $directory;
        }

        //
        if (file_exists("../dyn/mail.inc.php")) {
            include("../dyn/mail.inc.php");
        }
        if (isset($mail)) {
            $this->mail = $mail;
        }

        //
        if (file_exists("../dyn/menu.inc.php")) {
            include("../dyn/menu.inc.php");
        }
        if (isset($menu)) {
            $this->menu = $menu;
        }

        //
        if (file_exists("../dyn/version.inc.php")) {
            include("../dyn/version.inc.php");
        }
        if (isset($version)) {
            $this->version = $version;
        }

        //
        if (file_exists("../dyn/filestorage.inc.php")) {
            include("../dyn/filestorage.inc.php");
        }
        if (isset($filestorage)) {
            $this->filestorage = $filestorage;
        }

    }

    /**
     * Cette methode permet de parametrer les valeurs par defaut pour les
     * fichiers css et javascript a appeler sur toutes les pages
     *
     * @return void
     */
    function setDefaultValues() {
        //
        //$js = array(
        //        "../js/iepngfix_tilebg.js",
        //        );
        //$this->addHTMLHeadJs($js, "begin");
        //$js = array(
        //        );
        //$this->addHTMLHeadJs($js, "middle");
        //$js = array(
        //        );
        //$this->addHTMLHeadJs($js, "end");

        // Pour garder la compatibilite avec l'ancien emplacement du dossier
        // om-theme on teste si le nouvel emplacement existe, si il n'existe
        // pas on appelle les fichiers dans l'ancien emplacement.
        if (file_exists("../om-theme/om.css")) {
            $css = array(
                "../css/main.css",
                "../lib/jquery-thirdparty/jquery-minicolors/jquery.minicolors.css",
                "../om-theme/jquery-ui-theme/jquery-ui.custom.css",
                "../om-theme/om.css",
                "../app/css/app.css",
            );
        } else {
            $css = array(
                "../css/main.css",
                "../lib/jquery-thirdparty/jquery-minicolors/jquery.minicolors.css",
                "../lib/om-theme/jquery-ui-theme/jquery-ui.custom.css",
                "../lib/om-theme/om.css",
                "../app/css/app.css",
            );
        }
        $this->setHTMLHeadCss($css);

        $extras = "\t<link rel=\"stylesheet\" type=\"text/css\" media=\"print\" href=\"../css/print.css\" />\n";
        $this->setHTMLHeadExtras($extras);

    }

    /**
     * Cette methode permet d'affecter des parametres dans un attribut de
     * l'objet.
     *
     * @return void
     */
    function setMoreParams() {

        //
        if (file_exists("../dyn/var.inc")) {
            include_once("../dyn/var.inc");
        }

        //
        if (isset($chemin_plan)) {
            $this->config['chemin_plan'] = $chemin_plan;
        } else {
            $this->config['chemin_plan'] = "../trs/";
        }

    }

    function triggerAfterLogin($utilisateur = NULL) {}
    function checkParams() {
        (isset($this->config['session_name']) ? "" : $this->config['session_name'] = "openmairie");
        (isset($this->config['application']) ? "" : $this->config['application'] = _("openMairie"));
        (isset($this->config['title']) ? "" : $this->config['title'] = _("openMairie"));
        (isset($this->config['demo']) ? "" : $this->config['demo'] = false);
        (isset($this->config['upload_extension']) ? "" : $this->config['upload_extension'] = ".gif;.jpg;.jpeg;.png;.txt;.pdf;.csv;");
        (isset($this->config['upload_taille_max']) ? "" : $this->config['upload_taille_max'] = str_replace('M', '', ini_get('upload_max_filesize')) * 1024);
        (isset($this->config['dashboard_nb_column']) ? "" : $this->config['dashboard_nb_column'] = 3);
        (isset($this->config['permission_if_right_does_not_exist']) ? "" : $this->config['permission_if_right_does_not_exist'] = false);
        (isset($this->config['permission_by_hierarchical_profile']) ? "" : $this->config['permission_by_hierarchical_profile'] = true);

        // Active ou desactive la redefinition du mot de passe
        if (isset($this->config['password_reset']) and $this->config['password_reset'] == true) {
            array_push($this->special_flags, "password_reset");
        }
    }

    /**
     *
     */
    function get_custom($type = null, $elem = null) {
        //
        if ($type == "path") {
            //
            if (isset($this->custom[$elem."_dir"])) {
                return $this->custom["root"].$this->custom[$elem."_dir"];
            }
            return;
        }
        //
    }

    // }}}

    // {{{

    /**
     *
     * @return void
     */
    function goToDashboard() {

        //
        header("location: ".$this->url_dashboard."");

    }

    // }}}

    // {{{ AUTHENTICATION

    /**
     * Cette méthode permet de vérifier si l'utilisateur est authentifié ou
     * non à l'application et permet d'agir en conséquence
     *
     * @return boolean
     */
    function isAuthenticated() {

        //
        session_name($this->config['session_name']);
        @session_start();

        // Valeur par defaut de la cle du tableau de parametrage de la base de
        // donnees
        if (!isset($_SESSION['coll']) or
            (isset($_SESSION['coll']) and
             !isset($this->database[$_SESSION['coll']]))) {
            //
            $keys = array_keys($this->database);
            asort($keys);
            $_SESSION['coll'] = $keys[0];
        }

        // L'utilisateur est authentifie
        if (isset($_SESSION['login']) and $_SESSION['login'] != "") {

            // L'utilisateur vient de s'identifier
            if (isset($_SESSION['justlogin']) && $_SESSION['justlogin'] == true) {

                //
                $class = "ok";
                $message = _("Votre session est maintenant ouverte.");
                $this->addToMessage($class, $message);

                //
                $_SESSION['justlogin'] = false;

            }

            //
            return true;

        }
        
        // Si l'utilisateur n'est pas authentifie alors on le redirige
        // vers la page de login
        $this->redirectToLoginForm();

        //
        return false;

    }

    /**
     * Cette méthode redirige vers le fichier index.php du dossier parent
     * si le fla de la page n'est pas special
     *
     * @return void
     */
    function redirectToLoginForm() {
        //
        if (!in_array($this->flag, $this->special_flags)) {
            //
            $came_from = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://":"http://").$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            //
            if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") {
                echo "<script type=\"text/javascript\">location = '../index.php';</script>";
                die();
            } else {
                header("location: ../index.php?came_from=".urlencode($came_from));
                die();
            }
        }
    }

    /**
     * Cette méthode permet de vérifier si l'utilisateur connecté est toujours
     * valide dans la base utilisateur, de lui mettre à jour son profil si c'est
     * le cas et de le déconnecter si il ne fait plus partie des utilisateurs
     * valides
     *
     * @return void
     */
    function checkIfUserIsAlwaysValid() {
        //
        $this->user_infos = $this->retrieveUserProfile($_SESSION["login"]);
        //
        if (empty($this->user_infos)) {
            //
            $this->logout();
            //
            $this->redirectToLoginForm();
        } else {
            //
            $_SESSION["profil"] = $this->user_infos[$this->table_om_profil_field_hierarchie];
        }
    }

    // }}}

    /**
     *
     */
    function isAjaxRequest() {
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") {
            return true;
        } else {
            return false;
        }
    }

    // {{{



    /**
     * Cette méthode permet de récupérer l'ensemble de la table om_droit pour
     * la stocker dans un attribut et faire les vérifications de sécurité plus
     * rapidement
     *
     * @return void
     */
    function getAllRights() {
        //
        $sql = "select ";
        $sql .= "".$this->table_om_droit.".".$this->table_om_droit_field_id." as table_om_droit_field_id, ";
        $sql .= "".$this->table_om_profil.".".$this->table_om_profil_field_id." as table_om_profil_field_id, ";
        $sql .= "".$this->table_om_droit.".".$this->table_om_droit_field_libelle." as table_om_droit_field_libelle, ";
        $sql .= "".$this->table_om_profil.".".$this->table_om_profil_field_libelle." as table_om_profil_field_libelle, ";
        $sql .= "".$this->table_om_profil.".".$this->table_om_profil_field_hierarchie." as table_om_profil_field_hierarchie ";
        $sql .= " from ".DB_PREFIXE.$this->table_om_droit." ";
        $sql .= " left join ".DB_PREFIXE.$this->table_om_profil." ";
        $sql .= " on ".$this->table_om_droit.".".$this->table_om_droit_field_om_profil."=".$this->table_om_profil.".".$this->table_om_profil_field_id." ";
        //
        if ($this->getParameter("permission_by_hierarchical_profile") === false) {
            //
            $sql .= " where ".$this->table_om_profil.".".$this->table_om_profil_field_id."=".$this->user_infos[$this->table_om_utilisateur_field_om_profil];
        }
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->isDatabaseError($res);
        while ($row =& $res->fetchrow(DB_FETCHMODE_ASSOC)) {
            $this->rights[$row["table_om_droit_field_libelle"]] = $row["table_om_profil_field_hierarchie"];
        }
        $res->free();
        //
        $this->addToLog(__METHOD__."(): \$this->rights = ".print_r($this->rights, true)."", EXTRA_VERBOSE_MODE);
    }

    /**
     * Cette méthode permet de vérifier si l'utilisateur est autorisé ou non à
     * accéder à un élément et permet d'agir en conséquence
     *
     * @param
     * @return mixed
     */
    function isAuthorized($obj = NULL, $operator = "AND") {

        //
        if ($obj == NULL) {
            $obj = $this->right;
        }
        //
        if ($obj == NULL) {
            return true;
        }

        // L'utilisateur n'est pas autorisé à accéder à l'élément
        if (!$this->isAccredited($obj, $operator)) {

            //
            $message_class = "error";
            $message = _("Droits insuffisants. Vous n'avez pas suffisamment de ".
                         "droits pour acceder a cette page.");
            $this->addToMessage($message_class, $message);

            //
            $this->setFlag(NULL);
            if (!defined('REST_REQUEST')) {
                $this->display();
            }

            // Arrêt du script
            die();

        }

        // L'utilisateur est autorisé à accéder à l'élément
        return true;

    }

    /**
     * Cette méthode permet de vérifier si l'utilisateur est autorisé ou non à
     * accéder à un élément
     *
     * @param
     * @param
     * @return boolean
     */
    function isAccredited($obj = NULL, $operator = "AND") {
        
        //
        $log = "isAccredited(): \$obj = ";

        //
        if (is_array($obj)) {
            //
            $log .= print_r($obj, true)." - \$operator = ".$operator;

            //
            if (count($obj) == 0) {
                $this->addToLog(__METHOD__."(): ".$log." => return ".($this->config['permission_if_right_does_not_exist'] == true ? "true" : "false"), EXTRA_VERBOSE_MODE);
                return $this->config['permission_if_right_does_not_exist'];
            }

            //
            $permission_temporary = NULL;
            foreach ($obj as $elem) {

                //
                if (!isset($this->rights[$elem])) {
                    $permission_to_apply = $this->config['permission_if_right_does_not_exist'];
                } else {
                    if ($this->rights[$elem] <= $_SESSION['profil']) {
                        $permission_to_apply = true;
                    } else {
                        $permission_to_apply = false;
                    }
                }
                //
                if ($permission_temporary == NULL) {
                    $permission_temporary = $permission_to_apply;
                } else {
                    if ($operator == "OR") {
                        $permission_temporary |= $permission_to_apply;
                    } else {
                        $permission_temporary &= $permission_to_apply;
                    }
                }

            }
            //
            $this->addToLog(__METHOD__."(): ".$log." => return ".($permission_temporary == true ? "true" : "false"), EXTRA_VERBOSE_MODE);
            return $permission_temporary;

        } else {
            //
            $log .= $obj." - \$operator = ".$operator;

            //
            if (!isset ($this->rights[$obj])) {
                $this->addToLog(__METHOD__."(): ".$log." => return ".($this->config['permission_if_right_does_not_exist'] == true ? "true" : "false"), EXTRA_VERBOSE_MODE);
                return $this->config['permission_if_right_does_not_exist'];
            }

            //
            if (isset ($this->rights[$obj])
                and $this->rights[$obj] <= $_SESSION['profil']) {
                //
                $this->addToLog(__METHOD__."(): ".$log." => return true", EXTRA_VERBOSE_MODE);
                return true;
            }

            //
            return false;

        }

    }

    /**
     *
     */

    function getCollectivite() {
        $sql = "select * from ".DB_PREFIXE."om_parametre  where om_collectivite=".$_SESSION['collectivite'];
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->isDatabaseError($res);
        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->collectivite[$row['libelle']] = $row['valeur'];
        }
        $res->free();
    }

    /**
     *
     */
    function getPathFolderTrs() {
        return "../trs/".$_SESSION["coll"]."/";
    }
    /**
     *
     */
    function setMailConfig() {
        //
        if (!isset($this->database_config["mail"]) || !isset($this->mail[$this->database_config["mail"]])) {
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR - "._("Aucune entree dans le fichier de configuration"), DEBUG_MODE);
            //
            $this->mail_config = false;
            //
            return false;
        }

        if (!isset($this->mail[$this->database_config["mail"]]["mail_host"])
            || empty($this->mail[$this->database_config["mail"]]["mail_host"]) ) {
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR - "._("Un nom d'hote est obligatoire"), DEBUG_MODE);
            //
            $this->mail_config = false;
            //
            return false;
        }

        if (!isset($this->mail[$this->database_config["mail"]]["mail_from"])
            || empty($this->mail[$this->database_config["mail"]]["mail_from"]) ) {
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR - "._("Une adresse d'expediteur est obligatoire"), DEBUG_MODE);
            //
            $this->mail_config = false;
            //
            return false;
        }

        //
        $this->mail_config = $this->mail[$this->database_config["mail"]];
        //
        return true;

    }

    /**
     *
     */
    function setDirectoryConfig() {

        //
        if (!isset($this->database_config["directory"]) || !isset($this->directory[$this->database_config["directory"]])) {
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR - "._("Aucune entree dans le fichier de configuration"), DEBUG_MODE);
            //
            $this->directory_config = false;
            //
            return false;
        }

        //
        $this->directory_config = $this->directory[$this->database_config["directory"]];
        //
        return true;

    }

    // {{{ filestorage
    
    /**
     * Cette fonction permet de choisir une configuration de stockage des fichiers
     * spécifique.
     * 
     * @return bool true si la configuration cherchée est trouvée, autrement false
     */
    function setFilestorageConfig() {
        // Si aucune configuration n'est définie pour le stockage
        if (!isset($this->database_config["filestorage"])
            || !isset($this->filestorage[$this->database_config["filestorage"]])) {
            // Logger
            $this->addToLog(__METHOD__."(): "._("Aucune entree dans le fichier de configuration"), EXTRA_VERBOSE_MODE);
            // On définit alors la configuration dépréciée pour obtenir le même
            // fonctionnement que celui de l'ancien système de stockage 
            $this->filestorage_config = array (
                "storage" => "deprecated",
                "path" => $this->getPathFolderTrs(),
                "temporary" => array(
                    "storage" => "filesystem", // l'attribut storage est obligatoire
                    "path" => "../tmp/", // le repertoire de stockage
                ),
            );
        } else {
            // On définit alors la configuration paramétrée
            $this->filestorage_config = $this->filestorage[$this->database_config["filestorage"]];

            // Vérification de la clé temporary
            if(!isset($this->filestorage_config["temporary"])) {
                // Ajout d'une clé par defaut
                $this->filestorage_config["temporary"] = 
                array(
                   "storage" => "filesystem", // l'attribut storage est obligatoire
                    "path" => "../tmp/", // le repertoire de stockage
                   );
            }
        }
        $this->addToLog(__METHOD__."(): this->filestorage_config = ".print_r($this->filestorage_config, true), EXTRA_VERBOSE_MODE);
        // 
        return true;
    }

    /**
     * Cette fonction récupère le config de stockage des fichier s'il existe, et
     * s'il existe on crée une instance de la classe filestorage
     */
    function setFilestorage() {
        
        $this->storage = false;
        if ($this->setFilestorageConfig()) {        
            $this->storage = new filestorage($this->filestorage_config); 
        }
    }
    
    // }}}

    /**
     *
     */
    function setDatabaseConfig() {

        // On recupere la liste des cles du tableau associatif de configuration
        // de la connexion aux bases de donnees
        $database_keys = array_keys($this->database);
        // Si il y a plusieurs cles
        if (count($database_keys) != 0) {
            // On configure la premiere par defaut
            $coll = $database_keys[0];
        } else { // Si il n'y a aucune cle
            // Aucune base n'est configuree dans le fichier de configuration
            // donc on affiche un message d'erreur
            $class = "error";
            $message = _("Erreur de configuration. Contactez votre administrateur.");
            $this->addToMessage($class, $message);
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR: "._("Aucune entree dans le fichier de configuration"), DEBUG_MODE);
            // On affiche la structure de la page
            $this->setFlag(NULL);
            $this->display();
            // On arrete le traitement en cours
            die();
        }

        // Si la variable coll (representant la cle de la base sur laquelle
        // nous travaillons) n'est pas en variable SESSION ou est en variable
        // SESSION mais n'existe pas dans les cles du tableau associatif de
        // configuration de la connexion aux bases de donnees
        if (!isset($_SESSION['coll']) or
            (isset($_SESSION['coll']) and
             !isset($this->database[$_SESSION['coll']]))) {
            // On configure la premiere par defaut
            $_SESSION['coll'] = $coll;
        } else {
            // On recupere la cle du tableau associatif de configuration de la
            // connexion aux bases de donnees correspondante a la base de
            // donnees sur laquelle nous travaillons
            $coll = $_SESSION['coll'];
        }

        // On renvoi le tableau de parametres pour la connexion a la base
        $this->database_config = $this->database[$coll];

    }

    // }}}

    // {{{

    function setFlag($flag = NULL) { $this->flag = $flag; }
    function setTitle($title = NULL) { $this->title = $title; }
    function setIcon($icon = NULL) { $this->icon = $icon; }
    function setRight($right = NULL) { $this->right = $right; }
    function setHelp($help = NULL) { $this->help = $help; }
    function setDescription($description = "") { $this->description = $description; }

    // }}}

    // {{{ database

    /**
     * Cette méthode permet de se connecter à la base de données
     * @return void
     */
    function connectDatabase() {
        // On inclus la classe d'abstraction de base de donnees
        require_once PATH_OPENMAIRIE."om_database.class.php";
        // On recupere le tableau de parametres pour la connexion a la base
        $this->setDatabaseConfig();
        // On fixe les options
        $options = array(
            'debug' => 2,
            'portability' => DB_PORTABILITY_ALL,
        );
        // Instanciation de l'objet connexion a la base de donnees
        $db = database::connect($this->database_config, $options);
        // Logger
        $this->addToLog(__METHOD__."(): "._("Tentative de connexion au SGBD"), EXTRA_VERBOSE_MODE);
        // Traitement particulier de l'erreur en cas d'erreur de connexion a la
        // base de donnees
        if (database::isError($db, true)) {
            // Deconnexion de l'utilisateur
            $this->logout();
            // On affiche la page de login a l'ecran
            $this->setFlag("login");
            // On affiche un message d'erreur convivial pour l'utilisateur
            $class = "error";
            $message = _("Erreur de base de donnees. Contactez votre administrateur.");
            $this->addToMessage($class, $message);
            // On affiche la page
            if (!defined('REST_REQUEST')) {
                $this->display();
            }
            // On arrete le script
            die();
        } else {
            // On affecte la resource a l'attribut de la classe du meme nom
            $this->db = $db;
            // Logger
            $this->addToLog(__METHOD__."(): Connexion [".$this->database_config["phptype"]."] '".$this->database_config['database']."' OK", EXTRA_VERBOSE_MODE);

            // Compatibilite anterieure (deprecated)
            $this->phptype = $this->database_config["phptype"];
            $this->formatdate = $this->database_config["formatdate"];
            $this->schema = $this->database_config["schema"];

            // Definition des constantes pour l'acces aux informations de la base
            // donnees facilement.
            $temp = "";
            if ($this->database_config["schema"] != "") {
                $temp = $this->database_config["schema"].".";
            }
            $temp = $temp.$this->database_config["prefixe"];
            (defined("DB_PREFIXE") ? "" : define("DB_PREFIXE", $temp));
            (defined("FORMATDATE") ? "" : define("FORMATDATE", $this->database_config["formatdate"]));

            // Definition des constantes pour l'acces aux informations de la base
            // donnees facilement.
            (defined("OM_DB_FORMATDATE") ? "" : define("OM_DB_FORMATDATE", $this->database_config["formatdate"]));
            (defined("OM_DB_PHPTYPE") ? "" : define("OM_DB_PHPTYPE", $this->database_config["phptype"]));
            (defined("OM_DB_DATABASE") ? "" : define("OM_DB_DATABASE", $this->database_config["database"]));
            (defined("OM_DB_SCHEMA") ? "" : define("OM_DB_SCHEMA", $this->database_config["schema"]));
            (defined("OM_DB_TABLE_PREFIX") ? "" : define("OM_DB_TABLE_PREFIX", $this->database_config["prefixe"]));
        }

    }

    /**
     *
     * @return void
     */
    function disconnectDatabase() {

        //
        if ($this->db != NULL and !$this->isDatabaseError($this->db, true)) {
            $result = $this->db->disconnect();
            // Debug
            $this->addToLog(__METHOD__."(): "._("Deconnexion")." ".($result == true ? _("OK") : _("ECHOUEE")), EXTRA_VERBOSE_MODE);
        } else {
            // Debug
            $this->addToLog(__METHOD__."(): "._("Aucune base de donnees a deconnecter"), EXTRA_VERBOSE_MODE);
        }

    }

    /**
     *
     *
     */
    function isDatabaseError($dbobj = NULL, $return = false) {

        //
        if (database::isError($dbobj, $return)) {

            if ($return == true) {

                //
                return true;

            }

            //
            $class = "error";
            $message = _("Erreur de base de donnees. Contactez votre administrateur.");
            $this->addToMessage($class, $message);

            // Logger
            $this->addToLog(__METHOD__."(): ".$dbobj->getDebugInfo(), DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ".$dbobj->getMessage(), DEBUG_MODE);

            //
            $this->setFlag(NULL);
            if (!defined('REST_REQUEST')) {
                $this->display();
                //
                die();
            }

        }

        //
        return false;

    }

    // }}}

    // {{{ login & logout

    /**
     *
     * @return void
     */
    function logout() {

        if ($this->authenticated == true) {

            //
            $coll = $_SESSION['coll'];
            session_unset();
            $_SESSION['coll'] = $coll;
            $this->authenticated = false;

            //
            $class = "ok";
            $message = _("Votre session est maintenant terminee.");
            $this->addToMessage($class, $message);

        }
    }





    /**
     * Modifie le message d'erreur affiche apres un echec d'authentification
     *
     * @param string $message Message à afficher
     * @return void
     * @access public
     */
    public function setAuthenticationMessage($message) {

        $this->authentication_message = $message;
    }

    /**
     * Initialisation de la connexion au serveur LDAP
     *
     * @return void
     * @access public
     */
    public function connectDirectory($login = "", $password = "") {

        // Logger
        $this->addToLog(__METHOD__."(): start", EXTRA_VERBOSE_MODE);

        // On recupere le tableau de parametres pour la connexion a la base
        $this->setDirectoryConfig();

        // Instanciation de l'objet connexion a l'annuaire
        $this->dt =& ldap_connect($this->directory_config["ldap_server"],
                                  $this->directory_config["ldap_server_port"]);
        // Debug
        $this->addToLog(__METHOD__."(): ldap_connect(".$this->directory_config["ldap_server"].",".$this->directory_config["ldap_server_port"].")", EXTRA_VERBOSE_MODE);

        //
        ldap_set_option($this->dt, LDAP_OPT_PROTOCOL_VERSION, 3);

        //
        @$ldap_connect_user =& ldap_bind($this->dt, $login, $password);
        // Debug
        $this->addToLog(__METHOD__."(): ldap_bind(".$this->dt.",".$login.", ***)", EXTRA_VERBOSE_MODE);

        //
        if ($ldap_connect_user != true) {
            //
            $error = ldap_error($this->dt);
            //
            if ($error == "Invalid credentials") {
                $this->authentication_message = _("Votre identifiant ou votre mot de passe est incorrect.");
            } else {
                $this->authentication_message = _("L'application n'est pas en mesure de vous identifier pour l'instant. Contactez votre administrateur.");
            }
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR: "._("Erreur de l'annuaire")." - ".$error, DEBUG_MODE);
        }

        // Logger
        $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);

        //
        return $ldap_connect_user;
    }

    /**
     * Deconnexion avec le serveur LDAP
     *
     * @return bool Etat du succes de la deconnexion
     * @access public
     */
    public function disconnectDirectory() {

        // Debug
        $this->addToLog(__METHOD__."()", EXTRA_VERBOSE_MODE);

        //
        return ldap_unbind($this->dt);
    }

    /**
     * Renvoie la liste des utilisateurs de l'annuaire LDAP à ajouter, et la
     * la liste des utilisateurs de la base de données à supprimer.
     *
     * @return array tabeau retourne un tableau associatif contenant les utilisateurs
     * à ajouter (clef 'userToAdd') et les utilisateurs à supprimer (clef 'userToDelete')
     * @access public
     */
    function initSynchronization() {

        // Logger
        $this->addToLog(__METHOD__."(): start", VERBOSE_MODE);

        // Si la configuration de l'annuaire n'est pas correcte alors on
        // retourne false
        if ($this->isDirectoryAvailable() != true) {
            //
            $class = "error";
            $message = _("Erreur de configuration. Contactez votre administrateur.");
            $this->displayMessage($class, $message);
            // On retourne false
            return false;
        }

        // Logger
        $this->addToLog(__METHOD__."(): \$this->isDirectoryAvailable() == true", EXTRA_VERBOSE_MODE);

        // Authentification de l'administrateur du LDAP
        $auth = false;
        $auth = $this->connectDirectory($this->directory_config["ldap_admin_login"],
                                        $this->directory_config["ldap_admin_passwd"]);

        //
        if ($auth == false) {
            //
            $class = "error";
            $message = _("Mauvais parametres : l'authentification a l'annuaire n'est pas possible.");
            $this->displayMessage($class, $message);
            //
            return NULL;
        }

        //
        if ($auth) {

            // Logger
            $this->addToLog(__METHOD__."(): Authentification OK (\$auth == ".($auth==true?"true":"false").")", EXTRA_VERBOSE_MODE);

            // Logger
            $this->addToLog(__METHOD__."(): start ldap_search()", VERBOSE_MODE);

            // recheche des utilisateurs de l'annuaire
            $ldapResults = null;
            $ldapResults = ldap_search($this->dt,
                                        $this->directory_config['ldap_base_users'],
                                        $this->directory_config['ldap_user_filter'],
                                        array("*"));

            // Logger
            $this->addToLog(__METHOD__."(): ldap_search(".$this->dt.",
                                        \"".$this->directory_config['ldap_base_users']."\",
                                        \"".$this->directory_config['ldap_user_filter']."\",
                                        array(\"*\"))", EXTRA_VERBOSE_MODE);

            // Logger
            $this->addToLog(__METHOD__."(): end ldap_search()", VERBOSE_MODE);

            //
            if (!$ldapResults) {

                //
                $class = "error";
                $message = _("Impossible de poursuivre la recherche des utilisateurs. ".
                             "La methode de recherche renvoie le message: ");
                $message .= ldap_error($this->dt);
                $message .= ".";
                $this->displayMessage($class, $message);

                //return false;
            }

            // récupération des utilisateurs de l'annuaire
            $ldapEntries = null;
            $ldapEntries = ldap_get_entries($this->dt, $ldapResults);

            // Logger
            $this->addToLog(__METHOD__."(): \$ldapEntries = ".print_r($ldapEntries, true).";", EXTRA_VERBOSE_MODE);

            // récupération des utilisateurs de la base de données
            $sql = "SELECT * FROM ".DB_PREFIXE.$this->table_om_utilisateur." WHERE UPPER(".$this->table_om_utilisateur_field_om_type.") = 'LDAP';";
            $sqlRes = $this->db->query($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            //
            $this->isDatabaseError($sqlRes);

            $databaseEntries = array();

            while ($row =& $sqlRes->fetchrow(DB_FETCHMODE_ASSOC)) {
                array_push($databaseEntries, $row);
            }

            // tableau des utilisateurs se trouvant dans l'annuaire et non en base
            $userToAdd = array();

            // tableau des utilisateurs se trouvant dans la base et non en annuaire
            $userToDelete = $databaseEntries;

            // tableau des utilisateurs se trouvant dans la base et l'annuaire
            $userToUpdate = array();

            $nbrDatabaseEntries = count($databaseEntries);
            $matched = false;

            // pour chaque utilisateur de l'annuaire on recherche s'il
            // existe dans la base un utilisateur ayant le même login
            for ($i=0; $i<$ldapEntries['count']; $i++) {
                for ($j=0; $j<$nbrDatabaseEntries; $j++) {
                    if ($ldapEntries[$i][$this->directory_config["ldap_login_attrib"]][0] == $databaseEntries[$j]['login']) {
                        unset($userToDelete[$j]);
                        $matched = true;
                    }
                }

                // si l'utilisateur de l'annuaire n'est pas dans la base, on
                // l'ajoute a la liste des utilisateurs a ajouter
                if ($matched == false) {
                    array_push($userToAdd, $ldapEntries[$i]);

                // si l'utilisateur de l'annuaire est dans la base, on l'ajoute
                // a la liste des utilisateurs a mettre a jour
                } else {
                    array_push($userToUpdate, $ldapEntries[$i]);
                }

                $matched = false;
            }

            // Logger
            $this->addToLog(__METHOD__."(): end", VERBOSE_MODE);

            return array(
                "userToAdd" => $userToAdd,
                "userToDelete" => $userToDelete,
                "userToUpdate" => $userToUpdate,
            );
        }
    }

    function getValFUserToAdd($user) {
        //
        $id = $this->db->nextId(DB_PREFIXE.$this->table_om_utilisateur);
        //
        $login = $user[$this->directory_config['ldap_login_attrib']][0];
        //
        if (isset($this->directory_config['default_om_profil'])) {
            $default_profile = $this->directory_config['default_om_profil'];
        } else {
            $default_profile = $this->default_om_profil;
        }
        //
        $valF = array(
            $this->table_om_utilisateur_field_id => $id,
            $this->table_om_utilisateur_field_login => $login,
            $this->table_om_utilisateur_field_password => md5($login),
            $this->table_om_utilisateur_field_om_profil => $default_profile,
            $this->table_om_utilisateur_field_om_collectivite => $_SESSION['collectivite'],
            $this->table_om_utilisateur_field_om_type => "ldap",
        );
        //
        if (isset($this->directory_config['ldap_more_attrib'])) {
            foreach ($this->directory_config['ldap_more_attrib'] as $key => $value) {
               if (is_array($value)) {
                   foreach($value as $value1) {
                       if (isset($user[$value1][0])) {
                           $valF[$key] = $user[$value1][0];
                           break;
                       }
                   }
               } else {
                   if (isset($user[$value][0])) {
                       $valF[$key] = $user[$value][0];
                   }
               }
            }
        }
        //
        if (!isset($valF[$this->table_om_utilisateur_field_nom])) {
            $valF[$this->table_om_utilisateur_field_nom] = $login;
        }
        //
        if (!isset($valF[$this->table_om_utilisateur_field_email])) {
            $valF[$this->table_om_utilisateur_field_email] = "";
        }
        //
        return $valF;
    }

    function getValFUserToUpdate($user) {
        //
        $valF = $this->retrieveUserInfos($user[$this->directory_config['ldap_login_attrib']][0]);

        /* Suppression des valeurs de la collectivite retournees par la
           methode retrieveUserProfile. Ces donnees n'appartiennent pas a
           la table utilisateur et declencheront une erreur de base de donnees
           lors de l'appel a autoExecute avec le mode DB_AUTOQUERY_UPDATE */

        if (isset($valF['libelle']))
            unset($valF['libelle']);

        if (isset($valF['niveau']))
            unset($valF['niveau']);

        if (isset($this->directory_config['ldap_more_attrib'])) {
            foreach ($this->directory_config['ldap_more_attrib'] as $key => $value) {
               if (is_array($value)) {
                   foreach($value as $value1) {
                       if (isset($user[$value1][0])) {
                           $valF[$key] = $user[$value1][0];
                           break;
                       }
                   }
               } else {
                   if (isset($user[$value][0])) {
                       $valF[$key] = $user[$value][0];
                   }
               }
            }
        }
        //
        return $valF;
    }

    public function synchronizeUsers($users) {

        // {{{ AJOUT DES UTILISATEURS

        //
        $attribError = false;

        //
        if (is_array($users) and array_key_exists('userToAdd', $users)) {

            //
            foreach ($users['userToAdd'] as $user) {


                if (!array_key_exists($this->directory_config['ldap_login_attrib'], $user)) {
                    $attribError = true;
                    continue;
                }

                $res = null;
                //
                $valF = $this->getValFUserToAdd($user);
                //
                $res = $this->db->autoExecute(DB_PREFIXE.$this->table_om_utilisateur, $valF, DB_AUTOQUERY_INSERT);
                // Logger
                $this->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE.$this->table_om_utilisateur."\", ".print_r($valF, true).", DB_AUTOQUERY_INSERT)", VERBOSE_MODE);
                //
                if ($this->isDatabaseError($res, true)) {
                    //
                    $class = "error";
                    $message = _("Erreur de base de donnees. Contactez votre administrateur.");
                    $this->displayMessage($class, $message);
                    //
                    return false;
                }
            }

        }

        // }}}

        // {{{ SUPPRESSION DES UTILISATEURS

        //
        if (is_array($users) and array_key_exists('userToDelete', $users)) {

            //
            foreach ($users['userToDelete'] as $user) {
                // Instanciation de la classe om_utilisateur
                require_once '../obj/'.$this->table_om_utilisateur.'.class.php';
                $om_utilisateur = new om_utilisateur($user[$this->table_om_utilisateur_field_id], $this->db, DEBUG);
                $value_om_utilisateur = array(
                        $this->table_om_utilisateur_field_id => $user[$this->table_om_utilisateur_field_id],
                    );
                // Supprime l'enregistrement
                $om_utilisateur->supprimer($value_om_utilisateur, $this->db, DEBUG);
                
            }
        }

        // }}}

        // {{{ MISE A JOUR DES UTILISATEURS

        //
        if (is_array($users) and array_key_exists('userToUpdate', $users)) {

            foreach ($users['userToUpdate'] as $user) {

                $user_datas = $this->getValFUserToUpdate($user);
                $user_login = $user_datas[$this->table_om_utilisateur_field_login];
                unset($user_datas[$this->table_om_utilisateur_field_id]);
                unset($user_datas[$this->table_om_utilisateur_field_login]);
    
                $res = $this->db->autoExecute(
                                    DB_PREFIXE.$this->table_om_utilisateur,
                                    $user_datas, DB_AUTOQUERY_UPDATE,
                                    $this->table_om_utilisateur_field_login."='".$user_login."'");
                // Logger
                $this->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE.$this->table_om_utilisateur."\", ".print_r($user_datas, true).", DB_AUTOQUERY_UPDATE, \"".$this->table_om_utilisateur_field_login."='".$user_login."'\")", VERBOSE_MODE);
                //
                if ($this->isDatabaseError($res, true)) {
                    //
                    $class = "error";
                    $message = _("Erreur de base de donnees. Contactez votre administrateur.");
                    $this->displayMessage($class, $message);
                    //
                    return false;
                }
            
            }

        }

        // }}}

        if ($attribError) {
            $class = "error";
            $message = _("Certains enregistrements provenant du LDAP ".
                         "ne possedent pas l'attribut ".
                         $this->directory_config['ldap_login_attrib'].". ".
                         "Ils ne peuvent donc pas etre synchronises");
            $this->displayMessage($class, $message);
        }
        //
        $class = "ok";
        $message = _("La synchronisation des utilisateurs est terminee.");
        $this->displayMessage($class, $message);
        //
        return true;
    }

    /**
     * Cette methode permet verifier si la fonctionnalite annuaire est
     * disponible ou non. Si le support n'est pas active sur le serveur alors
     * les fonctions utilisees ne seront pas disponibles.
     *
     *
     */
    function isDirectoryAvailable() {

        //
        if (!function_exists("ldap_connect")) {
            // Debug
            $this->addToLog(__METHOD__."(): ERR", DEBUG_MODE);
            $this->addToLog(__METHOD__."(): ERR: "._("Les fonctions ldap ne sont pas disponibles sur cette installation."), DEBUG_MODE);
            //
            return false;
        }

        //
        if ($this->setDirectoryConfig() == false) {
            //
            return false;
        }

        //
        return true;
    }





    /**
     *
     */
    function changeDatabaseUserPassword($login, $password) {

        //
        $valF[$this->table_om_utilisateur_field_password] = md5($password);
        $cle = $this->table_om_utilisateur_field_login."='".$login."'";
        // Exécution de la requête
        $res = $this->db->autoExecute(DB_PREFIXE.$this->table_om_utilisateur, $valF, DB_AUTOQUERY_UPDATE, $cle);
        // Logger
        $this->addToLog(__METHOD__."(): db->autoExecute(\"".DB_PREFIXE.$this->table_om_utilisateur."\", ".print_r($valF, true).", DB_AUTOQUERY_UPDATE, \"".$cle."\")", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->isDatabaseError($res);

    }

    /**
     * Récupération des informations en base de données de l'utilisateur
     *
     * @param string $login Identifiant de l'utilisateur
     * @return array Informations de l'utilisateur
     * @access public
     */
    public function retrieveUserProfile($login) {

        $user_infos = array();

        //
        $sql = " SELECT * ";
        $sql .= " FROM ".DB_PREFIXE.$this->table_om_utilisateur;
        $sql .= " left join ".DB_PREFIXE.$this->table_om_collectivite;
        $sql .= " on ".$this->table_om_collectivite.".".$this->table_om_collectivite_field_id." = ".$this->table_om_utilisateur.".".$this->table_om_utilisateur_field_om_collectivite;
        $sql .= " left join ".DB_PREFIXE.$this->table_om_profil;
        $sql .= " on ".$this->table_om_utilisateur.".".$this->table_om_utilisateur_field_om_profil." = ".$this->table_om_profil.".".$this->table_om_profil_field_id;
        $sql .= " WHERE ".$this->table_om_utilisateur.".".$this->table_om_utilisateur_field_login." = '".$login."';";
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        //
        $this->isDatabaseError($res);

        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $user_infos = $row;
        }

        $res ->free();

        return $user_infos;
    }

    /**
     * Récupération des informations en base de données de l'utilisateur
     * uniquement des données de la table om_utilisateur
     *
     * @param string $login Identifiant de l'utilisateur
     * @return array Informations de l'utilisateur
     * @access public
     */
    public function retrieveUserInfos($login) {

        $user_infos = array();

        //
        $sql = " SELECT * ";
        $sql .= " FROM ".DB_PREFIXE.$this->table_om_utilisateur;
        $sql .= " WHERE ".$this->table_om_utilisateur.".".$this->table_om_utilisateur_field_login." = '".$login."';";
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        //
        $this->isDatabaseError($res);

        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $user_infos = $row;
        }

        $res ->free();

        return $user_infos;
    }
    /**
     *
     *
     * @return void
     */
    function displayLoginForm() {

        /**
         *
         */
        (defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
        require_once PATH_OPENMAIRIE."om_formulaire.class.php";

        // Cinq balises div uniquement pour permettre un style css particulier
        echo "\n<div id=\"loginform\" class=\"ui-widget\">";
        echo "<div id=\"loginform_t\">";
        echo "<div id=\"loginform_l\">";
        echo "<div id=\"loginform_r\">";
        echo "<div id=\"loginform_b\">\n";

        //
        echo "<div id=\"formulaire\">\n\n";
        //
        echo "<ul>\n";
        echo "\t<li><a href=\"#tabs-1\">"._("Identification")."</a></li>\n";
        echo "</ul>\n";
        //
        echo "\n<div id=\"tabs-1\">\n";

        //
        echo "\t<form method=\"post\" id=\"login_form\" action=\"../scr/login.php\">\n";

        //
        $validation = 0;
        $maj = 0;
        $champs = array("came_from", "login", "password");
        if (count($this->database) > 1) {
            array_push($champs, "coll");
        }
        //
        $form = new formulaire(NULL, $validation, $maj, $champs);
        //
        $form->setType("came_from", "hidden");
        $form->setTaille("came_from", 20);
        $form->setMax("came_from", 20);
        $came_from = (isset($_POST ['came_from'])?$_POST ['came_from']:(isset($_GET ['came_from'])?$_GET ['came_from']:""));
        $form->setVal("came_from", filter_var($came_from, FILTER_SANITIZE_STRING));
        //
        $form->setLib("login", _("Identifiant"));
        $form->setType("login", "text");
        $form->setTaille("login", 20);
        $form->setMax("login", 100);
        $form->setVal("login", ($this->config['demo']==true?"demo":""));
        //
        $form->setLib("password", _("Mot de passe"));
        $form->setType("password", "password");
        $form->setTaille("password", 20);
        $form->setMax("password", 100);
        $form->setVal("password", ($this->config['demo']==true?"demo":""));
        //
        if (count($this->database)>1) {
            $form->setLib("coll", _("Base de donnees"));
            $form->setType("coll", "select");
            $contenu = array(
                0 => array(),
                1 => array(),
            );
            foreach ($this->database as $key => $coll) {
                array_push($contenu[0], $key);
                array_push($contenu[1], $coll['title']);
            }
            $form->setSelect("coll", $contenu);
            if (isset($_SESSION['coll'])) {
                $form->setVal("coll", $_SESSION['coll']);
            }
        }
        //
        $form->entete();
        $form->afficher($champs, $validation, false, false);
        $form->enpied();

        //
        echo "\t\t<div class=\"formControls\">\n";
        echo "\t\t\t<input class=\"context boutonFormulaireLogin ui-button ui-state ui-corner-all\" ";
        echo "name=\"login.action.connect\" value=\""._("Se connecter")."\" type=\"submit\" />\n";
        echo "\t\t</div>\n";
        echo "\t</form>\n";

        // Ajout du lien de redefinition de mot de passe
        if (isset($this->config['password_reset']) and $this->config['password_reset'] == true) {
            echo "\t<p class=\"link-password-reset\">\n";
            echo "\t\t<a href=\"".$this->url_password_reset."\" title=\""._("Redefinition du mot de passe")."\">";
            echo "<span class=\"om-icon ui-icon ui-icon-info\"><!-- --></span>";
            echo _("Mot de passe oublie ?");
            echo "</a>\n";
            echo "\t</p>\n";
        }

        //
        echo "</div>";
        echo "</div>";

        //
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>\n";

    }

    // }}}

    // {{{ AUTHENTIFICATION ET GESTION DES UTILISATEURS

    /**
     * Cette methode permet d'effectuer toutes les verifications et les
     * traitements necessaires pour la gestion de l'authentification des
     * utilisateurs a l'application.
     *
     * @return void
     */
    function login() {

        // Debug
        $this->addToLog(__METHOD__."(): start", EXTRA_VERBOSE_MODE);

        $this->redirectAuthenticatedUsers();

        // Si l'utilisateur ne souhaite pas s'authentifier (le cas se presente
        // si nous sommes sur la page de login et que l'utilisateur n'a pas
        // valider le formulaire) alors on sort de la methode
        if ($this->wantToAuthenticate() != true) {
            // Logger
            $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
            // On retourne NULL
            return NULL;
        }

        // Si la valeur du champ coll dans le formulaire de login est definie
        if (isset($_POST['coll'])) {
            // On ajoute en variable de session la cle du tableau associatif de
            // configuration de base de donnees a laquelle l'utilisateur
            // souhaite se connecter
            $_SESSION['coll'] = $_POST['coll'];
            // Debug
            $this->addToLog(__METHOD__."(): \$_SESSION['coll']=\"".$_SESSION['coll']."\"", EXTRA_VERBOSE_MODE);
        }

        // On se connecte a la base de donnees
        $this->connectDatabase();

        // On recupere le login et le mot de passe de l'utilisateur qui
        // demande l'authentification
        $login = $this->getUserLogin();
        $password = $this->getUserPassword();

        // Logger
        $this->addToLog(__METHOD__."(): credentials \"".$login."\"/\"***\"", EXTRA_VERBOSE_MODE);

        // On procede a l'authentification
        $authenticated = $this->processAuthentication($login, $password);

        //
        if ($authenticated) {
            $user_infos = $this->retrieveUserProfile($login);
        }

        //
        if (isset($user_infos[$this->table_om_utilisateur_field_om_profil])) {

            // Identification OK
            $_SESSION["profil"] = $user_infos[$this->table_om_profil_field_hierarchie];
            $_SESSION["login"] = $user_infos[$this->table_om_utilisateur_field_login];
            $_SESSION["collectivite"] = $user_infos[$this->table_om_utilisateur_field_om_collectivite];
            $_SESSION["niveau"] = $user_infos[$this->table_om_collectivite_field_niveau];
            $_SESSION["justlogin"] = true;

            //
            $this->triggerAfterLogin($user_infos);

            //
            $class = "ok";
            $message = _("Votre session est maintenant ouverte.");
            $this->addToMessage($class, $message);

            //
            $this->disconnectDatabase();

            // Redirection vers le came_from si existant
            if (isset($_POST['came_from']) and $_POST['came_from'] != "" ) {

                //
                header("Location: ".urldecode($_POST['came_from']));

            } else {

                // Sinon on redirige vers le tableau de bord
                $this->goToDashboard ();

            }

        } else {

            //
            $class = "error";
            $this->addToMessage($class, $this->authentication_message);

            if (isset($_POST['came_from'])) {

                //
                $this->came_from = $_POST['came_from'];

            }

        }

        // Logger
        $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);

    }

    /**
     * Retourne l'etat de la demande d'authentification
     *
     * Cette methode pourra etre surchargee pour permettre d'utiliser un
     * systeme central d'authentification
     *
     * @return bool Etat de la demande d'authentification
     * @access public
     */
    public function wantToAuthenticate() {

        // Si l'utilisateur a valide le formulaire de login alors c'est qu'il
        // souhaite s'authentifier sinon l'authentification n'est pas
        // souhaitee
        return isset($_POST['login_action_connect']);

    }

    /**
     * Retourne l'identifiant de l'utilisateur lors de la demande
     * d'authentification
     *
     * Cette methode pourra etre surchargee pour permettre d'utiliser un
     * systeme central d'authentification
     *
     * @return string Identifiant de l'utilisateur
     * @access public
     */
    public function getUserLogin() {

        // Si la valeur du champ login dans le formulaire de login est definie
        if (isset($_POST['login'])) {
            // On retourne la valeur du champ login en supprimant les
            // caracteres #
            return $this->db->escapeSimple(str_replace('#', '', $_POST['login']));
        }

        // Si la valeur du champ login dans le formulaire de login n'est pas
        // definie alors on retour NULL
        return NULL;

    }

    /**
     * Retourne le mot de passe de l'utilisateur lors de la demande
     * d'authentification
     *
     * Cette methode pourra etre surchargee pour permettre d'utiliser un
     * systeme central d'authentification
     *
     * @return string Mot de passe de l'utilisateur
     * @access public
     */
    public function getUserPassword() {

        // Si la valeur du champ mot de passe dans le formulaire de login est
        // definie
        if (isset($_POST['password'])) {
            // On retourne la valeur du champ mot de passe
            return $_POST['password'];
        }

        // Si la valeur du champ mot de passe dans le formulaire de login
        // n'est pas definie alors on retour NULL
        return NULL;

    }

    /**
     * Traitement de l'authentification
     *
     * @param string $login Indentifiant de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @access public
     * @return bool Etat de l'authentification de l'utilisateur
     */
    public function processAuthentication($login, $password) {

        // Initialisation de la valeur de retour a false
        $authenticated = false;

        // On recupere le mode d'authenfication de l'utilisateur
        $mode = $this->retrieveUserAuthenticationMode($login);
        // Debug
        $this->addToLog(__METHOD__."(): le mode d'authentification est \"".$mode."\"", EXTRA_VERBOSE_MODE);

        // Si mode base de donnees
        if (strtolower($mode) == "db") {
            // On procede a l'authentification depuis la base de donnees
            $authenticated = $this->processDatabaseAuthentication($login, $password);
        } elseif (strtolower($mode) == "ldap") { // Si mode annuaire
            //
            if ($password == "") {
                $authenticated = false;
                $this->authentication_message = _("Votre identifiant ou votre mot de passe est incorrect.");
            } else {
                // On procede a l'authentification depuis l'annuaire
                $authenticated = $this->processDirectoryAuthentication($login, $password);
            }
        }

        // On retourne la valeur
        return $authenticated;

    }

    /**
     * Recuperation du mode d'authentification de l'utilisateur
     *
     * @param string $login Identifiant de l'utilisateur
     * @return string Mode d'authentification de l'utilisateur
     * @access public
     */
    public function retrieveUserAuthenticationMode($login) {

        // Initialisation de la valeur de retour a db
        $mode = "db";

        //
        $sql = " SELECT * ";
        $sql .= " FROM ".DB_PREFIXE.$this->table_om_utilisateur." ";
        $sql .= " WHERE ".$this->table_om_utilisateur_field_login."='".$login."' ";
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        //
        if ($this->isDatabaseError($res, true) == true) {
            //
            $mode = false;
            $this->authentication_message = _("Erreur de base de donnees. Contactez votre administrateur.");
        } else {
            //
            if ($res->numRows() == 1) {
                //
                $row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
                //
                if (isset($row[$this->table_om_utilisateur_field_om_type]) && $row[$this->table_om_utilisateur_field_om_type] != "") {
                    $mode = $row[$this->table_om_utilisateur_field_om_type];
                }
            } elseif ($res->numRows() < 1) {
                $mode = false;
                $this->authentication_message = _("Votre identifiant ou votre mot de passe est incorrect.");
            }
            //
            $res->free();
        }

        // On retourne la valeur
        return $mode;

    }

    /**
     * Traitement de l'authentification pour un utilisateur en base de donnees
     *
     * @param string $login Identifiant de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @return bool Etat de l'authentification de l'utilisateur
     * @access public
     */
    public function processDatabaseAuthentication($login, $password) {

        // Initialisation de la valeur de retour a false
        $authenticated = false;

        //
        $sql = " SELECT * ";
        $sql .= " FROM ".DB_PREFIXE.$this->table_om_utilisateur." ";
        $sql .= " WHERE ".$this->table_om_utilisateur_field_login."='".$login."' ";
        $sql .= " AND ".$this->table_om_utilisateur_field_password."='".md5($password)."' ";
        $res = $this->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        //
        $this->isDatabaseError($res);

        //
        while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            //
            $authenticated = true;
        }
        //
        $res->free();

        //
        if ($authenticated == false) {
            $this->authentication_message = _("Votre identifiant ou votre mot de passe est incorrect.");
        }

        // On retourne la valeur
        return $authenticated;

    }

    /**
     * Traitement de l'authentification pour un utilisateur en annuaire
     *
     * @param string $login Identifiant de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @return bool Etat de l'authentification de l'utilisateur
     * @access public
     */
    public function processDirectoryAuthentication($login, $password) {

        // Si la configuration de l'annuaire n'est pas correcte alors on
        // retourne false
        if ($this->isDirectoryAvailable() != true) {
            //
            $this->authentication_message = _("Erreur de configuration. Contactez votre administrateur.");
            // On retourne false
            return false;
        }

        // Tentative de connexion a l'annuaire
        $ldap_connect_user = $this->connectDirectory($this->directory_config["ldap_login_attrib"]."=".$login.",".$this->directory_config["ldap_base_users"], $password);

        // Deconnexion de l'annuaire
        $this->disconnectDirectory();

        //
        return $ldap_connect_user;

    }

    /**
     * Redirige les utilisateurs authentifies vers le tableau de bord.
     *
     * @param void
     * @return null
     * @access private
     */
    private function redirectAuthenticatedUsers() {

        // Si l'utilisateur est deja authentifie on le redirige sur le tableau
        // de bord de l'application et on sort de la methode
        if ($this->authenticated != false) {
            // Appel de la methode de redirection vers le tableau de bord
            $this->goToDashboard();
            // On retourne NULL
            return NULL;
        }

        return NULL;
    }

    // }}}


    /**
     *
     */
    function getActionsToDisplay() {
        //
        $actions_to_display = array();
        //
        if ($this->authenticated == false) {
            return $actions_to_display;
        }
        //
        if (!file_exists("../dyn/actions.inc.php")) {
            return $actions_to_display;
        }
        //
        require "../dyn/actions.inc.php";
        //
        if (!isset($actions)) {
            return $actions_to_display;
        }
        //
        foreach ($actions as $key => $value) {
            // Gestion des droits d'acces : si l'utilisateur n'a pas la
            // permission necessaire alors l'entree n'est pas affichee
            if (isset($value['right'])
                and !$this->isAccredited($value['right'], "OR")) {
                // On passe directement a l'iteration suivante de la boucle
                continue;
            }
            //
            $actions_to_display[] = $value;
        }
        //
        return $actions_to_display;
    }

    /**
     *
     */
    function getShortlinksToDisplay() {
        //
        $shortlinks_to_display = array();
        //
        if ($this->authenticated == false) {
            return $shortlinks_to_display;
        }
        //
        if (!file_exists("../dyn/shortlinks.inc.php")) {
            return $shortlinks_to_display;
        }
        //
        require "../dyn/shortlinks.inc.php";
        //
        if (!isset($shortlinks)) {
            return $shortlinks_to_display;
        }
        //
        foreach ($shortlinks as $key => $value) {
            // Gestion des droits d'acces : si l'utilisateur n'a pas la
            // permission necessaire alors l'entree n'est pas affichee
            if (isset($value['right'])
                and !$this->isAccredited($value['right'], "OR")) {
                // On passe directement a l'iteration suivante de la boucle
                continue;
            }
            //
            $shortlinks_to_display[] = $value;
        }
        //
        return $shortlinks_to_display;
    }

    /**
     *
     */
    function getFooterToDisplay() {
        //
        $footer_to_display = array();
        //
        if ($this->authenticated == false) {
            return $footer_to_display;
        }
        //
        if (!file_exists("../dyn/footer.inc.php")) {
            return $footer_to_display;
        }
        //
        require "../dyn/footer.inc.php";
        //
        if (!isset($footer)) {
            return $footer_to_display;
        }
        //
        foreach ($footer as $key => $value) {
            // Gestion des droits d'acces : si l'utilisateur n'a pas la
            // permission necessaire alors l'entree n'est pas affichee
            if (isset($value['right'])
                and !$this->isAccredited($value['right'], "OR")) {
                // On passe directement a l'iteration suivante de la boucle
                continue;
            }
            //
            $footer_to_display[] = $value;
        }
        //
        return $footer_to_display;
    }


    /**
     * Cette variable permet de stocker le résultat de la méthode getMenuToDisplay
     * pour éviter d'effectuer le calcul plusieurs fois. Si la variable vaut 
     * null alors le calcul n'a jamais été fait.
     */
    var $_menu_to_display = null;

    /**
     * Compose le menu à afficher.
     *
     * Cette méthode retourne la composition du menu, c'est-à-dire la liste des
     * rubriques et des entrées de menu disponibles pour l'utilisateur connecté
     * dans le contexte actuel.
     *
     * @return array
     */
    function getMenuToDisplay() {
        // Logger
        $this->addToLog(__METHOD__."(): start", EXTRA_VERBOSE_MODE);
        // Si le menu a déjà était composé
        if (!is_null($this->_menu_to_display)) {
            // On retourne directement le menu calculé précédemment
            $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
            return $this->_menu_to_display;
        }
        // On initialise le tableau avec un tableau vide 
        $this->_menu_to_display = array();
        // Si l'utilisateur n'existe pas ou si le fichier de configuration
        // du menu n'existe pas
        if ($this->authenticated == false
            || !file_exists("../dyn/menu.inc.php")) {
            // On retourne un menu vide
            $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
            return $this->_menu_to_display;
        }
        // On inclut le fichier de configuration du menu
        require "../dyn/menu.inc.php";
        // Si le fichier de configuration ne définit pas de menu
        if (!isset($menu)) {
            // On retourne un menu vide
            $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
            return $this->_menu_to_display;
        }
    
        // Recuperation des variables
        $scriptAppele = explode("/", $_SERVER["PHP_SELF"]);
        $scriptAppele = $scriptAppele[ count($scriptAppele) - 1 ];
        $obj = (isset($_GET['obj']) ? $_GET['obj'] : "");

        //
        foreach ($menu as $m => $rubrik) {
            // Gestion des paramètres
            if (isset($rubrik["parameters"])
                && is_array($rubrik["parameters"])) {
                //
                $flag_parameter = false;
                //
                foreach ($rubrik["parameters"] as $parameter_key => $parameter_value) {
                    //
                    if ($this->getParameter($parameter_key) != $parameter_value) {
                        //
                        $flag_parameter = true;
                        break;
                    }
                }
                //
                if ($flag_parameter == true) {
                    // On passe directement a l'iteration suivante de la boucle
                    continue;
                }
            }
            // Gestion des droits d'acces : si l'utilisateur n'a pas la
            // permission necessaire alors la rubrique n'est pas affichee
            if (isset($rubrik['right'])
                and !$this->isAccredited($rubrik['right'])) {
                // On passe directement a l'iteration suivante de la boucle
                continue;
            }
            // Initialisation
            $rubrik_to_display = $rubrik;
            $elems_in_rubrik_to_display = array();
            $cpt_links = 0;


            // Test des criteres pour determiner si la rubrique est active
            if (isset($rubrik['open'])) {
                foreach ($rubrik['open'] as $scriptobj) {
                    // separation du nom de fichier et du obj
                    $scriptobjarray = explode("|", $scriptobj);
                    $cle_script=$scriptobjarray[0];
                    $cle_obj=$scriptobjarray[1];

                    $cle_script_ok = true;
                    if ($cle_script!="" and $cle_script != $scriptAppele) {
                        $cle_script_ok = false;
                    }
                    $cle_obj_ok = true;
                    if ($cle_obj != "" and $cle_obj != $obj){
                            $cle_obj_ok = false;
                    }
                    if ($cle_obj_ok and $cle_script_ok){
                        $rubrik_to_display["selected"] = "selected";
                    }
                }
            }

            // Boucle sur les entrees de menu
            foreach ($rubrik['links'] as $link) {
                // Gestion des paramètres
                if (isset($link["parameters"])
                    && is_array($link["parameters"])) {
                    //
                    $flag_parameter = false;
                    //
                    foreach ($link["parameters"] as $parameter_key => $parameter_value) {
                        //
                        if ($this->getParameter($parameter_key) != $parameter_value) {
                            //
                            $flag_parameter = true;
                            break;
                        }
                    }
                    //
                    if ($flag_parameter == true) {
                        // On passe directement a l'iteration suivante de la boucle
                        continue;
                    }
                }
                // Gestion des droits d'acces : si l'utilisateur n'a pas la
                // permission necessaire alors l'entree n'est pas affichee
                if (isset($link['right'])
                    and !$this->isAccredited($link['right'], "OR")) {

                    // On passe directement a l'iteration suivante de la boucle
                    continue;

                }
                //
                $cpt_links++;

                // Entree de menu
                if (trim($link['title']) != "<hr />" and trim($link['title']) != "<hr/>"
                    and trim($link['title']) != "<hr>") {
                    // MENU OPEN
                    $link_actif="";
                    if (isset($link['open'])) {
                        if (gettype($link['open']) == "string") {
                                $link['open']=array($link['open'],);
                        }
    
                        foreach ($link['open'] as $scriptobj) {
                            // separation du nom de fichier et du obj
                            $scriptobjarray = explode("|", $scriptobj);
                            $cle_script=$scriptobjarray[0];
                            $cle_obj=$scriptobjarray[1];
    
                            $cle_script_ok = true;
                            if ($cle_script!="" and $cle_script != $scriptAppele) {
                                $cle_script_ok = false;
                            }
                            $cle_obj_ok = true;
                            if ($cle_obj != "" and $cle_obj != $obj) {
                                $cle_obj_ok = false;
                            }
                            if ($cle_obj_ok and $cle_script_ok){
                                $rubrik_to_display["selected"] = "selected";
                                $link["selected"] = "selected";
                            }
                        }
                    }
                }
                $elems_in_rubrik_to_display[] = $link;
            }
            
            //
            $rubrik_to_display["links"] = $elems_in_rubrik_to_display;
            // Si des liens ont ete affiches dans la rubrique alors on
            // affiche la rubrique
            if ($cpt_links != 0) {
                //
                $this->_menu_to_display[] = $rubrik_to_display;
            }
        }
        //
        $this->addToLog(__METHOD__."(): end", EXTRA_VERBOSE_MODE);
        return $this->_menu_to_display;
    }

    /**
     * Cette méthode permet de renvoyer la valeur d'un paramètre de
     * l'application, on utilise cette méthode car les paramètres peuvent
     * provenir de différentes sources :
     *   - le fichier dyn/var.inc
     *   - le fichier dyn/config.inc.php
     *   - la table om_parametre
     * En regroupant la récupération des paramètres dans une seule méthode :
     *  - on évite les erreurs
     *  - on peut se permettre de gérer des comportements
     * complexes comme : si le paramètre n'est pas disponible pour la
     * collectivité alors on va chercher dans la collectivité de niveau
     * supérieur.
     *  - on est indépendant du stockage de ces paramètres.
     *
     * Si on ne trouve pas de paramètre correspondant alors on retourne NULL
     */
    function getParameter($param = NULL) {
        //
        if ($param == NULL) {
            return NULL;
        }
        //
        if ($param == "isDirectoryOptionEnabled") {
            if (!isset($this->database_config["directory"])
                || $this->database_config["directory"] == NULL) {
                //
                return false;
            } else {
                //
                return true;
            }
        }
        //
        if (isset($this->config[$param])) {
            //
            return $this->config[$param];
        }
        //
        if (isset($this->collectivite[$param])) {
            //
            return $this->collectivite[$param];
        }
        //
        return NULL;
    }

    /**
     * Cette méthode permet de renvoyer la valeur soumise par post.
     *
     * Si on ne trouve pas de paramètre correspondant alors on retourne chaîne vide
     *
     * @param string $param clé de la valeur dans le tableau
     * 
     * @return null ou la valeur
     */
    function get_submitted_post_value($param = null) {
        //
        if ($param == null) {
            return $this->submitted_post_value;
        }
        //
        if (isset($this->submitted_post_value[$param])) {
            //
            return $this->submitted_post_value[$param];
        }
        //
        return null;
    }

    /**
     * Cette méthode permet de renvoyer la valeur soumise par get.
     *
     * Si on ne trouve pas de paramètre correspondant alors on retourne chaîne vide
     *
     * @param string $param clé de la valeur dans le tableau
     *
     * @return null ou la valeur
     */
    function get_submitted_get_value($param = null) {
        //
        if ($param == null) {
            return $this->submitted_get_value;
        }
        //
        if (isset($this->submitted_get_value[$param])) {
            //
            return $this->submitted_get_value[$param];
        }
        //
        return null;
    }

    /**
     * Méthode de prévention des failles de sécurités en nettoyant les variables
     * passées en paramètre.
     *
     * @param mixed $input valeurs à netoyer
     *
     * @return mixed valeurs nétoyées
     */
    function clean_break($input) {
        if(is_array($input)) {
            foreach($input as $key => $value) {
                $input[$key] = $this->clean_break($value);
            }
        } else {
            //remove whitespace...
            $input = trim($input);
            //disable magic quotes...
            if(get_magic_quotes_gpc()) {
                stripslashes($input);
            }
            //prevent sql injection...
            if(!is_numeric($input)) {
                if(isset($this->db)) {
                    $this->db->escapeSimple($input);
                }
            }
            //prevent xss...
            $input = filter_var($input, FILTER_SANITIZE_STRING);
        }
        return $input;
    }

    /**
     * Méthode permettant d'attribuer les valeurs de POST et GET.
     *
     * @return void
     */
    function set_submitted_value() {
        // S'il s'agit d'un GET
        if(isset($_GET) and !empty($_GET)) {
            foreach ($_GET as $key => $value) {
                $this->submitted_get_value[$key]=$this->clean_break($value);
            }
        }

        // S'il s'agit d'un POST
        if(isset($_POST) and !empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->submitted_post_value[$key]=$this->clean_break($value);
            }
        }
    }

    /**
     * Permet d'empêcher l'accès aux scripts dédiés à la localisation.
     *
     * Cette méthode vérifie si la valeur de l'option de localisation est 
     * différente de 'sig_interne' et si c'est le cas d'afficher un message 
     * d'erreur puis d'arrêter l'exécution du script.
     * Exemple d'utilisation : 
     * <?php 
     * require_once "../obj/utils.class.php";
     * $f = new utils("nohtml");
     * $f->handle_if_no_localisation();
     * ?>
     */
    function handle_if_no_localisation() {
        //
        if ($this->getParameter("option_localisation") != "sig_interne") {
            //
            $class = "error";
            $message = _("L'option de localisation 'sig_interne' n'est pas active. Contactez votre administrateur.");
            $this->addToMessage($class, $message);
            //
            $this->setFlag(NULL);
            $this->display();
            //
            die();
        }
    }

    // }}}

    // {{{ message

    /**
     *
     * @return void
     */
    function addToMessage($class = "", $message = "") {
        array_push($this->message, array("class" => $class, "message" => $message));
    }



    // }}}

    // {{{

    /**
     * Ces categories permettent d'appeler les fichiers javascript dans un
     * ordre particulier. Il est alors possible d'ajouter un fichier javascript
     * dans chacune des categories.
     */
    var $head_js_category = array("begin", "middle", "end",);

    /**
     * C'est la categorie par defaut dans laquelle sont ajoutes tous les
     * fichiers javascript qui ne specifient pas de categorie.
     */
    var $head_js_default_category = "middle";

    /**
     * C'est le tableau qui sert a stocker tous les fichiers javascript.
     */
    var $html_head_js = array();

    /**
     *
     */
    var $html_head_css = array();


    /**
     *
     * @param array
     */
    function addHTMLHeadJs($js = array(), $order = NULL) {
        //
        if ($order == "begin") {
            $order = 10;
        } elseif ($order == "middle") {
            $order = 20;
        } elseif ($order == "end") {
            $order = 30;
        } elseif (!is_numeric($order) || is_null($order)) {
            $order = 20;
        }
        //
        if (!isset($this->html_head_js["add"])) {
            $this->html_head_js["add"] = array();
        }
        //
        if (!isset($this->html_head_js["add"][$order])) {
            $this->html_head_js["add"][$order] = array();
        }
        //
        if (is_array($js)) {
            foreach ($js as $value) {
                $this->html_head_js["add"][$order][] = $value;
            }
        } else {
            $this->html_head_js["add"][$order][] = $js;
        }
    }

    /**
     *
     * @param array
     */
    function setHTMLHeadJs($js = array(), $categories = false) {
        //
        if (!isset($this->html_head_js["set"])) {
            $this->html_head_js["set"] = array();
        }
        //
        if ($categories == true) {
            //
            foreach($js as $key => $value) {
                //
                if ($key == "begin") {
                    $key = 10;
                } elseif ($key == "middle") {
                    $key = 20;
                } elseif ($key == "end") {
                    $key = 30;
                } elseif (!is_numeric($key) || is_null($key)) {
                    $key = 20;
                }
                $this->html_head_js["set"][$key] = $js;
            }
            
        } else {
            //
            if (is_array($js)) {
                $this->html_head_js["set"][20] = $js;
            } else {
                $this->html_head_js["set"][20] = array($js, );
            }
        }
    }

    /**
     *
     * @param array
     */
    function addHTMLHeadCss($css = array()) {
        //
        if (is_array($css)) {
            foreach ($css as $value) {
                array_push($this->html_head_css, $value);
            }
        } else {
            array_push($this->html_head_css, $css);
        }
    }

    /**
     *
     * @param array
     */
    function setHTMLHeadCss($css = array()) {
        //
        if (is_array($css)) {
            $this->html_head_css = $css;
        } else {
            $this->html_head_css = array($css,);
        }
    }

    function displayAllScriptJsCall() {

        foreach ($this->head_js_category as $category) {
            if (isset($this->html_head_js[$category])) {
                foreach($this->html_head_js[$category] as $js) {
                    $this->displayScriptJsCall($js);
                }
            }
        }

    }

    // }}}

    // {{{ VRAC

    /**
     * Declaration de la methode lang pour assurer la compatibilite avec les
     * versions anterieures.
     *
     * @param string $text Texte a traduire
     * @deprecated
     */
    function lang($text = "") {

        return _($text);

    }

    /**
     * Declaration de la methode langentete pour assurer la compatibilite avec
     * les versions anterieures.
     *
     * @param string $text Texte a traduire
     * @deprecated
     */
    function langentete($text = "") {

        return _($text);

    }

    ///**
    // * @deprecated
    // */
    ////function utils() {}
    ////function connexion() {}
    ////function collectivite() {}
    ////function droit($obj) {}
    ////function aide($ico, $obj) {}
    ////function header($menu = 0, $ent = "", $ico = "", $obj = "") {}
    ////function titre($ent) {}
    ////function entete ($ent = "", $ico = "", $obj = "") {}
    ////function menu() {}
    ////function headerhtml() {}
    ////function deconnexion() {}
    ////function footerhtml() {}
    ////function footer() {}

    /**
     * Fonction tmp()
     *
     * @param $fichier string Nom du fichier
     * @param $msg string Contenu du fichier
     */
    function tmp($fichier, $msg, $entete=false) {
        if (!$entete) {
            $ent = date("d/m/Y G:i:s")."\n";
            $ent .= "Collectivite : ".$_SESSION ['coll']." - ".$this->collectivite ['ville']."\n";
            $ent .= "Utilisateur : ".$_SESSION ['login']."\n";
            $ent .= "==================================================\n";
            $msg = $ent."\n".$msg ;
        }
        @$enr = file_put_contents($fichier, $msg);
        if (!$enr) {
            $msg = _("Impossible d'ecrire le fichier de log :");
            $msg .= " ".$fichier.".";
            $msg .= " "._("Le dossier n'est probablement pas accessible en ecriture.");
            $msg .= " "._("Contactez votre administrateur.");
            $this->displayMessage ("error", $msg);
        }
        return $enr;
    }

    /**
     *
     */
    function formatDate ($date, $show = true) {

        $date_db = explode ('-', $date);
        $date_show = explode ('/', $date);

        if (count ($date_db) != 3 and count ($date_show) != 3) {
            return false;
        }

        if (count ($date_db) == 3) {
            if (!checkdate($date_db[1], $date_db[2], $date_db[0])) {
                return false;
            }
            if ($show == true) {
                return $date_db [2]."/".$date_db [1]."/".$date_db [0];
            } else {
                return $date;
            }
        }
        if (count ($date_show) == 3) {
            if (!checkdate($date_show[1], $date_show[0], $date_show[2])) {
                return false;
            }
            if ($show == true) {
                return $date;
            } else {
                return $date_show [2]."-".$date_show [1]."-".$date_show [0];
            }

        }
        return false;

    }

    // }}}

    // {{{ Gestion des messages de debug

    /**
     *
     */
    function addToLog($message, $type = DEBUG_MODE) {
        //
        logger::instance()->log($this->elapsedtime()." : class ".get_class($this)." - ".$message, $type);
    }

    function disableLog() {
        //
        logger::instance()->display_log = false;
    }
    // }}}

    // {{{ REDEFINITION DU MOT DE PASSE

    /**
     * Cree la table de redefinition du mot de passe.
     *
     * @access private
     * @return void
     */
    private function createPasswordResetTable() {

        //
        $sql = " CREATE TABLE ".DB_PREFIXE.$this->table_om_password_reset." (";
        $sql .= "id integer NOT NULL, ";
        $sql .= "login varchar(30) NOT NULL, ";
        $sql .= "reset_key varchar(50) NOT NULL, ";
        $sql .= "timeout float8 NOT NULL, ";
        $sql .= "PRIMARY KEY (id) );";

        //
        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->isDatabaseError($res);
    }

    /**
     * Recuperation du prochain id de la table de reinitialisation de mot de passe.
     * Si cette table n'existe pas, elle est cree et l'id renvoye est 1.
     *
     * @param int $id_column Nom de la colonne contenant l'identifiant de type int
     * @param string $table Nom de la table à interroger
     * @access private
     * @return int Valeur du prochain identifiant devant être insere
     */
    private function getNextPasswordResetId($id_column, $table) {

        $id = null;
        $table_exists = true;

        //
        $sql = " SELECT MAX(".$id_column.") AS id";
        $sql .= " FROM ".DB_PREFIXE.$table;
        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);

        // Si une erreur survient, la table est creee
        if($this->isDatabaseError($res, true)) {
            $table_exists = false;
            $this->createPasswordResetTable();
        }

        // Si la table existait deja
        if ($table_exists == true) {

            while ($row =& $res->fetchrow(DB_FETCHMODE_ASSOC)) {
                $id = $row;
            }

            // On retourne l'id MAX
            return $id['id'] + 1;

        // Sinon on retourne 1
        } else {
            return 1;
        }
    }

    /**
     * Ajoute une nouvelle cle dans la table de redifinition de mot de
     * passe.
     *
     * @param $login Login de l'utilisateur reinitialisant son mot de passe
     * @param $key Cle valide necessaire au changement de mot de passe
     * @param $timeout Date de creation de la cle
     * @access public
     * @return void
     */
    public function addPasswordResetKey($login, $key, $timeout) {

        // Recuperation du prochain id
        $id = $this->getNextPasswordResetId("id", $this->table_om_password_reset);

        //
        $sql = "INSERT INTO ".DB_PREFIXE.$this->table_om_password_reset;
        $sql .= " VALUES (".$id.", '".$login."', '".$key."', ".$timeout.");";

        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->isDatabaseError($res);
    }

    /**
     * Supprime les cles expirees.
     *
     * @access public
     * @return void
     */
    public function deleteExpiredKey() {

        $timestamp = time();
        $now = date("YmdHis", $timestamp);
        $sql = "DELETE FROM ".DB_PREFIXE.$this->table_om_password_reset;
        $sql .= " WHERE timeout < ".$now;

        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);

        // Si une erreur survient, la table est cree
        if($this->isDatabaseError($res, true)) {
            $table_exists = false;

            //
            $this->createPasswordResetTable();
            $res = $this->db->query($sql);
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            $this->isDatabaseError($res);
        }
    }

    /**
     * Teste l'existence d'une cle.
     *
     * @param string $key la cle à rechercher dans la base
     * @access public
     * @return string|bool Si la cle existe, le login de l'utilisateur associe est retourne
     * sinon la methode renvoie false.
     */
    public function passwordResetKeyExists($key) {

        $sql = "SELECT * FROM ".DB_PREFIXE.$this->table_om_password_reset;
        $sql .= " WHERE reset_key = '".$key."';";

        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);

        // Si une erreur survient
        if($this->isDatabaseError($res, true)) {

            // La table de redefinition est cree
            $table_exists = false;
            $this->createPasswordResetTable();

            // On execute à nouveau la requete precedente
            $res = $this->db->query($sql);
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            $this->isDatabaseError($res);
        }

        $row = null;
        if ($res->numRows() == 1) {
            $row =& $res->fetchRow(DB_FETCHMODE_ASSOC);
            return $row['login'];

        // Si il existe plusieurs cles avec la meme signature,
        // une erreur est renvoyee stoppant ainsi le processus
        // de redefinition du mot de passe.
        // L'utilisateur doit alors re-generer une nouvelle
        // cle. Les doublons de ses cles seront supprimes apres
        // le succes de son prochain changement de mot de passe.
        } elseif ($res->numRows() > 1) {
            $this->addToMessage("error", "Une erreur est survenue. Vous pouvez essayer ".
                                         "de redefinir votre mot de passe une nouvelle fois. ".
                                         "Si le probleme persiste, contactez votre administrateur.");
            return false;
        }
        $this->addToMessage("error", "La cle que vous avez valide n'existe pas ou a expiree.");

        return false;
    }

    /**
     * Supprime toutes les cles associes a un utilisateur.
     *
     * @param string $login Login de l'utilisateur
     * @access public
     * @return void
     */
    public function deletePasswordResetKeys($login) {

        $sql = "DELETE FROM ".DB_PREFIXE.$this->table_om_password_reset;
        $sql .= " WHERE login = '".$login."';";

        $res = $this->db->query($sql);
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        $this->isDatabaseError($res);
    }

    /**
     * Affichage du formulaire permettant de redefinir le mot de passe.
     *
     * @param int $coll Collectivite de l'utilisateur
     * @param string $login Login de l'utilisateur
     * @access public
     * @return void
     */
    public function displayPasswordResetLoginForm() {

        //
        (defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
        require_once PATH_OPENMAIRIE."om_formulaire.class.php";

        echo "<div class=\"pageDescription\">";
        echo "\t <p>Pour des raisons de securite, nous gardons votre mot de passe chiffre,";
        echo "\t et nous ne pouvons pas vous l'envoyer. Si vous souhaitez re-initialiser";
        echo "\t votre mot de passe, remplissez le formulaire ci-dessous et nous vous enverrons";
        echo "\t un courrier electronique a l'adresse que vous avez donnee lors de l'enregistrement";
        echo "\t pour demarrer la phase de re-initialisation de votre mot de passe.";
        echo "\t </p>";
        echo "</div>";

        echo "<form method=\"post\" id=\"resetpw_form\" action=\"".$this->url_password_reset."\">";

        //
        $validation = 0;
        $maj = 0;
        $champs = array("came_from", "login");

        if (count($this->database) > 1) {
            array_push($champs, "coll");
        }

        //
        $form = new formulaire(NULL, $validation, $maj, $champs);
        //
        $form->setType("came_from", "hidden");
        $form->setTaille("came_from", 20);
        $form->setMax("came_from", 20);
        $came_from = (isset($_POST['came_from']) ? $_POST['came_from'] : (isset($_GET['came_from']) ? $_GET['came_from'] : ""));
        $form->setVal("came_from", $came_from);
        //
        $form->setLib("login", _("Identifiant"));
        $form->setType("login", "text");
        $form->setTaille("login", 20);
        $form->setMax("login", 100);

        //
        if (count($this->database)>1) {
            $form->setLib("coll", _("Base de donnees"));
            $form->setType("coll", "select");
            $contenu = array(
                0 => array(),
                1 => array(),
            );
            foreach ($this->database as $key => $coll) {
                array_push($contenu[0], $key);
                array_push($contenu[1], $coll['title']);
            }
            $form->setSelect("coll", $contenu);
            if (isset($_SESSION['coll'])) {
                $form->setVal("coll", $_SESSION['coll']);
            }
        }
        //
        $form->entete();
        $form->afficher($champs, $validation, false, false);
        $form->enpied();

        //
        echo "\t\t<div class=\"formControls\">\n";
        echo "\t\t\t<input class=\"context boutonFormulaireLogin ui-button ui-state ui-corner-all\" ";
        echo "name=\"resetpwd_action_sendmail\" value=\""._("Lancer la re-initialisation du mot de passe")."\" type=\"submit\" />\n";
        echo "\t\t</div>\n";
        echo "\t</form>\n";
    }

    /**
     * Affichage du formulaire de saisi du nouveau mot de passe.
     *
     * @param int $coll Collectivite de l'utilisateur
     * @param string $login Login de l'utilisateur
     * @access public
     * @return void
     */
    public function displayPasswordResetPasswordForm($coll, $login) {

        //
        (defined("PATH_OPENMAIRIE") ? "" : define("PATH_OPENMAIRIE", ""));
        require_once PATH_OPENMAIRIE."om_formulaire.class.php";

        echo "<form method=\"post\" id=\"resetpw_form\" action=\"".$this->url_password_reset."\">";

        //
        $validation = 0;
        $maj = 0;
        $champs = array("came_from", "pwd_one", "pwd_two", "coll", "user_login");

        //
        $form = new formulaire(NULL, $validation, $maj, $champs);
        //
        $form->setType("came_from", "hidden");
        $form->setTaille("came_from", 20);
        $form->setMax("came_from", 20);
        $came_from = (isset($_POST['came_from']) ? $_POST['came_from'] : (isset($_GET['came_from']) ? $_GET['came_from'] : ""));
        $form->setVal("came_from", $came_from);
        //
        $form->setLib("pwd_one", _("Nouveau mot de passe"));
        $form->setType("pwd_one", "password");
        $form->setTaille("pwd_one", 20);
        $form->setMax("pwd_one", 100);
        //
        $form->setLib("pwd_two", _("Confirmation du mot de passe"));
        $form->setType("pwd_two", "password");
        $form->setTaille("pwd_two", 20);
        $form->setMax("pwd_two", 100);
        //
        $form->setLib("coll", "coll");
        $form->setType("coll", "hidden");
        $form->setVal("coll", $coll);
        //
        $form->setLib("user_login", "user_login");
        $form->setType("user_login", "hidden");
        $form->setVal("user_login", $login);
        //
        $form->entete();
        $form->afficher($champs, $validation, false, false);
        $form->enpied();

        //
        echo "\t\t<div class=\"formControls\">\n";
        echo "\t\t\t<input class=\"context boutonFormulaireLogin ui-button ui-state ui-corner-all\" ";
        echo "name=\"resetpwd_action_newpwd\" value=\""._("Definir mon mot de passe")."\" type=\"submit\" />\n";
        echo "\t\t</div>\n";
        echo "\t</form>\n";
    }

    /**
     * Envoie un mail.
     *
     * @param string $title Titre du mail
     * @param string $message Corps du mail
     * @param string $recipient Destinataire(s) du mail (séparés par une virgule)
     * @param array $file Liste de fichiers à envoyer en pièce jointe
     * @access public
     * @return bool True si le mail est correctement envoye, false sinon.
     *
     * @todo XXX Décrire le format du tableau des pièces jointes à envoyer et
     * vérifier l'ajout dans la configuration d'un mode secure (tls)
     * 
     */
    public function sendMail($title, $message, $recipient, $file = array()) {

        @include_once "class.phpmailer.php";

        if (!class_exists("PHPMailer")) {
            $this->addToLog(__METHOD__."(): !class_exists(\"PHPMailer\")", DEBUG_MODE);
            return false;
        }

        //
        $this->setMailConfig();

        //
        if ($this->mail_config == false) {
            $this->addToLog(__METHOD__."(): aucune configuration mail", DEBUG_MODE);
            return false;
        }

        //
        $mail = new PHPMailer(true);

        //
        $mail->IsSMTP();
        $mail->Username = $this->mail_config["mail_username"];
        $mail->Password = $this->mail_config["mail_pass"];
        if ($this->mail_config["mail_username"] == '') {
            $mail->SMTPAuth = false;
        } else {
            $mail->SMTPAuth = true;
        }
        $mail->Port = $this->mail_config["mail_port"];
        $mail->Host = $this->mail_config["mail_host"];
        $mail->AddReplyTo($this->mail_config["mail_from"], $this->mail_config["mail_from_name"]);
        $mail->From = $this->mail_config["mail_from"];
        $mail->FromName = $this->mail_config["mail_from_name"];
        // Gestion des destinataires du mail
        foreach (explode(",", $recipient) as $adresse) {
            if (!$this->checkValidEmailAddress($adresse)) {
                $this->addToLog(__METHOD__."(): courriel incorrect ".$adresse, DEBUG_MODE);
                return false;
            } else {
                $mail->AddAddress(trim($adresse));
            }
        }
        //
        $mail->IsHTML(true);

        // Corps du message
        $mail_body ="<html>";
        $mail_body .= "<head><title>".$title."</title></head>";
        $mail_body .= "<body>".$message."</body>";
        $mail_body .= "</html>";

        $mail->Subject  = $title;
        $mail->MsgHTML($mail_body);

        // Gestion des pièces jointes
        foreach ($file as $oneFile) {
            //
            if (isset($oneFile['stream'])) {
                $mail->AddStringAttachment($oneFile['content'], $oneFile['title'], $oneFile['encoding'] = 'base64', $oneFile['type'] = 'application/octet-stream');
            } else {
                $mail->AddAttachment($oneFile['url']);
            }
        }

        // Envoie de l'email
        try {
            $mail->Send();
            return true;
        } catch (phpmailerException $e) {
            $this->addToLog("sendMail(): ".$e->errorMessage(), DEBUG_MODE);
        } catch (Exception $e) {
            $this->addToLog("sendMail(): ".$e->getMessage(), DEBUG_MODE);
        }
        //
        return false;
    }

    /**
     * Genere une cle de 31 caracteres aplphanumerique minuscule
     * puis ajoute la date de cette maniere:
     *
     *  $hash .= date("YmdHis", time());
     *
     * @return string key
     * @access public
     * @return void
     */
    public function genPasswordResetKey() {
        $hash = "";
        $alphanumeric = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
            "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
            "u", "v", "w", "x", "y", "z", "0", "1", "2", "3",
            "4", "5", "6", "7", "8", "9");
        for ($i=0; $i<=30; $i++) {
            $rand = array_rand($alphanumeric);
            $hash .= $alphanumeric[$rand];
        }

        // ajout du temps pour eviter les collisions
        $hash .= date("YmdHis", time());
        return $hash;
    }

    public function checkValidEmailAddress($address = "") {
        return preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $address);
    }

    // }}}

    // {{{ GESTION DU LAYOUT

    /**
     *
     */
    function display() {
        //
        $this->layout->set_parameter("actions_personnelles", $this->getActionsToDisplay());
        $this->layout->set_parameter("raccourcis", $this->getShortlinksToDisplay());
        $this->layout->set_parameter("actions_globales", $this->getFooterToDisplay());
        $this->layout->set_parameter("menu", $this->getMenuToDisplay());
        //
        $this->layout->set_parameter("page_title", $this->title);
        $this->layout->set_parameter("page_description", $this->description);
        //
        $this->layout->set_parameter("application", $this->config["application"]);
        $this->layout->set_parameter("version", $this->version);
        $this->layout->set_parameter("html_title", $this->config["title"]);
        $this->layout->set_parameter("url_dashboard", $this->url_dashboard);
        //
        $this->layout->set_parameter("style_header", $this->style_header);
        $this->layout->set_parameter("style_title", $this->style_title);
        //
        $this->layout->set_parameter("html_head_css", $this->html_head_css);
        $this->layout->set_parameter("html_head_js", $this->html_head_js);
        $this->layout->set_parameter("head_js_category", $this->head_js_category);
        //
        $this->layout->set_parameter("collectivite", $this->collectivite);
        //
        $this->layout->set_parameter("messages", $this->message);
        //
        $this->layout->set_parameter("flag", $this->flag);
        //
        $this->layout->display();
    }

    /**
     *
     */
    var $html_head_extras = NULL;
    function setHTMLHeadExtras($html_head_extras = "") {
        $this->html_head_extras = $html_head_extras;
        $this->layout->set_parameter("html_head_extras", $this->html_head_extras);
    }

    /**
     *
     */
    var $html_body = NULL;
    function setHTMLBody($html_body = "") {
        $this->html_body = $html_body;
        $this->layout->set_parameter("html_body", $this->html_body);
    }

    /**
     *
     */
    var $style_header = "ui-widget-header";
    function addStyleForHeader($style = "") { $this->style_header .= " ".$style; }
    function setStyleForHeader($style = "") {
        $this->style_header = $style;
        $this->layout->set_parameter("style_header", $this->style_header);
    }
    function getStyleForHeader() { return $this->style_header; }

    /**
     *
     */
    var $style_title = "ui-state-active ui-corner-all";
    function addStyleForTitle($style = "") { $this->style_title .= " ".$style; }
    function setStyleForTitle($style = "") {
        $this->style_title = $style;
        $this->layout->set_parameter("style_title", $this->style_title);
    }
    function getStyleForTitle() { return $this->style_title; }

    /**
     *
     */
    function displayHeader() {
        $this->layout->display_header();
    }
    function displayFooter() {
        $this->layout->display_footer();
    }
     function displayFooterTabSig() {
        $this->layout->display_footer_tab_sig();
    }
    function displayStartContent() {
        $this->layout->display_content_start();
    }
    function displayEndContent() {
        $this->layout->display_content_end();
    }
    function displayHTMLHeader() {
        $this->layout->display_html_header();
    }
    function displayHTMLFooter() {
        $this->layout->display_html_footer();
    }
    function displayTitle($page_title = "") {
        if ($page_title == "") {
            $page_title = $this->title;
        }
        $this->layout->display_page_title($page_title);
    }
    function displayLogo() {
        $this->layout->display_logo();
    }
    function displayMenu() {
        $this->layout->display_menu();
    }
    function displayDescription($description = "") {
        $this->layout->display_page_description($description);
    }
    function displayActionLogin() {
        $this->layout->display_action_login();
    }
    function displayActionCollectivite() {
        $this->layout->display_action_collectivite();
    }
    function displayActionExtras() {
        $this->layout->display_action_extras();
    }
    function displayActions() {
        $this->layout->display_actions();
    }
    function displaySubTitle($page_subtitle = NULL) {
        $this->layout->display_page_subtitle($page_subtitle);
    }
    function displayLinkJsCloseWindow($js_function_close = "") {
        $this->layout->display_link_js_close_window($js_function_close);
    }
    function displayMessage($class = "", $message = "") {
        if (!defined('REST_REQUEST')) {
            $this->layout->display_message($class, $message);
        }
    }
    function displayMessages() {
        $this->layout->set_parameter("messages", $this->message);
        $this->layout->display_messages();
    }
    function displayScriptJsCall($js = "") {
        $this->layout->display_script_js_call($js);
    }

    // }}}
    
}

/**
 * Declaration de la fonction lang pour assurer la compatibilite avec les
 * versions anterieures.
 *
 * @param string $text Texte a traduire
 * @deprecated
 */
function lang($text = "") {

    return _($text);

}

?>
