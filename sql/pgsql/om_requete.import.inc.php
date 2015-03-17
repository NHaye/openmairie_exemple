<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$import= "Insertion dans la table om_requete voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_requete";
$id='om_requete'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_requete" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "code" => array(
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
    "requete" => array(
        "notnull" => "",
        "type" => "blob",
        "len" => "-5",
    ),
    "merge_fields" => array(
        "notnull" => "",
        "type" => "blob",
        "len" => "-5",
    ),
);
?>