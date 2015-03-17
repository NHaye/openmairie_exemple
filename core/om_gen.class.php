<?php
/**
 * Ce script permet de déclarer la classe gen.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_gen.class.php 3096 2015-03-12 18:27:23Z fmichon $
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
 * @todo XXX remplacer les appels directs à logger par addtolog
 * @todo XXX remplacer les "@todo public" par l'attribut correct
 */
class gen {

    /**
     * Instance de la classe utils
     * @var resource 
     */
    var $f = null;

    /**
     * Nom de la table en cours de traitement
     * @var string
     */
    var $table = "";

    /**
     * Chaine de caractères stockant le message de retour du traitement pour 
     * l'utilisateur
     * @todo public
     * @var string
     */
    var $msg = "";

    /**
     * Type de la colonne clé primaire de la table en cours de traitement
     * A : clé alphanumérique ou N : clé numérique
     * @var string 
     */
    var $typecle = "";

    /**
     * Longueur de l'enregistrement de la table en cours de traitement
     * utilisée pour la largeur des colonnes dans la généréation des
     * pdf
     * @todo XXX Vérifier l'utilité de cet élément
     * @var integer
     */
    var $longueur = 0;

    /**
     *
     */
    var $info = array(); // description de la table

    /**
     *
     */
    var $clesecondaire = array(); // cles secondaires

    /**
     *
     */
    var $geom = array(); // champs geom
    
    /**
     *
     */
    var $sousformulaires = array(); // table en sousformulaire
    
    /**
     *
     */
    var $tablebase = array(); // tables de la base

    /**
     * Marqueur indiquant la présence de la colonne 'om_collectivite'
     * dans la table en cours de traitement.
     * - 1 : la colonne n'est pas présente
     * - 2 : la colonne n'est pas présente
     * @var integer
     */
    var $multi = 1;

    /**
     * Nom de la colonne clé primaire de la table en cours de traitement
     * @var string
     */
    var $primary_key;

    /**
     * Liste des cles etrangeres de la table actuelle.
     *
     * array_keys : noms des cles etrangeres.
     * array_values : tableaux d'informations sur les tables etrangeres.
     *
     * @var array(string)
     */
    var $foreign_tables = array();

    /**
     * Liste des couples "table.colonne" faisant reference a la table actuelle.
     *
     * array_keys : index numerique.
     * array_values : chaines de caracteres de la forme "table.colonne".
     *
     * @var array(string)
     */
    var $other_tables = array();

    /**
     * @var array(string) nom des colonnes uniques
     */
    var $unique_key = array();

    /**
     * @var array(string) nom des colonnes d'une cle uniques
     */
    var $unique_multiple_key = array();

    /**
     * @var array(string) liste des colonnes NOT NULL
     */
    var $_columns_notnull = array();

    /**
     * Marqueur indiquant la présence de la colonne 'om_validite_debut'
     * dans la table en cours de traitement
     * @var boolean
     */
    var $_om_validite_debut = false;

    /**
     * Marqueur indiquant la présence de la colonne 'om_validite_fin'
     * dans la table en cours de traitement
     * @var boolean 
     */
    var $_om_validite_fin = false;

    /**
     * Chaine de caractères représentant l'entête (deux premières lignes) des 
     * scripts PHP générés.
     * @var string
     */
    var $_php_script_header = null;

    /**
     * Tableau de configuration.
     *
     * Ce tableau de configuration permet de donner des informations de surcharges
     * sur certains objets pour qu'elles soient prises en compte par le générateur.
     * $_tables_to_overload = array(
     *    "<table>" => array(
     *        // définition de la liste des classes qui surchargent la classe
     *        // <table> pour que le générateur puisse générer ces surcharges 
     *        // et les inclure dans les tests de sous formulaire
     *        "extended_class" => array("<classe_surcharge_1_de_table>", ),
     *        // définition de la liste des champs à afficher dans l'affichage du
     *        // tableau champAffiche dans <table>.inc.php
     *        "displayed_fields_in_tableinc" => array("<champ_1>", ),
     *    ),
     * );
     *
     * @var mixed
     */
    var $_tables_to_overload = array();

    /**
     * Constructeur.
     *
     * @todo public
     */
    function __construct() {
        // 
        if (isset($GLOBALS["f"])) {
            $this->f = $GLOBALS["f"];
        }
    }

    /**
     *
     */
    function get_all_tables_from_database() {
        //
        $tables = array();
        //
        if (OM_DB_PHPTYPE == "mysql") {
            $sql = "SHOW TABLES FROM `".DB_PREFIXE.OM_DB_DATABASE."`";
        }
        if (OM_DB_PHPTYPE == "pgsql") {
            $sql = "select tablename from pg_tables where schemaname='".OM_DB_SCHEMA."' UNION select viewname from pg_views where schemaname='".OM_DB_SCHEMA."'";
        }
        // Exécution de la requête
        $res = $this->f->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        // Recuperation de la liste de toutes les tables de la base de donnees
        while ($row =& $res->fetchRow()) {
            // On enleve de la liste les sequences
            if (substr($row[0], -3, 3) != "seq") {
                //
                $tables[] = $row[0];
            }
        }
        //
        asort($tables);
        //
        return $tables;
    }

    /**
     *
     */
    function get_fields_list_from_table($table) {
        //
        $fields = array();
        //
        $infos = $this->f->db->tableInfo(DB_PREFIXE.$table);
        // Logger
        $this->addToLog(__METHOD__."(): db->tableInfo(\"".DB_PREFIXE.$table."\")", VERBOSE_MODE);
        //
        foreach ($infos as $key => $value) {
            $fields[] = $value["name"];
        }
        //
        asort($fields);
        //
        return $fields;
    }

    /**
     * Initialisation obligatoire des paramètres pour la table à générer.
     * 
     * @param string $table Nom de la table.
     *
     * @todo public
     */
    function init_generation_for_table($table) {

        // On stocke dans l'attribut table le nom de la table passé en 
        // paramètre à traiter
        $this->table = $table;

        // Cette méthode permet de récupérer les fichiers configurations
        // pour initialiser les paramètres et permettre leur utilisation
        // dans les méthodes de la classe
        $this->init_configuration();

        // On récupère la liste des tables de la base de données à laquelle on
        // enlève la table sur laquelle on est en train de faire la génération
        $this->tablebase = array_diff(
            $this->get_all_tables_from_database(),
            array($this->table, )
        );

        // RECUPERATION DES INFORMATIONS SUR LA TABLE SELECTIONNEE
        //
        $this->msg="<span class=\"bold\">"._("Table")." : ".$this->table."</span><br />";
        // Recuperation des informations de la table
        $this->info = $this->f->db->tableInfo(DB_PREFIXE.$this->table);
        // Logger
        $this->addToLog(__METHOD__."(): DB PEAR \$this->info = ".print_r($this->info, true), EXTRA_VERBOSE_MODE);
        // recuperation de la cle primaire
        $this->primary_key = $this->get_primary_key($this->table);
        // initialisation de la liste des colonnes NOT NULL
        $this->_init_constraint_notnull();
        // Chargement des tables de cles uniques et uniques multiple
        $this->set_unique_key($this->table);

        /** 
         * Définition de tous les paramètres par défaut
         */
        // Taille d'affichage du champ text (nombre de lignes)
        $max = 6;
        // Taille d'affichage du champ text (nombre de colonnes)
        $taille = 80;
        // Taille d'affichage du champ par defaut dans le cas ou nous sommes
        // dans l'impossibilite de determiner la taille du champ
        $pgsql_taille_defaut = 20;
        // Taille d'affichage du champ minimum pour ne pas afficher des
        // champs trop petits ou la saisie serait impossible
        $pgsql_taille_minimum = 10;
        // Taille d'affichage du champ maximum pour ne pas afficher des
        // champs trop grands ou le formulaire depasserait de l'ecran
        $pgsql_taille_maximum = 30;
        // 
        $pgsql_longueur_date = 12; // taille d'affichage de la date '
        // Surcharge des paramètres par défaut possible
        if (file_exists ("../gen/dyn/form.inc.php")){
            include ("../gen/dyn/form.inc.php");
            $this->msg.="<br />"._("Chargement du parametrage")." ../gen/dyn/form.inc.php";
        } elseif (file_exists ("../gen/dyn/form.inc")){
            include ("../gen/dyn/form.inc");
            $this->msg.="<br />"._("Chargement du parametrage")." ../gen/dyn/form.inc";
        }

        // CLES ETRANGERE
        // Initialisation de la liste des clés étrangères (contraintes
        // FOREIGN KEY) de la table en cours vers les autres tables
        $this->_init_foreign_tables();
        // SOUS FORMULAIRE
        // Initialisation de la liste des clés étrangères (contraintes
        // FOREIGN KEY) des autres tables vers la table en cours
        $this->_init_other_tables();

        // POSTULAT DE DEPART SUR LES TYPES DE DONNEES
        // variable chaine = string
        // variable numerique = int
        // variable textareal = blob
        // SPECIFICITES PGSQL POUR LES INFORMATIONS SUR LES COLONNES DE LA TABLE
        if (OM_DB_PHPTYPE == 'pgsql') {
            // Recherche des attributs dans les tables systèmes de PostgreSQL
            // Constitution d'une table de valeurs des colonnes de la table
            // utilisation de ces valeurs pour le générateur
            //
            // initialisation du tableau associatif comportant les attributs de champs
            $attchamps=array();
            // requête donnant les attributs par champ
            $sql = "SELECT attname, attnotnull, atttypmod FROM pg_attribute WHERE ";
            $sql .= "attrelid = '".DB_PREFIXE.$this->table."'::regclass ";
            $sql .= "AND attstattarget = -1;";
            //
            $res = $this->f->db->query($sql);
            // Logger
            $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
            // 
            $this->f->isDatabaseError($res);
            //remplissage du tableau associatif
            while ($res->fetchInto($row, DB_FETCHMODE_ASSOC) ) {
                // Remplissage de la table des attributs de champs
                // DB_FETCHMODE_ORDERED
                $attchamps[$row["attname"]]=array("attnotnull"=> $row["attnotnull"], "atttypmod"=>$row["atttypmod"]);
            }
            // boucle sur les champs de la table openmairie pour définir les attributs des champs
            for ($t = 0; $t < count($this->info); $t++) {
                // test d'existence du champ dans les résultats de requête
                if ( isset( $attchamps[$this->info[$t]["name"]] ) ) {
                    // la valeur existe
                    // champ notnull
                    $this->info[$t]["notnull"] = $attchamps[$this->info[$t]["name"]]["attnotnull"];
                    // champ atttypmod : fixer la taille pour les champs ayant len à -1
                    if ($this->info[$t]["len"] == -1) {
                        $this->info[$t]["len"] = $attchamps[$this->info[$t]["name"]]["atttypmod"]-4;
                        //
                        $this->info[$t]["max_saisie"] = $this->info[$t]["len"];
                        //
                        if ($this->info[$t]["len"] < $pgsql_taille_minimum) {
                            $this->info[$t]["taille_affichage"] = $pgsql_taille_minimum;
                        } elseif ($this->info[$t]["len"] > $pgsql_taille_maximum) {
                            $this->info[$t]["taille_affichage"] = $pgsql_taille_maximum;
                        } else {
                            $this->info[$t]["taille_affichage"] = $this->info[$t]["len"];
                        }
                    }
                } else {
                    // la valeur n'existe pas ; on utilise les valeurs par défaut
                    // champ notnull
                    $this->info[$t]["notnull"] = 'f';
                    // champ atttypmod : fixer la taille pour les champs ayant len à -1
                    if ($this->info[$t]["len"] == -1) {
                        $this->info[$t]["len"] = $pgsql_taille_defaut;
                        //
                        $this->info[$t]["taille_affichage"] = $this->info[$t]["len"];
                        $this->info[$t]["max_saisie"] = $this->info[$t]["len"];
                    }
                }
                // Taille des champs numeriques XXX Completer les tailles de champs en fonction des types
                if ($this->info[$t]["type"] == "int2") {
                    $this->info[$t]["type"] = "int";
                    $this->info[$t]["len"] = 6;
                } elseif ($this->info[$t]["type"] == "int4") {
                    $this->info[$t]["type"] = "int";
                    $this->info[$t]["len"] = 11;
                } elseif ($this->info[$t]["type"] == "int8") {
                    $this->info[$t]["type"] = "int";
                    $this->info[$t]["len"] = 20;
                } elseif (substr($this->info[$t]["type"], 0, 3) == "int") {
                    $this->info[$t]["len"] = 11;
                    $this->info[$t]["type"] = "int";
                } elseif (substr($this->info[$t]["type"], 0, 5) == "float"
                    or $this->info[$t]["type"] == "numeric"
                    or $this->info[$t]["type"] == "money") {
                    // float a gerer avec la recuperation des attributs dans la base
                    $this->info[$t]["type"] = "float";
                    $this->info[$t]["len"] = 20;
                } elseif ($this->info[$t]["type"] == "bpchar"
                    or $this->info[$t]["type"] == "char"
                    or $this->info[$t]["type"] == "varchar"
                    or $this->info[$t]["type"] == "character varying") {
                    // STRING
                    $this->info[$t]["type"] = "string";
                } elseif ($this->info[$t]["type"] == "text") {
                    // text = len -1
                    // TEXT
                    $this->info[$t]["type"] = "blob";
                    $this->info[$t]["max_saisie"] = $max;
                    $this->info[$t]["taille_affichage"] = $taille;
                } elseif ($this->info[$t]["type"] == "boolean") {
                    // BOOL
                    $this->info[$t]["type"] = "bool";
                } elseif ($this->info[$t]["type"] == "date") { // date = len 4
                    // Taille des colonnes de type DATE
                    $this->info[$t]["len"] = $pgsql_longueur_date;
                } elseif ($this->info[$t]["type"] == "geometry") {
                    $this->info[$t]["type"] = "geom";
                }
            }
        }
        // SPECIFICITES MYSQL POUR LES INFORMATIONS SUR LES COLONNES DE LA TABLE
        if (OM_DB_PHPTYPE == "mysql") {
            //
            for ($t =0; $t < count($this->info); $t++) {
                // NULL / NOT NULL
                // Gestion de l'attribut not null pour mysql
                if (strpos($this->info[$t]["flags"], "not_null") !== false) {
                    $this->info[$t]["notnull"] = 't';
                } else {
                    $this->info[$t]["notnull"] = 'f';
                }
                // INT
                if ($this->info[$t]["type"] == "int") {
                    $this->info[$t]["type"] = "int";
                    if ($this->info[$t]["len"] == 1) {
                        // gestion du tinyint comme booléen
                        $this->info[$t]["len"] = 1;
                    } elseif($this->info[$t]["len"] <= 2) {
                        $this->info[$t]["len"] = 6;
                    } elseif ($this->info[$t]["len"] <= 4) {
                        $this->info[$t]["len"] = 11;
                    } elseif ($this->info[$t]["len"] <= 8) {
                        $this->info[$t]["len"] = 20;
                    }
                } elseif ($this->info[$t]["type"] == "decimal"
                    or $this->info[$t]["type"] == "real"
                    or $this->info[$t]["type"] == "double") {
                    // float a gerer avec la recuperation des attributs dans la base
                    $this->info[$t]["type"] = "float";
                    $this->info[$t]["len"] = 20;
                }
                // STRING
                if ($this->info[$t]["type"] =="char"
                    or $this->info[$t]["type"] =="varchar"
                    or $this->info[$t]["type"] =="string") {
                    // char len = 1 (meme pour longueur =10) / character : bpchar len=-1 / character varying : varchar len=-1
                    $this->info[$t]["type"] = "string";
                    // TAILLE & MAX
                    $this->info[$t]["max_saisie"] = $this->info[$t]["len"];
                    if ($this->info[$t]["len"] < $pgsql_taille_minimum) {
                        $this->info[$t]["taille_affichage"] = $pgsql_taille_minimum;
                    } elseif ($this->info[$t]["len"] > $pgsql_taille_maximum) {
                        $this->info[$t]["taille_affichage"] = $pgsql_taille_maximum;
                    } else {
                        $this->info[$t]["taille_affichage"] = $this->info[$t]["len"];
                    }
                }
                // BOOL
                // Gestion du booleen pour mysql en partant du principe que
                // tout champ de type entier et de taille 1 est un booleen
                if ($this->info[$t]["type"] == "int"
                    && $this->info[$t]["len"] == "1") {
                    $this->info[$t]["type"] = "bool";
                }
                // BLOB
                if ($this->info[$t]["type"] == "blob") {
                    $this->info[$t]["max_saisie"] = $max;
                    $this->info[$t]["taille_affichage"] = $taille;
                }
            }
        }
        // TRAITEMENT STANDARD DES INFORMATIONS SUR LES COLONNES DE LA TABLE
        // Boucle sur chaque champ de la table
        foreach ($this->info as $key => $elem) {

            if ($elem['name'] == 'om_validite_debut') {
                $this->_om_validite_debut = true;
            }

            if ($elem['name'] == 'om_validite_fin') {
                $this->_om_validite_fin = true;
            }

            // longueur enregistrement (sans blob)
            if($elem["type"]!="blob") { // exclusion des blob (mysql)
                $this->longueur= $this->longueur+$elem["len"];
            }

            // cle num ou alpha_num
            if($elem['name'] == $this->primary_key) {
                if($elem['type'] == 'string') {
                    $this->typecle = 'A';
                } else {
                    $this->typecle = 'N';
                }
            } // primary key

            // table multi ayant un champ om_collectivite
            if ($elem["name"] == 'om_collectivite') {
                //
                $this->multi = "2";
            }

            // champs geom
            if ($elem["type"] == "geom"){
                array_push($this->geom, $elem["name"]);
            }
        }

        // Logger
        $this->addToLog(__METHOD__."(): \$this->info = ".print_r($this->info, true), EXTRA_VERBOSE_MODE);
    }

    // ------------------------------------------------------------------------
    // {{{ START - CONSTRUCTION DES CONTENUS DES SCRIPTS
    //     Toutes les méthodes de ce groupe renvoi des chaines de caractères
    //     Méthodes permettant de construire l'intégralité d'un script
    //     - table_sql_inc, table_sql_inc_core, table_sql_inc_gen
    //     - table_sql_forminc, table_sql_forminc_core, table_sql_forminc_gen
    //     - table_obj_class, table_obj_class_core, table_obj_class_gen
    //     - table_sql_pdfinc, table_sql_reqmoinc, table_sql_importinc
    //     Méthodes permettant de construire des parties d'un script
    //     - def_*
    // ------------------------------------------------------------------------

