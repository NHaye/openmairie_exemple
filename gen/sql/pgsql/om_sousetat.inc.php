<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$DEBUG=0;
$serie=15;
$ico="../img/ico_application.png";
$ent = _("parametrage")." -> "._("om_sousetat");
if(!isset($premier)) $premier='';
if(!isset($recherche1)) $recherche1='';
if(!isset($tricolsf)) $tricolsf='';
if(!isset($premiersf)) $premiersf='';
if(!isset($selection)) $selection='';
if(!isset($retourformulaire)) $retourformulaire='';
if (isset($idx) && $idx != ']' && trim($idx) != '') {
    $ent .= "->&nbsp;".$idx."&nbsp;";
}
if (isset($idz) && trim($idz) != '') {
    $ent .= "&nbsp;".strtoupper($idz)."&nbsp;";
}
// FROM 
$table = DB_PREFIXE."om_sousetat
    LEFT JOIN ".DB_PREFIXE."om_collectivite 
        ON om_sousetat.om_collectivite=om_collectivite.om_collectivite ";
// SELECT 
$champAffiche = array(
    'om_sousetat.om_sousetat as "'._("om_sousetat").'"',
    'om_sousetat.id as "'._("id").'"',
    'om_sousetat.libelle as "'._("libelle").'"',
    "case om_sousetat.actif when 't' then 'Oui' else 'Non' end as \""._("actif")."\"",
    'om_sousetat.titrehauteur as "'._("titrehauteur").'"',
    'om_sousetat.titrefont as "'._("titrefont").'"',
    'om_sousetat.titreattribut as "'._("titreattribut").'"',
    'om_sousetat.titretaille as "'._("titretaille").'"',
    'om_sousetat.titrebordure as "'._("titrebordure").'"',
    'om_sousetat.titrealign as "'._("titrealign").'"',
    'om_sousetat.titrefond as "'._("titrefond").'"',
    'om_sousetat.titrefondcouleur as "'._("titrefondcouleur").'"',
    'om_sousetat.titretextecouleur as "'._("titretextecouleur").'"',
    'om_sousetat.intervalle_debut as "'._("intervalle_debut").'"',
    'om_sousetat.intervalle_fin as "'._("intervalle_fin").'"',
    'om_sousetat.entete_flag as "'._("entete_flag").'"',
    'om_sousetat.entete_fond as "'._("entete_fond").'"',
    'om_sousetat.entete_orientation as "'._("entete_orientation").'"',
    'om_sousetat.entete_hauteur as "'._("entete_hauteur").'"',
    'om_sousetat.entetecolone_bordure as "'._("entetecolone_bordure").'"',
    'om_sousetat.entetecolone_align as "'._("entetecolone_align").'"',
    'om_sousetat.entete_fondcouleur as "'._("entete_fondcouleur").'"',
    'om_sousetat.entete_textecouleur as "'._("entete_textecouleur").'"',
    'om_sousetat.tableau_largeur as "'._("tableau_largeur").'"',
    'om_sousetat.tableau_bordure as "'._("tableau_bordure").'"',
    'om_sousetat.tableau_fontaille as "'._("tableau_fontaille").'"',
    'om_sousetat.bordure_couleur as "'._("bordure_couleur").'"',
    'om_sousetat.se_fond1 as "'._("se_fond1").'"',
    'om_sousetat.se_fond2 as "'._("se_fond2").'"',
    'om_sousetat.cellule_fond as "'._("cellule_fond").'"',
    'om_sousetat.cellule_hauteur as "'._("cellule_hauteur").'"',
    'om_sousetat.cellule_largeur as "'._("cellule_largeur").'"',
    'om_sousetat.cellule_bordure_un as "'._("cellule_bordure_un").'"',
    'om_sousetat.cellule_bordure as "'._("cellule_bordure").'"',
    'om_sousetat.cellule_align as "'._("cellule_align").'"',
    'om_sousetat.cellule_fond_total as "'._("cellule_fond_total").'"',
    'om_sousetat.cellule_fontaille_total as "'._("cellule_fontaille_total").'"',
    'om_sousetat.cellule_hauteur_total as "'._("cellule_hauteur_total").'"',
    'om_sousetat.cellule_fondcouleur_total as "'._("cellule_fondcouleur_total").'"',
    'om_sousetat.cellule_bordure_total as "'._("cellule_bordure_total").'"',
    'om_sousetat.cellule_align_total as "'._("cellule_align_total").'"',
    'om_sousetat.cellule_fond_moyenne as "'._("cellule_fond_moyenne").'"',
    'om_sousetat.cellule_fontaille_moyenne as "'._("cellule_fontaille_moyenne").'"',
    'om_sousetat.cellule_hauteur_moyenne as "'._("cellule_hauteur_moyenne").'"',
    'om_sousetat.cellule_fondcouleur_moyenne as "'._("cellule_fondcouleur_moyenne").'"',
    'om_sousetat.cellule_bordure_moyenne as "'._("cellule_bordure_moyenne").'"',
    'om_sousetat.cellule_align_moyenne as "'._("cellule_align_moyenne").'"',
    'om_sousetat.cellule_fond_nbr as "'._("cellule_fond_nbr").'"',
    'om_sousetat.cellule_fontaille_nbr as "'._("cellule_fontaille_nbr").'"',
    'om_sousetat.cellule_hauteur_nbr as "'._("cellule_hauteur_nbr").'"',
    'om_sousetat.cellule_fondcouleur_nbr as "'._("cellule_fondcouleur_nbr").'"',
    'om_sousetat.cellule_bordure_nbr as "'._("cellule_bordure_nbr").'"',
    'om_sousetat.cellule_align_nbr as "'._("cellule_align_nbr").'"',
    'om_sousetat.cellule_numerique as "'._("cellule_numerique").'"',
    'om_sousetat.cellule_total as "'._("cellule_total").'"',
    'om_sousetat.cellule_moyenne as "'._("cellule_moyenne").'"',
    'om_sousetat.cellule_compteur as "'._("cellule_compteur").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champAffiche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
//
$champNonAffiche = array(
    'om_sousetat.om_collectivite as "'._("om_collectivite").'"',
    'om_sousetat.titre as "'._("titre").'"',
    'om_sousetat.om_sql as "'._("om_sql").'"',
    );
//
$champRecherche = array(
    'om_sousetat.om_sousetat as "'._("om_sousetat").'"',
    'om_sousetat.id as "'._("id").'"',
    'om_sousetat.libelle as "'._("libelle").'"',
    'om_sousetat.titrehauteur as "'._("titrehauteur").'"',
    'om_sousetat.titrefont as "'._("titrefont").'"',
    'om_sousetat.titreattribut as "'._("titreattribut").'"',
    'om_sousetat.titretaille as "'._("titretaille").'"',
    'om_sousetat.titrebordure as "'._("titrebordure").'"',
    'om_sousetat.titrealign as "'._("titrealign").'"',
    'om_sousetat.titrefond as "'._("titrefond").'"',
    'om_sousetat.titrefondcouleur as "'._("titrefondcouleur").'"',
    'om_sousetat.titretextecouleur as "'._("titretextecouleur").'"',
    'om_sousetat.intervalle_debut as "'._("intervalle_debut").'"',
    'om_sousetat.intervalle_fin as "'._("intervalle_fin").'"',
    'om_sousetat.entete_flag as "'._("entete_flag").'"',
    'om_sousetat.entete_fond as "'._("entete_fond").'"',
    'om_sousetat.entete_orientation as "'._("entete_orientation").'"',
    'om_sousetat.entete_hauteur as "'._("entete_hauteur").'"',
    'om_sousetat.entetecolone_bordure as "'._("entetecolone_bordure").'"',
    'om_sousetat.entetecolone_align as "'._("entetecolone_align").'"',
    'om_sousetat.entete_fondcouleur as "'._("entete_fondcouleur").'"',
    'om_sousetat.entete_textecouleur as "'._("entete_textecouleur").'"',
    'om_sousetat.tableau_largeur as "'._("tableau_largeur").'"',
    'om_sousetat.tableau_bordure as "'._("tableau_bordure").'"',
    'om_sousetat.tableau_fontaille as "'._("tableau_fontaille").'"',
    'om_sousetat.bordure_couleur as "'._("bordure_couleur").'"',
    'om_sousetat.se_fond1 as "'._("se_fond1").'"',
    'om_sousetat.se_fond2 as "'._("se_fond2").'"',
    'om_sousetat.cellule_fond as "'._("cellule_fond").'"',
    'om_sousetat.cellule_hauteur as "'._("cellule_hauteur").'"',
    'om_sousetat.cellule_largeur as "'._("cellule_largeur").'"',
    'om_sousetat.cellule_bordure_un as "'._("cellule_bordure_un").'"',
    'om_sousetat.cellule_bordure as "'._("cellule_bordure").'"',
    'om_sousetat.cellule_align as "'._("cellule_align").'"',
    'om_sousetat.cellule_fond_total as "'._("cellule_fond_total").'"',
    'om_sousetat.cellule_fontaille_total as "'._("cellule_fontaille_total").'"',
    'om_sousetat.cellule_hauteur_total as "'._("cellule_hauteur_total").'"',
    'om_sousetat.cellule_fondcouleur_total as "'._("cellule_fondcouleur_total").'"',
    'om_sousetat.cellule_bordure_total as "'._("cellule_bordure_total").'"',
    'om_sousetat.cellule_align_total as "'._("cellule_align_total").'"',
    'om_sousetat.cellule_fond_moyenne as "'._("cellule_fond_moyenne").'"',
    'om_sousetat.cellule_fontaille_moyenne as "'._("cellule_fontaille_moyenne").'"',
    'om_sousetat.cellule_hauteur_moyenne as "'._("cellule_hauteur_moyenne").'"',
    'om_sousetat.cellule_fondcouleur_moyenne as "'._("cellule_fondcouleur_moyenne").'"',
    'om_sousetat.cellule_bordure_moyenne as "'._("cellule_bordure_moyenne").'"',
    'om_sousetat.cellule_align_moyenne as "'._("cellule_align_moyenne").'"',
    'om_sousetat.cellule_fond_nbr as "'._("cellule_fond_nbr").'"',
    'om_sousetat.cellule_fontaille_nbr as "'._("cellule_fontaille_nbr").'"',
    'om_sousetat.cellule_hauteur_nbr as "'._("cellule_hauteur_nbr").'"',
    'om_sousetat.cellule_fondcouleur_nbr as "'._("cellule_fondcouleur_nbr").'"',
    'om_sousetat.cellule_bordure_nbr as "'._("cellule_bordure_nbr").'"',
    'om_sousetat.cellule_align_nbr as "'._("cellule_align_nbr").'"',
    'om_sousetat.cellule_numerique as "'._("cellule_numerique").'"',
    'om_sousetat.cellule_total as "'._("cellule_total").'"',
    'om_sousetat.cellule_moyenne as "'._("cellule_moyenne").'"',
    'om_sousetat.cellule_compteur as "'._("cellule_compteur").'"',
    );
//
if ($_SESSION['niveau'] == '2') {
    array_push($champRecherche, "om_collectivite.libelle as \""._("collectivite")."\"");
}
$tri="ORDER BY om_sousetat.libelle ASC NULLS LAST";
$edition="om_sousetat";
/**
 * Gestion de la clause WHERE => $selection
 */
// Filtre listing standard
if ($_SESSION["niveau"] == "2") {
    // Filtre MULTI
    $selection = "";
} else {
    // Filtre MONO
    $selection = " WHERE (om_sousetat.om_collectivite = '".$_SESSION["collectivite"]."') ";
}
// Liste des clés étrangères avec leurs éventuelles surcharges
$foreign_keys_extended = array(
    "om_collectivite" => array("om_collectivite", ),
);
// Filtre listing sous formulaire - om_collectivite
if (in_array($retourformulaire, $foreign_keys_extended["om_collectivite"])) {
    if ($_SESSION["niveau"] == "2") {
        // Filtre MULTI
        $selection = " WHERE (om_sousetat.om_collectivite = '".$idx."') ";
    } else {
        // Filtre MONO
        $selection = " WHERE (om_sousetat.om_collectivite = '".$_SESSION["collectivite"]."') AND (om_sousetat.om_collectivite = '".$idx."') ";
    }
}

?>