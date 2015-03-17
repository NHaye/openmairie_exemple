<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$import= "Insertion dans la table om_sig_wms voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_sig_wms";
$id='om_sig_wms'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_sig_wms" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "libelle" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "50",
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
    "chemin" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "255",
    ),
    "couches" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "255",
    ),
    "cache_type" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "3",
    ),
    "cache_gfi_chemin" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "255",
    ),
    "cache_gfi_couches" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "255",
    ),
);
?>