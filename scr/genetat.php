<?php
/**
 * Ce script permet d'interfacer l'assistant de création des états.
 * 
 * @package openmairie_exemple
 * @version SVN : $Id: genetat.php 2917 2014-10-09 12:52:39Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils(
    null,
    "gen",
    _("administration")." -> "._("generateur")." -> "._("om_etat")
);

/**
 * Description de la page
 */
$description = _("cet assistant vous permet de creer des etats ".
                 "directement a partir de vos tables ");
$f->displayDescription($description);

/**
 *
 */
set_time_limit(3600);
$DEBUG=0;
if (isset($_POST['choice-import']) and $_POST['choice-import'] != "---") {
    $obj = $_POST['choice-import'];
} elseif(isset($_GET['obj'])) {
    $obj = $_GET['obj'];
} else {
    $obj = "";    
}
if(isset($_GET['validation'])) {
    $validation = $_GET['validation'];
} else {
    $validation = 0;    
}
if (isset($_POST['choice-field'])){
    $field=$_POST['choice-field'];
}else{
    $field='';
}

/**
 * On liste les tables pour que l'utilisateur puisse choisir sur quel table
 * il souhaite créer un état
 */
// On instancie l'utilitaire de génération
require_once PATH_OPENMAIRIE."om_gen.class.php";
$g = new gen();
// On récupère la liste des tables de la base de données
$tables = $g->get_all_tables_from_database();
//
echo "\n<div id=\"form-choice-import\" class=\"formulaire\">\n";
echo "<form action=\"../scr/genetat.php\" method=\"post\">\n";
echo "<fieldset>\n";
echo "\t<legend>"._("Choix table :")."</legend>\n";
echo "\t<div class=\"field\">";
echo "<label>"._("fichier")."</label>";
echo "<select onchange=\"submit()\" name=\"choice-import\" class=\"champFormulaire\">";
echo "<option>---</option>";
foreach ($tables as $table) {
    echo "<option value=\"".$table."\"";
    if ($obj == $table) {
        echo " selected=\"selected\" ";
    }
    echo ">".$table."</option>";
}
echo "</select>";
echo "</div>\n";
echo "</fieldset>\n";
echo "</form>\n";
echo "</div>\n";

/**
 * choix des champs
 */
if ($obj != "" and $field=='') {
    //
    echo "\n<br>&nbsp;<div id=\"form-csv-import\" class=\"formulaire\">\n";
    echo "<form action=\"../scr/genetat.php?obj=".$obj."&validation=1\" method=\"post\" name=\"f1\">\n";
    echo "<fieldset>\n";
    echo "\t<legend>"._("choix des champs")."</legend>";
    echo "Utilisez ctrl key pour choix multiple<br><br>";
    //
    $sql = "select * from ".DB_PREFIXE.$obj;
    $res2 = $f->db->query($sql);
    $f->addToLog("scr/genetat.php: db->query(\"".$sql."\");", VERBOSE_MODE);
    $f->isDatabaseError($res2);
    //
    $info=$res2->tableInfo();
    echo "<select multiple name=\"choice-field[]\" class=\"champFormulaire\">";
    foreach($info as $elem){
        echo "<option>".$obj.".".$elem['name']."</option>";
    }
    echo "</select>";
    echo "<br><br>";
    echo "<input type=\"submit\" name=\"submit-csv-import\" value=\""._("Import")." ".
            $obj." "._("dans la base")."\" class=\"boutonFormulaire\" />";
    echo "</div>";
    echo "</fieldset>";
    echo "</form>";
    echo "</div>\n";
}
/**
 *  transfert dans la base
 */
if ($obj != "" and $field!='') {

    //
    $f->db->autoCommit(false);

    //
    echo "\n<br>&nbsp";
    echo "<fieldset>\n";
    echo "\t<legend> Insertion dans la table etat</legend>";

    /**
     * Composition de la requête SQL
     */
    // sql
    $temp='';
    $temp1='';
    if($field!=array()){
        for ($i = 0; $i < sizeof($field); $i++) {    
            $temp2=explode(".",$field[$i]);
            $temp3=$temp2[1];
            $temp.=$field[$i].' as '.$temp3.',';
            $temp1.="[".$temp3.']'.chr(13).chr(10);
        }
        $temp=substr($temp, 0, strlen($temp)-1);
    }

    /**
     * Préparation des paramètres de l'état
     */
    // parametres
    $variable='&';
    $etat['orientation']='P';
    $etat['format']='A4';
    // logo
    $etat['logo']='logopdf.png';
    $etat['logoleft']='58';
    $etat['logotop']='7';
    // titre
    $etat['titreleft']='41';
    $etat['titretop']='36';
    $etat['titrelargeur']='130';
    $etat['titrehauteur']='10';
    $etat['titrefont']='helvetica';
    $etat['titreattribut']='B';
    $etat['titretaille']='15';
    $etat['titrebordure']='0';
    $etat['titrealign']='C'; 
    // corps
    $etat['corpsleft']='7';
    $etat['corpstop']='57';
    $etat['corpslargeur']='195';
    $etat['corpshauteur']='5';
    $etat['corpsfont']='helvetica';
    $etat['corpsattribut']='';
    $etat['corpstaille']='10';
    $etat['corpsbordure']='0';
    $etat['corpsalign']='J';
    // sous etat
    $etat['se_font']='helvetica';
    $etat['se_margeleft']='8';
    $etat['se_margetop']='5';
    $etat['se_margeright']='5';
    $etat['se_couleurtexte']="0-0-0";
    // Inclusion d'un éventuel fichier de paramétrage qui permet de surcharger
    // les valeurs par défaut
    if (file_exists ("../gen/dyn/etat.inc.php"))
        include("../gen/dyn/etat.inc.php");
    elseif (file_exists ("../gen/dyn/etat.inc"))
        include("../gen/dyn/etat.inc");

    $etat['titre']="le ".$variable."aujourdhui";
    $etat['corps']=$temp1;
    // id
    $etat['id']= $obj;
    $etat['libelle']= $obj." gen le ".date('d/m/Y');
    $etat['actif']= FALSE;
    $etat['sousetat']=''; // contrainte null pgsql
    // om_collectivite
    $etat['om_collectivite']= $_SESSION['collectivite'];
    $etat['om_etat']="";

    /**
     * Création de la requête
     */
    //
    require_once "../obj/om_requete.class.php";
    //
    $om_requete = new om_requete("]", $f->db, NULL);
    // XXX
    $om_requete->deverrouille(0);
    //
    $val = array(
        "om_requete" => "",
        "code" => $obj,
        "libelle" => _("Requete")." '".$obj."'",
        "description" => "",
        "requete" => "select ".$temp." from &DB_PREFIXE".$obj." where ".$obj.".".$obj."='".$variable."idx'",
        "merge_fields" => "",
    );
    //
    $om_requete->ajouter($val, $f->db, NULL);
    //
    $etat['om_sql'] = $om_requete->valF[$om_requete->clePrimaire];


    /**
     * Création de l'état
     */
    //
    require_once "../obj/om_etat.class.php";
    //
    $om_etat = new om_etat("]", $f->db, NULL);
    // XXX
    $om_etat->deverrouille(0);
    //
    $om_etat->ajouter($etat, $f->db, NULL);

    
    //
    $f->displayMessage("ok", $obj." "._("enregistre"));

    
    echo "</fieldset>";

    //
    $f->db->commit();

}

?>
