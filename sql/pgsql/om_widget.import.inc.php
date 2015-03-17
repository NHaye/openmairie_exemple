<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$import= "Insertion dans la table om_widget voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_widget";
$id='om_widget'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_widget" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "libelle" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "lien" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "80",
    ),
    "texte" => array(
        "notnull" => "1",
        "type" => "blob",
        "len" => "-5",
    ),
    "type" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "40",
    ),
);
?>