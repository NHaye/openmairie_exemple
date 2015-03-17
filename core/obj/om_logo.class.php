<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_logo.class.php 2928 2014-10-15 15:17:16Z fmichon $
 */

//
require_once "../gen/obj/om_logo.class.php";

/**
 *
 */
class om_logo_core extends om_logo_gen {

    /**
     *
     */
    function setType(&$form, $maj) {
        //
        parent::setType($form, $maj);
        // ajouter et modifier
        if ($maj == 0 || $maj == 1) {
            //
            if ($this->retourformulaire == 'om_collectivite') {
                $form->setType('fichier', 'upload2');
            } else {
                $form->setType('fichier', 'upload');
            }
        }
        if ($maj == 2) {
            $form->setType('fichier', 'filestatic');
        }
        if ($maj == 3) {
            $form->setType('fichier', 'file');
        }
    }

    /**
     *
     */
    function verifier($val = array(), &$dnu1 = null, $dnu2 = null) {
        // On appelle la methode de la classe parent
        parent::verifier($val);
        // On verifie si il y a un autre id 'actif' pour la collectivite
        if ($this->valF['actif'] == "Oui") {
            //
            if ($this->getParameter("maj") == 0) {
                //
                $this->verifieractif("]", $val);
            } else {
                //
                $this->verifieractif($val[$this->clePrimaire], $val);
            }
        }
    }

    /**
     * verification sur existence d un etat deja actif pour la collectivite
     */
    function verifieractif($id, $val) {
        //
        $table = "om_logo";
        $primary_key = "om_logo";
        //
        $sql = " SELECT ".$table.".".$primary_key." ";
        $sql .= " FROM ".DB_PREFIXE."".$table." ";
        $sql .= " WHERE ".$table.".id='".$val['id']."' ";
        $sql .= " AND ".$table.".om_collectivite='".$val['om_collectivite']."' ";
        $sql .= " AND ".$table.".actif IS TRUE ";
        if ($id != "]") {
            $sql .=" AND ".$table.".".$primary_key."<>'".$id."' ";
        }
        // Exécution de la requête
        $res = $this->f->db->query($sql);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        //
        $nbligne = $res->numrows();
        if ($nbligne > 0) {
            $this->correct = false;
            $msg = $nbligne." ";
            $msg .= _("logo(s) existant(s) dans l'etat actif. Il ".
                      "n'est pas possible d'avoir plus d'un logo");
            $msg .= " \"".$val["id"]."\" "._("actif par collectivite.");
            $this->addToMessage($msg);
        }
    }

}

?>
