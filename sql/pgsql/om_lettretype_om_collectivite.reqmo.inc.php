<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$reqmo['libelle']=_('reqmo-libelle-om_lettretype');
$reqmo['reqmo_libelle']=_('reqmo-libelle-om_lettretype');
$ent=_('om_lettretype_om_collectivite');
$reqmo['sql']="select  [om_lettretype], [id], [libelle], [actif], [orientation], [format], [logo], [logoleft], [logotop], [titre_om_htmletat], [titreleft], [titretop], [titrelargeur], [titrehauteur], [titrebordure], [corps_om_htmletatex], [om_sql], [margeleft], [margetop], [margeright], [margebottom], [se_font], [se_couleurtexte] from ".DB_PREFIXE."om_lettretype where om_collectivite = '[om_collectivite]' order by [tri]";
$reqmo['om_lettretype']='checked';
$reqmo['om_collectivite']="select * from ".DB_PREFIXE."om_collectivite";
$reqmo['id']='checked';
$reqmo['libelle']='checked';
$reqmo['actif']='checked';
$reqmo['orientation']='checked';
$reqmo['format']='checked';
$reqmo['logo']='checked';
$reqmo['logoleft']='checked';
$reqmo['logotop']='checked';
$reqmo['titre_om_htmletat']='checked';
$reqmo['titreleft']='checked';
$reqmo['titretop']='checked';
$reqmo['titrelargeur']='checked';
$reqmo['titrehauteur']='checked';
$reqmo['titrebordure']='checked';
$reqmo['corps_om_htmletatex']='checked';
$reqmo['om_sql']='checked';
$reqmo['margeleft']='checked';
$reqmo['margetop']='checked';
$reqmo['margeright']='checked';
$reqmo['margebottom']='checked';
$reqmo['se_font']='checked';
$reqmo['se_couleurtexte']='checked';
$reqmo['tri']=array('om_lettretype','id','libelle','actif','orientation','format','logo','logoleft','logotop','titre_om_htmletat','titreleft','titretop','titrelargeur','titrehauteur','titrebordure','corps_om_htmletatex','om_sql','margeleft','margetop','margeright','margebottom','se_font','se_couleurtexte');
?>