    /**
     * Renvoi l'entête des scripts PHP générés.
     *     
     * Attention repris en modification de fichier.
     *
     * @return string
     */
    function def_php_script_header() {
        // Si l'attribut n'est pas défini alors on le définit
        if ($this->_php_script_header == null) {
            $this->_php_script_header = "<?php\n//\$Id\$ \n//gen openMairie le ".date('d/m/Y H:i')."\n";
        }
        // On renvoi l'attribut
        return $this->_php_script_header;
    }

    /**
     * Construit le contenu du script [sql/<OM_DB_PHPTYPE>/<TABLE>.inc.php].
     *
     * @todo public
     *
     * @return string
     */
    function table_sql_inc() {

        //
        if ($this->is_omframework_table($this->table)) {
            //
            $template = "%s
include \"../core/sql/%s/%s.inc.php\";

?>
";
        } else {
            //
            $template = "%s
include \"../gen/sql/%s/%s.inc.php\";

?>
";
        }
        //
        return sprintf(
            $template,
            $this->def_php_script_header(),
            OM_DB_PHPTYPE,
            $this->table
        );

    }

    /**
     * Construit le contenu du script [core/sql/<OM_DB_PHPTYPE>/<TABLE>.inc.php].
     *
     * @todo public
     *
     * @return string
     */
    function table_sql_inc_core() {

        //
        if ($this->is_omframework_table($this->table)) {
            //
            $template = "%s
include \"../gen/sql/%s/%s.inc.php\";

?>
";
            //
            return sprintf(
                $template,
                $this->def_php_script_header(),
                OM_DB_PHPTYPE,
                $this->table
            );
        } else {
            //
            return "";
        }

    }

    /**
     * Construit le contenu du script [gen/sql/<OM_DB_PHPTYPE>/<TABLE>.inc.php].
     *
     * @param mixed $dyn Fichier de paramétrage.
     *
     * @todo public
     * @return string
     */
    function table_sql_inc_gen($dyn = null) {
        // Initialisation des variables de travail
        $champaffiche = "";
        $champnonaffiche = "";
        $champrecherche = "";
        //Test si plusieurs cles etrangeres vers la meme table
        $fkLinkedTable=array();
        foreach ($this->foreign_tables as $value) {
            $fkLinkedTable[]=$value['foreign_table_name'];
        }
        //Tableau avec nom de table comme cle et nombre d'occurence comme valeurs
        $countLinkedTable=array_count_values($fkLinkedTable);
        //
        $tri = ""; // champ de tri

        // table du libelle
        $libelle_t = '';
        // colonne du libelle
        $libelle_c = '';
        // alias de la table du libelle
        $alias = '';

        // recuperation du libelle
        $libelle = $this->get_libelle_of($this->table);

        // si la colonne du libelle est une cle etrangere
        // on utilise le libelle de la table etrangere pour le trier
        if (in_array($libelle, $this->clesecondaire)) {
            if($countLinkedTable[$this->foreign_tables[$libelle]['foreign_table_name']]>1) {
                $alias = $this->foreign_tables[$libelle]['foreign_table_name'].
                            array_search($libelle, $this->clesecondaire);
            } else {
                $alias = $this->foreign_tables[$libelle]['foreign_table_name'];
            }
            $libelle_t = $this->foreign_tables[$libelle]['foreign_table_name'];
            $libelle_c = $this->get_libelle_of($libelle_t);

        
        } else {
            // sinon on affiche la colonne telle quelle
            $alias = $this->table;
            $libelle_t = $this->table;
            $libelle_c = $libelle;
        }

        // creation de la clause ORDER BY
        $tri = 'ORDER BY';
        $tri .= ' ';

        // affichage des valeurs NULL en dernier pour MySQL
        if (OM_DB_PHPTYPE == 'mysql') {
            $tri .= 'ISNULL('.$alias.'.'.$libelle_c.') ASC,';
            $tri .= ' ';
        }

        // ordre croissant explicite
        $tri .= $alias.'.'.$libelle_c.' ASC';

        // affichage des valeurs NULL en dernier pour PostgresSQL
        if (OM_DB_PHPTYPE == 'pgsql') {
            $tri .= ' ';
            $tri .= 'NULLS LAST';
        }

        $serie = 15; // nombre d'enregistrement par page
        $ico = "../img/ico_application.png"; // icone de l'application
        //
        if (file_exists ("../gen/dyn/form.inc.php")){
            include ("../gen/dyn/form.inc.php");
            $this->msg.="<br />"._("Chargement du parametrage")." ../gen/dyn/form.inc.php";
        } elseif (file_exists ("../gen/dyn/form.inc")){
            include ("../gen/dyn/form.inc");
            $this->msg.="<br />"._("Chargement du parametrage")." ../gen/dyn/form.inc";
        }

        //
        $edition=$this->table;
        
        // On ajoute la clé primaire dans le champaffiche et le champrecherche
        // en première position
        $champaffiche.= "\n    '".$this->table.".".$this->primary_key." as \"'._(\"".$this->primary_key."\").'\"',";
        $champrecherche.= "\n    '".$this->table.".".$this->primary_key." as \"'._(\"".$this->primary_key."\").'\"',";

        // variable suivant champs : champaffiche et champrecherche
        // $champaffiche; // tableau des noms des champs affiches dans table.inc
        // $champrecherche; // tableau des noms des champs de recherche dans table.inc
        foreach ($this->info as $elem) {

            if ($elem['name'] == $this->primary_key) {
                continue;
            }

            //
            $is_field_in_overrided_list = null;
            if (isset($this->_tables_to_overload[$this->table]) 
                && isset($this->_tables_to_overload[$this->table]["displayed_fields_in_tableinc"])) {
                //
                if (in_array($elem["name"], $this->_tables_to_overload[$this->table]["displayed_fields_in_tableinc"])) {
                    $is_field_in_overrided_list = true;
                } else {
                    $is_field_in_overrided_list = false;
                }
            }
            // pas d affichage de blob en tableinc
            // affichage au format date
            $temp ='def_champaffichedate'.OM_DB_PHPTYPE;
            //
            if ($elem["type"] != "blob" 
                && !($this->multi == 2 && $elem["name"] == "om_collectivite" && $this->table != "om_collectivite")
                && $is_field_in_overrided_list !== false) {
                //
                $champaffiche.="\n    ";
                //
                if ($elem["type"] == "date") {
                    //
                    $champaffiche .= $this->$temp($this->table.".".$elem["name"], $elem["name"]).",";
                } elseif ($elem["type"] == "bool") {
                    //
                    if (OM_DB_PHPTYPE == "mysql") {
                        //
                        $champaffiche.= "\"case ".$this->table.".".$elem["name"]." when 1 then 'Oui' else 'Non' end as \\\"\"._(\"".$elem["name"]."\").\"\\\"\",";
                    } elseif (OM_DB_PHPTYPE == "pgsql") {
                        //
                        $champaffiche.= "\"case ".$this->table.".".$elem["name"]." when 't' then 'Oui' else 'Non' end as \\\"\"._(\"".$elem["name"]."\").\"\\\"\",";
                    }
                } else {

                    // Si le champ que nous sommes en train d'afficher est une cle secondaire
                    if (!empty($this->clesecondaire) && in_array($elem["name"], $this->clesecondaire)) {

                        $ftable = $this->foreign_tables[$elem["name"]]['foreign_table_name'];

                        // recuperation du libelle
                        $flibelle = $this->get_libelle_of($ftable);

                        if($countLinkedTable[$this->foreign_tables[$elem["name"]]['foreign_table_name']]>1) {
                            $champaffiche.= "'$ftable".
                                array_search($elem["name"], $this->clesecondaire).
                                '.'.$flibelle;
                        } else {
                            $champaffiche.= "'".$ftable.".".$flibelle;
                        }
                        
                    } else {
                        $champaffiche.= "'".$this->table.".".$elem["name"];
                    }

                    $champaffiche.= ' ';
                    $champaffiche.= "as \"'._(\"".$elem["name"]."\").'\"',";
                }
            } else {
                $champnonaffiche.="\n    ";
                $champnonaffiche.= "'".$this->table.".".$elem["name"]." as \"'._(\"".$elem["name"]."\").'\"',";
            }
            // 
            if (($elem["type"] == "string" || $elem["type"] == "int" || $elem["type"] == "float") && $is_field_in_overrided_list !== false) {
                //
                if ($this->multi == 2 && $elem["name"] == "om_collectivite" && $this->table != "om_collectivite") {
                    //
                    echo "";
                } else {

                    $champrecherche.="\n    ";
                    // Si le champ que nous sommes en train d'afficher est une cle secondaire
                    if (!empty($this->clesecondaire) && in_array($elem["name"], $this->clesecondaire)) {

                        $ftable = $this->foreign_tables[$elem["name"]]['foreign_table_name'];

                        // recuperation du libelle
                        $flibelle = $this->get_libelle_of($ftable);
                        if($countLinkedTable[$this->foreign_tables[$elem["name"]]['foreign_table_name']]>1) {
                            $champrecherche.= "'".$ftable.array_search($elem["name"], $this->clesecondaire).".".$flibelle." ";
                        } else {
                            $champrecherche.= "'".$ftable.".".$flibelle." ";
                        }
                    } else {
                         $champrecherche.="'".$this->table.".".$elem["name"]." ";
                    }

                    $champrecherche.= "as \"'._(\"".$elem["name"]."\").'\"',";
                }
            }
        }
        // creation de table.inc.php
        $tableinc= $this->def_php_script_header();
        $tableinc.="\n\$DEBUG=0;";
        $tableinc.="\n\$serie=".$serie.";";
        $tableinc.="\n\$ico=\"".$ico."\";";
        $tableinc.=$this->def_ent();

        if ($this->is_om_validite() == true) {
            $tableinc.="\n\$om_validite = true;";
        }

        $tableinc.="\nif(!isset(\$premier)) \$premier='';";
        $tableinc.="\nif(!isset(\$recherche1)) \$recherche1='';";
        $tableinc.="\nif(!isset(\$tricolsf)) \$tricolsf='';";
        $tableinc.="\nif(!isset(\$premiersf)) \$premiersf='';";
        $tableinc.="\nif(!isset(\$selection)) \$selection='';";
        $tableinc.="\nif(!isset(\$retourformulaire)) \$retourformulaire='';";
        // idz entete onglet
        $tableinc.="\nif (isset(\$idx) && \$idx != ']' && trim(\$idx) != '') {";
        $tableinc.="\n    \$ent .= \"->&nbsp;\".\$idx.\"&nbsp;\";";
        $tableinc.="\n}";
        $tableinc.="\nif (isset(\$idz) && trim(\$idz) != '') {";
        $tableinc.="\n    \$ent .= \"&nbsp;\".strtoupper(\$idz).\"&nbsp;\";";
        $tableinc.="\n}";
        // ***
        // TABLE
        $tableinc .= "\n// FROM ";
        $tableinc .= "\n\$table = DB_PREFIXE.\"".$this->table;
        //
        if (!empty($this->clesecondaire)) {
            //
            foreach ($this->clesecondaire as $key => $elem) {
                //
                if (isset($this->foreign_tables[$elem])) {
                    $tableinc .= "\n    LEFT JOIN \".DB_PREFIXE.\"".$this->foreign_tables[$elem]["foreign_table_name"]." ";
                    $ftable=$this->foreign_tables[$elem]['foreign_table_name'];
                    if($countLinkedTable[$ftable]>1) {
                        $tableinc .= "as $ftable".$key." ";
                    }
                    $tableinc .= "\n        ON ".$this->table.".".$elem."=";
                    if($countLinkedTable[$ftable]>1) {
                        $tableinc .= "$ftable".$key;
                    } else {
                        $tableinc .=  $ftable;
                    }
                    $tableinc .= ".".$this->foreign_tables[$elem]["foreign_column_name"]." ";
                    
                } else {
                    //
                    $tableinc .= "\n    LEFT JOIN \".DB_PREFIXE.\"".$elem." as a".$key." ";
                    $tableinc .= "\n        ON ".$this->table.".".$elem."=a".$key.".".$elem." ";
                }
            }
        }
        //
        $tableinc.= "\";";
        // CHAMP AFFICHE
        $tableinc .= "\n// SELECT ";
        $tableinc.="\n\$champAffiche = array(".$champaffiche."\n    );";

        if ($this->multi == 2 && $this->table != 'om_collectivite') {
            if (isset($countLinkedTable["om_collectivite"])
                && $countLinkedTable["om_collectivite"] > 1) {
                $alias = 'om_collectivite'.
                            array_search('om_collectivite', $this->clesecondaire).
                            '.libelle';
            } else {
                $alias = 'om_collectivite.libelle';
            }

            $tableinc .= "\n//\nif (\$_SESSION['niveau'] == '2') {";
            $tableinc .= "\n    array_push(\$champAffiche, \"".$alias." as \\\"\"._(\"collectivite\").\"\\\"\");";
            $tableinc .= "\n}";
        }
        $tableinc.="\n//\n\$champNonAffiche = array(".$champnonaffiche."\n    );";
        $tableinc.="\n//\n\$champRecherche = array(".$champrecherche."\n    );";
        if ($this->multi == 2 && $this->table != 'om_collectivite') {
            if (isset($countLinkedTable["om_collectivite"])
                && $countLinkedTable["om_collectivite"] > 1) {
                $alias = 'om_collectivite'.
                            array_search('om_collectivite', $this->clesecondaire).
                            '.libelle';
            } else {
                $alias = 'om_collectivite.libelle';
            }
            
            $tableinc .= "\n//\nif (\$_SESSION['niveau'] == '2') {";
            $tableinc .= "\n    array_push(\$champRecherche, \"".$alias." as \\\"\"._(\"collectivite\").\"\\\"\");";
            $tableinc .= "\n}";
        }
        $tableinc.="\n\$tri=\"".$tri."\";";
        $tableinc.="\n\$edition=\"".$edition."\";";
        // les sous formulaires
        // href
        $tableinc.=$this->def_selection_inc();
        $tableinc.=$this->def_sousformulaire_inc();
        $tableinc.="\n?>";
        return $tableinc;
    }

    /**
     * Renvoi le titre de l'écran.
     *
     * @return string
     */
    function def_ent() {
        //
        $out = "\n\$ent = ";
        //
        if (isset($this->_tables_to_overload[$this->table])
            && isset($this->_tables_to_overload[$this->table]["breadcrumb_in_page_title"])) {
            //
            foreach ($this->_tables_to_overload[$this->table]["breadcrumb_in_page_title"] as $elem) {
                $out .= "_(\"".$elem."\").\" -> \".";
            }
        } else {
            //
            if (in_array(
                    $this->table, 
                    array(
                        'om_etat', 'om_sousetat', 'om_lettretype', 
                        'om_requete', 'om_logo', 
                    )
                )) {
                $breadcrumb = "parametrage";
            } elseif (strpos($this->table, "om_") === 0) {
                $breadcrumb = "administration";
            } else {
                $breadcrumb = "application";
            }
            //
            $out .= "_(\"".$breadcrumb."\").\" -> \".";
        }
        //
        if (isset($this->_tables_to_overload[$this->table])
            && isset($this->_tables_to_overload[$this->table]["tablename_in_page_title"])) {
            //
            $out .= "_(\"".$this->_tables_to_overload[$this->table]["tablename_in_page_title"]."\");";
        } else {
            //
            $out .= "_(\"".$this->table."\");";
        }
        //
        return $out;
    }

    /**
     * Construit une partie du script [gen/sql/<OM_DB_PHPTYPE>/<TABLE>.inc.php].
     *
     * La partie construite ici concerne la clause WHERE de la requête. Cette 
     * clause est stockée dans la variable $selection du script en question.
     * 
     * @return string
     */
    function def_selection_inc() {

        /**
         * TEMPLATES
         */
        //
        $template_selection = '
/**
 * Gestion de la clause WHERE => $selection
 */%s
';
        //
        $template_listing_standard = '
// Filtre listing standard
%s';
        //
        $template_listing_standard_multi = 'if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    %s%s
} else {
    // Filtre MONO
    %s%s
}';
        //
        $template_listing_standard_mono = '%s%s';
        //
        $template_selection_standard_common = '$selection = "%s";';
        //
        $template_selection_standard_mono = '$selection = " WHERE (%s) %s";';
        //
        $template_listing_sousformulaire = '
// Filtre listing sous formulaire - %s
if (in_array($retourformulaire, $foreign_keys_extended["%s"])) {
    %s%s
}';
        //
        $template_contenu_listing_sousformulaire_mono = '%s';
        //
        $template_contenu_listing_sousformulaire_multi = 'if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        %s
    } else {
        // Filtre MONO
        %s
    }';
        //
        $template_selection_sousformulaire_common = '$selection = " WHERE (%s) %s";';
        //
        $template_selection_sousformulaire_mono = '$selection = " WHERE (%s) AND (%s) %s";';
        //
        $template_om_validite_where = '
$where_om_validite = "%s";';
        //
        $template_om_validite_logique = '
