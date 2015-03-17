<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$import= "Insertion dans la table om_dashboard voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_dashboard";
$id='om_dashboard'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_dashboard" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "om_profil" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
        "fkey" => array(
            "foreign_table_name" => "om_profil",
            "foreign_column_name" => "om_profil",
            "sql_exist" => "select * from ".DB_PREFIXE."om_profil where om_profil = '",
        ),
    ),
    "bloc" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "10",
    ),
    "position" => array(
        "notnull" => "",
        "type" => "int",
        "len" => "11",
    ),
    "om_widget" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
        "fkey" => array(
            "foreign_table_name" => "om_widget",
            "foreign_column_name" => "om_widget",
            "sql_exist" => "select * from ".DB_PREFIXE."om_widget where om_widget = '",
        ),
    ),
);
?>