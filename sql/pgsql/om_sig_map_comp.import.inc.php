<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$import= "Insertion dans la table om_sig_map_comp voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_sig_map_comp";
$id='om_sig_map_comp'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_sig_map_comp" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "om_sig_map" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
        "fkey" => array(
            "foreign_table_name" => "om_sig_map",
            "foreign_column_name" => "om_sig_map",
            "sql_exist" => "select * from ".DB_PREFIXE."om_sig_map where om_sig_map = '",
        ),
    ),
    "libelle" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "50",
    ),
    "ordre" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "actif" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "3",
    ),
    "comp_maj" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "3",
    ),
    "type_geometrie" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "30",
    ),
    "comp_table_update" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "30",
    ),
    "comp_champ" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "30",
    ),
);
?>