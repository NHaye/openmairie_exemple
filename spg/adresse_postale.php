<?php
/**
 * GEOLOCALISATION - Ce script permet ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: adresse_postale.php 2949 2014-11-07 18:25:20Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("nohtml");

//
$f->handle_if_no_localisation();

/**
 * Affichage de la structure HTML
 */
$f->setFlag("htmlonly");
$f->display();
$f->displayStartContent();
/**
 * Parametres
 */
$DEBUG = 0;
$nbligne = 0;
// nom du formulaire pere
(isset($_GET["form"]) ? $form = $_GET["form"] : $form = "f1");
// valeur par defaut (cp / ville) + epsg + description form + description fichier interne
if (file_exists ("../dyn/var_adresse_postale.inc"))
    include ("../dyn/var_adresse_postale.inc");
// recuperer les valeurs du champ formulaire dans le get
(isset($_GET["libelle_voie"]) ? $s_voie = $_GET["libelle_voie"] : $s_voie = "");
(isset($_GET["numero_voie"]) ? $s_numero = $_GET["numero_voie"] : $s_numero = "");
(isset($_GET["cp"]) ? $s_cp = $_GET["cp"] : $s_cp = "");
(isset($_GET["ville"]) ? $s_ville = $_GET["ville"] : $s_ville = "");
(isset($_GET["insee"]) ? $s_insee = $_GET["insee"] : $s_insee = "");
// form f3
echo "<form name=\"f3\" method=\"post\" action=\"../spg/adresse_postale.php\">";
if (strlen($s_voie) > $longueurRecherche) {
    if($adresse_interne=="Oui"){
        /**
         * Construction de la requete DB_PREFIXE ****
         */
		$s_voie=str_replace("\'","''",$s_voie);
        $sql = "select ".$t_voie." as t_voie,"
                        .$t_adresse." as t_adresse,"
                        .$t_numero." as t_numero,"
                        .$t_complement." as t_complement,"
                        .$t_geom." as t_geom "
                        ."from ".DB_PREFIXE.$t." where "
                        .$t_adresse." like '%".$s_voie."%'";
        if($s_numero!='')
            $sql .= " and ".$t_numero." = ".$s_numero."";
        if($s_cp!='')
            $sql .= " and ".$t_cp." = '".$s_cp."'";
        if($s_ville!='')
            $sql .= " and ".$t_ville." = '".$s_ville."'";
        if($s_insee!='')
            $sql .= " and ".$t_insee." = '".$s_insee."'";        
        $sql.= " order by t_voie,t_adresse,t_numero";
		
        if ($DEBUG == 1) {
            echo $sql;
        }    
        /**
         * Execution de la requete
         */
        if($db_externe=="Oui"){
            $db_externe=& DB :: connect($dsn_externe, $db_option_externe);
            $res = $db_externe->query($sql);
        }else{
            // base interne -> connexion par util.class
            $res = $f->db->query($sql);
            $f->isDatabaseError($res);
        }
        // ***
        $nbligne = $res->numrows();
        switch($nbligne) {
           case 0 : // il n y a pas de ligne 
                //
                echo _("Votre saisie ne donne aucune correspondance");
                break;
            case 1 : // il y a une ligne
                //
                while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // Envoi des donnees dans le formulaire de la fenetre parent
                    echo "\n<script type=\"text/javascript\">\n";
                    echo "opener.document.".$form.".".$f_voie.".value = \"".$row["t_voie"]."\";\n";
                    echo "opener.document.".$form.".".$f_libelle.".value = \"".$row["t_adresse"]."\";\n";
                    if($s_numero=='')
                        echo "opener.document.".$form.".".$f_numero.".value = \"".$row["t_numero"]."\";\n";
                    echo "opener.document.".$form.".".$f_complement.".value = \"".$row["t_complement"]."\";\n";
                    echo "opener.document.".$form.".".$f_geom.".value = \"".$row["t_geom"]."\";\n";
                    echo "this.close();\n";
                    echo "</script>\n";
                }
                break;
            default :
                echo "\n<div class=\"instructions\">\n";
                echo "<p>\n";
                echo _("Selectionner dans la liste ci-dessous la correspondance avec ".
                       "votre recherche")." ".$s_numero." ".$s_voie.". ";
                echo _("Puis valider votre choix en cliquant sur le bouton : \"Valider\".");
                echo "</p>\n";
                echo "</div>\n";
                //
                echo "<select size=\"1\" name=\"adresse_postale\" class=\"champFormulaire\">\n";
                while ($row =& $res->fetchRow(DB_FETCHMODE_ASSOC)) {
                    $opt= "<option value=\"".$row["t_voie"]."£".$row["t_numero"]."£".$row["t_adresse"].
                            "£".$row["t_geom"]."£".$row["t_complement"]."\">";
                    $opt .= $row["t_numero"]." ".$row["t_complement"]." ".$row["t_adresse"];
                    $opt .= "</option>\n";
                    //
                    echo $opt;
                }
                echo "</select>\n";
                // Envoi des donnees dans le formulaire de la fenetre parent
                echo "\n<script type=\"text/javascript\">\n";
                echo "function recup()\n{\n";
                echo "var s = document.f3.adresse_postale.value;\n";
                echo "var x = s.split( \"£\" );\n";
                echo "opener.document.".$form.".".$f_voie.".value = x[0];\n";
                if($s_numero=='')
                    echo "opener.document.".$form.".".$f_numero.".value = x[1];\n";
                echo "opener.document.".$form.".".$f_libelle.".value = x[2];\n";
                echo "opener.document.".$form.".".$f_geom.".value = x[3];\n";
                echo "opener.document.".$form.".".$f_complement.".value = x[4];\n";
                echo "this.close();\n}\n";
                echo "</script>\n";
                //           
                echo "<div class=\"formControls\">\n";
                echo "<input type=\"submit\" tabindex=\"70\" value=\""._("Valider")."\" onclick=\"javascript:recup();\" class=\"boutonFormulaire\" />\n";                
                break;
        }
    if ($nbligne < 1) {
        echo "<div class=\"formControls\">\n";
    }
    echo "</div>\n";
    echo "</form>";
    $f->displayEndContent();
}//else{ // pas de recherche interne
    $f->displayStartContent();
    echo "\n<div class=\"instructions\">\n";
    If($osm=="Oui"){
        echo "<a href=\"./adresse_postale_mapquest.php?libelle_voie=".
               $s_voie."&numero_voie=".$s_numero.
               "&cp=".$s_cp."&ville=".$s_ville."&form=".$form."\"><span class=\"om-icon om-icon-sig om-icon-fix mapquest\" title=\""._("mapquest")."\">"._("Mapquest")."</span>".
               "</a>";
    }
    If($google=="Oui"){
        echo "<a href=\"./adresse_postale_google.php?libelle_voie=".
               $s_voie."&numero_voie=".$s_numero.
               "&cp=".$s_cp."&ville=".$s_ville."&form=".$form."\"><span class=\"om-icon om-icon-sig om-icon-fix google\" title=\""._("google")."\">"._("Google")."</span>".
               "</a>";
    }
    If($bing=="Oui"){    
        echo "<a href=\"./adresse_postale_bing.php?libelle_voie=".
               $s_voie."&numero_voie=".$s_numero.
               "&cp=".$s_cp."&ville=".$s_ville."&form=".$form."\"><span class=\"om-icon om-icon-sig om-icon-fix bing\" title=\""._("Bing")."\">"._("Bing")."</span>".
               "</a>";
    }
    echo "</div>\n";
    $f->displayEndContent();
} else {
    $message = _("Vous devez saisir une valeur d'au moins");
    $message .= " ".($longueurRecherche+1)." ";
    $message .= _("caracteres dans le champ");
    $message .= " ".$f_libelle.".";
    $f->displayMessage("error", $message);
    
}
$f->displayLinkJsCloseWindow();
?>
