<?php
/**
 * GEOLOCALISATION - Gestion du SIG
 *
 * version wms : alain baldachino (Vitrolles), francois raynaud (Arles)
 * =====================================================================
 * la version wms rajoute les fonctions suivantes :
 * - multigeometries
 * - panier pour saisie de données (goupe de parcelle)
 * - multipolygon et multiline
 * - flux wms : methode getMap
 * - flux wms : getfeature
 * =======================================================================
 *
 * @package openmairie_exemple
 * @version SVN : $Id: tab_sig.php 2997 2014-12-11 10:23:06Z baldachino $
 */
// ==============================
// utils + librairies javascripts
// ==============================
include ("../obj/utils.class.php");
$f = new utils ('nohtml');
$f->disableLog();

// ===================================
// *** recuperation de variable en URL
// ===================================
// numero d objet
if (isset ($_GET['idx'])){
   $idx=$f->db->escapeSimple($_GET['idx']);
}else{
   $idx='';
}
// obj
if (isset ($_GET['obj'])){
   $obj=$f->db->escapeSimple($_GET['obj']);
}
// géométrie sélectionnée (polygon, point ou lines)
if (isset ($_GET['seli'])){
   $seli=$_GET['seli'];
}else {
	if (isset ($_SESSION['sig_'.$obj]['seli']))
		$seli=$_SESSION['sig_'.$obj]['seli'];
	else 
		$seli=0;
}
// parametrage de l etendue dans l url 
if (isset ($_GET['etendue'])){
   $etendue=$_GET['etendue'];
}

if (isset ($_SESSION['sig_'.$obj])) {
	$s_zoom=$_SESSION['sig_'.$obj]['zoom'];
	$s_base=$_SESSION['sig_'.$obj]['base'];	
} else {
	$s_base='';
	$s_zoom='';
}
$f->addHTMLHeadJs(array("../lib/openlayers/OpenLayers.js",
                        "../lib/openlayers/proj4js-compressed.js",
                        "../js/sig.js",
                        "../app/js/sig.js"));


