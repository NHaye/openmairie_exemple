<?php
/**
 * Ce fichier permet de ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: var_adresse_postale.inc 2211 2013-03-29 08:49:03Z fmichon $
 */

// parametre general
$longueurRecherche=1;
// *** ADRESSE POSTALE INTERNE ***
// table et champs de la requete adresse postale
$t="adresse_postale";                       // table adresse postale
$t_voie = "rivoli";                                    // code adresse 
$t_numero="numero";                                    // numero dans la voie
$t_complement="complement";                            // suffixe (bis, ter ...)
$t_geom="geom";                                        // geometry point(X,Y)
$t_adresse="libelle";                                  // libelle de l adresse
$t_quartier='';
// *** a voir 
$t_cp='';                                   // nom champ cp
$t_ville='';                                // nom champ ville
$t_insee='';                                // nom champ insee
// prevoir un champ selection sur le code commune pour ign
// $selection =" where codeinsee='13004'"; // 
// champ du formulaire 
$f_numero='numero_voie';        // nom champ du numero dans la voie
$f_voie='voie';                 // nom champ du code de la voie (rivoli) 
$f_complement='complement';     // nom champ du complement de numero
$f_geom='geom';                 // nom champ geometrique point(X,Y)
$f_libelle='libelle_voie';      // nom champ libelle de la voie
// *** a voir
$f_cp='';                       // nom champ cp
$f_ville='';                    // nom champ ville
$f_insee='';                    // nom champ insee
// base de donnees externe a la base en cours -> utiliser les vues ?
$db_externe='Non';
//$dsn_externe= array(
//    'title'  =>"base des adresses de l'IGN",
//    'phptype'  => "pgsql",
//    'dbsyntax' => "pgsql",
//    'username' => "postgres",
//    'password' => "postgres",
//    'protocol' => "tcp",
//    'hostspec' => "localhost",
//    'port'     => "5432",
//    'socket'   => "",
//    'database' => "odp",
//    'formatdate'=> "AAAA-MM-JJ",
//    'schema'  => "public",
//    'prefixe'  =>""
//);
//$db_option_externe=array('debug'=>2,
//    'portability'=>DB_PORTABILITY_ALL);
// *** ADRESSE POSTALE EXTERNE ***
// variables par defaut cp et ville si non renseignées dans le formulaire
// pour recherche
$cp="13200"; 
$ville="Arles";
$pays = ""; // a voir
// epsg de transformation pt adresse postale dans la base en cours
$epsg= "EPSG:2154";
// acces au script adresse_postale externe
$adresse_interne="Oui";
$google="Oui"; // google
$bing="Oui";   // bing
$osm="Oui";    // mapquest

?>
