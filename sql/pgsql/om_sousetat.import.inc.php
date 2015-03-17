<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$import= "Insertion dans la table om_sousetat voir rec/import_utilisateur.inc";
$table= DB_PREFIXE."om_sousetat";
$id='om_sousetat'; // numerotation automatique
$verrou=1;// =0 pas de mise a jour de la base / =1 mise a jour
$fic_rejet=1; // =0 pas de fichier pour relance / =1 fichier relance traitement
$ligne1=1;// = 1 : 1ere ligne contient nom des champs / o sinon
/**
 *
 */
$fields = array(
    "om_sousetat" => array(
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
    "titre" => array(
        "notnull" => "1",
        "type" => "blob",
        "len" => "-5",
    ),
    "titrehauteur" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "titrefont" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "titreattribut" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "titretaille" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "titrebordure" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "titrealign" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "titrefond" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "titrefondcouleur" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "titretextecouleur" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "intervalle_debut" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "intervalle_fin" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "entete_flag" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "entete_fond" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "entete_orientation" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "entete_hauteur" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "entetecolone_bordure" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "200",
    ),
    "entetecolone_align" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "entete_fondcouleur" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "entete_textecouleur" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "tableau_largeur" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "tableau_bordure" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "tableau_fontaille" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "bordure_couleur" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "se_fond1" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "se_fond2" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "cellule_fond" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "cellule_hauteur" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "cellule_largeur" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "200",
    ),
    "cellule_bordure_un" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "200",
    ),
    "cellule_bordure" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "200",
    ),
    "cellule_align" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "cellule_fond_total" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "cellule_fontaille_total" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "cellule_hauteur_total" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "cellule_fondcouleur_total" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "cellule_bordure_total" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "200",
    ),
    "cellule_align_total" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "cellule_fond_moyenne" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "cellule_fontaille_moyenne" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "cellule_hauteur_moyenne" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "cellule_fondcouleur_moyenne" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "cellule_bordure_moyenne" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "200",
    ),
    "cellule_align_moyenne" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "cellule_fond_nbr" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "20",
    ),
    "cellule_fontaille_nbr" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "cellule_hauteur_nbr" => array(
        "notnull" => "1",
        "type" => "int",
        "len" => "11",
    ),
    "cellule_fondcouleur_nbr" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "11",
    ),
    "cellule_bordure_nbr" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "200",
    ),
    "cellule_align_nbr" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "cellule_numerique" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "200",
    ),
    "cellule_total" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "cellule_moyenne" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "cellule_compteur" => array(
        "notnull" => "1",
        "type" => "string",
        "len" => "100",
    ),
    "om_sql" => array(
        "notnull" => "1",
        "type" => "blob",
        "len" => "-5",
    ),
);
?>