// ================================================
// *** recuperation sql dans le sgbd postgresql ***
// ================================================
// table om_sig_map
$titre="";
$sql="select * from ".DB_PREFIXE."om_sig_map where id='".$obj."'";
$res = $f -> db -> query($sql);
if (DB :: isError($res)){
	$class = "error";
    $message = _($res->getMessage()." ERREUR SQL ".$sql);
    $f->addToMessage($class, $message);
    $f->setFlag(NULL);
    $f->display();
    die();
}else{
    while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
        $titre=$row['libelle'];
        $zoom=$row['zoom'];
        $fond_sat=$row['fond_sat'];
        $fond_osm=$row['fond_osm'];
        $fond_bing=$row['fond_bing'];
        $layer_info=$row['layer_info'];
	    if (!isset ($_GET['etendue'])) //***fr
			$etendue= $row['etendue'];
        $maj=$row['maj'];
        $projection_externe=$row['projection_externe'];
        $table=$row['table_update'];
        $champ=$row['champ'];
        $retour=$row['retour'];
		// récupération champ type géométrie dans om_sig_map (1ere geometrie)
		$om_sig_map=$row['om_sig_map'];
		$geometrie=$row['type_geometrie'];
		$lib_geometrie=$row['lib_geometrie'];
    }
}
if($titre=="") {
	$class = "error";
    $message = _("L'objet est invalide.");
    $f->addToMessage($class, $message);
    $f->setFlag(NULL);
    $f->display();
    die();
}
$nb_imp = 0;
$sql = "select om_sig_map_wms, ol_map, chemin as chemin, couches as couches, visibility, panier,";
$sql .= " pa_nom , pa_layer, pa_attribut, pa_encaps, pa_sql, pa_type_geometrie, sql_filter, baselayer, singletile, maxzoomlevel, ";
$sql .= " cache_type, cache_gfi_chemin, cache_gfi_couches ";
$sql .= " from ".DB_PREFIXE."om_sig_map, ".DB_PREFIXE."om_sig_wms, ".DB_PREFIXE."om_sig_map_wms ";
$sql .= " where om_sig_map.om_sig_map = om_sig_map_wms.om_sig_map ";
$sql .= " and om_sig_wms.om_sig_wms = om_sig_map_wms.om_sig_wms and om_sig_map.id='".$obj."' ";
$sql .= " order by om_sig_map_wms.ordre desc";
$res = $f -> db -> query($sql);
$ol_map = array();
$ol_chemin = array();
$ol_couches = array();
$ol_visibility = array();
$ol_om_sig_map_wms = array();
$ol_panier = array();
$ol_pa_nom = array();
$ol_pa_layer = array();
$ol_pa_attribut = array();
$ol_pa_encaps = array();
$ol_imp_titre = array();
$ol_pa_type_geometrie = array();
$ol_filter = array();
$ol_baselayer = array();
$ol_singletile = array();
$ol_maxzoomlevel = array();
$ol_cache_type = array();
$ol_cache_gfi_chemin = array();
$ol_cache_gfi_couches = array();
if (DB :: isError($res)){
	$class = "error";
    $message = _($res->getMessage()." ERREUR SQL ".$sql);
    $f->addToMessage($class, $message);
    $f->setFlag(NULL);
    $f->display();
    die();
}else{
	$i=0;
   while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
	  array_push($ol_map,$row['ol_map']);
	  array_push($ol_chemin,$row['chemin']);
	  array_push($ol_couches,$row['couches']);
	  if (isset($_SESSION['sig_'.$obj]['visibility'][$i]) && $row['baselayer'] != 'Oui')
	  	array_push($ol_visibility,$_SESSION['sig_'.$obj]['visibility'][$i]);
	  else
		array_push($ol_visibility,$row['visibility']);
	  array_push($ol_om_sig_map_wms,$row['om_sig_map_wms']);
	  array_push($ol_panier,$row['panier']);
	  array_push($ol_pa_nom,$row['pa_nom']);
	  array_push($ol_pa_layer,$row['pa_layer']);
	  array_push($ol_pa_attribut,$row['pa_attribut']);
	  array_push($ol_pa_encaps,$row['pa_encaps']);
	  $ol_imp_titre_tmp="";
	  if ($row['cache_type']=="IMP" && $row['pa_sql']!="") {
		$resImp = $f -> db -> query(str_replace("&user",$_SESSION['login'],str_replace("²",'"',str_replace("&idx",$idx,str_replace("&DB_PREFIXE",DB_PREFIXE,$row['pa_sql'])))));
		if (DB :: isError($resImp)){
			$class = "error";
			$message = _($resImp->getMessage()." ERREUR SQL ".str_replace("²",'"',str_replace("&idx",$idx,str_replace("&DB_PREFIXE",DB_PREFIXE,$row['pa_sql']))));
			$f->addToMessage($class, $message);
			$f->setFlag(NULL);
			$f->display();
			die();
		}else{
			while ($rowImp=& $resImp->fetchRow(DB_FETCHMODE_ASSOC)){
				$ol_imp_titre_tmp=$rowImp['titre'];
			}
		}		
	  }
	  array_push($ol_imp_titre,$ol_imp_titre_tmp);
	  array_push($ol_pa_type_geometrie,$row['pa_type_geometrie']);
	  $ol_filter_tmp="";
	  if ($row['sql_filter']!="") {
		$resBuffer = $f -> db -> query(str_replace("²",'"',str_replace("&idx",$idx,str_replace("&DB_PREFIXE",DB_PREFIXE,$row['sql_filter']))));
		if (DB :: isError($resBuffer)){
			$class = "error";
			$message = _($resBuffer->getMessage()." ERREUR SQL ".str_replace("²",'"',str_replace("&idx",$idx,str_replace("&DB_PREFIXE",DB_PREFIXE,$row['sql_filter']))));
			$f->addToMessage($class, $message);
			$f->setFlag(NULL);
			$f->display();
			die();
		}else{
			while ($rowBuffer=& $resBuffer->fetchRow(DB_FETCHMODE_ASSOC)){
				$ol_filter_tmp=$rowBuffer['buffer'];
			}
		}
	  }
	  array_push($ol_filter,$ol_filter_tmp);
	  array_push($ol_baselayer,$row['baselayer']);
	  array_push($ol_singletile,$row['singletile']);
	  array_push($ol_maxzoomlevel,$row['maxzoomlevel']);
	  array_push($ol_cache_type,$row['cache_type']);
	  array_push($ol_cache_gfi_chemin,$row['cache_gfi_chemin']);
	  array_push($ol_cache_gfi_couches,$row['cache_gfi_couches']);
   	  if ($row['cache_type']=="IMP") {
		$nb_imp = $nb_imp+1;
	  }
	  $i=$i+1;
	}
}
$lst_geom_om_sig_map = array();
$lst_geom_titre = array();
$lst_geom_maj = array();
$lst_geom_type_geometrie = array();
$lst_geom_table_update = array();
$lst_geom_champ = array();
array_push($lst_geom_om_sig_map,$om_sig_map);
array_push($lst_geom_titre,$lib_geometrie);
array_push($lst_geom_maj,$maj);
array_push($lst_geom_type_geometrie,$geometrie);
array_push($lst_geom_table_update,$table);
array_push($lst_geom_champ,$champ);
// table : om_sig_map_comp  : geometrie complementaire
$sql = "select c.om_sig_map_comp as om_sig_map_comp, c.libelle as comp_lib, c.comp_maj as comp_maj, c.type_geometrie as comp_type_geometrie, c.comp_table_update as comp_table_update, c.comp_champ as comp_champ from ".DB_PREFIXE."om_sig_map_comp c,".DB_PREFIXE."om_sig_map p where p.id='".$obj."' and p.om_sig_map = c.om_sig_map and c.actif='Oui' order by c.ordre";
$res = $f -> db -> query($sql);
if (DB :: isError($res)){
	$class = "error";
    $message = _($res->getMessage()." ERREUR SQL ".$sql);
    $f->addToMessage($class, $message);
    $f->setFlag(NULL);
    $f->display();
    die();
}else{
	while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
		array_push($lst_geom_om_sig_map,$row['om_sig_map_comp']);
		array_push($lst_geom_titre,$row['comp_lib']);
		array_push($lst_geom_maj,$row['comp_maj']);
		array_push($lst_geom_type_geometrie,$row['comp_type_geometrie']);
		array_push($lst_geom_table_update,$row['comp_table_update']);
		array_push($lst_geom_champ,$row['comp_champ']);
    }
}