// Gestion OMValidité - Suppression du filtre si paramètre
if (isset($_GET["valide"]) and $_GET["valide"] == "false") {
    if (!isset($where_om_validite) 
        or (isset($where_om_validite) and $where_om_validite == "")) {
        if (trim($selection) != "") {
            $selection = "";
        }
    } else {
        $selection = trim(str_replace($where_om_validite, "", $selection));
    }
}';

        /**
         * COMMON
         */
        //
        $contenu_selection = "";
        //
        $filter_om_collectivite = sprintf(
            '%s.om_collectivite = \'".$_SESSION["collectivite"]."\'',
            $this->table
        );
        //
        if ($this->is_om_validite() == true) {
            //
            $filter_om_validite = $this->filter_om_validite($this->table);
            //
            $filter_om_validite_with_where = ' WHERE '.$filter_om_validite;
            $filter_om_validite_with_and = ' AND '.$filter_om_validite;
            $where_om_validite_with_where = sprintf(
                $template_om_validite_where,
                $filter_om_validite_with_where
            );
            $where_om_validite_with_and = sprintf(
                $template_om_validite_where,
                $filter_om_validite_with_and
            );
        } else {
            //
            $filter_om_validite_with_where = '';
            $filter_om_validite_with_and = '';
            $where_om_validite_with_where = '';
            $where_om_validite_with_and = '';
        }

        /**
         * LISTING STANDARD
         */
        //
        $filter_mono = sprintf(
            $template_selection_standard_mono,
            $filter_om_collectivite,
            $filter_om_validite_with_and
        );
        //
        $filter_common = sprintf(
            $template_selection_standard_common,
            $filter_om_validite_with_where
        );
        //
        $contenu_listing_standard = "";
        // 
        if ($this->multi == 2) {
            //
            $contenu_listing_standard = sprintf(
                $template_listing_standard_multi,
                $filter_common,
                $where_om_validite_with_where,
                $filter_mono,
                $where_om_validite_with_and
            );
        } else {
            //
            $contenu_listing_standard = sprintf(
                $template_listing_standard_mono,
                $filter_common,
                $where_om_validite_with_where
            );
        }
        //
        $contenu_selection .= sprintf(
            $template_listing_standard,
            $contenu_listing_standard
        );

        /**
         * LISTING SOUSFORMULAIRE
         */
        if (!empty($this->clesecondaire)) {

            $ftables = array();
            
            foreach ($this->foreign_tables as $key => $infos) {

                if (!key_exists($infos['foreign_table_name'], $ftables)) {
                    $ftables[$infos['foreign_table_name']] = array($key);
                } else {
                    if (!in_array($key, $ftables[$infos['foreign_table_name']])) {
                        array_push($ftables[$infos['foreign_table_name']], $key);
                    }
                }
            }

            // Définition des clés étrangères avec leurs possibles surcharges
            $contenu_selection .= $this->def_sql_var_foreign_keys_extended();

            foreach ($ftables as $table => $columns) {
                //
                $contenu_tmp = '';
                foreach ($columns as $column) {
                    $contenu_tmp .= sprintf(
                        '%s.%s = \'".$idx."\' OR ',
                        $this->table,
                        $column
                    );
                }
                $contenu_tmp = substr($contenu_tmp, 0, strlen($contenu_tmp) - 4);
                //
                $filter_mono = sprintf(
                    $template_selection_sousformulaire_mono,
                    $filter_om_collectivite,
                    $contenu_tmp,
                    $filter_om_validite_with_and
                );
                //
                $filter_common = sprintf(
                    $template_selection_sousformulaire_common,
                    $contenu_tmp,
                    $filter_om_validite_with_and
                );
                //
                $contenu_listing_sousformulaire = "";
                //
                if ($this->multi == 2) {
                    //
                    $contenu_listing_sousformulaire .= sprintf(
                        $template_contenu_listing_sousformulaire_multi,
                        $filter_common,
                        $filter_mono
                    );
                } else {
                    //
                    $contenu_listing_sousformulaire .= sprintf(
                        $template_contenu_listing_sousformulaire_mono,
                        $filter_common
                    );
                }
                //
                $contenu_selection .= sprintf(
                    $template_listing_sousformulaire,
                    $table,
                    $table,
                    $contenu_listing_sousformulaire,
                    $where_om_validite_with_and
                );
            }
        }

        //
        if ($this->is_om_validite() == true) {
            //
            $contenu_selection .= $template_om_validite_logique;
        }

        /**
         *
         */
        //
        return sprintf(
            $template_selection,
            $contenu_selection
        );
    }

    /**
     * Construit la partie...
     *
     * @return string
     */
    function def_sousformulaire_inc() {

        $code = '';

        if (!empty($this->sousformulaires)) {

            //
            $comment = "";
            if (isset($this->_tables_to_overload[$this->table])
                && isset($this->_tables_to_overload[$this->table]["tabs_in_form"])
                && $this->_tables_to_overload[$this->table]["tabs_in_form"] === false) {
                $comment = "//";
            }

            //
            foreach($this->sousformulaires as $sousformulaire) {
                $code .= "\n    ";
                $code .= $comment."'".$sousformulaire."',";
            }
            
            $code = "
/**
 * Gestion SOUSFORMULAIRE => \$sousformulaire
 */
\$sousformulaire = array(".$code."
);
";
        }

        return $code;
    }

    /**
     * Construit le contenu du script [sql/<OM_DB_PHPTYPE>/<TABLE>.form.inc.php].
     *
     * @return string
     */
    function table_sql_forminc() {

        //
        if ($this->is_omframework_table($this->table)) {
            //
            $template = "%s
include \"../core/sql/%s/%s.form.inc.php\";

?>
";
        } else {
            //
            $template = "%s
include \"../gen/sql/%s/%s.form.inc.php\";

?>
";
        }
        //
        return sprintf(
            $template,
            $this->def_php_script_header(),
            OM_DB_PHPTYPE,
            $this->table
        );

    }

    /**
     * Construit le contenu du script [core/sql/<OM_DB_PHPTYPE>/<TABLE>.form.inc.php].
     *
     * @return string
     */
    function table_sql_forminc_core() {

        //
        if ($this->is_omframework_table($this->table)) {
            //
            $template = "%s
include \"../gen/sql/%s/%s.form.inc.php\";

?>
";
            //
            return sprintf(
                $template,
                $this->def_php_script_header(),
                OM_DB_PHPTYPE,
                $this->table
            );
        } else {
            //
            return "";
        }

    }

    /**
     * Construit le contenu du script [gen/sql/<OM_DB_PHPTYPE>/<TABLE>.form.inc.php].
     *
     * @param mixed $dyn Fichier de paramétrage.
     *
     * @return string
     */
    function table_sql_forminc_gen($dyn = null) {
        // blob
        $max=6; // nb de ligne blob
        $taille=80; // taille du blob
        // pgsql
        $pgsql_taille_defaut = 20; // taille du champ par defaut si retour pg_field_prtlen =0
        $pgsql_taille_minimum = 10; // taille minimum d affichage d un champ
        $pgsql_longueur_date=12; // taille d'affichage de la date '
        $ico = "../img/ico_application.png"; // icone de l'application
        if (file_exists ("../gen/dyn/form.inc.php")){
            include ("../gen/dyn/form.inc.php");
            $this->msg.="<br />"._("Chargement du parametrage")." ../gen/dyn/form.inc.php";
        } elseif (file_exists ("../gen/dyn/form.inc")){
            include ("../gen/dyn/form.inc");
            $this->msg.="<br />"._("Chargement du parametrage")." ../gen/dyn/form.inc";
        }   
        //
        $champ="";
        foreach($this->info as $elem){
                $champ.= "\n    \"".$elem["name"]."\",";
        }
        $champ = substr($champ, 0, strlen($champ)-1);
        $tableforminc= $this->def_php_script_header();
        $tableforminc.="\n\$DEBUG=0;";
        $tableforminc.="\n\$ico=\"".$ico."\";";
        $tableforminc.=$this->def_ent();
        $tableforminc.="\n\$tableSelect=DB_PREFIXE.\"".$this->table."\";";
        $tableforminc.="\n\$champs=array(".$champ.");";
        $tableforminc.=$this->def_sql_select();
        $tableforminc.="\n?>";
        return $tableforminc;
    }

    /**
     * Construit une partie du script...
     *
     * @return string
     */
    function def_sql_select() {
        if(!empty($this->clesecondaire)){
            $contenu ="\n//champs select";
            foreach($this->clesecondaire as $elem){

                if (isset($this->foreign_tables[$elem])) {

                    // recherche de la table et de la cle primaire
                    $ftable = $this->foreign_tables[$elem]['foreign_table_name'];
                    $fprimary_key = $this->foreign_tables[$elem]['foreign_column_name'];

                    $contenu .= "\n\$sql_".$elem."=\"SELECT ";
                    $contenu .= $ftable.'.'.$fprimary_key;
                    $contenu .= ', ';

                    // recuperation du libelle
                    $libelle = $this->get_libelle_of($ftable);

                    $contenu .= $ftable.'.'.$libelle;
                    $contenu .= " FROM \".DB_PREFIXE.\"".$this->foreign_tables[$elem]["foreign_table_name"];
                } else {

                    // on ne passe jamais ici
                    $ftable = $elem;
                    $contenu.="\n\$sql_".$elem."=\"SELECT * FROM \".DB_PREFIXE.\"".$elem;
                }

                if ($this->check_om_validite($ftable) == true) {
                    $contenu.=" WHERE ".$this->filter_om_validite($ftable);
                }

                if (isset($this->foreign_tables[$elem])) {
                    $contenu.=" ORDER BY ".$ftable.'.'.$this->get_libelle_of($ftable)." ASC";
                }

                $contenu.="\";";

                // creation de la requete sql_object_by_id
                $fprimary_key = $this->get_primary_key($ftable);
                $flibelle = $this->get_libelle_of($ftable);
                $infos = $this->f->db->tableInfo(DB_PREFIXE.$ftable);

                $contenu .= "\n";
                $contenu .= "\$sql_".$elem."_by_id = \"";
                $contenu .= "SELECT ".$ftable.'.'.$fprimary_key.", ".$ftable.'.'.$flibelle.' ';
                $contenu .= "FROM \".DB_PREFIXE.\"".$ftable." ";

                if (strtolower(substr($infos[0]['type'], 0, 3)) == 'int') {
                    $id = "<idx>";
                } else {
                    $id = "'<idx>'";
                }

                $contenu .= "WHERE ".$fprimary_key." = ".$id."\";";
            }
            return $contenu;
        }// fin clesecondaire
    }

    /**
     * Construit le contenu du script [obj/<TABLE>.class.php].
     *
     * Cette méthode permet de générer la définition de la classe qui est
     * définie dans le dossier obj/ et qui étend la classe définit dans le
     * dossier gen/obj/. Cette classe a pour objectif de contenir les
     * surcharges spécifiques aux objets en questions dans l'applicatif.
     *
     * @todo public
     * @return string
     */
    function table_obj_class() {
        //
        if ($this->is_omframework_table($this->table)) {
            //
            $template = '%s
require_once "../core/obj/%s.class.php";

class %s extends %s_core {

    function __construct($id, &$dnu1 = null, $dnu2 = null) {
        $this->constructeur($id);
    }

}

?>
';
        } else {
            //
            $template = '%s
require_once "../gen/obj/%s.class.php";

class %s extends %s_gen {

    function __construct($id, &$dnu1 = null, $dnu2 = null) {
        $this->constructeur($id);
    }

}

?>
';
        }
        //
        return sprintf(
            $template,
            $this->def_php_script_header(),
            $this->table,
            $this->table,
            $this->table
        );

    }

    /**
     * Construit le contenu du script [core/obj/<TABLE>.class.php].
     *
     * Cette méthode permet de générer la définition de la classe qui est
     * définie dans le dossier core/obj/ et qui étend la classe définit dans le
     * dossier gen/obj/. Cette classe a pour objectif de contenir les
     * surcharges spécifiques aux objets en questions dans l'applicatif.
     *
     * @todo public
     * @return string
     */
    function table_obj_class_core() {

        //
        if ($this->is_omframework_table($this->table)) {
            //
            $template = "%s
require_once \"../gen/obj/%s.class.php\";

class %s_core extends %s_gen {

}

?>
";
            //
            return sprintf(
                $template,
                $this->def_php_script_header(),
                $this->table,
                $this->table,
                $this->table,
                $this->table
            );
        } else {
            //
            return "";
        }

    }

    /**
     * Construit le contenu du script [gen/obj/<TABLE>.class.php].
     *
     * Cette méthode permet de générer la définition de la classe qui est 
     * définie dans le dossier gen/obj/ et qui étend la classe 
     * obj/om_dbform.class.php. Cette classe a pour objectif de contenir 
     * les méthodes générées à partir du modèle de données.
     *
     * @param mixed $dyn Fichier de paramétrage.
     *
     * @todo public
     * @return string 
     */
    function table_obj_class_gen($dyn = null) {

        //
        $template = "%s
require_once \"../obj/om_dbform.class.php\";

class %s_gen extends om_dbform {

    %s
    %s
    %s
    %s
    %s
    %s

%s

    //==================================
    // sous Formulaire 
    //==================================
    %s

    //==================================
    // cle secondaire 
    //==================================
    %s

}

?>
";

        //
        $tableobj = "";
        //
        $tableobj .= $this->def_obj_meth_setvalf();
        //
        if($this->typecle=="N"){ // cle automatique si numerique
            $tableobj.="\n\n    //=================================================";
            $tableobj.="\n    //cle primaire automatique [automatic primary key]";
            $tableobj.="\n    //==================================================";
            $tableobj.=$this->def_obj_meth_setid();
            $tableobj.=$this->def_obj_meth_setvalfajout();
            $tableobj.=$this->def_obj_meth_verifierajout();
        }
        //
        $tableobj.=$this->def_obj_meth_verifier();
        //
        $tableobj.="\n\n    //==========================";
        $tableobj.="\n    // Formulaire  [form]";
        $tableobj.="\n    //==========================";
        $tableobj.=$this->def_obj_meth_settype();
        $tableobj.=$this->def_obj_meth_setonchange();
        $tableobj.=$this->def_obj_meth_settaille();
        $tableobj.=$this->def_obj_meth_setmax();
        $tableobj.=$this->def_obj_meth_setlib();
        $tableobj.=$this->def_obj_meth_setselect();
        $tableobj.=$this->def_obj_meth_setval();
        //
        return sprintf(
            $template,
            $this->def_php_script_header(),
            $this->table,
            //
            $this->def_obj_attr_table(),
            $this->def_obj_attr_cleprimaire(),
            $this->def_obj_attr_typecle(),
            $this->def_obj_attr_required_field(),
            $this->def_obj_attr_unique_key(),
            $this->def_obj_attr_foreign_keys_extended(),
            //
            $tableobj,
            //
            $this->def_obj_meth_setvalsousformulaire(),
            $this->def_obj_meth_clesecondaire()
        );
    }

    // table.obj

    /**
     * Construit la définition de l'attribut $table pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_table() {
        //
        return sprintf(
            "var \$table = \"%s\";",
            $this->table
        );
    }

    /**
     * Construit la définition de l'attribut $clePrimaire pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_cleprimaire() {
        //
        return sprintf(
            "var \$clePrimaire = \"%s\";",
            $this->primary_key
        );
    }

    /**
     * Construit la définition de l'attribut $typeCle pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_typecle() {
        //
        return sprintf(
            "var \$typeCle = \"%s\";",
            $this->typecle
        );
    }

    /**
     * Construit la définition de l'attribut $required_field pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_required_field() {
        //
        $output = "";
        // Tableau des contraintes not null
        $s8="        ";
        if (!empty($this->_columns_notnull)) {
            $output .= "var \$required_field = array(\n".$s8."\"".implode("\",\n".$s8."\"", $this->_columns_notnull)."\"\n    );";
        }
        //
        return $output;
    }

    /**
     * Construit la définition de l'attribut $unique_key pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_unique_key() {
        //
        $output = "";
        // Tableaux des contraintes uniques
        if(!empty($this->unique_key) || !empty($this->unique_multiple_key)) {
            $output.="var \$unique_key = array(";
            foreach($this->unique_key as $contraint) {
                $output.="\n      \"".$contraint."\",";
            }
            if(!empty($this->unique_multiple_key)) {
                foreach($this->unique_multiple_key as $multiple) {
                    $output.="\n      array(\"".implode("\",\"", $multiple)."\"),";
                }

            }
            $output.="\n    );";
        }
        //
        return $output;
    }

    /**
     * Construit la définition de l'attribut $foreign_keys_extended pour table_obj.
     *
     * @return string
     */
    function def_obj_attr_foreign_keys_extended() {
        return $this->def_var_foreign_keys_extended("attr");
    }

    /**
     * Construit la définition de l'attribut $foreign_keys_extended pour table_obj.
     *
     * @return string
     */
    function def_sql_var_foreign_keys_extended() {
        return $this->def_var_foreign_keys_extended("var");
    }

    /**
     * Construit la définition de l'attribut $foreign_keys_extended pour table_obj.
     *
     * @param string $context Contexte d'utilisation de la méthode :
     *                         - attr : attribut de classe,
     *                         - var : variable dans un script.
     *
     * @return string
     */
    function def_var_foreign_keys_extended($context = "attr") {

        if ($context == "var") {
            //
            $template_listing_sousformulaire_extended_class = '
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
%s);';
            //
            $template_content_foreign_keys_extended = '    "%s" => array("%s", %s),
';
        } else {
            //
            $template_listing_sousformulaire_extended_class = 'var $foreign_keys_extended = array(
%s    );';
            //
            $template_content_foreign_keys_extended = '        "%s" => array("%s", %s),
';
        }

        //
        $ftables = array();
        //
        if (!empty($this->clesecondaire)) {
            foreach ($this->foreign_tables as $key => $infos) {

                if (!key_exists($infos['foreign_table_name'], $ftables)) {
                    $ftables[$infos['foreign_table_name']] = array($key);
                } else {
                    if (!in_array($key, $ftables[$infos['foreign_table_name']])) {
                        array_push($ftables[$infos['foreign_table_name']], $key);
                    }
                }
            }
        }
        //
        $content_foreign_keys_extended = "";
        foreach ($ftables as $table => $columns) {
            //
            $extended_class = '';
            //
            if (isset($this->_tables_to_overload[$table])
                && isset($this->_tables_to_overload[$table]["extended_class"])
                && is_array($this->_tables_to_overload[$table]["extended_class"])
                && count($this->_tables_to_overload[$table]["extended_class"])
                ) {
                $extended_class = '"'.implode('", "', $this->_tables_to_overload[$table]["extended_class"]).'", ';
            }
            //            
            $content_foreign_keys_extended .= sprintf(
                $template_content_foreign_keys_extended,
                $table,
                $table,
                $extended_class
            );
        }
        return sprintf(
            $template_listing_sousformulaire_extended_class,
            $content_foreign_keys_extended
        );
    }

    /**
     * Construit la définition de la méthode verifier() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_verifier() {
        $content = '';

        // verification pour les objets a date de validite
        if ($this->is_om_validite() == true) {

            // Entete de la methode
            $content .= "\n";
            $content .= "    /**\n     * Methode verifier\n     */\n";
            $content .= "    function verifier(\$val = array(), &\$dnu1 = null, \$dnu2 = null) {\n";
            $content .= "        // On appelle la methode de la classe parent\n";
            $content .= "        parent::verifier(\$val, \$this->f->db, null);\n";

            $s = "        ";
            
            // om_validite_debut < om_validite_fin
            $content .= "\n";
            $content .= $s."// gestion des dates de validites\n";
            $content .= $s."\$date_debut = \$this->valF['om_validite_debut'];\n";
            $content .= $s."\$date_fin = \$this->valF['om_validite_fin'];\n";
            $content .= "\n";
            $content .= $s."if (\$date_debut != '' and \$date_fin != '') {\n";
            $content .= $s."\n";
            $content .= $s."    \$date_debut = explode('-', \$this->valF['om_validite_debut']);\n";
            $content .= $s."    \$date_fin = explode('-', \$this->valF['om_validite_fin']);\n";
            $content .= "\n";
            $content .= $s."    \$time_debut = mktime(0, 0, 0, \$date_debut[1], \$date_debut[2],\n";
            $content .= $s."                         \$date_debut[0]);\n";
            $content .= $s."    \$time_fin = mktime(0, 0, 0, \$date_fin[1], \$date_fin[2],\n";
            $content .= $s."                         \$date_fin[0]);\n";
            $content .= "\n";
            $content .= $s."    if (\$time_debut > \$time_fin or \$time_debut == \$time_fin) {\n";
            $content .= $s."        \$this->correct = false;\n";
            $content .= $s."        \$this->addToMessage(_('La date de fin de validite doit etre future a la de debut de validite.'));\n";
            $content .= $s."    }\n";
            $content .= $s."}\n";

            $content .= "    }\n";
        }
        //
        return $content;
    }

    /**
     * Construit la définition de la méthode setType() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_settype() {
        //
        $template_meth_settype = '
    /**
     *
     */
    function setType(&$form, $maj) {

        // MODE AJOUTER
        if ($maj == 0) {%s
        }

        // MDOE MODIFIER
        if ($maj == 1) {%s
        }

        // MODE SUPPRIMER
        if ($maj == 2) {%s
        }

        // MODE CONSULTER
        if ($maj == 3) {%s
        }

    }
