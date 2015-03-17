<?php
/**
 * GEOLOCALISATION - Gestion du SIG
 *
 * Transfert données json UNIQUEMENT POUR POINT
 * FORMAT Json
 * "geometry": {
 *        "type": "Point",
 *        "coordinates": [4.61761, 43.67089]
 *        },
 *        // TEST de wkt  "POINT(784673.869964063 155480.349967809)"
 *        $x = "784673.869964063";
 *        $y = "155480.349967809"
 *
 * @package openmairie_exemple
 * @version SVN : $Id: sig_json.php 2949 2014-11-07 18:25:20Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");
$f->disableLog();

// variables
// * Attention le mode debug affiche a l ecran et ne peut pas etre pris par
// * openLayer
$debug=0;
$tab=array();
$json=array();
if (isset ($_GET['obj'])){
   $obj=$f->db->escapeSimple($_GET['obj']);
}else{
   $obj='';
}
// possibilite de restriction en sql
if (isset ($_GET['idx'])){
   $idx=$f->db->escapeSimple($_GET['idx']);
}else{
   $idx='99999';
}
// ***
// utils

// =====================================================
// construction du tableau tab sur la base des
// enregistrements de la requete
// le tableau comprend :
//     une zone titre
//     une zone description dans lequel il y a le lien
//      une zone idx
// ======================================================
$temp="select om_sql from ".DB_PREFIXE."om_sig_map where id ='".$obj."'";
$sql= $f -> db -> getOne($temp);
$sql=str_replace("&DB_PREFIXE",DB_PREFIXE,$sql);
$sql=str_replace("&idx",$idx,$sql); // restriction en sql
$temp="select url from ".DB_PREFIXE."om_sig_map where id ='".$obj."'";
$url= $f -> db -> getOne($temp);
$res = $f -> db -> query($sql);

if (database :: isError($res)){

   die($res->getMessage()."erreur ".$sql);

}else{
    while ($row=& $res->fetchRow(DB_FETCHMODE_ASSOC)){
        if($row["geom"]!='' or $row["geom"]!=null){
            array_push($tab,
                        array('geom' => $row['geom'],
                            'titre' => $row['titre'],
                            'description' =>
                 "<font style='text-align: left; font-size: small;'>".
                                 "<a href=javascript:ouvre_popup('".
                                 $url.$row['idx']."')>".
                                 $row['description']."</a></font>",
                            'idx' => $row['idx'],
                            'delete' => 0
                        )
                    );
 
        }
   }
}

// ================================================================================
// traitement des doublons sur un meme point
// ce traitement permet de mettre l'ensemble des enregistrements sur un meme point
// la variable delete permet d eliminer les doublons
// ================================================================================

$archive=0;
$archive_i=0;

for($i=0;$i < sizeof($tab);$i++){
   if($i<>sizeof($tab)-1){ // ne pas traiter le dernier point (pb i+2)
      if($tab[$i]['geom']==$tab[$i+1]['geom']){
     if($archive==0){
          $archive_i=$i;
          $archive=1;
     }
     $tab[$archive_i]['description'].= '<br>'.$tab[$i+1]['description'];
     $tab[$i+1]['delete']=1;
      }else
     $archive=0;
   }
}

// ===========================================
// construction du tableau json en format json
// ===========================================

// entete
$cpt=0;
$json="";

// corps
for($i=0;$i < sizeof($tab);$i++){
    if($tab[$i]['delete']==0){ // elimine les doublons dans le tableau
		$cpt=$cpt+1;
        $xy=$row['geom'];
        $xy=substr($tab[$i]['geom'],6,strlen($tab[$i]['geom'])-7);
        $xy=explode(' ',$xy);
        $x=$xy[0];
        $y=$xy[1];
        $json.= '{ "type": "Feature",'."\n";
        $json.= '"geometry":';
        $json.= "{\"type\": \"Point\",";
        $json.= "\"coordinates\": [".$x.", ".$y."]},";
        $json.="\n";
        $json.= '"properties": {'."\n";
        $json.= '"titre": "'.$tab[$i]['titre'].'"'.",\n";
        $json.= '"description": "'.$tab[$i]['description'].'"'."\n,";
        $json.= '"idx": "'.$tab[$i]['idx'].'"'."\n"; // derniere champ (sans virgule)
        $json.= "}\n";
        $json.= "},\n";
    }
}
// enpied
if($cpt>0) {
	$json= '  "features": ['."\n".$json;
	$json = '{ "type": "FeatureCollection",'."\n".$json;
	$json=substr($json,0, strlen($json)-2);
	$json.= "]"."\n";
	$json.="}";
}

// debug
if($debug==1){
    $json=str_replace("\n","<br>",$json);
}

// envoi du tableau dans la couche json d'openlayer
echo $json;
?>