// ==========================================================================
// *** variables par defaut pouvant etre surchargees dans dyn/var_sig.php ***
// ==========================================================================
$http_google="http://maps.google.com/maps/api/js?v=3&amp;sensor=false";
$cle_bing ='"AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf"';
$cle_google = "";
$fichier_jsons="sig_json.php?obj=";
$fichier_wkt="sig_wkt.php";

//zoom par couche : zoom standard permettant un passage de zoom a l autre
$zoom_osm_maj=18;
$zoom_osm=14;
$zoom_sat_maj=8;
$zoom_sat=4;
$zoom_bing_maj=8;
$zoom_bing=4;

//popup data contenuHTML
$width_popup=200; 
$cadre_popup=1;
$couleurcadre_popup="black";
$fontsize_popup=12;
$couleurtitre_popup="black";
$weightitre_popup="bold";
$fond_popup="white";
$opacity_popup="0.7";

// image localisation maj ou consultation
$img_maj="../img/punaise_sig.png";
$img_maj_hover="../img/punaise_hover.png";
$img_consult="../img/punaise_point.png";
$img_consult_hover="../img/punaise_point_hover.png";
$img_w=14;
$img_h=32;
$img_click="1.3";// multiplicateur hauteur et largeur image cliquee

//style bouton 
$style_panier='border:0px;background-image:url(../img/panier.png);background-repeat:no-repeat;background-position:1% 1%;width:55px;height:35px;background-color:#ffffff;color:#000000;font-weight:bold';
$style_retour='margin-bottom: 0px;text-indent: -9999px;background-image:url(../img/retour.png);background-repeat:no-repeat;background-position:right;width:40px;height:30px;background-color:#ffffff;color:#000000;font-weight:normal';
$style_valider='text-indent: -9999px;border:0px;background-image:url(../img/valider.png);background-repeat:no-repeat;background-position:top;width:40px;height:40px;background-color:#ffffff;color:#000000;font-weight:normal';
$style_data='text-indent: -9999px;border:0px;background-image:url(../img/data.png);background-repeat:no-repeat;background-position:top;width:40px;height:40px;background-color:#ffffff;color:#000000;font-weight:normal';
$style_deplacer='text-indent: -9999px;border:0px;background-image:url(../img/deplacer.png);background-repeat:no-repeat;background-position:top;width:40px;height:40px;background-color:#ffffff;color:#000000;font-weight:normal';
$style_dessiner='text-indent: -9999px;border:0px;background-image:url(../img/dessiner.png);background-repeat:no-repeat;background-position:top;width:40px;height:40px;background-color:#ffffff;color:#000000;font-weight:normal';
$style_enregistrer='text-indent: -9999px;border:0px;background-image:url(../img/enregistrer.png);background-repeat:no-repeat;background-position:top;width:40px;height:40px;background-color:#ffffff;color:#000000;font-weight:normal';
$style_position='text-indent: -9999px;border:0px;background-image:url(../img/position.png);background-repeat:no-repeat;background-position:top;width:40px;height:40px;background-color:#ffffff;color:#000000;font-weight:normal';
$style_imprimer='text-indent: -9999px;border:0px;background-image:url(../img/imprimer.png);background-repeat:no-repeat;background-position:top;width:40px;height:40px;background-color:#ffffff;color:#000000;font-weight:normal';
// personnalisation des variables
if (file_exists("../dyn/var_sig.inc"))
   include ("../dyn/var_sig.inc");
if (!isset ($projection_mercator)) {
	$projection_mercator='EPSG:3857';
} 
else if ($projection_mercator == '') {
	$projection_mercator='EPSG:3857';
}
// ========================================
// appel a des API pour affichage des fonds
// ========================================
if($fond_sat=="Oui")
    $f->addHTMLHeadJs(array($http_google.$cle_google));

