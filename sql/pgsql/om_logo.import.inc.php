<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$import= "Insertion dans la table om_logo voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_logo";
$id='om_logo'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_logo" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
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
    "description" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "200",
    ),
    "fichier" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "resolution" => array(
        "notnull" => "",
        "type" => "int",
        "len" => "11",
    ),
    "actif" => array(
        "notnull" => "",
        "type" => "bool",
        "len" => "1",
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
);
?>