';
        //
        return sprintf(
            //
            $template_meth_settype,
            //
            $this->def_obj_meth_settype_by_maj(0),
            //
            $this->def_obj_meth_settype_by_maj(1),
            //
            $this->def_obj_meth_settype_by_maj(2),
            //
            $this->def_obj_meth_settype_by_maj(3)
        );
    }

    /**
     * Construit une partie de la définition de la méthode setType().
     *
     * Methode permettant de definir le widget de formulaire a utiliser 
     * en fonction du type de champ dans la base de donnees
     *
     * @param integer $maj Valeur de l'action de formulaire pour laquelle on
     *                     souhaite définir les widgets.
     *
     * @return string
     */
    function def_obj_meth_settype_by_maj($maj) {
        // Niveau d'arborescence 0
        $template_settype_0 = '
            $form->setType("%s", "%s");';
        // Niveau d'arborescence 1
        $template_settype_1 = '
                $form->setType("%s", "%s");';
        // Niveau d'arborescence 2
        $template_settype_2 = '
                    $form->setType("%s", "%s");';
        //
        $template_settype_widgets_specifiques = '
            if ($this->retourformulaire == "") {%s
            } else {%s
            }';
        //
        $template_settype_multi = '
            if ($_SESSION["niveau"] == 2) {%s
            } else {%s
            }';
        $template_settype_date_om_validite = '
            if ($this->f->isAccredited(array($this->table."_modifier_validite", $this->table, ))) {%s
            } else {%s
            }';
        //
        $template_settype_retourformulaire = '
            if ($this->is_in_context_of_foreign_key("%s", $this->retourformulaire)) {%s
            } else {%s
            }';
        //
        $template_settype_retourformulaire_multi = '
            if ($this->is_in_context_of_foreign_key("om_collectivite", $this->retourformulaire)) {
                if($_SESSION["niveau"] == 2) {%s
                } else {%s
                }
            } else {
                if($_SESSION["niveau"] == 2) {%s
                } else {%s
                }
            }';
        //
        $tableobj = "";
        //
        foreach($this->info as $elem) {

            // Gestion de la clé primaire
            if ($elem['name'] == $this->primary_key) {
                if ($maj == 0) {
                    $tableobj .= sprintf($template_settype_0, $elem['name'], ($this->typecle == "N" ? "hidden" : "text"));
                } elseif ($maj == 1) {
                    $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                } elseif ($maj == 2) {
                    $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                } elseif ($maj == 3) {
                    $tableobj .= sprintf($template_settype_0, $elem['name'], "static");
                }
                // On passe à l'itération suivante de la boucle
                continue;
            }

            // Gestion des clés secondaires
            // XXX Ce ne sont pas les bonnes clés secondaires voir foreign_keys
            if (!empty($this->clesecondaire)) {
                //
                if (in_array($elem['name'], $this->clesecondaire)) {
                    //
                    $elem1 = $elem['name'];
                    //
                    if ($elem['name'] == "om_collectivite") {
                        //
                        if ($maj == 0 || $maj == 1) {
                            $tableobj .= sprintf(
                                $template_settype_retourformulaire_multi,
                                sprintf($template_settype_2, $elem['name'], "selecthiddenstatic"),
                                sprintf($template_settype_2, $elem['name'], "hidden"),
                                sprintf($template_settype_2, $elem['name'], "select"),
                                sprintf($template_settype_2, $elem['name'], "hidden")
                            );
                        } elseif ($maj == 2) {
                            $tableobj .= sprintf(
                                $template_settype_multi,
                                sprintf($template_settype_1, $elem['name'], "selectstatic"),
                                sprintf($template_settype_1, $elem['name'], "hidden")
                            );
                        } elseif ($maj == 3) {
                            $tableobj .= sprintf(
                                $template_settype_retourformulaire_multi,
                                sprintf($template_settype_2, $elem['name'], "selectstatic"),
                                sprintf($template_settype_2, $elem['name'], "hidden"),
                                sprintf($template_settype_2, $elem['name'], "selectstatic"),
                                sprintf($template_settype_2, $elem['name'], "hidden")
                            );
                        }
                    } else {
                        if ($maj == 0 || $maj == 1) {
                            $tableobj .= sprintf(
                                $template_settype_retourformulaire,
                                $this->foreign_tables[$elem1]['foreign_table_name'],
                                sprintf($template_settype_1, $elem['name'], "selecthiddenstatic"),
                                sprintf($template_settype_1, $elem['name'], "select")
                            );
                        } elseif ($maj == 2) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "selectstatic");
                        } elseif ($maj == 3) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "selectstatic");
                        }
                    }
                    // On passe à l'itération suivante de la boucle
                    continue;
                }
            }

            //
            switch ($elem['type']) {
                case "date" :
                    if ($maj == 3) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "datestatic");
                    } elseif ($maj == 0 || $maj == 1) {
                        //
                        if ($elem['name'] == 'om_validite_debut' || $elem['name'] == 'om_validite_fin') {
                            $tableobj .= sprintf(
                                $template_settype_date_om_validite,
                                sprintf($template_settype_1, $elem['name'], "date"),
                                sprintf($template_settype_1, $elem['name'], "hiddenstaticdate")
                            );
                        } else {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "date");
                        }
                    } elseif ($maj == 2) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                    }
                    // On sort du switch
                    break;
                case "blob" :
                    if ($maj == 0 || $maj == 1) {
                        if (strpos($elem['name'], '_om_htmletatex') !== false) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "htmlEtatEx");
                        } elseif (strpos($elem['name'], '_om_htmletat') !== false) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "htmlEtat");
                        } elseif (strpos($elem['name'], '_om_html') !== false) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "html");
                        } else {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "textarea");
                        }
                    } elseif ($maj == 3) {
                        if (strpos($elem['name'], '_html') !== false) {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "htmlstatic");
                        } else {
                            $tableobj .= sprintf($template_settype_0, $elem['name'], "textareastatic");
                        }
                    } elseif ($maj == 2) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                    }
                    // On sort du switch
                    break;
                case "geom" :
                    if ($maj == 0 || $maj == 1 || $maj == 2 || $maj == 3) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "geom");
                    }
                    // On sort du switch
                    break;
                case "bool" :
                    if ($maj == 0 || $maj == 1) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "checkbox");
                    } elseif ($maj == 3) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "checkboxstatic");
                    } elseif ($maj == 2) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                    }
                    // On sort du switch
                    break;
                default :
                    if ($maj == 0) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "text");
                    } elseif ($maj == 1) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "text");
                    } elseif ($maj == 2) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "hiddenstatic");
                    } elseif ($maj == 3) {
                        $tableobj .= sprintf($template_settype_0, $elem['name'], "static");
                    }
            } // switch
        }
        //
        return $tableobj;
    }

    /**
     * Construit la définition de la méthode setvalF() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setvalf() {
        //
        $content = "";
        // Entête de la methode
        $content .= "\n";
        $content .= "\n    function setvalF(\$val) {";
        $content .= "\n        //affectation valeur formulaire";
        // Boucle sur chaque colonne
        foreach ($this->info as $elem) {

            // Test sur le type de données
            if ($elem['type'] == "date") {

                // Gestion des champs de type DATE

                // Si la valeur renvoyée par le formulaire n'est pas numérique
                $content .= "\n        if (\$val['".$elem['name']."'] != \"\") {";
                // On affecte la valeur renvoyée retraitée par la méthode de
                // retraitement des dates
                $content .= "\n            \$this->valF['".$elem['name']."'] = \$this->dateDB(\$val['".$elem['name']."']);";
                $content .= "\n        }";
    
                 // Test sur l'attribut NULL du champ
                if ($elem['notnull'] == 'f') {
                    
                    // Soit le champ accepte la valeur NULL
                    // On affecte la valeur NULL
                    $content .= " else {";
                    $content .= "\n            \$this->valF['".$elem['name']."'] = NULL;";
                    $content .= "\n        }";
                }

            } elseif ($elem['type'] == "bool") {

                // Gestion des champs de type BOOLaffectation valeur formulaire

                //
                $content .= "\n        if (\$val['".$elem['name']."'] == 1 || \$val['".$elem['name']."'] == \"t\" || \$val['".$elem['name']."'] == \"Oui\") {";
                $content .= "\n            \$this->valF['".$elem['name']."'] = true;";
                $content .= "\n        } else {";
                $content .= "\n            \$this->valF['".$elem['name']."'] = false;";
                $content .= "\n        }";
            
            } elseif ($elem['type'] == "geom") {
                // gestion des champs geom non pris en compte si valeur vide
                $content .= "\n        if (\$val['".$elem['name']."'] == \"\") {";
                $content .= "\n            unset(\$this->valF['".$elem['name']."']);";
                $content .="\n        } else {";
                // On affecte la valeur du formulaire directement
                $content .="\n            \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";
                $content .="\n        }";
            
            } elseif ($elem['type'] == "int" or $elem['type'] == "float") {

                // Gestion des champs de type INT

                // Si la valeur renvoyée par le formulaire n'est pas numérique
                // => ceci n'est pas sensé arriver car une fonction javascript
                //    vérifie que la saisie est composée uniquement de chiffres
                //    mais ce cas inclut aussi la chaine vide ce qui par contre
                //    arrive fréquemment
                $content .="\n        if (!is_numeric(\$val['".$elem['name']."'])) {";
                // Test sur l'attribut NULL du champ
                if ($elem['notnull'] == 'f') {

                    // Soit le champ accepte la valeur NULL
                    // On affecte la valeur NULL
                    $content .="\n            \$this->valF['".$elem['name']."'] = NULL;";

                } elseif ($elem['notnull'] == 't'
                          && in_array($elem["name"], $this->_columns_notnull)) {

                    // Soit le champ n'accepte pas la valeur NULL et fait partie
                    // des champs requis
                    // On affecte la valeur ""
                    // => sous entendu le champ n'accepte pas la valeur NULL
                    //    donc il est obligatoire donc la méthode vérifier
                    //    des champs requis empêchera le passage de la valeur à
                    //    la base
                    $content .="\n            \$this->valF['".$elem['name']."'] = \"\"; // -> requis";

                } else {
                    
                    // Le cas restant est : le champ n'accepte pas la valeur
                    // NULL et a une valeur par défaut dans la base
                    //
                    // XXX ici il faut affecter la valeur du default de la base
                    //
                    // On affecte la valeur 0
                    $content .="\n            \$this->valF['".$elem['name']."'] = 0; // -> default";
                    
                }
                // Sinon si la valeur renvoyée par le formulaire est numérique
                $content .="\n        } else {";
                // Test si on se trouve sur le champ 'om_collectivite'
                if ($elem['name'] == "om_collectivite") {

                    // Champ 'om_collectivite'
                    // => sous entendu ce champ est forcément de type INT

                    // Si on est en mode MONO
                    $content .="\n            if(\$_SESSION['niveau']==1) {";
                    // On affecte la valeur de la collectivité depuis la
                    // variable de SESSION
                    $content .="\n                \$this->valF['".$elem['name']."'] = \$_SESSION['collectivite'];";
                    // Si on est en mode MULTI
                    $content .="\n            } else {";
                    // On affecte la valeur du formulaire directement
                    $content .="\n                \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";
                    $content .="\n            }";

                } else {

                    // Un autre champ que 'om_collectivite'

                    // On affecte la valeur du formulaire directement
                    $content .="\n            \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";

                }
                //
                $content .="\n        }";

            } elseif ($elem['type'] == "string") {

                // Gestion des champs de type STRING

                // Test sur l'attribut NULL du champ
                if ($elem['notnull'] == 'f') {

                    // Si la valeur renvoyée par le formulaire est une chaine vide
                    $content .="\n        if (\$val['".$elem['name']."'] == \"\") {";
                    // Soit le champ accepte la valeur NULL
                    // On affecte la valeur NULL
                    $content .="\n            \$this->valF['".$elem['name']."'] = NULL;";
                    //
                    $content .="\n        } else {";
                    // On affecte la valeur du formulaire directement
                    $content .="\n            \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";
                    //
                    $content .="\n        }";
                } elseif ($elem['notnull'] == 't'
                          && in_array($elem["name"], $this->_columns_notnull)) {

                    // Soit le champ n'accepte pas la valeur NULL et fait partie
                    // des champs requis
                    // On affecte la valeur ""
                    // => sous entendu le champ n'accepte pas la valeur NULL
                    //    donc il est obligatoire donc la méthode vérifier
                    //    des champs requis empêchera le passage de la valeur à
                    //    la base
                    $content .="\n        \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";

                } else {

                    // Si la valeur renvoyée par le formulaire est une chaine vide
                    $content .="\n        if (\$val['".$elem['name']."'] == \"\") {";
                    // Le cas restant est : le champ n'accepte pas la valeur
                    // NULL et a une valeur par défaut dans la base
                    //
                    // XXX ici il faut affecter la valeur du default de la base
                    //
                    // On affecte la valeur ""
                    $content .="\n            \$this->valF['".$elem['name']."'] = \"\"; // -> default";
                    //
                    $content .="\n        } else {";
                    // On affecte la valeur du formulaire directement
                    $content .="\n            \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";
                    //
                    $content .="\n        }";

                }
            } else {

                // On affecte la valeur du formulaire directement
                $content .="\n            \$this->valF['".$elem['name']."'] = \$val['".$elem['name']."'];";

            }
        }
        // Pied de la methode
        $content .="\n    }";
        //
        return $content;
    }

    /**
     * Construit la définition de la méthode setOnchange() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setonchange() {
        //
        $counter = 0;
        //
        $tableobj = "\n\n    function setOnchange(&\$form, \$maj) {";
        $tableobj .= "\n    //javascript controle client";
        //
        foreach ($this->info as $elem) {
            //
            if ($elem['type'] == 'date') {
                //
                $counter++;
                //
                $tableobj.="\n        \$form->setOnchange('".$elem['name']."','fdate(this)');";
            } elseif($elem['type'] == 'int') {
                //
                $counter++;
                //
                $tableobj.="\n        \$form->setOnchange('".$elem['name']."','VerifNum(this)');";
            } elseif($elem['type'] == 'float') {
                //
                $counter++;
                //
                $tableobj.="\n        \$form->setOnchange('".$elem['name']."','VerifFloat(this)');";
            }
        }
        //
        $tableobj.="\n    }";
        // Si aucun élément n'est affiché alors inutile de générer la méthode
        if ($counter == 0) {
            //
            $tableobj = "";
        }
        // On renvoi le contenu de la méthode à générer
        return $tableobj;
    }

    /**
     * Construit la définition de la méthode setTaille() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_settaille() {
        //
        $content = "";
        // Entete de la methode
        $content .= "\n";
        $content .= "    /**\n     * Methode setTaille\n     */\n";
        $content .= "    function setTaille(&\$form, \$maj) {\n";
        //
        foreach ($this->info as $elem) {
            //
            $content .="        \$form->setTaille(\"".$elem['name']."\", ".(isset($elem['taille_affichage']) ? $elem['taille_affichage'] : $elem['len']).");\n";
        }
        // Pied de la methode
        $content .="    }\n";
        //
        return $content;
    }

    /**
     * Construit la définition de la méthode setMax() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setmax() {
        //
        $content = "";
        // Entete de la methode
        $content .= "\n";
        $content .= "    /**\n     * Methode setMax\n     */\n";
        $content .= "    function setMax(&\$form, \$maj) {\n";
        //
        foreach ($this->info as $elem) {
            //
            $content .= "        \$form->setMax(\"".$elem['name']."\", ".(isset($elem['max_saisie']) ? $elem['max_saisie'] : $elem['len']).");\n";
        }
        // Pied de la methode
        $content .="    }\n";
        //
        return $content;
    }

    /**
     * Construit la définition de la méthode setLib() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setlib() {
        $tableobj="\n\n    function setLib(&\$form, \$maj) {";
        $tableobj.="\n    //libelle des champs";

        foreach($this->info as $elem){

            $tableobj.="\n        \$form->setLib('".$elem['name']."',_('".$elem['name']."')";

            $tableobj.=');';
        }

        $tableobj.="\n    }";
        return $tableobj;
    }

    /**
     * Construit la définition de la méthode setSelect() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setselect() {
        //
        $template_meth_setselect = '
    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {

        // Inclusion du fichier de requêtes
        if (file_exists("../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php")) {
            include "../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php";
        } elseif (file_exists("../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc")) {
            include "../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc";
        }
%s
    }
';
        //
        $contenu = "";
        //
        if (!empty($this->clesecondaire)) {
            //
            $template_meth_setselect_clesecondaire ='
        // %s
        $this->init_select($form, $this->f->db, $maj, null, "%s", $sql_%s, $sql_%s_by_id, %s);';
            //
            foreach($this->clesecondaire as $elem) {
                //
                if (isset($this->foreign_tables[$elem])) {
                    $ftable = $this->foreign_tables[$elem]['foreign_table_name'];
                } else {
                    $ftable = $elem;
                }
                //
                $contenu .= sprintf(
                    $template_meth_setselect_clesecondaire,
                    $elem,
                    $elem,
                    $elem,
                    $elem,
                    ($this->check_om_validite($ftable) == true ? "true" : "false")
                );
            }
        }
        //
        if (!empty($this->geom)) {
            //
            $template_meth_setselect_geom = '
        // %s
        if ($maj == 1 || $maj == 3) {
            $contenu = array();
            $contenu[0] = array("%s", $this->getParameter("idx"), "%s");
            $form->setSelect("%s", $contenu);
        }';
            // appel pour multigéométrie
            $nbgeom = 0;
            //
            foreach ($this->geom as $elem) {
                //
                $contenu .= sprintf(
                    $template_meth_setselect_geom,
                    $elem,
                    $this->table,
                    $nbgeom,
                    $elem
                );
                //
                $nbgeom += 1;
            }
        }
        //
        return sprintf(
            $template_meth_setselect,
            $contenu
        );
    }

    /**
     * Construit la définition de la méthode setVal() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setval() {
        //
        $contenu = "";
        // si $this->multi = 2
        // valorisation de la variable $this->retourformulaire
        // valorisation champ cle formulaire en creation et modification
        if ($this->multi == 2) {
            $contenu="\n\n    function setVal(&\$form, \$maj, \$validation, &\$dnu1 = null, \$dnu2 = null) {";
            $contenu.="\n        if(\$validation==0 and \$maj==0 and \$_SESSION['niveau']==1) {";
            $contenu.="\n            \$form->setVal('om_collectivite', \$_SESSION['collectivite']);";
            $contenu.="\n        }// fin validation";
            $contenu.="\n        \$this->set_form_default_values(\$form, \$maj, \$validation);";
            $contenu.="\n    }// fin setVal";
        }
        return $contenu;
    }

    /**
     * Construit la définition de la méthode setValsousformulaire() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setvalsousformulaire() {
        // si cle secondaire
        // valorisation de la variable $this->retourformulaire
        // valorisation champ cle formulaire en creation et modification
        $contenu="\n\n    function setValsousformulaire(&\$form, \$maj, \$validation, \$idxformulaire, \$retourformulaire, \$typeformulaire, &\$dnu1 = null, \$dnu2 = null) {";
        $contenu.="\n        \$this->retourformulaire = \$retourformulaire;";
        //
        if ($this->multi == 2 && $this->table != "om_collectivite") {
            //
            $contenu.="\n        if(\$validation==0 and \$maj==0 and \$_SESSION['niveau']==1) {";
            $contenu.="\n            \$form->setVal('om_collectivite', \$_SESSION['collectivite']);";
            $contenu.="\n        }// fin validation";
        }
        // clesecondaire
        if (!empty($this->clesecondaire)) {
            $contenu_tmp = "\n        if(\$validation == 0) {";

            $ftables = array();
            foreach ($this->foreign_tables as $key => $infos) {

                if (!key_exists($infos['foreign_table_name'], $ftables)) {
                    $ftables[$infos['foreign_table_name']] = array($key);
                } else {
                    if (!in_array($key, $ftables[$infos['foreign_table_name']])) {
                        array_push($ftables[$infos['foreign_table_name']], $key);
                    }
                }
            }

            $multiple_fkeys = array();
            foreach ($ftables as $table => $columns) {
                if (count($columns) > 1) {
                    $multiple_fkeys = array_merge($multiple_fkeys, $columns);
                }
            }

            $code_exists = false;

            foreach ($this->clesecondaire as $elem){
                if (!in_array($elem, $multiple_fkeys)) {
                    $code_exists = true;
                    $contenu_tmp .= "\n            if(\$this->is_in_context_of_foreign_key('".$this->foreign_tables[$elem]['foreign_table_name']."', \$this->retourformulaire))";
                    $contenu_tmp .= "\n                \$form->setVal('".$elem."', \$idxformulaire);";
                }
            }
            $contenu_tmp .= "\n        }// fin validation";

            if ($code_exists == true) {
                $contenu .= $contenu_tmp;
            }

            $code_exists = false;
            $contenu_tmp = "\n        if (\$validation == 0 and \$maj == 0) {";

            $ftables = array();
            foreach ($this->foreign_tables as $key => $infos) {

                if (!key_exists($infos['foreign_table_name'], $ftables)) {
                    $ftables[$infos['foreign_table_name']] = array($key);
                } else {
                    if (!in_array($key, $ftables[$infos['foreign_table_name']])) {
                        array_push($ftables[$infos['foreign_table_name']], $key);
                    }
                }
            }

            $multiple_fkeys = array();
            foreach ($ftables as $table => $columns) {
                if (count($columns) > 1) {
                    $multiple_fkeys = array_merge($multiple_fkeys, $columns);
                }
            }

            foreach ($this->clesecondaire as $elem){
                if (in_array($elem, $multiple_fkeys)) {
                    $code_exists = true;
                    $contenu_tmp .= "\n            if(\$this->is_in_context_of_foreign_key('".$this->foreign_tables[$elem]['foreign_table_name']."', \$this->retourformulaire))";
                    $contenu_tmp .= "\n                \$form->setVal('".$elem."', \$idxformulaire);";
                }
            }
            $contenu_tmp .= "\n        }// fin validation";

            if ($code_exists == true) {
                $contenu .= $contenu_tmp;
            }

        } // fin clescondaire
        $contenu.="\n        \$this->set_form_default_values(\$form, \$maj, \$validation);";
        $contenu.="\n    }// fin setValsousformulaire";
        return $contenu;
        }

    /**
     * Construit la définition de la méthode clesecondaire() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_clesecondaire() {
        //
        $content = "";
        // Si il existe des sous-formulaires on surcharge la methode sinon
        // on ne fait rien
        if (!empty($this->sousformulaires)) {
            // Entete de la methode
            $content .= "\n";
            $content .= "    /**\n     * Methode clesecondaire\n     */\n";
            $content .= "    function cleSecondaire(\$id, &\$dnu1 = null, \$val = array(), \$dnu2 = null) {\n";
            $content .= "        // On appelle la methode de la classe parent\n";
            $content .= "        parent::cleSecondaire(\$id);\n";

            // boucle sur chaque sous-formulaire
            foreach ($this->other_tables as $config) {

                // $config est de la forme "nom_table.nom_colonne"
                $infos = array();
                $infos =  explode('.', $config);

                // table  -> table ayant une reference vers la table actuelle
                // column -> colonne de cette table faisant reference
                $table = $infos[0];
                $column = $infos[1];

                $content .= "        // Verification de la cle secondaire : ".$table."\n"; 
                $content .= "        \$this->rechercheTable(\$this->f->db, \"".$table."\", \"".$column."\", \$id);\n";
            }

            // Pied de la methode
            $content .="    }\n";
        }
        //
        return $content;
    }

    /**
     * Construit la définition de la méthode setId() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setid() {
        $tableobj="\n\n    function setId(&\$dnu1 = null) {";
        $tableobj.="\n    //numero automatique";
        $tableobj.="\n        \$this->valF[\$this->clePrimaire] = \$this->f->db->nextId(DB_PREFIXE.\$this->table);";
        $tableobj.="\n    }";
        return $tableobj;
    }

    /**
     * Construit la définition de la méthode setValFAjout() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_setvalfajout() {
        $tableobj="\n\n    function setValFAjout(\$val) {";
        $tableobj.="\n    //numero automatique -> pas de controle ajout cle primaire";
        $tableobj.="\n    }";
        return $tableobj;
    }

    /**
     * Construit la définition de la méthode verifierAjout() pour table_obj.
     *
     * @return string
     */
    function def_obj_meth_verifierajout() {
        $tableobj="\n\n    function verifierAjout() {";
        $tableobj.="\n    //numero automatique -> pas de verfication de cle primaire";
        $tableobj.="\n    }";
        return $tableobj;
    }

    /**
     * Construit le contenu du script [sql/<OM_DB_PHPTYPE>/<TABLE>.pdf.inc.php].
     *
     * Cette méthode permet de générer l'intégralité du script.
     *
     * @param mixed $dyn Fichier de paramétrage.
     *
     * @todo public
     * @return string 
     */
    function table_sql_pdfinc($dyn = null) {
        // pdf liste de fichier ancienne version openmairie 1.00
        // les blob (mysql)ne sont pas pris en compte dans le tableau
        // les dates sont au format français
        $temp= $this->def_php_script_header();
        // parametrage pdf standard
        $longueurtableau= 280;
        $orientation='L';// orientation P-> portrait L->paysage";
        $format='A4';// format A3 A4 A5;
        $police='arial';
        $margeleft=10;// marge gauche;
        $margetop=5;// marge haut;
        $margeright=5;//  marge droite;
        $border=1; // 1 ->  bordure 0 -> pas de bordure";
        $C1=0;// couleur texte  R";
        $C2=0;// couleur texte  V";
        $C3=0;// couleur texte  B";
        $size=10; //taille POLICE";
        $height=4.6; // hauteur ligne tableau ";
        $align='L';
        // fond 2 couleurs
        $fond=1;// 0- > FOND transparent 1 -> fond";
        $C1fond1=234;// couleur fond  R ";
        $C2fond1=240;// couleur fond  V ";
        $C3fond1=245;// couleur fond  B ";
        $C1fond2=255;// couleur fond  R";
        $C2fond2=255;// couleur fond  V";
        $C3fond2=255;// couleur fond  B";
        // spe openelec
        $flagsessionliste=0;// 1 - > affichage session liste ou 0 -> pas d'affichage";
        // titre
        $bordertitre=0; // 1 ->  bordure 0 -> pas de bordure";
        $aligntitre='L'; // L,C,R";
        $heightitre=10;// hauteur ligne titre";
        $grastitre='B';//\$gras='B' -> BOLD OU \$gras=''";
        $fondtitre=0; //0- > FOND transparent 1 -> fond";
        $C1titrefond=181;// couleur fond  R";
        $C2titrefond=182;// couleur fond  V";
        $C3titrefond=188;// couleur fond  B";
        $C1titre=75;// couleur texte  R";
        $C2titre=79;// couleur texte  V";
        $C3titre=81;// couleur texte  B";
        $sizetitre=15;
        // entete colonne
        $flag_entete=1;//entete colonne : 0 -> non affichage , 1 -> affichage";
        $fondentete=1;// 0- > FOND transparent 1 -> fond";
        $heightentete=10;//hauteur ligne entete colonne";
        $C1fondentete=210;// couleur fond  R";
        $C2fondentete=216;// couleur fond  V";
        $C3fondentete=249;// couleur fond  B";
        $C1entetetxt=0;// couleur texte R";
        $C2entetetxt=0;// couleur texte V";
        $C3entetetxt=0;// couleur texte B";
        $C1border=159;// couleur texte  R";
        $C2border=160;// couleur texte  V";
        $C3border=167;// couleur texte  B";
        $bt=1;// border 1ere  et derniere ligne  du tableau par page->0 ou 1";
        if (file_exists ("../gen/dyn/pdf.inc.php")){
            include ("../gen/dyn/pdf.inc.php");
            $this->msg.="<br />"._("Chargement du parametrage")." ../gen/dyn/pdf.inc.php";
        } elseif (file_exists ("../gen/dyn/pdf.inc")){
            include ("../gen/dyn/pdf.inc");
            $this->msg.="<br />"._("Chargement du parametrage")." ../gen/dyn/pdf.inc";
        }
        $temp.="\n\$DEBUG=0;";
        // param sousetat.inc.php
        $temp.="\n\$orientation='".$orientation."';// orientation P-> portrait L->paysage";
        $temp.="\n\$format='".$format."';// format A3 A4 A5";
        $temp.="\n\$police='".$police."';";
        $temp.="\n\$margeleft=".$margeleft.";// marge gauche";
        $temp.="\n\$margetop=".$margetop.";// marge haut";
        $temp.="\n\$margeright=".$margeright.";//  marge droite";
        $temp.="\n\$border=".$border."; // 1 ->  bordure 0 -> pas de bordure";
        $temp.="\n\$C1=".$C1.";// couleur texte  R";
        $temp.="\n\$C2=".$C2.";// couleur texte  V";
        $temp.="\n\$C3=".$C3.";// couleur texte  B";
        $temp.="\n\$size=".$size."; //taille POLICE";
        $height=intval($height); // bug si virgule 4,6
        $temp.="\n\$height=".$height."; // hauteur ligne tableau ";
        $temp.="\n\$align='".$align."';";
        $temp.="\n\$fond=".$fond.";// 0- > FOND transparent 1 -> fond";
        $temp.="\n\$C1fond1=".$C1fond1.";// couleur fond  R 241";
        $temp.="\n\$C2fond1=".$C2fond1.";// couleur fond  V 241";
        $temp.="\n\$C3fond1=".$C3fond1.";// couleur fond  B 241";
        $temp.="\n\$C1fond2=".$C1fond2.";// couleur fond  R";
        $temp.="\n\$C2fond2=".$C2fond2.";// couleur fond  V";
        $temp.="\n\$C3fond2=".$C3fond2.";// couleur fond  B";
        $temp.="\n\$libtitre='Liste ".DB_PREFIXE.$this->table."'; // libelle titre";
        $temp.="\n\$flagsessionliste=".$flagsessionliste.";// 1 - > affichage session liste ou 0 -> pas d'affichage";
        $temp.="\n\$bordertitre=".$bordertitre."; // 1 ->  bordure 0 -> pas de bordure";
        $temp.="\n\$aligntitre='".$aligntitre."'; // L,C,R";
        $temp.="\n\$heightitre=".$heightitre.";// hauteur ligne titre";
        $temp.="\n\$grastitre='".$grastitre."';//\$gras='B' -> BOLD OU \$gras=''";
        $temp.="\n\$fondtitre=".$fondtitre."; //0- > FOND transparent 1 -> fond";
        $temp.="\n\$C1titrefond=".$C1titrefond.";// couleur fond  R";
        $temp.="\n\$C2titrefond=".$C2titrefond.";// couleur fond  V";
        $temp.="\n\$C3titrefond=".$C3titrefond.";// couleur fond  B";
        $temp.="\n\$C1titre=".$C1titre.";// couleur texte  R";
        $temp.="\n\$C2titre=".$C2titre.";// couleur texte  V";
        $temp.="\n\$C3titre=".$C3titre.";// couleur texte  B";
        $temp.="\n\$sizetitre=".$sizetitre.";";
        $temp.="\n\$flag_entete=".$flag_entete.";//entete colonne : 0 -> non affichage , 1 -> affichage";
        $temp.="\n\$fondentete=".$fondentete.";// 0- > FOND transparent 1 -> fond";
        $temp.="\n\$heightentete=".$heightentete.";//hauteur ligne entete colonne";
        $temp.="\n\$C1fondentete=".$C1fondentete.";// couleur fond  R";
        $temp.="\n\$C2fondentete=".$C2fondentete.";// couleur fond  V";
        $temp.="\n\$C3fondentete=".$C3fondentete.";// couleur fond  B";
        $temp.="\n\$C1entetetxt=".$C1entetetxt.";// couleur texte R";
        $temp.="\n\$C2entetetxt=".$C2entetetxt.";// couleur texte V";
        $temp.="\n\$C3entetetxt=".$C3entetetxt.";// couleur texte B";
        $temp.="\n\$C1border=".$C1border.";// couleur texte  R";
        $temp.="\n\$C2border=".$C2border.";// couleur texte  V";
        $temp.="\n\$C3border=".$C3border.";// couleur texte  B";
        // calcul de la taille des colones
        $i=0;
        $j=0;
        $longueur=0; // ***
        $temp1=$longueurtableau;
        $temp3="";
        //$indice=2.5; // indice taille affichage
        $indice =$longueurtableau/$this->longueur;
        $limite =$longueurtableau/2.5;
        $troplong=0;
        $dernierchamp=0;
        if($indice<2.5){
            $this->msg.="<br />->affichage colone incomplet ".$indice."  < 2.5 ";
            foreach($this->info as $elem){
                if($troplong==0){     // ***
                    if($elem['type']!="blob"){
                        $longueur = $longueur + $elem['len'];
                        //$this->msg.="<br>->".$elem['name'].' longueur '.$longueur." ***".$troplong;
                        $dernierchamp++;
                    }
                    //*** A TESTER Longueur de champ
                    if($longueur>=$limite){
                        $troplong=1;
                        $longueur=    $longueur - $elem['len'];
                    }
                }//***
            }
            $dernierchamp=$dernierchamp-2;
            //$this->msg.="<br>->".$dernierchamp.' longueur '.$limite;
            $indice=$longueurtableau/$longueur;
        } else {
            $this->msg.="<br />->affichage colone ok ".$indice." >= 2.5";
            $dernierchamp=count($this->info)-1;
            if($this->info[$dernierchamp]['type']=="blob") { //mysql
                $dernierchamp=$dernierchamp-1;
            }
        }
        $seulpassage=0;
        foreach($this->info as $elem){
            if ($elem['type']!="blob") {
                if ($j<$dernierchamp) {
                    $temp2= $elem['len']*intval($indice);
                    $temp.="\n\$l".$i."=".$temp2."; // largeur colone -> champs ".$i." - ".$elem['name'];
                    $temp.="\n\$be".$i."='L';// border entete colone";
                    $temp.="\n\$b".$i."='L';// border cellule colone";
                    $temp.="\n\$ae".$i."='C'; // align cellule entete colone";
                    $temp.="\n\$a".$i."='L';";
                    $temp1 = $temp1-$temp2;
                    $temp4 = "def_champaffichedatepdf".OM_DB_PHPTYPE;// fonction date
                    if ($elem["type"]=="date") {
                        $temp3.= $this->$temp4($elem["name"]).",";
                    } else {
                        $temp3.=$elem['name'].", ";
                    }
                } else {
                    if ($seulpassage == 0) {
                        $temp.="\n\$l".$i."=".$temp1."; // largeur colone -> champs".$i." - ".$elem['name'];
                        $temp.="\n\$be".$i."='LR';// border entete colone";
                        $temp.="\n\$b".$i."='LR';// border cellule colone";
                        $temp.="\n\$ae".$i."='C'; // align cellule entete colone";
                        $temp.="\n\$a".$i."='L';";
                        $temp4 = "def_champaffichedatepdf".OM_DB_PHPTYPE;// fonction date
                        if ($elem["type"]=="date") {
                            $temp3.= $this->$temp4($elem["name"]);
                        } else {
                            $temp3.=$elem['name'];
                        }
                        $seulpassage=1;
                    }
                }
                $i++; // compteur champ dans pdf
            }
            $j++; // compteur champ dans tableinfo
        }
        $temp.="\n\$widthtableau=".$longueurtableau.";";
        $temp.="\n\$bt=".$bt.";// border 1ere  et derniere ligne  du tableau par page->0 ou 1";
        $temp.="\n\$sql=\"select ".$temp3." from \".DB_PREFIXE.\"".$this->table."\";";
        $temp.="\n?>";
        return $temp;
    }

    /**
     * Construit le contenu du script [sql/<OM_DB_PHPTYPE>/<ELEM>.reqmo.inc.php].
     *
     * Cette méthode permet de générer l'intégralité du script.
     *
     * @param string $cle Clé secondaire éventuelle.
     *
     * @todo public
     * @return string 
     */
    function table_sql_reqmoinc($cle = "") {
        // cle = cle secondaire -> select
        $contenu=$this->def_php_script_header();
        $contenu.="\n\$reqmo['libelle']=_('reqmo-libelle-".$this->table."');";
        $contenu.="\n\$reqmo['reqmo_libelle']=_('reqmo-libelle-".$this->table."');";
        if ($cle == ""){
            $contenu .= "\n\$ent=_('".$this->table."');";
        } else {
            $contenu .= "\n\$ent=_('".$this->table.'_'.$cle."');";
        }
        // sql
        $temp = "select ";
        $temp1="";
        $temp2="array(";
        $temp3="";
        foreach($this->info as $elem){
            if($cle==""){
                $temp.=" [".$elem['name']."],";
                $temp1.= "\n\$reqmo['".$elem['name']."']='checked';";
                $temp2.="'".$elem['name']."',";
            }elseif($cle==$elem['name']){
                    $temp1.= "\n\$reqmo['".$elem['name']."']=\"select * from \".DB_PREFIXE.\"".$elem['name']."\";";
            }else{
                    $temp.=" [".$elem['name']."],";//sql
                    $temp1.= "\n\$reqmo['".$elem['name']."']='checked';";
                    $temp2.="'".$elem['name']."',";        // tri
            }
        }
        $temp =  substr($temp, 0, strlen($temp)-1);
        if($cle!="") {
            $temp3 = "where ".$cle." = '[".$cle."]'"; // sql
        }
        $contenu.="\n\$reqmo['sql']=\"".$temp." from \".DB_PREFIXE.\"".$this->table." ".$temp3." order by [tri]\";";
        $temp2 =  substr($temp2, 0, strlen($temp2)-1);
        $contenu.="".$temp1;
        $contenu.="\n\$reqmo['tri']=".$temp2.");";
        $contenu.="\n?>";
        return $contenu;
    }

    /**
     * Construit le contenu du script [sql/<OM_DB_PHPTYPE>/<TABLE>.import.inc.php].
     *
     * Cette méthode permet de générer l'intégralité du script.
     *
     * @todo public
     *
     * @return string 
     */
    function table_sql_importinc() {
        // creer un fichier d import
        $i=0;
        $contenu=$this->def_php_script_header();
        $contenu.="\n\$import= \"Insertion dans la table ".$this->table." voir rec/import_utilisateur.inc\";";
        $contenu.="\n\$table= DB_PREFIXE.\"".$this->table."\";";
        if($this->typecle=="N") {
            $contenu.="\n\$id='".$this->primary_key."'; // numerotation automatique";
        } else {
            $contenu.="\n\$id=''; // numerotation non automatique";
        }
        $contenu.="\n\$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour";
        $contenu.="\n\$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement";
        $contenu.="\n\$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon";

        //
        $contenu .= '
/**
 *
 */
$fields = array(';
        //
        foreach ($this->info as $elem) {
            // Initialisation du tableau pour l'élément
            $contenu .= "
    \"".$elem["name"]."\" => array(";
            // Attributs communs à tous les champs
            $contenu .= "
        \"notnull\" => \"".($elem["notnull"]  == 't' ? true : false)."\",
        \"type\" => \"".$elem["type"]."\",
        \"len\" => \"".$elem["len"]."\",";
            // Gestion des clés étrangères (Critère EXIST)
            if (isset($this->foreign_tables[$elem["name"]])) {
                $contenu .= "
        \"fkey\" => array(
            \"foreign_table_name\" => \"".$this->foreign_tables[$elem["name"]]['foreign_table_name']."\",
            \"foreign_column_name\" => \"".$this->foreign_tables[$elem["name"]]['foreign_column_name']."\",
            \"sql_exist\" => \"select * from \".DB_PREFIXE.\"".$this->foreign_tables[$elem["name"]]['foreign_table_name']." where ".$this->foreign_tables[$elem["name"]]["foreign_column_name"]." = '\",
        ),";
            }
            // Fin de l'initialisation du tableau pour l'élément
            $contenu .= "
    ),";
        }
        //
        $contenu .= '
);';
        $contenu.="\n?>";
        return $contenu;
    }

    /**
     * Renvoi l'élément de requête pour des dates au format francais pour MySQL.
     *
     * Avec alias et séquence d'échappement.
     *
     * @param string $temp  Nom de la colonne date à traiter.
     * @param mixed  $alias Alias éventuel de la colonne date.
     *
     * @return string
     */
    function def_champaffichedatemysql($temp, $alias=null) {
        //
        if ($alias == null) {
            $alias = $temp;
        }
        // avec sequence d echappement
        return "'concat(substring(".$temp.",9,2),\'/\',substring(".$temp.",6,2),\'/\',substring(".$temp.",1,4)) as ".$alias."'";
    }

    /**
     * Renvoi l'élément de requête pour des dates au format francais pour PostGreSQL.
     *
     * Avec alias et séquence d'échappement.
     *
     * @param string $temp  Nom de la colonne date à traiter.
     * @param mixed  $alias Alias éventuel de la colonne date.
     *
     * @return string
     */
    function def_champaffichedatepgsql($temp, $alias=null) {
        //
        if ($alias == null) {
            $alias = $temp;
        }
        // avec sequence d echappement
        return "'to_char(".$temp." ,\'DD/MM/YYYY\') as \"'._(\"".$alias."\").'\"'";
    }

    /**
     * Renvoi l'élément de requête pour des dates au format francais pour MySQL.
     *
     * Sans alias et séquence d'échappement.
     *
     * @param string $temp Nom de la colonne date à traiter.
     *
     * @return string
     */
    function def_champaffichedatepdfmysql($temp) {
        // avec sequence d echappement
        return "concat(substring(".$temp.",9,2),'/',substring(".$temp.",6,2),'/',substring(".$temp.",1,4)) as ".$temp;
    }

    /**
     * Renvoi l'élément de requête pour des dates au format francais pour PostGreSQL.
     *
     * Sans alias et séquence d'échappement.
     *
     * @param string $temp Nom de la colonne date à traiter.
     *
     * @return string
     */
    function def_champaffichedatepdfpgsql($temp) {
        // sans sequence d echappement
        return "to_char(".$temp." ,'DD/MM/YYYY') as ".$temp;
    }

    // ------------------------------------------------------------------------
    // {{{ END - CONSTRUCTION DES CONTENUS DES SCRIPTS
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - RECUPERATION DES INFORMATIONS SUR LE MODELE
    // ------------------------------------------------------------------------

    /**
     * Initialisation des fichiers de configuration du générateur.
     *
     * Cette méthode permet de récupérer les fichiers configurations
     * pour initialiser les paramètres et permettre leur utilisation
     * dans les méthodes de la classe
     *
     * @return void
     */
    function init_configuration() {
        //
        if (file_exists("../gen/dyn/gen.inc.php")) {
            include "../gen/dyn/gen.inc.php";
        }
        //
        if (isset($tables_to_overload)) {
            $this->_tables_to_overload = $tables_to_overload;
        }
        //
        if (isset($breadcrumbs_to_overload)) {
            $this->_breadcrumbs_to_overload = $breadcrumbs_to_overload;
        }
    }

    /**
     * Renvoi la valeur d'une option.
     *
     * @param string $option Libellé de l'option souhaitée.
     *
     * @return mixed
     */
    function get_general_option($option = "") {
        //
        if ($option == "") {
            //
            return null;
        }
        //
        if (file_exists("../gen/dyn/gen.inc.php")) {
            //
            include "../gen/dyn/gen.inc.php";
        }
        //
        if (isset($$option)) {
            //
            return $$option;
        } else {
            //
            switch ($option) {
                case "key_constraints_mode":
                    return "constraints";
                    break;
                default:
                    return null;
            }
        }
    }

    /**
     * Renvoi la valeur du cas d'utilisation 'om_validite'.
     *
     * Retourne true si la table traitee actuellement contient les colonnes:
     *      - om_validite_debut
     *      - om_validite_fin
     *
     * Cette methode ne fait aucune requete en base de donnees.
     * Elle se contente de lire les informations analysees par la methode gen().
     *
     * Voir aussi check_om_validite()
     *
     * @return boolean
     */
    function is_om_validite() {
        if ($this->_om_validite_debut == true
            and $this->_om_validite_fin == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vérifie en base l'existence des colonnes 'om_validite' sur une table. 
     *
     * Retourne true si la table specifiee contient les colonnes:
     *      - om_validite_debut
     *      - om_validite_fin
     *
     * Cette methode effectue une requete en base de donnees.
     * Utilisez cette methode si la table actuelle != table specifiee.
     *
     * Voir aussi is_om_validite()
     *
     * @param string $table Nom de la table à examiner.
     *
     * @return boolean
     */
    function check_om_validite($table) {

        $infos = $this->f->db->tableInfo(DB_PREFIXE.$table);

        $om_validite_debut = false;
        $om_validite_fin = false;

        foreach ($infos as $column) {

            if ($column['name'] == 'om_validite_debut') {
                $om_validite_debut = true;
            } else if ($column['name'] == 'om_validite_fin') {
                $om_validite_fin = true;
            }
        }

        return $om_validite_debut and $om_validite_fin;
    }

    /**
     * Retourne la condition SQL des objets à date de validité.
     *
     * @param string  $table         Nom de la table.
     * @param boolean $with_operator Indicateur avec ou sans opérateur.
     * 
     * @return string
     */
    function filter_om_validite($table, $with_operator = false) {

        if ($with_operator == true) {
            $filtre = ' AND ';
        } else {
            $filtre = '';
        }
    
        $filtre .= '(('.$table.'.om_validite_debut IS NULL AND ';
        $filtre .= '('.$table.'.om_validite_fin IS NULL OR '.$table.'.om_validite_fin > CURRENT_DATE))';
        $filtre .= ' OR ';
        $filtre .= '('.$table.'.om_validite_debut <= CURRENT_DATE AND ';
        $filtre .= '('.$table.'.om_validite_fin IS NULL OR '.$table.'.om_validite_fin > CURRENT_DATE)))';
        return $filtre;
    }

    /**
     * Initialisation des informations concernant les clés étrangères.
     * 
     * Cette méthode permet d'initialiser les deux attributs :
     *  - $this->clesecondaire
     *  - $this->foreign_tables
     *
     * Ces deux attributs permettent de gérer la notion de FOREIGN KEY de la
     * table en cours vers les autres tables de la base.
     *
     * @return void
     */
    function _init_foreign_tables() {

        //
        $mode = $this->get_general_option("key_constraints_mode");

        //
        if ($mode == "constraints") {

            // MODE 1 - Recherche de FOREIGN KEY en interrogeant les contraintes
            // de la base de données par 'information_schema' 

            //
            $method = "_init_foreign_tables_information_schema_for_".OM_DB_PHPTYPE;
            if (method_exists($this, $method)) {
                $this->$method();
            }

        } elseif ($mode == "postulate") {

            // MODE 2 - Recherche de FOREIGN KEY par le postulat : "le nom d'un
            // champ 'clé étrangère' a pour nom le nom de la table vers laquelle 
            // elle fait référence, et fait référence au champ clé primaire de
            // cette table."

            // Boucle sur chaque champ de la table
            foreach ($this->info as $elem) {
                // Boucle sur chaque table de la base
                foreach ($this->tablebase as $elem1) {
                    // Si le nom de la colonne est identique au nom de la table
                    // et qu'il n'est pas déjà présent dans la liste alors on
                    // l'ajoute
                    if ($elem['name'] == $elem1
                        && !in_array($elem['name'], $this->clesecondaire)) {
                        //
                        array_push($this->clesecondaire, $elem1);
                        //
                        $this->foreign_tables[$elem1] = array(
                            'column_name' => $elem1,
                            'foreign_table_name' => $elem1,
                            'foreign_column_name' =>
                                $this->get_primary_key($elem1)
                        );
                    }
                }
            }

        }

        //
        sort($this->clesecondaire);
        //
        ksort($this->foreign_tables);

    }

    /**
     * Initialisation des informations concernant les autres tables.
     *
     * Cette méthode permet d'initialiser les deux attributs :
     *  - $this->sousformulaires
     *  - $this->other_tables
     *
     * Ces deux attributs permettent de gérer la notion de FOREIGN KEY des
     * autres tables de la base vers la table en cours.
     *
     * @return void
     */
    function _init_other_tables() {

        //
        $mode = $this->get_general_option("key_constraints_mode");

        //
        if ($mode == "constraints") {

            // MODE 1 - Recherche de FOREIGN KEY en interrogeant les contraintes
            // de la base de données par 'information_schema' 

            //
            $method = "_init_other_tables_information_schema_for_".OM_DB_PHPTYPE;
            if (method_exists($this, $method)) {
                $this->$method();
            }

        } elseif ($mode == "postulate") {

            // MODE 2 - Recherche de FOREIGN KEY par le postulat : "le nom d'un
            // champ 'clé étrangère' a pour nom le nom de la table vers laquelle 
            // elle fait référence, et fait référence au champ clé primaire de
            // cette table."

            // Boucle sur chaque table de la base
            foreach ($this->tablebase as $elem1) {
                // Récupération des infos de la table
                $table_infos = $this->f->db->tableInfo(DB_PREFIXE.$elem1);
                // Boucle sur chaque colonne
                foreach ($table_infos as $column) {
                    // Si le nom de la colonne est identique au nom de la table en
                    // cours et qu'il n'est pas déjà présent dans la liste
                    // alors on l'ajoute
                    if ($column['name'] == $this->table
                        && !in_array($elem1, $this->sousformulaires)) {
                        //
                        array_push($this->sousformulaires, $elem1);
                        //
                        if (!in_array($elem1.'.'.$column['name'], $this->other_tables)) {
                            $this->other_tables[] = $elem1.'.'.$column['name'];
                        }
                    }
                }
            }

        }

        //
        sort($this->sousformulaires);
        //
        sort($this->other_tables);

    }


    /**
     * Initialisation des informations concernant les clés étrangères (MySQL).
     *
     * Cette méthode permet d'initialiser les FOREIGN KEY de la table en cours
     * vers les autres tables de la base en recherchant les contraintes
     * dans la base de données pour MySQL.
     *
     * @return void
     */
    function _init_foreign_tables_information_schema_for_mysql() {

        // tables referencees par des cles etrangeres de la table actuelle
        $sql = 'SELECT table_name, '.
                      'column_name,'.
                      'REFERENCED_TABLE_NAME AS foreign_table_name, '.
                      'REFERENCED_COLUMN_NAME AS foreign_column_name '.
                'FROM information_schema.key_column_usage '.
                'WHERE '.
                  'REFERENCED_TABLE_NAME is not NULL '.
                  'AND table_name = \''.$this->table.'\' '.
                  'AND table_schema = \''.OM_DB_DATABASE.'\'';

        $foreign_keys = $this->f->db->query($sql);

        $message =  'gen(): db->query("'.$sql.'");';
        logger::instance()->log('class gen - '.$message, VERBOSE_MODE);

        if (database::isError($foreign_keys)) {
            die();
        }

        //
        while ($key =& $foreign_keys->fetchrow(DB_FETCHMODE_ASSOC)) {
            //
            array_push($this->clesecondaire, $key['column_name']);
            //
            $this->foreign_tables[$key['column_name']] = $key;
        }

    }

    /**
     * Initialisation des informations concernant les clés étrangères (PostGreSQL).
     *
     * Cette méthode permet d'initialiser les FOREIGN KEY de la table en cours
     * vers les autres tables de la base en recherchant les contraintes
     * dans la base de données pour PostGreSQL.
     * 
     * @return void
     */
    function _init_foreign_tables_information_schema_for_pgsql() {

        // tables referencees par des cles etrangeres de la table actuelle
        $sql = 'SELECT tc.table_name, '.
                      'kcu.column_name,'.
                      'ccu.table_name AS foreign_table_name, '.
                      'ccu.column_name AS foreign_column_name '.
                 'FROM information_schema.table_constraints AS tc '.
                 'JOIN information_schema.key_column_usage AS kcu '.
                   'USING (constraint_schema, constraint_name) '.
                 'JOIN information_schema.constraint_column_usage AS ccu '.
                   'USING (constraint_schema, constraint_name) '.
                'WHERE constraint_type = \'FOREIGN KEY\' '.
                  'AND tc.table_name = \''.$this->table.'\' '.
                  'AND tc.table_schema = \''.OM_DB_SCHEMA.'\'';

        $foreign_keys = $this->f->db->query($sql);

        $message =  'gen(): db->query("'.$sql.'");';
        logger::instance()->log('class gen - '.$message, VERBOSE_MODE);

        if (database::isError($foreign_keys)) {
            die();
        }

        //
        while ($key =& $foreign_keys->fetchrow(DB_FETCHMODE_ASSOC)) {
            //
            array_push($this->clesecondaire, $key['column_name']);
            //
            $this->foreign_tables[$key['column_name']] = $key;
        }

    }

    /**
     * Initialisation des autres tables (MySQL).
     *
     * Cette méthode permet d'initialiser les FOREIGN KEY des autres tables de
     * la base vers la table en cours en recherchant les contraintes dans la
     * base de données pour MySQL.
     * 
     * @return void
     */
    function _init_other_tables_information_schema_for_mysql() {

        // tables referencees par des cles etrangeres de la table actuelle
        $sql = 'SELECT table_name, '.
                      'column_name,'.
                      'REFERENCED_TABLE_NAME AS foreign_table_name, '.
                      'REFERENCED_COLUMN_NAME AS foreign_column_name '.
                'FROM information_schema.key_column_usage '.
                'WHERE '.
                  'REFERENCED_TABLE_NAME is not NULL '.
                  'AND referenced_table_name = \''.$this->table.'\' '.
                  'AND table_schema = \''.OM_DB_DATABASE.'\'';
        
        /* Exemple de restultat avec $this->table = 'om_collectivite'
          
           table_name   |   column_name   | foreign_column_name 
        ----------------+-----------------+---------------------
         om_parametre   | om_collectivite | om_collectivite
         om_utilisateur | om_collectivite | om_collectivite
         exemple        | ville_1         | om_collectivite
         exemple        | ville_2         | om_collectivite
         ... */
        
        $other_tables = $this->f->db->query($sql);
        
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        if (database::isError($other_tables, true)) {
            if (DEBUG >= DEBUG_MODE) {
                echo 'erreur';
            }
        }
        
        while ($table =& $other_tables->fetchrow(DB_FETCHMODE_ASSOC)) {
        
            // initialisation de la liste des sous formulaires, sans doublon
            if (!in_array($table['table_name'], $this->sousformulaires)) {
                array_push($this->sousformulaires, $table['table_name']);
            }
        
            $infos = $table['table_name'].'.'.$table['column_name'];
        
            // initialisation des couples table.colonne sans doublon
            if (!in_array($infos, $this->other_tables)) {
                $this->other_tables[] = $infos;
            }
        
        }

    }

    /**
     * Initialisation des autres tables (PostGreSQL).
     *
     * Cette méthode permet d'initialiser les FOREIGN KEY des autres tables de
     * la base vers la table en cours en recherchant les contraintes dans la
     * base de données pour PostGreSQL.
     *
     * @return void
     */
    function _init_other_tables_information_schema_for_pgsql() {

        // tables ayant des references vers la table actuelle
        $sql = 'SELECT tc.table_name, '.
                      'kcu.column_name,'.
                      'ccu.column_name AS foreign_column_name '.
                 'FROM information_schema.table_constraints AS tc '.
                 'JOIN information_schema.key_column_usage AS kcu '.
                   'USING (constraint_schema, constraint_name) '.
                 'JOIN information_schema.constraint_column_usage AS ccu '.
                   'USING (constraint_schema, constraint_name) '.
                'WHERE constraint_type = \'FOREIGN KEY\' '.
                  'AND ccu.table_name = \''.$this->table.'\' '.
                  'AND ccu.table_schema = \''.OM_DB_SCHEMA.'\'';

        /* Exemple de restultat avec $this->table = 'om_collectivite'
          
           table_name   |   column_name   | foreign_column_name 
        ----------------+-----------------+---------------------
         om_parametre   | om_collectivite | om_collectivite
         om_utilisateur | om_collectivite | om_collectivite
         exemple        | ville_1         | om_collectivite
         exemple        | ville_2         | om_collectivite
         ... */

        $other_tables = $this->f->db->query($sql);

        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        if (database::isError($other_tables, true)) {
            if (DEBUG >= DEBUG_MODE) {
                echo 'erreur';
            }
        }

        while ($table =& $other_tables->fetchrow(DB_FETCHMODE_ASSOC)) {
    
            // initialisation de la liste des sous formulaires, sans doublon
            if (!in_array($table['table_name'], $this->sousformulaires)) {
                array_push($this->sousformulaires, $table['table_name']);
            }

            $infos = $table['table_name'].'.'.$table['column_name'];

            // initialisation des couples table.colonne sans doublon
            if (!in_array($infos, $this->other_tables)) {
                $this->other_tables[] = $infos;
            }

        }

    }

    /**
     * Initialisation des contraintes NOT NULL.
     *
     * Cette méthode permet d'initialiser l'attribut :
     *  - $this->_columns_notnull
     *
     * Cet attribut permet de gérer la notion de NOT NULL et de champs requis
     * de la table en cours de traitement.
     *
     * @return void
     */
    function _init_constraint_notnull() {

        if (OM_DB_PHPTYPE == 'mysql') {
            //
            $sql = " select column_name, column_default from information_schema.columns ";
            $sql .= " where table_schema='".OM_DB_DATABASE."' ";
            $sql .= " and table_name='".$this->table."' ";
            $sql .= " and is_nullable='NO' ";
            $sql .= " and column_default is NULL ORDER BY column_name; ";
            $res = $this->f->db->query($sql);
            database::isError($res);
            while ($row =& $res->fetchrow(DB_FETCHMODE_ASSOC)) {
                $this->_columns_notnull[] = strtolower($row['column_name']);
            }
        } elseif (OM_DB_PHPTYPE == 'pgsql') {
            //
            $sql =  'SELECT DISTINCT column_name '.
                    'FROM INFORMATION_SCHEMA.COLUMNS '.
                    'WHERE is_nullable = \'NO\' '.
                        'AND column_default IS NULL '.
                        'AND table_name = \''.$this->table.'\' '.
                        'AND table_schema = \''.OM_DB_SCHEMA.'\' '.
                    'ORDER BY column_name';
            $res_notnull = $this->f->db->query($sql);
    
            $message =  'gen(): db->query("'.$sql.'");';
            logger::instance()->log('class gen - '.$message, VERBOSE_MODE);
    
            if (database::isError($res_notnull)) {
                die();
            }
            while ($column =& $res_notnull->fetchrow(DB_FETCHMODE_ASSOC)) {
                $this->_columns_notnull[] = $column['column_name'];
            }
        }

    }

    /**
     * Permet de vérifier si une table fait partie du framework ou non.
     *
     * Le générateur adopte un comportement différent si la table générée fait
     * partie du framework. Cette méthode indique si c'est le cas ou non. 
     * 
     * @param string $table Le nom de la table.
     *
     * @return boolean
     */
    function is_omframework_table($table = null) {
        //
        if (is_null($table)) {
            //
            return false;
        }
        //
        if (substr($table, 0, 3) != "om_") {
            //
            return false;
        }
        //
        return true;
    }

    /**
     * Rempli les tableaux unique_key et unique_multiple_key.
     *
     * @param string $table Nom de la table à examiner.
     *
     * @return void
     */
    function set_unique_key($table) {

        if (OM_DB_PHPTYPE == "pgsql") {

            // Sur PostGreSQL des problèmes ont été rencontré avec la cohérence
            // des informations retournées par tableinfo

            //

            $sql = 'SELECT tc.constraint_name, '.
                      'kcu.column_name '.
                 'FROM information_schema.table_constraints AS tc '.
                 'JOIN information_schema.key_column_usage AS kcu '.
                   'USING (constraint_schema, constraint_name) '.
                'WHERE constraint_type = \'UNIQUE\' '.
                  'AND tc.table_name = \''.$this->table.'\' '.
                  'AND tc.table_schema = \''.OM_DB_SCHEMA.'\' '.
                'ORDER BY kcu.column_name';
            
        } else {

            $sql = "SELECT CONSTRAINT_NAME, COLUMN_NAME FROM information_schema.table_constraints ".
                    "JOIN information_schema.key_column_usage k ".
                    "USING(constraint_name,table_schema,table_name) ".
                    "WHERE TABLE_SCHEMA='".OM_DB_DATABASE."' AND CONSTRAINT_TYPE='UNIQUE'  AND TABLE_NAME='".$this->table."' ".
                    "ORDER BY COLUMN_NAME";
        }
        $res_unique_key = $this->f->db->query($sql);
    
        $message =  'gen(): db->query("'.$sql.'");';
        logger::instance()->log('class gen - '.$message, VERBOSE_MODE);

        if (database::isError($res_unique_key)) {
            die();
        }
        $unique_key=array();
        while ($key =& $res_unique_key->fetchrow(DB_FETCHMODE_ASSOC)) {
            $unique_key[$key['constraint_name']][] = $key['column_name'];
        }
        foreach($unique_key as $unique_constraint) {
            if(count($unique_constraint)>1) {
                $this->unique_multiple_key[] = $unique_constraint;
            } else {
                $this->unique_key[] = $unique_constraint[0];
            }
        }
    }

    /**
     * Vérifie si la table en cours est générable.
     *
     * @todo public
     * @return boolean
     */
    function is_generable() {

        //
        if (!$this->has_primary_key($this->table, true)) {
            return false;
        }

        //
        if (!$this->foreign_tables_have_primary_key(true)) {
            return false;
        }

        //
        return true;

    }

    /**
     * Indique si la table à une clé primaire.
     *
     * @param string  $table         Nom de la table à examiner.
     * @param boolean $display_error Affichage d'erreur à l'écran.
     *
     * @return boolean
     */
    function has_primary_key($table, $display_error = true) {

        //
        $primary_key = $this->get_primary_key($table);
        
        //
        if ($primary_key != null) {
            //
            return true;
        }
    
        //
        if ($display_error == true) {
            $m = new message();
            $message = _("Generation impossible, aucune cle primaire n'est ".
                         "presente ou plusieurs cles primaires sont presentes ".
                         "dans la table");
            $m->displayMessage('error', $message.' '.$table.'.');
        }
        //
        return false;

    }

    /**
     * Indique si les tables des clés étarngères ont une clé primaire.
     *
     * @param boolean $display_error Affichage d'erreur à l'écran.
     *
     * @return boolean
     */
    function foreign_tables_have_primary_key($display_error = true) {

        //
        foreach ($this->sousformulaires as $foreign_table_name) {
            //
            if (!$this->has_primary_key($foreign_table_name, $display_error)) {
                //
                return false;
            }
        }
        //
        return true;

    }

    /**
     * Renvoi le libellé.
     *
     * Cette méthode permet de récupérer le libellé de la clé primaire de la
     * table passée en paramètre.
     *
     * @param string $table Nom de la table.
     * 
     * @return string
     */
    function get_primary_key($table) {

        //
        $primary_key = "";
        $is_error = false;

        //
        $mode = $this->get_general_option("key_constraints_mode");

        //
        if ($mode == "constraints") {

            // MODE 1 - Recherche de PRIMARY KEY en interrogeant les contraintes
            // de la base de données par 'tableinfo'

            if (OM_DB_PHPTYPE == "pgsql") {

                // Sur PostGreSQL des problèmes ont été rencontré avec la cohérence
                // des informations retournées par tableinfo

                //
                $sql = 'SELECT tc.table_name, '.
                              'kcu.column_name '.
                         'FROM information_schema.table_constraints AS tc '.
                         'JOIN information_schema.key_column_usage AS kcu '.
                           'USING (constraint_schema, constraint_name) '.
                         'JOIN information_schema.constraint_column_usage AS ccu '.
                           'USING (constraint_schema, constraint_name) '.
                        'WHERE constraint_type = \'PRIMARY KEY\' '.
                          'AND tc.table_name = \''.$table.'\' '.
                          'AND tc.table_schema = \''.OM_DB_SCHEMA.'\'';
        
                $res_primary_key = $this->f->db->query($sql);
        
                $message =  'gen(): db->query("'.$sql.'");';
                logger::instance()->log('class gen - '.$message, VERBOSE_MODE);
        
                if (database::isError($res_primary_key)) {
                    die();
                }
    
                if ($res_primary_key->numrows() > 1) {
                    $primary_key = "";
                    $is_error = true;
                } else {
                    while ($key =& $res_primary_key->fetchrow(DB_FETCHMODE_ASSOC)) {
                        $primary_key = $key['column_name'];
                    }
                }
                
            } else {

                //
                $infos = $this->f->db->tableInfo(DB_PREFIXE.$table);
                //
                foreach ($infos as $column) {
                    // Si 'tableinfo' nous renvoi le flag 'primary_key' sur une
                    // colonne
                    if (strpos($column['flags'], 'primary_key')) {
                        // Si nous n'avons pas déjà trouvé une clé primaire et qu'il
                        // n'y a pas eu une erreur
                        if ($primary_key == "" && $is_error == false) {
                            // On récupère le libellé de la clé primaire
                            $primary_key = $column['name'];
                        } else {
                            // Si il y a plusieurs clés primaires sur la même table
                            // On re-initialise la clé primaire et on positionne
                            // le marqueur d'erreur à 'true'
                            $primary_key = "";
                            $is_error = true;
                        }
                    }
                }
            }

        } elseif ($mode == "postulate") {

            // MODE 2 - Recherche de PRIMARY KEY par le postulat : "le nom d'un
            // champ 'clé primaire' a pour nom le nom de la table."

            //
            $infos = $this->f->db->tableInfo(DB_PREFIXE.$table);
            //
            foreach ($infos as $column) {
                //
                if ($column['name'] == $table) {
                    //
                    $primary_key = $table;
                    break;
                }
            }

        }

        // Si on ne trouve aucune clé primaire
        if ($primary_key == "" && $is_error == false) {
            // On positionne le marqueur d'erreur à 'true'
            $is_error = true;
        }

        //
        if ($is_error == true) {
            return null;
        }
        //
        return $primary_key;
    }

    /**
     * Renvoi la colonne représentant le libellé d'un enregistrement de la table.
     *
     * @param string $table Nom de la table.
     *
     * @return string
     */
    function get_libelle_of($table) {

        /* Recherche du libelle.
          
          Si il existe une colonne libelle, elle est utilisee.
          Sinon, on utilise la seconde colonne de la table.
          Sinon, on utilise la cle primaire de la table.
        */

        $libelle = '';
        $libelle_exists = false;

        $infos = $this->f->db->tableInfo(DB_PREFIXE.$table);
        database::isError($infos);

        foreach ($infos as $column) {
            if ($column['name'] == 'libelle') {
                $libelle_exists = true;
                break;
            }
        }

        // si il existe une colonne libelle, elle est utilisee
        if ($libelle_exists == true) {
            $libelle = 'libelle';
        } else {

            // sinon, on utilise la seconde colonne de la table
            if (key_exists(1, $infos)) {
                $libelle = $infos[1]['name'];
            } else {
                // sinon, on utilise la cle primaire de la table.
                $libelle = $this->get_primary_key($table);
            }
        }

        $message = 'get_libelle_of(\''.DB_PREFIXE.$table.'\');';
        $message .= ' ';
        $message .= 'return \''.$libelle.'\';';
        logger::instance()->log('class gen - '.$message, VERBOSE_MODE);

        return $libelle;
    }

    // ------------------------------------------------------------------------
    // }}} END - RECUPERATION DES INFORMATIONS SUR LE MODELE
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - GESTION TECHNIQUE GENERATION
    //      * Écriture et suppression des scripts PHP
    //      * Vérification des permissions d'écriture sur le disque
    //      * Comparaison des fichiers avant génération
    // ------------------------------------------------------------------------

    /**
     * Écrit le contenu dans le fichier sur le disque.
     *
     * @param string $path_to_file Le chemin du fichier à écrire.
     * @param string $content      Contenu du fichier.
     *
     * @todo public
     * @return boolean
     */
    function ecrirefichier($path_to_file, $content) {
        //
        $messages = array();
        //
        if ($this->is_editable($path_to_file)) {
            // le fichier est genere seulement s'il est different de l'existant
            // la ligne date et heure de generation n'est pas prise en compte
            if (!$this->stream_slightly_equals_file($content, $path_to_file)) {
                // Traitement d'écriture 
                $inf = fopen($path_to_file, "w");
                fwrite($inf, $content);
                fclose($inf);
                //
                $messages[] = array(
                    "class" => "bold gen-ok",
                    "message" => _("Generation de")." ".$path_to_file,
                );
            } else {
                // sinon il n'est pas re-ecrit sur le disque
                //
                $messages[] = array(
                    "class" => "gen-nochange",
                    "message" => _("Aucun changement de")." ".$path_to_file,
                );
            }
            // Tout s'est bien passé
            $output = true;
        } else {
            //
            $messages[] = array(
                "class" => "gen-error",
                "message" => _("Erreur de droits d'ecriture sur")." ".$path_to_file,
            );
            // Il y a eu un problème
            $output = false;
        }
        //
        foreach ($messages as $message) {
            $this->msg .= sprintf(
                '<br/><span class="%s"> %s </span>',
                $message["class"],
                $message["message"]
            );
        }
        //
        return $output;
    }

    /**
     * Supprime le fichier du disque.
     *
     * @param string $path_to_file Le chemin du fichier à supprimer.
     *
     * @todo public
     * @return void
     */
    function supprimerfichier($path_to_file) {
        //
        $messages = array();
        //
        if (is_writable($path_to_file)) {
            // Traitement de suppression 
            unlink($path_to_file);
            //
            $messages[] = array(
                "class" => "bold gen-ok",
                "message" => _('Supression de')." ".$path_to_file,
            );
        } else {
            if(!file_exists($path_to_file)) {
                //
                $messages[] = array(
                    "class" => "bold",
                    "message" => _('Fichier inexistant ou illisible')." ".$path_to_file,
                );
            } else {
                //
                $messages[] = array(
                    "class" => "bold gen-error",
                    "message" => _('Impossible de supprimer')." ".$path_to_file,
                );
            }
        }
        //
        foreach ($messages as $message) {
            $this->msg .= sprintf(
                '<br/><span class="%s"> %s </span>',
                $message["class"],
                $message["message"]
            );
        }
    }

    /**
     * Vérifie les permissions sur le fichier à générer.
     *
     * @param string $path_to_file Le chemin du fichier à examiner.
     *
     * @return boolean
     */
    function is_editable($path_to_file) {
        // Récuperation du chemin vers le dossier parent
        $path_to_folder = $this->getPathFromFile($path_to_file);
        // Vérification des droits d'écriture sur le fichier :
        // - soit le fichier existe et on n'a pas la permission de l'écraser
        // - soit le fichier n'existe pas et on n'a pas la permission d'écrire
        //   dans le répertoire parent
        if ((!is_writable($path_to_file) && file_exists($path_to_file)) 
            || (!file_exists($path_to_file) && !is_writable($path_to_folder))) {
            // Donc le fichier n'est pas éditable
            return false;
        } else {
            // Sinon le fichier est éditable
            return true;
        }
    }

    /**
     * Compare un flux avec un fichier.
     *
     * L'entete du fichier contenant la date et l'heure de generation n'est pas
     * prise en compte (d'ou le "legerement").
     *
     * Retourne true si:
     *      - le flux et le fichier sont legerement identiques
     *
     * Retourne false si:
     *      - le fichier ne s'ouvre pas
     *      - le flux et le fichier sont differents
     *
     * @param string $stream       Contenu du fichier à générer.
     * @param string $path_to_file Chemin vers le fichier existant.
     *
     * @return boolean
     */
    function stream_slightly_equals_file($stream, $path_to_file) {

        // si le fichier n'existe pas
        if (!file_exists($path_to_file)) {
            return false;
        }
        
        $f = fopen($path_to_file, 'r');
        // si le fichier ne s'ouvre pas
        if (!$f) {
            return false;
        }

        $i = 0;
        $pointer = strlen($this->def_php_script_header());

        // boucle sur les lignes du fichier
        while (($line = fgets($f)) !== false) {

            $i++;

            // on saute les lignes d'entete (affichant l'heure)
            if ($i <= substr_count($this->def_php_script_header(), "\n")) {
                continue;
            }

            // si deux lignes sont differentes, $stream et $path_to_file sont differents
            if ($line != substr($stream, $pointer, strlen($line))) {
                return false;
            }


            $pointer += strlen($line);
        }

        // les fichiers sont legerement identiques
        return true;
    }

    // ------------------------------------------------------------------------
    // }}} END - GESTION TECHNIQUE GENERATION
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - GESTION DE L'INTERFACE DE GENERATION
    // ------------------------------------------------------------------------

    /**
     * Renvoi le tableau de paramètres listants les fichiers générables.
     *
     * @todo public
     * @return array
     */
    function get_gen_parameters() {
        //
        $parameters = array();
        //
        $parameters["table_obj_class_gen"] = array(
            "rubrik" => "formulaire",
            "path" => "../gen/obj/".$this->table.".class.php",
            "method" => "table_obj_class_gen",
            "checked_delete" => true,
            "checked_generate" => true,
        );
        //
        if ($this->is_omframework_table($this->table)) {
            //
            $parameters["table_obj_class_core"] = array(
                "rubrik" => "formulaire",
                "path" => "../core/obj/".$this->table.".class.php",
                "method" => "table_obj_class_core",
                "checked_delete" => true,
                "checked_generate" => "not_exists",
            );
        }
        //
        $parameters["table_obj_class"] = array(
            "rubrik" => "formulaire",
            "path" => "../obj/".$this->table.".class.php",
            "method" => "table_obj_class",
            "checked_delete" => true,
            "checked_generate" => "not_exists",
        );
        //
        $parameters["table_sql_inc_gen"] = array(
            "rubrik" => "formulaire",
            "path" => "../gen/sql/".OM_DB_PHPTYPE."/".$this->table.".inc.php",
            "method" => "table_sql_inc_gen",
            "checked_delete" => true,
            "checked_generate" => true,
        );
        //
        if ($this->is_omframework_table($this->table)) {
            //
            $parameters["table_sql_inc_core"] = array(
                "rubrik" => "formulaire",
                "path" => "../core/sql/".OM_DB_PHPTYPE."/".$this->table.".inc.php",
                "method" => "table_sql_inc_core",
                "checked_delete" => true,
                "checked_generate" => "not_exists",
            );
        }
        //
        $parameters["table_sql_inc"] = array(
            "rubrik" => "formulaire",
            "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table.".inc.php",
            "method" => "table_sql_inc",
            "checked_delete" => true,
            "checked_generate" => "not_exists",
        );
        //
        $parameters["table_sql_forminc_gen"] = array(
            "rubrik" => "formulaire",
            "path" => "../gen/sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php",
            "method" => "table_sql_forminc_gen",
            "checked_delete" => true,
            "checked_generate" => true,
        );
        //
        if ($this->is_omframework_table($this->table)) {
            //
            $parameters["table_sql_forminc_core"] = array(
                "rubrik" => "formulaire",
                "path" => "../core/sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php",
                "method" => "table_sql_forminc_core",
                "checked_delete" => true,
                "checked_generate" => "not_exists",
            );
        }
        //
        $parameters["table_sql_forminc"] = array(
            "rubrik" => "formulaire",
            "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php",
            "method" => "table_sql_forminc",
            "checked_delete" => true,
            "checked_generate" => "not_exists",
        );
        //
        $parameters["editioninc"] = array(
            "rubrik" => "edition",
            "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table.".pdf.inc.php",
            "method" => "table_sql_pdfinc",
            "checked_delete" => true,
            "checked_generate" => false,
        );
        //
        $parameters["reqmoinc"] = array(
            "rubrik" => "reqmo",
            "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table.".reqmo.inc.php",
            "method" => "table_sql_reqmoinc",
            "checked_delete" => true,
            "checked_generate" => false,
        );
        // On ajoute des fichiers générables pour chacune des clés étrangères
        // de l'objet sur lequel on se trouve
        if (!empty($this->clesecondaire)) {
            // On boucle sur chacune des clés étrangères
            foreach ($this->clesecondaire as $elem) {
                //
                $parameters["reqmo_".$elem] = array(
                    "rubrik" => "reqmo",
                    "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table."_".$elem.".reqmo.inc.php",
                    "method" => "table_sql_reqmoinc",
                    "method_param" => $elem,
                    "checked_delete" => true,
                    "checked_generate" => false,
                );
            }
        }
        //
        $parameters["importinc"] = array(
            "rubrik" => "divers",
            "path" => "../sql/".OM_DB_PHPTYPE."/".$this->table.".import.inc.php",
            "method" => "table_sql_importinc",
            "checked_delete" => true,
            "checked_generate" => false,
        );
        // Rétro-compatibilite : test du fichier .inc si pas de .inc.php
        foreach ($parameters as $key => $elem) {
            // Si le fichier .inc.php n'existe pas
            if (!file_exists($elem["path"])) {
                // Si le fichier .inc existe
                if (file_exists(substr($elem["path"], 0,
                                strlen($elem["path"])-4))) {
                    // Alors on modifie le path
                    $parameters[$key]["path"] = substr(
                        $elem["path"], 
                        0,
                        strlen($elem["path"]) - 4
                    );
               }
            }
        }
        //
        return $parameters;
    }

    /**
     * Affiche une ligne de tableau.
     *
     * @param string $col1 Contenu de la colonne 1.
     * @param string $col2 Contenu de la colonne 2.
     * @param string $col3 Contenu de la colonne 3.
     *
     * @todo public
     * @return void
     */
    function affichecol($col1, $col2, $col3) {
        echo "<tr class=\"tab-data even\">\n";
        echo "\t<td  class=\"col-1\">".$col1."</td>\n";
        echo "\t<td  class=\"col-2\">".$col2."</td>\n";
        echo "\t<td  class=\"col-3\">";
        $param['lien']=$col3;
        $this->f->layout->display_lien($param);
        echo"</td>\n";
        echo "</tr>\n";  
    }

    /**
     * Affiche une ligne de tableau.
     *
     * @param string $col Contenu de la colonne.
     *
     * @todo public
     * @return void
     */
    function affichetitre($col) {
        echo "<tr class=\"name\">\n";
        echo "\t<td colspan=\"3\">";
        echo $col;
        echo "</td>\n";
        echo "</tr>\n";
    }

    /**
     * Affiche une ligne de tableau.
     *
     * @param string $col1 Contenu de la colonne 1.
     * @param string $col2 Contenu de la colonne 2.
     *
     * @return void
     */
    function afficheinfo($col1, $col2) {
        echo "<tr class=\"tab-data odd\">\n";
        echo "\t<td  class=\"col-1\">".$col1."</td>\n";
        echo "\t<td  class=\"col-2\">".$col2."</td>\n";
        echo "</tr>\n";
    }

    /**
     * Renvoi le chemin vers le répertoire parent.
     *
     * @param string $path_to_file Le chemin du fichier à examiner.
     *
     * @todo public
     * @return string
     */
    function getPathFromFile($path_to_file) {
        //
        $path_to_file_as_array = explode("/", $path_to_file);
        $file_name = array_pop($path_to_file_as_array);
        $path_to_folder_as_array = $path_to_file_as_array;
        $path_to_folder = implode("/", $path_to_folder_as_array);
        //
        return $path_to_folder;
    }

    /**
     * Retourne en vert si le fichier existe, sinon une erreur en rouge.
     *
     * @param string $path_to_file Le chemin du fichier à examiner.
     *
     * @todo public
     * @return string
     */
    function returnFSRightOnFile($path_to_file) {
        //
        $path_to_folder = $this->getPathFromFile($path_to_file);
        //
        $messages = array();
        if (file_exists($path_to_file)) {
            $messages[] = array(
                "class" => "text-green",
                "message" => _("Le fichier existe"),
            );
            if (!is_writable($path_to_file)) {
                $messages[] = array(
                    "class" => "text-red",
                    "message" => _("Pas les droits d'ecriture sur le fichier"),
                );
            }
        } else {
            if(!file_exists($path_to_folder)) {
                $messages[] = array(
                    "class" => "text-red",
                    "message" => _("Le dossier n'existe pas ou n'est pas accessible"),
                );
            } elseif(!is_writable($path_to_folder)) {
                $messages[] = array(
                    "class" => "text-red",
                    "message" => _("Le fichier n'existe pas ou n'est pas accessible"),
                );
                $messages[] = array(
                    "class" => "text-red",
                    "message" => _("Pas les droits d'ecriture sur le dossier"),
                );
            } else {
                $messages[] = array(
                    "class" => "text-red",
                    "message" => _("Le fichier n'existe pas ou n'est pas accessible"),
                );
            }
        }
        //
        $output = "";
        foreach ($messages as $message) {
            $output .= sprintf(
                ' <span class="%s">[ %s ]</span> ',
                $message["class"],
                $message["message"]
            );
        }
        return $output;
    }

    /**
     * Affiche les informations sur la table en cours de traitement.
     *
     * Cet affichage permet sur l'écran de génération d'indiquer à l'utilisateur
     * les informations dont le générateur dispose sur la table en cours de 
     * traitement.
     * 
     * @todo public
     * @return void
     */
    function display_analyse() {
        //
        $this->f->layout->display_start_fieldset(array(
            "fieldset_class" => "startClosed",
            "legend_content" => _("analyse du modele de donnees"),
        ));

        // Ouverture de la balise tableA VOIR POUR MOBILE
        $param['idcolumntoggle']="genanalyse";
        $this->f->layout->display_table_start_class_default($param);
        $array_entete = array("element","infos");
        echo "<thead><tr class=\"ui-tabs-nav ui-accordion ui-state-default tab-title\">";
        $param = array(
                    "key" => 0,
                    "info" =>  $array_entete
             );
        $this->f->layout->display_table_cellule_entete_colonnes($param);
        echo "&nbsp;&nbsp;Elements</th>";
        $param = array(
                    "key" => 1,
                    "info" => $array_entete
             );
        $this->f->layout->display_table_cellule_entete_colonnes($param);
        echo "&nbsp;&nbsp;Infos</th>";
        echo "</tr></thead>";
        // tables de la base
        $contenu = "";
        if (!empty($this->tablebase)) {
            foreach ($this->tablebase as $elem) {
                $contenu .= " [ ".$elem. " ] ";
            }
            $lib = _("Tables de la base de donnees");
            $this->afficheinfo($lib, $contenu);
        } 
        // table
        $contenu = "";
        $lib = _("Table :")." <span class=\"bold\">".$this->table."</span>";
        $contenu .= "[ "._('cle')." ".$this->typecle." - ";
        if ($this->typecle == 'N') { // XXX - Ce test est-il correct ?
            $contenu .= _("cle automatique")." ]";
        } else {
            $contenu .= _("cle manuelle")." ]";
        }
        $contenu .= " <span class=\"bold\">[".$this->primary_key."]</span> ";
        $contenu .=" [ "._('longueur')." "._("enregistrement")." : ".$this->longueur." ]";
        $this->afficheinfo($lib, $contenu); 
        // champs
        $contenu = "";
        $lib = _("Champs");
        foreach ($this->info as $elem) {
            $contenu .= "[ ".$elem["name"]." ".$elem["len"]." ".$elem["type"]." ] ";
        }
        $this->afficheinfo($lib, $contenu); 
        // sous formulaire
        $contenu = "";
        if (!empty($this->sousformulaires)) {
            foreach ($this->sousformulaires as $elem) {
                $contenu .= " [ ".$elem. " ] ";
            }
        }
        $lib= _("Sous formulaire");
        $this->afficheinfo($lib, $contenu);
        // cle secondaire
        $contenu = ""; 
        if (!empty($this->clesecondaire)) {
            foreach ($this->clesecondaire as $elem) {
                $contenu .= " [ ".$elem. " ] ";
            }
        }
        $lib = _("Cle secondaire");
        $this->afficheinfo($lib, $contenu);
        // Fermeture de la balise table
        echo "</table>"; 
        //
        $this->f->layout->display_stop_fieldset();
    }

    // ------------------------------------------------------------------------
    // }}} END - GESTION DE L'INTERFACE UTILISATEUR DE GENERATION
    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    // {{{ START - GENERATION DES PERMISSIONS
    // ------------------------------------------------------------------------

    /**
     * VIEW - view_gen_permissions.
     *
     * Vue permettant de gérer l'interface utilisateur de génération 
     * automatique des permissions de l'application.
     * 
     * @todo public
     * @return void
     */
    function view_gen_permissions() {
        // XXX Améliorer l'interface en ajoutant une description, un bouton, 
        // une vérification sur la présence de la table vocabulaire des
        // permissions.
        // Début de la transaction
        $this->f->db->autoCommit(false);
        // Si le traitement est réalisé avec succès
        if ($this->treatment_gen_permissions() === true) {
            // Commit de la transaction
            $this->f->db->commit();
            //
            $this->f->displayMessage("valid", _("Traitement terminé"));
        } else {
            // Annulation de la transaction
            $this->f->db->rollback();
            // XXX Améliorer la gestion des erreurs
            // il faut remonter des messages plus explicites à l'utilisateur
            $this->f->displayMessage("error", _("Erreur"));
        }
    }

    /**
     * TREATMENT - treatment_gen_permissions.
     *
     * Ce traitement permet de : 
     * - mettre à jour la table de vocabulaire des permissions avec les 
     *   permissions de l'application "calculées" directement à partir du code,
     * - mettre à jour le fichier SQL d'initialisation des permissions 
     *   avec les permissions de l'application "calculées" directement à partir
     *   du code,
     * - supprimer tous les éléments obsolètes de la table de matrice des 
     *   droits.
     *
     * @return bool
     */
    function treatment_gen_permissions() {
        // Récupération de la liste des permissions.
        $permissions = $this->get_all_permissions();

        // Composition des requêtes d'insertion des permissions (une variable
        // pour le fichier et une variable pour la base de données)
        $template_insert = "
INSERT INTO %som_permission (om_permission, libelle, type) VALUES (nextval('%som_permission_seq'), '%s', 'gen');";
        $insert_file = "";
        $insert_db = "";
        foreach ($permissions as $key => $value) {
            $insert_file .= sprintf($template_insert, "", "", $value);
            $insert_db .= sprintf($template_insert, DB_PREFIXE, DB_PREFIXE, $value);
        }

        // Suppression des permissions existantes dans om_permission ayant le
        // type GEN
        $query = "DELETE FROM ".DB_PREFIXE."om_permission WHERE lower(om_permission.type) = 'gen'";
        $res = $this->f->db->query($query);
        $this->addToLog(__METHOD__."(): db->query(\"".$query."\");", VERBOSE_MODE);
        if ($this->f->isDatabaseError($res, true)) {
            return false;
        }

        // Insertion de toutes les nouvelles permissions dans la table 
        // om_permission
        $res = $this->f->db->query($insert_db);
        $this->addToLog(__METHOD__."(): db->query(\"".$insert_db."\");", VERBOSE_MODE);
        if ($this->f->isDatabaseError($res, true)) {
            return false;
        }

        // Suppression des lignes dans la table om_droit dont le libellé
        // n'existe pas dans la table permission
        $query = "DELETE FROM ".DB_PREFIXE."om_droit WHERE om_droit.libelle NOT IN (SELECT om_permission.libelle FROM ".DB_PREFIXE."om_permission)";
        $res = $this->f->db->query($query);
        $this->addToLog(__METHOD__."(): db->query(\"".$query."\");", VERBOSE_MODE);
        if ($this->f->isDatabaseError($res, true)) {
            return false;
        }

        // Écriture des requêtes d'insertion des permissions dans le fichier 
        // data/pgsql/init_permissions.sql
        $path_to_file = "../data/pgsql/init_permissions.sql";
        if (!$inf = @fopen($path_to_file, "w")) {
            $this->addToLog(__METHOD__."(): Impossible d'ouvrir le fichier (".$path_to_file.")", DEBUG_MODE);
            return false;
        }
        if (!@fwrite($inf, $insert_file)) {
            $this->addToLog(__METHOD__."(): Impossible d'écrire dans le fichier (".$path_to_file.")", DEBUG_MODE);
            return false;
        }
        if (!@fclose($inf)) {
            $this->addToLog(__METHOD__."(): Impossible de fermer le fichier (".$path_to_file.")", DEBUG_MODE);
            return false;
        }

        // Si aucune erreur n'a été retournée, alors le traitement s'est 
        // déroulé correctement
        return true;
    }

    /**
     * Retourne la liste des permissions "calculées".
     *
     * Cette méthode "calcule" l'intégralité des permissions présente dans 
     * l'application :
     * - toutes les permissions spécifiques déclarées dans 
     *   gen/dyn/permissions.inc.php,
     * - toutes les permissions utilisées dans l'attribut class_actions de 
     *   chacune des classes présentes dans le répertoire obj/,
     * - toutes les permissions utilisées dans dyn/menu.inc.php,
     * - toutes les permissions utilisées dans dyn/actions.inc.php,
     * - toutes les permissions utilisées dans dyn/footer.inc.php,
     * - toutes les permissions utilisées dans dyn/shortlinks.inc.php.
     *
     * @return array
     */
    function get_all_permissions() {
        //
        if (file_exists("../gen/dyn/permissions.inc.php")) {
            include "../gen/dyn/permissions.inc.php";
        }
        //
        if (!isset($permissions) || !is_array($permissions)) {
            //
            $permissions = array();
        }
        if (!isset($files_to_avoid) || !is_array($files_to_avoid)) {
            //
            $files_to_avoid = array();
        }
        //
        $files_to_avoid = array_merge($files_to_avoid, array(
            ".",
            "..",
            ".htaccess",
            "index.php",
            "utils.class.php",
            "om_dbform.class.php",
            "om_formulaire.class.php",
            "om_table.class.php",
        ));

        // GET PERMISSIONS FROM OBJ
        $folder_contents_to_scan = scandir("../obj/");
        foreach ($folder_contents_to_scan as $file) {
            //
            if (in_array($file, $files_to_avoid)) {
                continue;
            }
            //
            require_once "../obj/".$file;
            $name_class = explode(".", $file);
            $name_class = $name_class[0];
            $inst_class = new $name_class(0, $f->db, 0);
            if (!method_exists($inst_class, "init_class_actions")) {
                continue;
            }
            $permissions[] = $name_class;
            $permissions[] = $name_class."_tab";
            $inst_class->init_class_actions();
            foreach ($inst_class->class_actions as $action) {
                // Si l'action n'est pas définie
                if (!is_array($action)) {
                    continue;
                }
                //
                $perm = "";
                if (isset($action["permission_suffix"])) {
                    $perm = $action["permission_suffix"];
                }
                $permissions[] = $name_class."_".$perm;
            }
        }

        // GET PERMISSIONS FROM MENU
        require "../dyn/menu.inc.php";
        //
        foreach ($menu as $m => $rubrik) {
            //
            if (isset($rubrik['right'])) {
                if (!is_array($rubrik['right'])) {
                    $permissions[] = $rubrik['right'];
                } else {
                    foreach ($rubrik['right'] as $permission) {
                        $permissions[] = $permission;
                    }
                }
            }
            // Boucle sur les entrees de menu
            foreach ($rubrik['links'] as $link) {
                // Gestion des droits d'acces : si l'utilisateur n'a pas la
                // permission necessaire alors l'entree n'est pas affichee
                if (isset($link['right'])) {
                    if (!is_array($link['right'])) {
                        $permissions[] = $link['right'];
                    } else {
                        foreach ($link['right'] as $permission) {
                            $permissions[] = $permission;
                        }
                    }
                }
            }
        }

        // GET PERMISSIONS FROM ACTIONS, FOOTER, SHORTLINKS
        //
        $files_to_scan = array("actions", "footer", "shortlinks", );
        //
        foreach ($files_to_scan as $element) {
            //
            require "../dyn/".$element.".inc.php";
            //
            foreach ($$element as $action) {
                if (isset($action["right"])) {
                    $permissions[] = $action["right"];
                }
            }
        }

        //
        $permissions = array_unique($permissions);
        sort($permissions);

        //
        return $permissions;
    }

    // ------------------------------------------------------------------------
    // }}} END - GENERATION DES PERMISSIONS
    // ------------------------------------------------------------------------

    // {{{

    /**
     * Permet d'ajout des messages de log dans une pile.
     *
     * @param string  $message Message à logger.
     * @param integer $type    Niveau de log du message.
     *
     * @return void
     */
    function addToLog($message, $type = DEBUG_MODE) {
        //
        logger::instance()->log("class ".get_class($this)." - ".$message, $type);
    }

    // }}}

}

?>