// ==========
// *** entete
// ==========
// initialisation variable php indiquant l'ouverture de fenètre en mode popup
if(isset($_GET["popup"]) and $_GET["popup"]==1){
    $f->setFlag("htmlonly_nodoctype");
	$ouv_popup=$_GET["popup"];
} else {
    $f->setFlag("nodoctype");
	$ouv_popup="0";
}
$f->display();
// ===========================
// *** Etat de l interface ***
// ===========================
$bl="&nbsp;&nbsp;&nbsp;&nbsp;";
$msg_dessin=$bl._("Dessiner").$bl;
$msg_valider=$bl._("Enregistrer").$bl;
$msg_data=$bl._("Data").$bl;
$msg_deplacer=$bl."Deplacer".$bl; 
$msg_panier=$bl."Panier".$bl; 
echo "<form name=f1sig method=GET>";
echo "<font id='titre-id' class='ui-corner-all'>".$obj."&nbsp;".$idx."</font>";
echo "<input type='hidden' name='seli' value='".$seli."' >";
if (count($lst_geom_maj) <= $seli) {;
	$class = "error";
    $message = _("le champ sélectionné n'est pas déclaré dans om_sig_map_comp");
    $f->addToMessage($class, $message);
    $f->setFlag(NULL);
    $f->display();
    die();
}
echo "<input type='hidden' name='selmaj' value='".$lst_geom_maj[$seli]."' >";
echo "<input type='hidden' name='selgeom' value='".$lst_geom_type_geometrie[$seli]."' >";
echo "<input type='hidden' name='seltable' value='".$lst_geom_table_update[$seli]."' >";
echo "<input type='hidden' name='selchamp' value='".$lst_geom_champ[$seli]."' >";
$panier_exist="non";
for($i=0; $i<count($ol_map); $i++) {
	if (($ol_panier[$i]=="Oui")&&($ol_pa_type_geometrie[$i]==$lst_geom_type_geometrie[$seli])) {
		$panier_exist="oui";
	}
}
echo "<input type='hidden' name='panier_sel' value='' >";
echo "<input type='hidden' name='panier_val' value='' >";
echo $bl;
if (count($lst_geom_titre)>1) {
	echo "<select name='selcomp' size='1' class='champFormulaire' onChange='onChangeSelComp();'>";
	for($i=0; $i<count($lst_geom_titre); $i++) {
		if ($i==$seli) {
			echo "<option SELECTED value='".$i."¤".$lst_geom_maj[$i]."¤".$lst_geom_type_geometrie[$i]."¤".$lst_geom_table_update[$i]."¤".$lst_geom_champ[$i]."'>".$lst_geom_titre[$i]."</option>";
			$table=$lst_geom_table_update[$i];
			$champ=$lst_geom_champ[$i];
			$geometrie=$lst_geom_type_geometrie[$i];
			$lib_geometrie=$lst_geom_titre[$i];
			$maj=$lst_geom_maj[$i];
		}else{
			echo "<option value='".$i."¤".$lst_geom_maj[$i]."¤".$lst_geom_type_geometrie[$i]."¤".$lst_geom_table_update[$i]."¤".$lst_geom_champ[$i]."'>".$lst_geom_titre[$i]."</option>";
		}
	}
	echo "</select>$bl";
}
if ($maj == 'Oui') {
	echo "<input type='button' name='dessiner' class='option' id='1' value='Dessiner'  onclick='msg(\"$msg_dessin\");panneau_controle(this);'  style='".$style_dessiner."'/>";
	echo "<input type='button' name='deplacer' class='option' id='2' value='Deplacer' onclick='msg(\"$msg_deplacer\");panneau_controle(this);'  style='".$style_deplacer."'/>";
	echo "<input type='button' name='enregistrer' class='option' id='3' value='Enregistrer' onclick='msg(\"$msg_valider\");panneau_controle(this);'  style='".$style_enregistrer."' />";
}else{
	echo "<input type='button' disabled='disabled' name='dessiner' class='option' id='1' value='Dessiner'  onclick='msg(\"$msg_dessin\");panneau_controle(this);'  style='".$style_dessiner."'>";
	echo "<input type='button' disabled='disabled' name='deplacer' class='option' id='2' value='Deplacer' onclick='msg(\"$msg_deplacer\");panneau_controle(this);'  style='".$style_deplacer."'/>";
	echo "<input type='button' disabled='disabled' name='enregistrer' class='option' id='3' value='Enregistrer' onclick='msg(\"$msg_valider\");panneau_controle(this);'  style='".$style_enregistrer."' />";
}

// layer info : bouton data
if($layer_info=="Oui"){
   echo "<input type='button'  class='option' id='4' value='Data' onclick='msg(\"$msg_data\");panneau_controle(this);'   style='".$style_data."'/>";
}


// ajout des boutons d'interface du panier
if ($maj == 'Oui') {
	if ($panier_exist == "oui") {
		for($i=0; $i<count($ol_map); $i++) {
			if (($ol_panier[$i]=="Oui")&&($ol_pa_type_geometrie[$i]==$lst_geom_type_geometrie[$seli])) {
				echo "<input type='button' name='bpanier_'".$i." class='option' id='5' value='".$ol_pa_nom[$i]."' onclick='choisirpanier(\"$i\",this)'  style='".$style_panier."'/>";
			}
		}
		echo "<input type='button' name='bpanier_recup class='option' id='6' value='récupérer' onclick='recuppanier(\"$i\",this)'  style='".$style_valider."'/>";
	}
}
// ajout du bouton d'interface de la géolocalisation
echo "<input type='button' name='locale' class='option' id='6' value='localisation' onclick='locate()'  style='".$style_position."'/>";
// ajout des boutons d'interface du panier
echo "&nbsp;&nbsp;<font id='indication'>".$msg_data."</font>";
// traitement de variable popup en URL
if(isset($_GET["popup"]) and $_GET["popup"]==1){
    echo "<input type='button'  class='option' id='4' value='"._("Fermer")."' onclick='window.close()' style='".$style_retour."'/>"; // fond
} else {
	// ajout l'idx dans l'url de retour  (mode non popup)
	$retour=str_replace("&idx=","&idx=".$idx,$retour);
	// retour
    echo "<input type='button'  class='option' id='4' value='"._("Retour")."' onclick='window.location.href=\"".$retour."\"'  style='".$style_retour."'/>"; // fond
}
if ($nb_imp>0) {
	echo "<select name='selimp' size='1' class='champFormulaire' onChange='onChangeSelImp();'>";
	$j=0;
	echo "<option value=-1></option>";
	for($i=0; $i<count($ol_map); $i++) {
		if ($ol_cache_type[$i]=="IMP") {
			echo "<option value=".$i.">".$ol_map[$i]."</option>";
			if ($ol_pa_sql[$i]!="") {
				$resImpTitre = $f -> db -> query(str_replace("²",'"',str_replace("&idx",$idx,str_replace("&DB_PREFIXE",DB_PREFIXE,$ol_pa_sql[$i]))));
				
			}
		}
	}
	echo "</select>$bl";
	echo "<input type='button  class='option' id='5' value='"._("imprimer")."' onclick='imprimer()'  style='".$style_imprimer."'/>";
}
if (file_exists("../dyn/tab_sig_barre.inc.php"))
   include ("../dyn/tab_sig_barre.inc.php");

