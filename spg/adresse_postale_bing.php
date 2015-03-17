<?php
/**
 * GEOLOCALISATION - Ce script permet ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: adresse_postale_bing.php 2949 2014-11-07 18:25:20Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

//
$f->handle_if_no_localisation();

// $id
(isset($_GET["libelle_voie"]) ? $s_voie = $_GET["libelle_voie"] : $s_voie = "");
(isset($_GET["numero_voie"]) ? $s_numero = $_GET["numero_voie"] : $s_numero = "");
(isset($_GET["cp"]) ? $cp = $_GET["cp"] : $cp = "");
(isset($_GET["ville"]) ? $ville = $_GET["ville"] : $ville = "");

if (file_exists ("../dyn/var_adresse_postale.inc"))
    include ("../dyn/var_adresse_postale.inc");

?>

<html>
<head>
<script src="../lib/openlayers/OpenLayers.js" type="text/javascript"></script>
<script src="../lib/openlayers/proj4js-compressed.js" type="text/javascript"></script>
<script type="text/javascript" src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.3"></script>
</script>
</head>
<body>
<script type="text/javascript">
Proj4js.defs["EPSG:27563"] = "+proj=lcc +lat_1=44.10000000000001 +lat_0=44.10000000000001 +lon_0=0 +k_0=0.999877499 +x_0=600000 +y_0=200000 +a=6378249.2 +b=6356515 +towgs84=-168,-60,320,0,0,0,0 +pm=paris +units=m +no_defs";
Proj4js.defs["EPSG:2154"] = "+proj=lcc +lat_1=49 +lat_2=44 +lat_0=46.5 +lon_0=3 +x_0=700000 +y_0=6600000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs";
 /* Déclaration des variables globales */ 
var addr, latitude, longitude;
/* Récupération du champ "adresse" */ 
addr = "<?php echo $s_numero;?>"+" "+"<?php echo $s_voie;?>"+" "+"<?php echo $cp;?>"+" "+"<?php echo $ville;?>";
/* Tentative de géocodage */ 
var map     = null;
function GetMap(){
  map = new VEMap('myMap');
  map.LoadMap();
  //map.Search(addr, findCallback);
  map.Geocode(addr, findCallback);
}

function findCallback(layer, findResults, placeResults, moreResults, error){
    var s='';
    var y='xxxxxxxxxx';
    if (placeResults != null){ 
       s += 'Where Results:\n'
       for (var i=0; i < placeResults.length; ++i){ 
       s += 'Name: ' + placeResults[i].Name + '\n'; s+= 'LatLong: ' + placeResults[i].LatLong + '\n'; s+= 'MatchCode: ' + placeResults[i].MatchCode+ '\n'; s+= 'MatchConfidence: ' + placeResults[i].MatchConfidence+ '\n'; s += '\n\n';
       }
       //alert(s);
       y=""+placeResults[0].LatLong; // met sous forme de chaine
       var x =y.split(','); 
    } 
     
        var point = new OpenLayers.Geometry.Point(x[1],x[0]);
        pointProj = new OpenLayers.Projection.transform(
                        point,
                        new OpenLayers.Projection("EPSG:4326"), 
                        new OpenLayers.Projection("<?php echo $epsg;?>") );
        
        opener.document.f1.geom.value = pointProj;
        //alert (pointProj);
        //this.close();      
}
</script>
   <body onload="GetMap();">
      <div id='myMap' style="position:relative; width:400px; height:270px;"></div>
      <?php echo $s_numero;?> <?php echo $s_voie;?><br> <?php echo $cp;?> <?php echo $ville;?><br>
   </body>
</html>
