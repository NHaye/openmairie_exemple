<?php
/**
 * GEOLOCALISATION - Ce script permet ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: adresse_postale_google.php 2949 2014-11-07 18:25:20Z fmichon $
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
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">
</script>
</head>
<body>
<script type="text/javascript">
Proj4js.defs["EPSG:27563"] = "+proj=lcc +lat_1=44.10000000000001 +lat_0=44.10000000000001 +lon_0=0 +k_0=0.999877499 +x_0=600000 +y_0=200000 +a=6378249.2 +b=6356515 +towgs84=-168,-60,320,0,0,0,0 +pm=paris +units=m +no_defs";
Proj4js.defs["EPSG:2154"] = "+proj=lcc +lat_1=49 +lat_2=44 +lat_0=46.5 +lon_0=3 +x_0=700000 +y_0=6600000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs";
 /* Déclaration des variables globales */ 
 var geocoder = new google.maps.Geocoder();
 var addr, latitude, longitude;
/* Récupération du champ "adresse" */ 
addr = "<?php echo $s_numero;?>"+" "+"<?php echo $s_voie;?>"+" "+"<?php echo $cp;?>"+" "+"<?php echo $ville;?>";
//alert(addr); 
/* Tentative de géocodage */ 
geocoder.geocode( { 'address': addr}, function(results, status) {
    /* Si géolocalisation réussie */ 
    if (status == google.maps.GeocoderStatus.OK) {
         /* Récupération des coordonnées */ 
        latitude = results[0].geometry.location.lat();
        longitude = results[0].geometry.location.lng();
         /* Insertion des coordonnées dans les input text */ 
        //alert(latitude);
        //alert(longitude);
        var point = new OpenLayers.Geometry.Point(longitude,latitude);
        pointProj = new OpenLayers.Projection.transform(
                        point,
                        new OpenLayers.Projection("EPSG:4326"), 
                        new OpenLayers.Projection("<?php echo $epsg;?>") );
        alert (pointProj);
        opener.document.f1.geom.value = pointProj;
        this.close();
    }
});
</script>
</body>
</html>