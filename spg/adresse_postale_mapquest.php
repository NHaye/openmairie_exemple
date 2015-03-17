<?php
/**
 * GEOLOCALISATION - Ce script permet ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: adresse_postale_mapquest.php 2949 2014-11-07 18:25:20Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

//
$f->handle_if_no_localisation();

// $id
/* todo
  recherche de la projection auto et non 27563
  envoi du form
  envoi du geom
  envoi cp et ville
*/  

(isset($_GET["form"]) ? $form = $_GET["form"] : $form = "f1");
(isset($_GET["libelle_voie"]) ? $s_voie = $_GET["libelle_voie"] : $s_voie = "");
(isset($_GET["numero_voie"]) ? $s_numero = $_GET["numero_voie"] : $s_numero = "");
// variables cp ville et epsg
if (file_exists ("../dyn/var_adresse_postale.inc"))
    include ("../dyn/var_adresse_postale.inc");
// et/ou dans l url
if(isset($_GET["cp"])) $cp = $_GET["cp"];
if(isset($_GET["ville"]))  $ville = $_GET["ville"];
// utils
$f->addHTMLHeadJs(array("../lib/openlayers/OpenLayers.js",
                        "../lib/openlayers/proj4js-compressed.js"));
$f->setFlag("htmlonly");
$f->display();
$f->displayStartContent();
?>
<script type="text/javascript">
Proj4js.defs["EPSG:27563"] = "+proj=lcc +lat_1=44.10000000000001 +lat_0=44.10000000000001 +lon_0=0 +k_0=0.999877499 +x_0=600000 +y_0=200000 +a=6378249.2 +b=6356515 +towgs84=-168,-60,320,0,0,0,0 +pm=paris +units=m +no_defs";
Proj4js.defs["EPSG:2154"] = "+proj=lcc +lat_1=49 +lat_2=44 +lat_0=46.5 +lon_0=3 +x_0=700000 +y_0=6600000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs";
</script>
<?php
echo "recherche<br>";
$recherche= $s_numero." ".$s_voie." ".$cp." ".$ville;
$url  = "http://nominatim.openstreetmap.org/search?format=json&polygon=0&addressdetails=0&q="; 
$url .= urlencode($recherche);
$result = json_decode( @file_get_contents($url), true );
switch(sizeof($result)) {
  case 0 : // il n y a pas de ligne -> recherche avec des outils internet
    echo _("aucune correspondance")." ".$recherche."<br>";
    $f->displayLinkJsCloseWindow();    
    break;
  case 1 : // il y a 1 seul resultat
    echo "1&nbsp;"._("resultat")." ".$result[0]['display_name']." [".
      $result[0]['class']." ".$result[0]['type']."]"."<br>";   
    ?>
    <script type="text/javascript">
    var point = new OpenLayers.Geometry.Point(<?php echo $result[0]['lon'];?>,<?php echo $result[0]['lat'];?>);
    pointProj = new OpenLayers.Projection.transform(
                    point,
                    new OpenLayers.Projection("EPSG:4326"), 
                    new OpenLayers.Projection("<?php echo $epsg;?>") );
    opener.document.f1.geom.value =pointProj; 
    //this.close();
    </script>
    <?php
    $f->displayLinkJsCloseWindow();
    break;
  default :
    echo "\n<div class=\"instructions\">\n";
    echo "<p>\n";
    echo _("Selectionner dans la liste ci-dessous la correspondance avec ".
           "votre recherche")." ".$recherche."<br>";
    echo _("Puis valider votre choix en cliquant sur le bouton : \"Valider\".");
    echo "</p>\n";
    echo "</div>\n";
    echo "<form name=\"f3\" method=\"post\" action=\"../sig/adresse_postale_mapquest.php\">";
    echo "<select size=\"1\" name=\"adresse_postale\" class=\"champFormulaire\">\n";
    for($i=0;$i<sizeof($result);$i++){
      $opt= "<option value=\"".$result[$i]['lon']."£".$result[$i]['lat']."\">";
      $opt .= $result[$i]['display_name']." [".$result[$i]['class']." ".$result[$i]['type']."]";
      $opt .= "</option>\n";
      echo $opt;
    }
    echo "</select>\n";
    ?>
    <script type="text/javascript">
    function recup(){
      var s = ""+document.f3.adresse_postale.value;
      var x = s.split( "£" );
      var point = new OpenLayers.Geometry.Point(x[0],x[1]);
      pointProj = new OpenLayers.Projection.transform(
                      point,
                      new OpenLayers.Projection("EPSG:4326"), 
                      new OpenLayers.Projection("EPSG:2154") );
      opener.document.f1.geom.value =pointProj;
      this.close();
    }
    </script>
    <?php          
    echo "<div class=\"formControls\">\n";
    echo "<input type=\"submit\" tabindex=\"70\" value=\"".
      _("Valider")."\" onclick=\"javascript:recup();\" class=\"boutonFormulaire\" />\n";
    // echo "</div>\n";
    echo "</form>";
    break;
}
$f->displayEndContent();
?>
