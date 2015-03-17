<?php
/**
 * Ce script permet d'interfacer l'assistant de création des lettres type.
 * 
 * @package openmairie_exemple
 * @version SVN : $Id: genlettretype.php 2917 2014-10-09 12:52:39Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils(
    null, 
    "gen", 
    _("administration")." -> "._("generateur")." -> "._("om_lettretype")
);

/**
 * Description de la page
 */
$description = _("cet assistant vous permet de creer des lettres type ".
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
 * il souhaite créer une lettre type
 */
// On instancie l'utilitaire de génération
require_once PATH_OPENMAIRIE."om_gen.class.php";
$g = new gen();
// On récupère la liste des tables de la base de données
$tables = $g->get_all_tables_from_database();
//
echo "\n<div id=\"form-choice-import\" class=\"formulaire\">\n";
echo "<form action=\"../scr/genlettretype.php\" method=\"post\">\n";
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
    echo "<form action=\"../scr/genlettretype.php?obj=".$obj."&validation=1\" method=\"post\" name=\"f1\">\n";
    echo "<fieldset>\n";
    echo "\t<legend>"._("choix des champs")."</legend>";
    echo "Utilisez ctrl key pour choix multiple<br><br>";
    //
    $sql = "select * from ".DB_PREFIXE.$obj;
    $res2 = $f->db->query($sql);
    $f->addToLog("scr/genlettretype.php: db->query(\"".$sql."\");", VERBOSE_MODE);
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
    $variable='&'; // nouveau
    $lettretype['orientation']='P';
    $lettretype['format']='A4';
    // logo
    $lettretype['logo']='logopdf.png';
    $lettretype['logoleft']='58';
    $lettretype['logotop']='7';
    // titre
    $lettretype['titreleft']='41';
    $lettretype['titretop']='36';
    $lettretype['titrelargeur']='130';
    $lettretype['titrehauteur']='10';
    $lettretype['titrefont']='helvetica';
    $lettretype['titreattribut']='B';
    $lettretype['titretaille']='15';
    $lettretype['titrebordure']='0';
    $lettretype['titrealign']='C'; 
    // corps
    $lettretype['corpsleft']='7';
    $lettretype['corpstop']='57';
    $lettretype['corpslargeur']='195';
    $lettretype['corpshauteur']='5';
    $lettretype['corpsfont']='helvetica';
    $lettretype['corpsattribut']='';
    $lettretype['corpstaille']='10';
    $lettretype['corpsbordure']='0';
    $lettretype['corpsalign']='J';
    // Inclusion d'un éventuel fichier de paramétrage qui permet de surcharger
    // les valeurs par défaut
    if (file_exists ("../gen/dyn/lettretype.inc.php"))
        include("../gen/dyn/lettretype.inc.php");
    elseif (file_exists ("../gen/dyn/lettretype.inc"))
        include("../gen/dyn/lettretype.inc");

    $lettretype['titre']="le ".$variable."aujourdhui";
    $lettretype['corps']=$temp1;
    // id
    $lettretype['id']= $obj;
    $lettretype['libelle']= $obj." gen le ".date('d/m/Y');
    $lettretype['actif']=FALSE; // contrainte null pgsql
    // om_collectivite
    $lettretype['om_collectivite']= $_SESSION['collectivite'];
    $lettretype['om_lettretype'] = "";

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
    $lettretype['om_sql'] = $om_requete->valF[$om_requete->clePrimaire];


    /**
     * Création de la lettre type
     */
    //
    require_once "../obj/om_lettretype.class.php";
    //
    $om_lettretype = new om_lettretype("]", $f->db, NULL);
    // XXX
    $om_lettretype->deverrouille(0);
    //
    $om_lettretype->ajouter($lettretype, $f->db, NULL);

    
    //
    $f->displayMessage("ok", $obj." "._("enregistre"));

    
    echo "</fieldset>";

    //
    $f->db->commit();

}

?>
