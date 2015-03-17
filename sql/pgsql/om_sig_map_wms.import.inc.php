<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$import= "Insertion dans la table om_sig_map_wms voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_sig_map_wms";
$id='om_sig_map_wms'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_sig_map_wms" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "om_sig_wms" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
        "fkey" => array(
            "foreign_table_name" => "om_sig_wms",
            "foreign_column_name" => "om_sig_wms",
            "sql_exist" => "select * from ".DB_PREFIXE."om_sig_wms where om_sig_wms = '",
        ),
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
    "ol_map" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "50",
    ),
    "ordre" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "visibility" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "3",
    ),
    "panier" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "3",
    ),
    "pa_nom" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "50",
    ),
    "pa_layer" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "50",
    ),
    "pa_attribut" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "50",
    ),
    "pa_encaps" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "3",
    ),
    "pa_sql" => array(
        "notnull" => "",
        "type" => "blob",
        "len" => "-5",
    ),
    "pa_type_geometrie" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "30",
    ),
    "sql_filter" => array(
        "notnull" => "",
        "type" => "blob",
        "len" => "-5",
    ),
    "baselayer" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "3",
    ),
    "singletile" => array(
        "notnull" => "",
        "type" => "string",
        "len" => "3",
    ),
    "maxzoomlevel" => array(
        "notnull" => "",
        "type" => "int",
        "len" => "11",
    ),
);
?>