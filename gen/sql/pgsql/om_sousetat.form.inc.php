<?php
//$Id: om_sousetat.form.inc.php 2484 2013-09-18 13:02:28Z nhaye $ 
//gen openMairie le 18/09/2013 14:46

$DEBUG=0;
$ico="../img/ico_application.png";
$ent = _("parametrage")." -> "._("om_sousetat");
$tableSelect=DB_PREFIXE."om_sousetat";
$champs=array(
    "om_sousetat",
    "om_collectivite",
    "id",
    "libelle",
    "actif",
    "titre",
    "titrehauteur",
    "titrefont",
    "titreattribut",
    "titretaille",
    "titrebordure",
    "titrealign",
    "titrefond",
    "titrefondcouleur",
    "titretextecouleur",
    "intervalle_debut",
    "intervalle_fin",
    "entete_flag",
    "entete_fond",
    "entete_orientation",
    "entete_hauteur",
    "entetecolone_bordure",
    "entetecolone_align",
    "entete_fondcouleur",
    "entete_textecouleur",
    "tableau_largeur",
    "tableau_bordure",
    "tableau_fontaille",
    "bordure_couleur",
    "se_fond1",
    "se_fond2",
    "cellule_fond",
    "cellule_hauteur",
    "cellule_largeur",
    "cellule_bordure_un",
    "cellule_bordure",
    "cellule_align",
    "cellule_fond_total",
    "cellule_fontaille_total",
    "cellule_hauteur_total",
    "cellule_fondcouleur_total",
    "cellule_bordure_total",
    "cellule_align_total",
    "cellule_fond_moyenne",
    "cellule_fontaille_moyenne",
    "cellule_hauteur_moyenne",
    "cellule_fondcouleur_moyenne",
    "cellule_bordure_moyenne",
    "cellule_align_moyenne",
    "cellule_fond_nbr",
    "cellule_fontaille_nbr",
    "cellule_hauteur_nbr",
    "cellule_fondcouleur_nbr",
    "cellule_bordure_nbr",
    "cellule_align_nbr",
    "cellule_numerique",
    "cellule_total",
    "cellule_moyenne",
    "cellule_compteur",
    "om_sql");
//champs select
$sql_om_collectivite="SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite ORDER BY om_collectivite.libelle ASC";
$sql_om_collectivite_by_id = "SELECT om_collectivite.om_collectivite, om_collectivite.libelle FROM ".DB_PREFIXE."om_collectivite WHERE om_collectivite = <idx>";
?>