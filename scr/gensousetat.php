<?php
/**
 * Ce script permet d'interfacer l'assistant de création des sous-états.
 * 
 * @package openmairie_exemple
 * @version SVN : $Id: gensousetat.php 2919 2014-10-09 16:50:28Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils(
    null,
    "gen",
    _("administration")." -> "._("generateur")." -> "._("om_sous_etat")
);

/**
 * Description de la page
 */
$description = _("cet assistant vous permet de creer des sous etats ".
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
if (isset($_POST['choice-cle'])){
    $cle=$_POST['choice-cle'];
}else{
    $cle='';
}

/**
 * On liste les tables pour que l'utilisateur puisse choisir sur quel table
 * il souhaite créer un sous état
 */
// On instancie l'utilitaire de génération
require_once PATH_OPENMAIRIE."om_gen.class.php";
$g = new gen();
// On récupère la liste des tables de la base de données
$tables = $g->get_all_tables_from_database();
//
echo "\n<div id=\"form-choice-import\" class=\"formulaire\">\n";
echo "<form action=\"../scr/gensousetat.php\" method=\"post\">\n";
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
    echo "<form action=\"../scr/gensousetat.php?obj=".$obj."&validation=1\" method=\"post\" name=\"f1\">\n";
    echo "<fieldset>\n";
    echo "\t<legend>"._("choix des champs")."</legend>";
    echo "<table><tr>";
    echo "Utilisez ctrl key pour choix multiple<br><br>";
    //
    $sql = "select * from ".DB_PREFIXE.$obj;
    $res2 = $f->db->query($sql);
    $f->addToLog("scr/gensousetat.php: db->query(\"".$sql."\");", VERBOSE_MODE);
    $f->isDatabaseError($res2);
    //
    $info=$res2->tableInfo();
    echo "<td><select multiple name=\"choice-field[]\" class=\"champFormulaire\">";
    foreach($info as $elem){
        echo "<option value=\"".$obj."|".$elem['name']."|".$elem['len']."|".$elem['type']."\">".$obj.".".$elem['name']."</option>";
    }
    echo "</select></td>";
    echo "<td>"._("choisir la cle de selection")."</td>";
    echo "<td><select name=\"choice-cle\" class=\"champFormulaire\">";
    foreach($info as $elem){
        echo "<option value=\"".$obj.".".$elem['name']."\">".$obj.".".$elem['name']."</option>";
    }
    echo "</select></td>";
    echo "</tr><tr>";
    echo "<td><br><br><input type=\"submit\" name=\"submit-csv-import\" value=\""._("Import")." ".
            $obj." "._("dans la base")."\" class=\"boutonFormulaire\" />";
    echo "</td></tr></table></div>";
    echo "</fieldset>";
    echo "</form>";
    echo "</div>\n";
}
/**
 *  transfert dans la base
 */
