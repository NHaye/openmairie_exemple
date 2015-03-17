<?php
/**
 * GEOLOCALISATION - Gestion du SIG
 *
 * Transfert du point en cours de saisie au format wkt
 *
 * @package openmairie_exemple
 * @version SVN : $Id: sig_wkt.php 2949 2014-11-07 18:25:20Z fmichon $
 */

include ("../obj/utils.class.php");
$f = new utils ('nohtml');
$f->disableLog();
if (isset ($_GET['idx'])){
   $idx=$f->db->escapeSimple($_GET['idx']);
}else{
   $idx=0;
}
if (isset ($_GET['obj'])){
   $obj=$f->db->escapeSimple($_GET['obj']);
}else{
   $obj='';
}
if (isset ($_GET['table'])){
   $table=$f->db->escapeSimple($_GET['table']);
}else{
   $table='';
}
if (isset ($_GET['champ'])){
   $champ=$f->db->escapeSimple($_GET['champ']);
}else{
   $champ='';
}
$lst_geom_table = array();
$lst_geom_champ = array();
if ($table=='') {
	$sql="select table_update,champ from ".DB_PREFIXE."om_sig_map where id='".$obj."'";
	$res = $f -> db -> query($sql);
	if (DB :: isError($res)){
		die($res->getMessage()."erreur ".$sql);
	}else{
		while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			array_push($lst_geom_table,$row['table_update']);
			array_push($lst_geom_champ,$row['champ']);
		}
	}
	$sql = "select c.comp_table_update as comp_table_update,c.comp_champ as comp_champ from ".DB_PREFIXE."om_sig_map_comp c,".DB_PREFIXE."om_sig_map p where p.id='".$obj."' and p.om_sig_map = c.om_sig_map and c.actif='Oui' order by c.ordre";
	$res = $f -> db -> query($sql);
	if (DB :: isError($res)){
		die($res->getMessage()."erreur ".$sql);
	}else{
		while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			array_push($lst_geom_table,$row['comp_table_update']);
			array_push($lst_geom_champ,$row['comp_champ']);
		}
	}
} else {
	array_push($lst_geom_table,$table);
	array_push($lst_geom_champ,$champ);
}
$wkt = 'GEOMETRYCOLLECTION(';
for($i=0; $i<count($lst_geom_table); $i++) {
	$table=$lst_geom_table[$i];
	$champ=$lst_geom_champ[$i];
	$sql = "select st_astext(".$champ.") as geom from ".DB_PREFIXE.$table." where ".$table."='".$idx."'";
	$res = $f -> db -> query($sql);
	if (DB :: isError($res)){
		die($res->getMessage()."erreur ".$sql);
	}else{
		while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			if($row["geom"]!='')
			$wkt.= $row["geom"].",";
		}
	}
}
$wkt=substr($wkt,0, strlen($wkt)-1);
$wkt.= ")";
echo $wkt;
?>