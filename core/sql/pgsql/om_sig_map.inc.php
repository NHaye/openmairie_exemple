<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sig_map.inc.php 2946 2014-11-03 10:22:38Z baldachino $
 */

//
include "../gen/sql/pgsql/om_sig_map.inc.php";

//
$table=DB_PREFIXE."om_sig_map inner join ".DB_PREFIXE.
        "om_collectivite on om_collectivite.om_collectivite = om_sig_map.om_collectivite";
$champAffiche=array(
    'om_sig_map as "'._("om_sig_map").'"',
    'id as "'._("id").'"',
    'om_sig_map.libelle as "'._("libelle").'"',
    'om_collectivite.libelle ||\' (\'||om_collectivite.niveau||\')\' as "'._("om_collectivite").'"',
    'zoom as "'._("zoom").'"',
    'fond_osm as "'._("osm").'"',
    'fond_bing as "'._("bing").'"',
    'fond_sat as "'._("sat").'"',
    'layer_info as "'._("info").'"',
//    'etendue as "'._("etendue").'"',
//    'projection_externe as "'._("projection_externe").'"',
    'maj as "'._("maj").'"',
//    'lib_geometrie as "'._("lib_geometrie").'"',
//    'table_update as "'._("table_update").'"',
//    'champ as "'._("champ").'"',
    'actif as "'._("actif").'"',
    'niveau as "'._("niveau").'"',
    );
$champRecherche=array(
    'id as "'._("id").'"',
    'om_sig_map.libelle as "'._("libelle").'"',
    'zoom as "'._("zoom").'"',
    'fond_osm as "'._("fond_osm").'"',
    'fond_bing as "'._("fond_bing").'"',
    'fond_sat as "'._("fond_sat").'"',
    'etendue as "'._("etendue").'"',
    'projection_externe as "'._("projection_externe").'"',
    'maj as "'._("maj").'"',
    'table_update as "'._("table_update").'"',
    'actif as "'._("actif").'"',
    );

$tri=' order by om_sig_map.id';//.libelle';
$href[3] = array(
    "lien" => "../scr/valid_copie.php?obj=".$obj."&amp;idx=",
    "id" => "",
    "lib" => "<span class=\"om-icon om-icon-16 om-icon-fix copy-16\" title=\""._("Copier")."\">"._("Copier")."</span>",
);
if ($_SESSION['niveau']== '2')
    $selection='';
else{
    $selection=" where om_sig_map.om_collectivite = '".$_SESSION['collectivite']."' or om_collectivite.niveau ='2'";
    $options = array();
    $option = array(
        "type" => "condition",
        "field" => "niveau",
        "case" => array(
            "0" => array(
                "values" => array('2', ),
                "style" => "tab_desactive",
                "href" => array(
                    0 => array("lien" => "", "id" => "", "lib" => ""),
                    1 => array("lien" => "", "id" => "", "lib" => ""),
                    2 => array("lien" => "", "id" => "", "lib" => ""),
                    //3 => array("lien" => "", "id" => "", "lib" => ""),
            3 => array("lien" => "../scr/valid_copie.php?obj=".$obj."&amp;idx=","id" => "",
                  "lib" => "<span class=\"om-icon om-icon-16 om-icon-fix copy-16\" title=\""._("Copier")."\">"._("Copier")."</span>"),
        ),
              ),
       ),
    );
    array_push($options, $option);
}

?>
