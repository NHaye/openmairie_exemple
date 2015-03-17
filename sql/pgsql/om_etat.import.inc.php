<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$import= "Insertion dans la table om_etat voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_etat";
$id='om_etat'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_etat" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "om_collectivite" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
        "fkey" => array(
            "foreign_table_name" => "om_collectivite",
            "foreign_column_name" => "om_collectivite",
            "sql_exist" => "select * from ".DB_PREFIXE."om_collectivite where om_collectivite = '",
        ),
    ),
    "id" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "50",
    ),
    "libelle" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "actif" => array(
        "notnull" => "",
        "type" => "bool",
        "len" => "1",
    ),
    "orientation" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "2",
    ),
    "format" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "5",
    ),
    "logo" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "30",
    ),
    "logoleft" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "logotop" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "titre_om_htmletat" => array(
        "notnull" => "1",
        "type" => "blob",
        "len" => "-5",
    ),
    "titreleft" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "titretop" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "titrelargeur" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "titrehauteur" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "titrebordure" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "corps_om_htmletatex" => array(
        "notnull" => "1",
        "type" => "blob",
        "len" => "-5",
    ),
    "om_sql" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
        "fkey" => array(
            "foreign_table_name" => "om_requete",
            "foreign_column_name" => "om_requete",
            "sql_exist" => "select * from ".DB_PREFIXE."om_requete where om_requete = '",
        ),
    ),
    "se_font" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "se_couleurtexte" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "margeleft" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "margetop" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "margeright" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "margebottom" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
);
?>