echo "</form>";
// map
echo "<div id='map-id'></div>";
// ==================================================
// script js - interactivite - gestion des evenements
// ==================================================
?>
<style>
#map-id {position:relative;background-color:#ffffff;color:#000000;border:1px solid black}
#titre-id {border:12px solid #0B2042;background-color:#0B2042;font-weight:bold;color:#ffffff;font-size:15px;}
#indication{font-size:24px;background-image:url(../img/suivi.png);background-repeat:no-repeat;background-position:1% 1%;height:50px;background-color:transparent;color:darkgray;font-weight:bold};
table{color:#000000;border:0px solid black;font-size:12px;text-align:left}
tr{text-align:left;background-color:gray;color:#ffffff;border:1px solid #ffffff;font-size:12px;width:100%}
td{background-color:#ffffff;color:#000000;border:1px solid black;font-size:12px;width:50%;}

</style>
<script type='text/javascript'>
// initialisation variable js indiquant l'ouverture de fenètre en mode popup
var ouv_popup=<?php echo $ouv_popup;?>;
// calcul de la hauteur de la carte
var $window = $(window).on('resize', function(){
		var position = $("#map-id").position();
		if (ouv_popup=="1")
			$('#map-id').height($( window ).height()- position.top-2	);
		else
			$('#map-id').height($( window ).height()- position.top-50);
    }).trigger('resize');
// tableau des references des lambertsud (27563) et lambert93 (2154)
// les references geographic et mercator sont geres par openLayers
Proj4js.defs["EPSG:27563"] = "+proj=lcc +lat_1=44.10000000000001 +lat_0=44.10000000000001 +lon_0=0 +k_0=0.999877499 +x_0=600000 +y_0=200000 +a=6378249.2 +b=6356515 +towgs84=-168,-60,320,0,0,0,0 +pm=paris +units=m +no_defs";
Proj4js.defs["EPSG:2154"] = "+proj=lcc +lat_1=49 +lat_2=44 +lat_0=46.5 +lon_0=3 +x_0=700000 +y_0=6600000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs";
Proj4js.defs["EPSG:3857"] = "+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +no_defs";
// variables globales
var pfenetre;
var fenetreouverte = false;
// correction conflit vector (géométrie courante) vectors (tableau des représentation) - déclaration
var vector;
// déclaration variable couche pour center
var vector_center;
// déclaration variable baseLayer sélectionnée par l'utilisateur
var baseLayerSelected="";
var s_test='#<?php echo  $s_zoom;?>#';
if (s_test=='##') {
	var zoomSelected=<?php echo  $zoom;?>;
	var zoom=<?php echo  $zoom;?>;
	var s_base='';
} else {
	var s_base=encodeURIComponent('<?php echo  $s_base;?>');
	var s_zoom=parseInt('<?php echo $s_zoom;?>');
	var zoomSelected=s_zoom;
	var zoom=s_zoom;	
}
var map, selectedFeature;
var geographic = new OpenLayers.Projection("EPSG:4326");
var projection_externe = new OpenLayers.Projection("<?php echo  $projection_externe;?>");
var projection_mercator = "<?php echo  $projection_mercator;?>";
var mercator = new OpenLayers.Projection(projection_mercator); // projection interne
// initialisation variable js indiquant l'ouverture de fenètre en mode popup
var msg_dessin="<?php echo  $msg_dessin;?>";
var msg_valider="<?php echo  $msg_valider;?>";
var msg_data="<?php echo  $msg_data;?>";
var msg_deplacer="<?php echo  $msg_deplacer;?>";
var msg_panier="<?php echo  $msg_panier;?>";
// variables pour traitement des clics
var feature;
var selfeature = false;
var popupClose = false;
// champ table pour la mise a jour des data geom
var table="<?php echo  $table;?>";
var champ="<?php echo  $champ;?>";
//variables get
var idx = OpenLayers.Util.getParameters().idx; // idx no enregistrement selectionne
var obj = OpenLayers.Util.getParameters().obj; // objet openMairie
// baseLayer passée en paramètre

var coucheBase=encodeURIComponent(OpenLayers.Util.getParameters().coucheBase);
if (coucheBase=='undefined' || coucheBase=='') coucheBase=s_base;
// fonds affiche
var fond_sat="<?php echo  $fond_sat;?>";
var fond_osm="<?php echo  $fond_osm;?>";
var fond_bing="<?php echo  $fond_bing;?>";
var layer_info="<?php echo  $layer_info;?>";
// zoom par couche
var zoom_osm=<?php echo  $zoom_osm;?>;
var zoom_osm_maj=<?php echo  $zoom_osm_maj;?>;
var zoom_bing=<?php echo  $zoom_bing;?>;
var zoom_bing_maj=<?php echo  $zoom_bing_maj;?>;
var zoom_sat=<?php echo  $zoom_sat;?>;
var zoom_sat_maj=<?php echo  $zoom_sat_maj;?>;
// récupération de la clé Bing
var cle_bing=<?php echo $cle_bing;?>;
// maj
var maj="<?php echo  $maj;?>";
// var fichiers
// passage de l'id de l'objet en paramètre du fichier json
var fichier_jsons="<?php echo  $fichier_jsons.$obj."&idx=".$idx;?>"; // ***
var fichier_wkt="<?php echo  $fichier_wkt;?>";
// popup data
var width_popup=<?php echo  $width_popup;?>;
var fontsize_popup=<?php echo  $fontsize_popup;?>;
var weightitre_popup="<?php echo $weightitre_popup;?>";
var couleurtitre_popup="<?php echo $couleurtitre_popup;?>";
var fond_popup="<?php echo $fond_popup;?>";
var cadre_popup=<?php echo $cadre_popup;?>;
var couleurcadre_popup="<?php echo $couleurcadre_popup;?>";
var opacity_popup=<?php echo $opacity_popup;?>;// ne marche pas
// image localisation
var img_maj="<?php echo $img_maj;?>";
var img_maj_hover="<?php echo $img_maj_hover;?>";
var img_consult="<?php echo $img_consult;?>";
var img_consult_hover="<?php echo $img_consult_hover;?>";
var img_w=<?php echo $img_w;?>;
var img_h=<?php echo $img_h;?>;
var img_click=<?php echo $img_click;?>;
var img_w_c=<?php echo $img_w;?>*img_click;
var img_h_c=<?php echo $img_h;?>*img_click;
// extension de la carte
var etendue = new OpenLayers.Bounds(<?php echo  $etendue;?>).transform(geographic, mercator);
// geometry
var geometrie="<?php echo $geometrie;?>";
// ouverture de popup -> utile pour form_sig.php pour maj en formulaire f1 ou f2
var ouv_popup="<?php echo $ouv_popup;?>";
OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3; 
map = new OpenLayers.Map(
	'map-id',
	{
		projection: mercator, units: "m", restrictedExtent: etendue, maxZoomLevel:"auto",
		controls: [
			new OpenLayers.Control.ScaleLine({'bottomOutUnits':''}),
			new OpenLayers.Control.PanZoomBar(),
			new OpenLayers.Control.Navigation(),
			new OpenLayers.Control.OverviewMap({maximized: true}),
			new OpenLayers.Control.KeyboardDefaults(),
			new OpenLayers.Control.MousePosition(),
			new OpenLayers.Control.ZoomIn(),
			new OpenLayers.Control.LayerSwitcher({'ascending':false})
		]
		//,eventListeners: {"changelayer": mapBaseLayerChanged, "moveend": mapChangeZoom}
	}
 );

// fond osm 
if(fond_osm=="Oui"){
   var osm = new OpenLayers.Layer.OSM("OpenStreetMap");
   map.addLayer(osm);
   if(coucheBase==encodeURIComponent(osm.name))
    map.setBaseLayer(osm);
   // mapquest
   var mapquest = new OpenLayers.Layer.OSM("MapQuest", "http://otile1.mqcdn.com/tiles/1.0.0/osm/${z}/${x}/${y}.png"); 
   map.addLayer(mapquest);
   if(coucheBase==encodeURIComponent(mapquest.name)){
        map.setBaseLayer(mapquest);
   }
   // mapquest_sat 
   var mapquest_sat = new OpenLayers.Layer.OSM("MapQuest sat", "http://oatile1.mqcdn.com/naip/${z}/${x}/${y}.png");
   map.addLayer(mapquest_sat);
   if(coucheBase=='mapquest_sat'){
      map.setBaseLayer(mapquest_sat);
   }
}
// fond google 
if(fond_sat=="Oui"){ 
   var sat = new OpenLayers.Layer.Google(
				"Google Hybrid",
				{type: google.maps.MapTypeId.HYBRID, sphericalMercator: true, numZoomLevels: 30}
			);
	 map.addLayer(sat);
   if(coucheBase==encodeURIComponent(sat.name)){
	  map.setBaseLayer(sat);
   }
   	var gSat = new OpenLayers.Layer.Google(
				"Google Satellite",
				{type: google.maps.MapTypeId.SATELLITE, sphericalMercator: true, numZoomLevels: 30}
			);
	map.addLayer(gSat);
	if(coucheBase==encodeURIComponent(gSat.name)){
		map.setBaseLayer(gSat);
	}
	var gStreets = new OpenLayers.Layer.Google(
				"Google Hybrid",
				{type: google.maps.MapTypeId.ROADMAP, sphericalMercator: true, numZoomLevels: 30}
			);
	map.addLayer(gStreets);
	if(coucheBase==encodeURIComponent(gStreets.name)){
		map.setBaseLayer(gStreets);
	}
}
// fond bing (microsoft)
if(fond_bing=="Oui"){ 
	var bingRoad = new OpenLayers.Layer.Bing({ key: cle_bing, type: "Road", metadataParams: { mapVersion: "v1"}, name: "Bing Road", transitionEffect: 'resize'});
	map.addLayer(bingRoad);
	if(coucheBase==encodeURIComponent(bingRoad.name)){
        map.setBaseLayer(bingRoad);
    }	
    var bingAerial = new OpenLayers.Layer.Bing({key: cle_bing, type: "Aerial", name: "Bing Aerial", transitionEffect: 'resize'});
    map.addLayer(bingAerial);
	if(coucheBase==encodeURIComponent(bingAerial.name)){
        map.setBaseLayer(bingAerial);
    }
	var bingHybrid = new OpenLayers.Layer.Bing({ key: cle_bing, type: "AerialWithLabels", name: "Bing Aerial + Labels", transitionEffect: 'resize'});
	map.addLayer(bingHybrid);
	if(coucheBase==encodeURIComponent(bingHybrid.name)){
        map.setBaseLayer(bingHybrid);
    }
}
// ajout des flux WMS qui ne sont pas considérés comme des paniers
var ol_map = <?php echo json_encode($ol_map) ?>;
var ol_chemin = <?php echo json_encode($ol_chemin) ?>;
var ol_couches = <?php echo json_encode($ol_couches) ?>;
var ol_visibility = <?php echo json_encode($ol_visibility) ?>;
var wms_maps = new Array();
var wms_info = new Array();
var ol_om_sig_map_wms = <?php echo json_encode($ol_om_sig_map_wms) ?>;
var ol_panier = <?php echo json_encode($ol_panier) ?>;
var ol_pa_nom = <?php echo json_encode($ol_pa_nom) ?>;
var ol_pa_layer = <?php echo json_encode($ol_pa_layer) ?>;
var ol_pa_attribut = <?php echo json_encode($ol_pa_attribut) ?>;
var ol_pa_encaps = <?php echo json_encode($ol_pa_encaps) ?>;
var ol_imp_titre = <?php echo json_encode($ol_imp_titre) ?>;
var panier; 
var catalogue; 
var ol_filter = <?php echo json_encode($ol_filter) ?>;
var ol_baselayer = <?php echo json_encode($ol_baselayer) ?>;
var ol_singletile = <?php echo json_encode($ol_singletile) ?>;
var ol_maxzoomlevel = <?php echo json_encode($ol_maxzoomlevel) ?>;
var ol_cache_type = <?php echo json_encode($ol_cache_type) ?>;
var ol_cache_gfi_chemin = <?php echo json_encode($ol_cache_gfi_chemin) ?>;
var ol_cache_gfi_couches = <?php echo json_encode($ol_cache_gfi_couches) ?>;
function mbtilesURL (bounds) {
	var res = this.map.getResolution();
	var x = Math.round ((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
	var y = Math.round ((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
	var z = this.map.getZoom();	
	return this.url+"/"+this.layer+"/"+z+"/"+x+"/"+y+"."+this.type;
}
for(var i=0; i<ol_map.length; i++) {
	if (ol_panier[i] == '' && ol_cache_type[i] !='IMP') {
		paramsWms = {};
		paramsWms.layers=ol_couches[i];
		paramsWms.transparent=true;
		paramsWms.srs=projection_mercator;
		if (ol_filter[i] != "") {
			paramsWms.filter=ol_filter[i];
		}
		optionsWms = {};
		if (ol_baselayer[i]=="Oui") {
			optionsWms.isBaseLayer=true;
			optionsWms.maxZoomLevel=Number(ol_maxzoomlevel[i]);				
		}else{
			optionsWms.isBaseLayer=false;
		}
		if (ol_visibility[i] == 'Oui') {
			optionsWms.visibility=true;
		}else{
			optionsWms.visibility=false;
		}			
		if (ol_singletile[i] == 'Oui' && ol_cache_type[i] != "TCF") {
			optionsWms.singleTile= true;
			optionsWms.ratio=1;
		}
		if (ol_cache_type[i] == "SMT") {
			paramsWms = {};
			paramsWms.layer=ol_couches[i];
			paramsWms.type='png';
			paramsWms.getURL= mbtilesURL;
			paramsWms.attribution="mb tiles";
			if (ol_baselayer[i]=="Oui") {
				paramsWms.isBaseLayer=true;
			}else{
				paramsWms.isBaseLayer=false;
				paramsWms.opacity=0.3;
			}
			if (ol_visibility[i] == 'Oui') {
				paramsWms.visibility=true;
			}else{
				paramsWms.visibility=false;
			}			
			paramsWms.numZoomLevels=Number(ol_maxzoomlevel[i]);
		}
		if (ol_cache_type[i] == "TCF")
			wms_maps[i] = new OpenLayers.Layer.WMS( ol_map[i],ol_chemin[i],paramsWms,optionsWms);
		else if (ol_cache_type[i] == "SMT")
			wms_maps[i] = new OpenLayers.Layer.TMS(
				ol_map[i], 
				ol_chemin[i], 
				paramsWms
			);
		else
			wms_maps[i] = new OpenLayers.Layer.WMS( ol_map[i],ol_chemin[i],paramsWms,optionsWms);
		map.addLayer(wms_maps[i]);
		if (ol_baselayer[i]=="Oui" && coucheBase==encodeURIComponent(wms_maps[i].name)) {
			map.setBaseLayer(wms_maps[i]);
		}
	}
}
// *** gestion des multi-geométries
var lst_geom_om_sig_map = <?php echo json_encode($lst_geom_om_sig_map) ?>;
var lst_geom_titre = <?php echo json_encode($lst_geom_titre) ?>;
var lst_geom_maj = <?php echo json_encode($lst_geom_maj) ?>;
var lst_geom_type_geometrie = <?php echo json_encode($lst_geom_type_geometrie) ?>;
var lst_geom_table_update = <?php echo json_encode($lst_geom_table_update) ?>;
var lst_geom_champ = <?php echo json_encode($lst_geom_champ) ?>;
var seli=<?php echo $seli;?>;
var vectors = new Array();
var controls = new Array();
for(var i=(lst_geom_om_sig_map.length-1); i>=0; i--) {
	tmp="";
	tmp=fichier_wkt+"?idx="+idx+"&obj="+obj+"&table="+lst_geom_table_update[i]+"&champ="+lst_geom_champ[i];
	vectors[i] = new OpenLayers.Layer.Vector( lst_geom_titre[i], {
		protocol: new OpenLayers.Protocol.HTTP({ url: tmp, format: new OpenLayers.Format.WKT({internalProjection:mercator,externalProjection:projection_externe})}),
		strategies: [new OpenLayers.Strategy.Fixed()],
		styleMap: new OpenLayers.StyleMap({"default": {strokeColor: "red",strokeWidth:3,strokeOpacity: 0.8,fillColor : "red", fillOpacity: 0.4, pointRadius : 5},"select": {strokeColor: "black",strokeWidth:3,strokeOpacity: 0.8,fillColor : "green", pointRadius : 5}})
	 });
	map.addLayer(vectors[i]);
}
var typ_layer;
typ_layer=OpenLayers.Handler.Point;
if (lst_geom_type_geometrie[seli]=='line') typ_layer=OpenLayers.Handler.Path;
if (lst_geom_type_geometrie[seli]=='polygon') typ_layer=OpenLayers.Handler.Polygon;
var draw, modify, select;
vector = vectors[seli];
if (lst_geom_type_geometrie.length > 1) {
	tmp="";
	tmp=fichier_wkt+"?idx="+idx+"&obj="+obj;
	vector_center = new OpenLayers.Layer.Vector('pour centrer', {
		protocol: new OpenLayers.Protocol.HTTP({ url: tmp,format: new OpenLayers.Format.WKT({internalProjection:mercator,externalProjection:projection_externe})}),
		strategies: [new OpenLayers.Strategy.Fixed()],
		styleMap: new OpenLayers.StyleMap({"default": {strokeColor: "red",strokeWidth:3,strokeOpacity: 0.8,fillColor : "red", fillOpacity: 0.4, pointRadius : 5},"select": {strokeColor: "black",strokeWidth:3,strokeOpacity: 0.8,fillColor : "green", pointRadius : 5}})
	  });
	map.addLayer(vector_center);
	vector.events.on({
		"featureselected": onSaisieSelect,
		"featureunselected": onSaisieUnselect
	});
	vector_center.events.on({
		"loadend": onLayerLoaded
	});
} else {
	vector_center = vector;
	vector.events.on({
		"featureselected": onSaisieSelect,
		"featureunselected": onSaisieUnselect,
		"loadend": onLayerLoaded
	});
}
// correction conflit vector (géométrie courante) vectors (tableau des représentation) - affectation, event, control, gestion centermap
controls = {
	draw: new OpenLayers.Control.DrawFeature(vector,typ_layer),
	select: new OpenLayers.Control.SelectFeature(vector), // select
	modify: new OpenLayers.Control.ModifyFeature(vector), // modify
	drag: new OpenLayers.Control.DragFeature(vector), // drag
}
// correction conflit vector (géométrie courante) vectors (tableau des représentation) - affectation, event, control
for(var key in controls) {
	map.addControl(controls[key]);
}
imgdata=img_maj;
img_hover= img_maj_hover;

// *** layer json
if(layer_info=="Oui"){
	 layer_json =  new OpenLayers.Layer.Vector( 'datas', {
		protocol: new OpenLayers.Protocol.HTTP({ url: fichier_jsons, format: new OpenLayers.Format.GeoJSON({internalProjection:mercator,externalProjection:projection_externe})}),
        strategies: [new OpenLayers.Strategy.Fixed()],
        styleMap: new OpenLayers.StyleMap({ "default": {externalGraphic: imgdata, graphicWidth:img_w, graphicHeight: img_h, graphicYOffset: -img_h},"select": {externalGraphic: img_hover,graphicWidth:  img_w_c, graphicHeight:  img_h_c, graphicYOffset: -img_h_c}})
	  });
     map.addLayer(layer_json);
     // evenement de selection / deselection point  pour layer_json
     select_json = new OpenLayers.Control.SelectFeature(layer_json);     
     layer_json.events.on({
             "featureselected": onFeatureSelect,
             "featureunselected": onFeatureUnselect
         });
     map.addControl(select_json);
     select_json.activate();
 }
// centrer la carte sur l etendue
// zoom forcer dans url
if(OpenLayers.Util.getParameters().zoom!="" && OpenLayers.Util.getParameters().zoom!=undefined)
   zoom = OpenLayers.Util.getParameters().zoom; // niveau de zoom
// zoom par defaut si non defini
map.setCenter(etendue.getCenterLonLat(), zoom);
// ajout de l'évènement sur clic
map.events.register('click', map, traiteclic);
map.events.register('changelayer', map, mapBaseLayerChanged);
map.events.register('moveend', map, mapChangeZoom);
addLocalisation();
mapChangeSession();
</script>