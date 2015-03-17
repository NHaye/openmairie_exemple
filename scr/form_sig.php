<?php
/**
 * Gestion du SIG
 *
 * @package openmairie_exemple
 * @version SVN : $Id: form_sig.php 2997 2014-12-11 10:23:06Z baldachino $
 */

if (file_exists ("../dyn/session.inc.php")) {
    include ("../dyn/session.inc.php");
} elseif (file_exists ("../dyn/session.inc")) {
    include ("../dyn/session.inc");
}
if (file_exists ("../obj/utils.class.php"))
    include ("../obj/utils.class.php");
$f = new utils ('htmlonly');

// GET
if (isset ($_GET['validation'])){
   $validation=$_GET['validation'];
}else{
   $validation=0;
}
if (isset ($_GET['idx'])){
   $idx=$f->db->escapeSimple($_GET['idx']);
}else{
   $idx=0;
}
if (isset ($_GET['obj'])){
   $obj=$f->db->escapeSimple($_GET['obj']);
}else{
   $obj="";
}
if (isset ($_GET['popup'])){
   $popup=$_GET['popup'];
}else{
   $popup="0";
}
if (isset ($_GET['table'])){
   $table=$f->db->escapeSimple($_GET['table']);
}else{
   $table="";
}
if (isset ($_GET['champ'])){
   $champ=$f->db->escapeSimple($_GET['champ']);
}else{
   $champ="";
}
if (isset ($_GET['coucheBase'])){
   $coucheBase=$_GET['coucheBase'];
}else{
   $coucheBase="";
}
if (isset ($_GET['zoom'])){
   $zoom=$_GET['zoom'];
}else{
   $zoom="";
}
echo "\n<div id=\"form-choice-import\" class=\"formulaire\">\n";
echo "<fieldset class=\"cadre ui-corner-all ui-widget-content\">\n"; 
echo "\t<legend class=\"ui-corner-all ui-widget-content ui-state-active\">";
echo _("objet")."&nbsp;".$obj."&nbsp;"._("enregistrement")."&nbsp;".$idx;
echo "</legend>\n"; 
if($validation==0){ // validation
    $validation=1;
    echo "<form name='f2sig' method=\"POST\" action='form_sig.php?idx=".
    $idx."&obj=".$obj."&validation=".$validation."&table=".$table."&champ=".$champ.
        "&popup=".$popup."'>";
	echo "\t<div class=\"field\">";
    echo "<select name='maj' class='champFormulaire'>";
    echo "<option value='1'>Modifier</option>";
    echo "<option value='2'>Supprimer</option>";
    echo "</select>";
    echo "&nbsp;&nbsp;<input type='submit' value='valider'>";
    echo "<textarea name='geom' cols='40' rows='3' class='champFormulaire'></textarea>";
	echo "</form>";
    echo "</div>";
}else{ // execution
?>
<script type="text/javascript">
function replaceUrlParam(url, name, value) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(url);
    if (results == null) {
        return url+"&"+name+"="+value;
    } else {
		return url.replace( results[0], "&"+name+"="+value);
    }
};
function getUrlParam(url, name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(url);
    if (results == null)
        return "";
    else {
		return results[0].replace("&"+name+"=","");
    }
};
function fermer(){
	urlParent=window.opener.location.href;
	window.opener.location.href = urlParent;
	window.close();
}
</script>
<?php
    if (isset ($_POST['maj'])){
         $maj=$_POST['maj'];
    }else{
         $maj="1";
    } 
	// récupération de la géométrie du champ pour enregistrement
    if (isset ($_POST['geom'])){
        $geom=$f->db->escapeSimple($_POST['geom']);
    } 
	// récupération de la géométrie du champ pour enregistrement
    if ($f->isAccredited($obj) || $f->isAccredited($obj."_modifier")) {
        If($maj==1){
            $sql = "select srid from geometry_columns where f_table_schema= '".OM_DB_SCHEMA."' and f_table_name = '".$table."'";
            $projection = $f -> db -> getOne($sql);
			$f->addToLog("scr/form_sig.php: db->getone(\"".$sql."\");", VERBOSE_MODE);
			$f->isDatabaseError($projection);
            // gestion multi pour polygon et linestring
            if ( strstr($geom, "POINT")) {
                $fct_deb=" =st_geometryfromtext('";
                $fct_fin=")";
            } else {
                $fct_deb=" =st_multi(st_geometryfromtext('";
                $fct_fin="))";
            }
            if(is_numeric($idx))
                $sql ="update ".DB_PREFIXE.$table." set ".$champ.$fct_deb.$geom.
                      "', ".$projection.$fct_fin." where ".$table." =".$idx;
             else
                $sql ="update ".DB_PREFIXE.$table." set ".$champ.$fct_deb.$geom.
                      "', ".$projection.$fct_fin." where ".$table." ='".$idx."'";
            $res = $f -> db -> query($sql);
			$f->addToLog("scr/form_sig.php: db->query(\"".$sql."\");", VERBOSE_MODE);
            if ($f->isDatabaseError($res)){
				echo "<br><center>";
				$f->displayLinkJsCloseWindow("fermer()");
				echo "</center><br>";
				die($res->getMessage()."erreur -  ".$sql);
            }else{
				echo "<center><br>"._("mise a jour")." "._("effectuee");
				echo "<br>";
				$f->displayLinkJsCloseWindow("fermer()");
				echo "</center><br>";
				echo "<br><br>".$sql."<br><br>";
            }
            if (file_exists("../dyn/form_sig_update.inc.php")){
                require_once "../dyn/form_sig_update.inc.php";
            }
       }else{
             if(is_numeric($idx)) 
                $sql ="update ".DB_PREFIXE.$table." set ".$champ." = Null where ".$table." =".$idx;
             else
                $sql ="update ".DB_PREFIXE.$table." set ".$champ." = Null where ".$table." ='".$idx."'";
            $res = $f -> db -> query($sql);
			$f->addToLog("scr/form_sig.php: db->query(\"".$sql."\");", VERBOSE_MODE);
            if ($f->isDatabaseError($res)){
               die($res->getMessage()."erreur -  ".$sql);
            }else{
               echo "<center><br>"._("geometrie")." "._("supprimee");
            }
			if (file_exists("../dyn/form_sig_delete.inc.php")){
			    require_once "../dyn/form_sig_delete.inc.php";
			}
			echo "<br><br><br><center>";
			$f->displayLinkJsCloseWindow("fermer()");
			echo "</center>";
        }
    } else {
        $f->displayMessage("error", _("Vous n'avez pas les permissions."));
		echo "<br><br><br><center>";
		$f->displayLinkJsCloseWindow("fermer()");
		echo "</center>";
    }
}

echo "</fieldset>\n";
?>

<script type='text/javascript'>
	// récupération de la géométrie de la variable de la fenètre appelante vers le champ
	document.f2sig.geom.value=window.opener.geomenr;
</script>


