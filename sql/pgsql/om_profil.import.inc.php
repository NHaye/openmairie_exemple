<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

$import= "Insertion dans la table om_profil voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_profil";
$id='om_profil'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_profil" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "libelle" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "hierarchie" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
);
?>