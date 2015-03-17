<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_sig_wms.inc.php 2726 2014-03-07 08:59:25Z fmichon $
 */

//
include "../gen/sql/pgsql/om_sig_wms.inc.php";

//-- AB-DEB 2012-05-11 - affichage, tri
$table=DB_PREFIXE."om_sig_wms inner join ".DB_PREFIXE.
        "om_collectivite on om_collectivite.om_collectivite = om_sig_wms.om_collectivite";
$champAffiche=array('om_sig_wms',
                    'om_sig_wms.libelle',
                    'id',
                    "(om_collectivite.libelle||' ('||om_collectivite.niveau||')') as collectivite",
					" CASE WHEN cache_type = 'IMP' THEN 'Impression' WHEN cache_type = 'TCF' THEN 'flux tilecache' WHEN cache_type = 'SMT' THEN 'Slippy Map Tiles' ELSE 'WMS' END as type"
					);
$tri=' order by om_sig_wms.id';//.libelle';
//-- AB-FIN 2012-05-11 - affichage, lien sig, tri

?>
