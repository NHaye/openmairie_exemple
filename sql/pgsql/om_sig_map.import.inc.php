<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$import= "Insertion dans la table om_sig_map voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_sig_map";
$id='om_sig_map'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_sig_map" => array(
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
        "len" => "50",
    ),
    "actif" => array(
        "notnull" => "",
        "type" => "bool",
        "len" => "1",
    ),
    "zoom" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "3",
    ),
    "fond_osm" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "3",
    ),
    "fond_bing" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "3",
    ),
    "fond_sat" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "3",
    ),
    "layer_info" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "3",
    ),
    "etendue" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "60",
    ),
    "projection_externe" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "60",
    ),
    "url" => array(
        "notnull" => "1",
        "type" => "blob",
        "len" => "-5",
    ),
    "om_sql" => array(
        "notnull" => "1",
        "type" => "blob",
        "len" => "-5",
    ),
    "maj" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "3",
    ),
    "table_update" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "30",
    ),
    "champ" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "30",
    ),
    "retour" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "50",
    ),
    "type_geometrie" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "30",
    ),
    "lib_geometrie" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "50",
    ),
);
?>