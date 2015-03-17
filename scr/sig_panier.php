<?php
/**
 * GEOLOCALISATION - Gestion du SIG
 *
 * Récupère les géométries du flux $_GET['idx'] défini dans om_sig_map_wms dont
 * la liste est donnée dans $_GET['lst']
 *
 * @package openmairie_exemple
 * @version SVN : $Id: sig_panier.php 2949 2014-11-07 18:25:20Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");
$f->disableLog();

if (isset ($_GET['idx'])){
   $idx=$f->db->escapeSimple($_GET['idx']);
}else{
   $idx=0;
}
if (isset ($_GET['lst'])){
   $lst=$f->db->escapeSimple($_GET['lst']);
   $lst = str_replace("''","'",$lst);
}else{
   $lst='';
}
$panier="";
if ($lst!='') {
	$sql = "select pa_sql from ".DB_PREFIXE."om_sig_map_wms where om_sig_map_wms='".$idx."'";
	$res = $f -> db -> query($sql);
	$pa_sql = "";
	if (DB :: isError($res)){
		die($res->getMessage()."erreur".$sql);
	}else{
		while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
			if($row["pa_sql"]!='')
				$pa_sql = $row["pa_sql"];
		}

	}
	if ($pa_sql!='') {
		$pa_sql=str_replace("&lst",$lst,$pa_sql);
		$pa_sql=str_replace("&DB_PREFIXE",DB_PREFIXE,$pa_sql);
		$res = $f -> db -> query($pa_sql);
		if (DB :: isError($res)){
			die($res->getMessage()."erreur ".$sql);
		}else{
			$panier = 'GEOMETRYCOLLECTION(';
			while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
				if($row["geom"]!='')
				$panier.= $row["geom"].",";
			}
		}
		$panier=substr($panier,0, strlen($panier)-1);
		$panier.= ")";
	}
}
echo $panier;
