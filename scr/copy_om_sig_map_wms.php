<?php
/**
 * GEOLOCALISATION - Ce script permet ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id$
 */

require_once "../obj/utils.class.php";
$f = new utils(null, "om_sig_map_wms", _("copie"));

// Vérification de l'activation de l'option localisation
$f->handle_if_no_localisation();

// On affiche la description du traitement
$description = _("Ce traitement permet de copier un flux vers l'ensemble des cartes.");
$f->displayDescription($description);

// Ce traitement peut durer longtemps, on fixe donc sa durée maximale à 3 minutes
set_time_limit(180);

// Si le paramètre n'est pas défini ou que ce n'est pas un entier
if (!isset($_GET["idx"]) || !is_numeric($_GET["idx"])) {
    // On affiche un message d'erreur
    $class = "error";
    $message = _("L'objet est invalide.");
    $f->displayMessage($class, $message);
    // On interrompt le script
    die();
}
// On prend la valeur entière du paramètre
$idx = intval($_GET["idx"]);

// Requête d'insertion d'un flux dans toutes les cartes à partir du flux
// de la carte dont l'identifiant est passé en paramètre
$sql = "
    INSERT INTO ".DB_PREFIXE."om_sig_map_wms 
        (om_sig_map_wms, om_sig_wms, om_sig_map, ol_map, ordre, visibility,
         panier, pa_nom, pa_layer, pa_attribut, pa_encaps, pa_sql, 
         pa_type_geometrie, sql_filter, baselayer, singletile, maxzoomlevel)
    SELECT 
        nextval('".DB_PREFIXE."om_sig_map_wms_seq'), ori.om_sig_wms, 
        map.om_sig_map, ori.ol_map, 
        CASE 
            WHEN ord.ordre IS NULL 
            THEN 1 
            ELSE ord.ordre+1 
        END AS ordre,
        ori.visibility, ori.panier, ori.pa_nom, ori.pa_layer, 
        ori.pa_attribut, ori.pa_encaps, ori.pa_sql, ori.pa_type_geometrie, 
        ori.sql_filter, ori.baselayer, ori.singletile, ori.maxzoomlevel 
    FROM 
        ".DB_PREFIXE."om_sig_map map 
            LEFT JOIN (
                SELECT 
                    om_sig_map, max(ordre) AS ordre 
                FROM 
                    ".DB_PREFIXE."om_sig_map_wms group by om_sig_map
                ) ord 
                ON ord.om_sig_map = map.om_sig_map 
            JOIN ".DB_PREFIXE."om_sig_map_wms ori ON ori.om_sig_map_wms=".$idx."
    WHERE 
        map.om_sig_map NOT IN (
            SELECT 
                distinct om_sig_map 
            FROM 
                ".DB_PREFIXE."om_sig_map_wms 
            WHERE 
                om_sig_wms IN (
                    SELECT 
                        om_sig_wms 
                    FROM 
                        ".DB_PREFIXE."om_sig_map_wms 
                    WHERE 
                        om_sig_map_wms = ".$idx."
                )
        )
";
// Exécution de la requête
$res = $f->db->query($sql);
// Logger
$f->addToLog("../scr/copy_om_sig_map_wms.php: db->query(\"".$sql."\");", VERBOSE_MODE);
// Vérification d'une éventuelle erreur de base de données
$f->isDatabaseError($res);
// On affiche un message de validation
$class = "valid";
$message = _("Copie terminée");
$f->displayMessage($class, $message);

?>