if ($obj != "" and $field!='' and $cle!='') {
    echo "\n<br>&nbsp";
    echo "<fieldset>\n";
    echo "\t<legend> Insertion dans la table sous etat</legend>";
    // sql
    $temp=''; // field
    $temp1=''; // champ requete
    $longueur=0;
    $dernierchamp=0;
    if($field!=array()){
        for ($i = 0; $i < sizeof($field); $i++) {    
            $temp=explode("|",$field[$i]);
            $table=$temp[0];
            $champ=$temp[1];
            $len[$i]=$temp[2];
            $type=$temp[3];
            $temp1.=$table.".".$champ.' as '.$champ.',';
            if($len[$i]!='')
                $len[$i]=40;
            $longueur=$longueur+$len[$i];
            $dernierchamp++;
        }
        $temp1=substr($temp1, 0, strlen($temp1)-1);
    }
    //parametres
    $longueurtableau= 195;
    $variable='&'; // nouveau
    //titre
    $sousetat['titrehauteur']=10;
    $sousetat['titrefont']='helvetica';
    $sousetat['titreattribut']='B';
    $sousetat['titretaille']=10;
    $sousetat['titrebordure']=0;
    $sousetat['titrealign']='L';
    $sousetat['titrefond']=0;
    $sousetat['titrefondcouleur']="255-255-255";
    $sousetat['titretextecouleur']="0-0-0";
    // intervalle
    $sousetat['intervalle_debut']=0;
    $sousetat['intervalle_fin']=5;
    // entete
    $sousetat['entete_flag']=1;
    $sousetat['entete_fond']=1;
    $sousetat['entete_hauteur']=7;
    $sousetat['entete_fondcouleur']="255-255-255";
    $sousetat['entete_textecouleur']="0-0-0";
    // tableau
    $sousetat['tableau_bordure']=1;
    $sousetat['tableau_fontaille']=10;
    // bordure
    $sousetat['bordure_couleur']="0-0-0";
    // sous etat fond
    $sousetat['se_fond1']="243-246-246";
    $sousetat['se_fond2']="255-255-255";
    // cellule
    $sousetat['cellule_fond']=1;
    $sousetat['cellule_hauteur']=7;
    // total
    $sousetat['cellule_fond_total']=1;
    $sousetat['cellule_fontaille_total']=10;
    $sousetat['cellule_hauteur_total']=15;
    $sousetat['cellule_fondcouleur_total']="255-255-255";
    // moyenne
    $sousetat['cellule_fond_moyenne']=1;
    $sousetat['cellule_fontaille_moyenne']=10;
    $sousetat['cellule_hauteur_moyenne']=5;
    $sousetat['cellule_fondcouleur_moyenne']="212-219-220";
    // nombre d enregistrement
    $sousetat['cellule_fond_nbr']=1;
    $sousetat['cellule_fontaille_nbr']=10;
    $sousetat['cellule_hauteur_nbr']=7;
    $sousetat['cellule_fondcouleur_nbr']="255-255-255";

    // parametre custom
    if (file_exists ("../gen/dyn/sousetat.inc.php"))
        include("../gen/dyn/sousetat.inc.php");
    elseif (file_exists ("../gen/dyn/sousetat.inc"))
        include("../gen/dyn/sousetat.inc");

    // parametres sousetat
    $sousetat['om_sql']="select ".$temp1." from &DB_PREFIXE".$obj." where ".$cle."='".$variable."idx'";
    // id
    $temp='';
    $temp=explode('.',$cle);
    $sousetat['id']= $obj.'.'.$temp[1];
    $sousetat['libelle']= "gen le ".date('d/m/Y');
    $sousetat['titre']=_("liste")." ".$obj;
    // om_collectivite
    $sousetat['om_collectivite']= $_SESSION['collectivite'];

    // parametre ************************************
    // calcul de la longueur
    echo "<br>Tableau de : ".$longueurtableau." pour ".
         $longueur." caracteres <br><br>";
    $quotient=$longueurtableau/$longueur;
    $temp1="";$temp2="";$temp3="";$temp4="";$temp5="";
    for ($i = 0; $i < sizeof($len); $i++){
        // largeur
        $temp=$len[$i]*$quotient;
        if($i==$dernierchamp-1){
            $temp1.=$temp; // largeur
            $temp2.='C'; // align
            $temp3.='LTBR';// bordure
            $temp4.='0';  // stats
            $temp5.='999'; // total
        }else{
            // separateur ."|"
            $temp1.=$temp."|"; // largeur
            $temp2.="C"."|"; // alihgn
            $temp3.="TLB"."|"; // bordure
            $temp4.="0"."|"; // stats
            $temp5.='999'."|"; // total
        }
    }
    $sousetat['tableau_largeur']=$longueurtableau;
    $sousetat['cellule_largeur']=$temp1;
    $sousetat['entetecolone_align']=$temp2;
    $sousetat['entetecolone_bordure']=$temp3;
    $sousetat['entete_orientation']=$temp4;

    $sousetat['cellule_bordure_un']=$temp3;
    $sousetat['cellule_bordure']=$temp3;
    $sousetat['cellule_align']=$temp2;

    $sousetat['cellule_bordure_total']=$temp3;
    $sousetat['cellule_align_total']=$temp2;

    $sousetat['cellule_bordure_moyenne']=$temp3;
    $sousetat['cellule_align_moyenne']=$temp2;

    $sousetat['cellule_bordure_nbr']=$temp3;
    $sousetat['cellule_align_nbr']=$temp2;
    //*
    $sousetat['cellule_numerique']=$temp5;
    $sousetat['cellule_total']=$temp4;
    $sousetat['cellule_moyenne']=$temp4;
    $sousetat['cellule_compteur']=$temp4;   
 
    $sousetat['actif']=FALSE; // contrainte null pgsql
    
    // next Id
    $sousetat['om_sousetat'] = $f->db->nextId(DB_PREFIXE.'om_sousetat');
    // Logger
    $f->addToLog("scr/gensousetat.php: db->nextId(\"".DB_PREFIXE."om_sousetat\");", VERBOSE_MODE);
    // Exécution de la requête
    $res = $f->db->autoExecute(DB_PREFIXE.'om_sousetat', $sousetat, DB_AUTOQUERY_INSERT);
    // Logger
    $f->addToLog("scr/gensousetat.php: db->autoExecute(\"".DB_PREFIXE."om_sousetat\", ".print_r($sousetat, true).", DB_AUTOQUERY_INSERT);", VERBOSE_MODE);
    // Vérification d'une éventuelle erreur de base de données
    $f->isDatabaseError($res);
    //
    echo $obj." "._("enregistre");    echo "</fieldset>";
}

?>
