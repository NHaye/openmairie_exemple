<?php
/**
 *
 *
 * @package openmairie_exemple
 * @version SVN : $Id: om_etat.class.php 3008 2015-01-15 08:47:18Z fmichon $
 */

//
require_once "../gen/obj/om_etat.class.php";

/**
 *
 */
class om_etat_core extends om_etat_gen {

    /**
     * Définition des actions disponibles sur la classe.
     *
     * @return void
     */
    function init_class_actions() {

        // On récupère les actions génériques définies dans la méthode 
        // d'initialisation de la classe parente
        parent::init_class_actions();

        // ACTION - 004 - copier
        //
        $this->class_actions[4] = array(
            "identifier" => "copier",
            "portlet" => array(
                "type" => "action-direct-with-confirmation",
                "libelle" => _("copier"),
                "order" => 30,
                "class" => "copy-16",
            ),
            "view" => "formulaire",
            "method" => "copier",
            "button" => "copier",
            "permission_suffix" => "copier",
        );

        // ACTION - 005 - previsualiser
        //
        $this->class_actions[5] = array(
            "identifier" => "previsualiser",
            "portlet" => array(
                "type" => "action-blank",
                "libelle" => _("previsualiser"),
                "order" => 40,
                "class" => "pdf-16",
            ),
            "view" => "view_edition",
            "permission_suffix" => "previsualiser",
        );

    }

    /**
     *
     */
    function setOnchange(&$form, $maj) {
        //
        parent::setOnchange($form, $maj);
        //
        if ($this->getParameter("maj") == 1) {
            $form->setOnchange('om_sql','warn_user_query_change(form)');
        }
    }

    /**
     *
     */
    function setLib(&$form, $maj) {
        //
        parent::setLib($form, $maj);
        // Ajout du libellé poour que la traduction soit prise en compte
        $form->setLib('merge_fields', _("merge_fields"));
    }

    /**
     *
     */
    function setType(&$form, $maj) {
        //
        parent::setType($form, $maj);
        //
        $form->setType('merge_fields', 'textareastatic');
        //
        if ($maj == 0) {
            $form->setType('merge_fields', 'hidden');
        }
        // ajouter et modifier
        if ($maj == 0 || $maj == 1) {
            //
            $form->setType('orientation', 'select');
            $form->setType('format', 'select');
            $form->setType('titrebordure', 'select');
            //
            $form->setType('logo', 'select');
            //
            $form->setType('se_font', 'select');
            $form->setType('se_couleurtexte', 'rvb');
            //
            $form->setType('logotop', 'localisation_edition');
            $form->setType('titretop', 'localisation_edition');
        }
        // supprimer et consulter
        if ($maj == 2 or $maj == 3) {
            //
            $form->setType('orientation', 'selectstatic');
            $form->setType('format', 'selectstatic');
            $form->setType('titrebordure', 'selectstatic');
            //
            $form->setType('logo', 'selectstatic');
            //
            $form->setType('se_font', 'selectstatic');
        }
    }

    /**
     *
     */
    function setSelect(&$form, $maj, &$dnu1 = null, $dnu2 = null) {
        //
        parent::setSelect($form, $maj);
        //
        $contenu = array();
        $contenu[0] = array('P', 'L');
        $contenu[1] = array(_('portrait'), _('paysage'));
        $form->setSelect('orientation',  $contenu);
        //
        $contenu = array();
        $contenu[0] = array('A4', 'A3');
        $contenu[1] = array('A4', 'A3');
        $form->setSelect('format', $contenu);
        //
        $contenu = array();
        $contenu[0] = array('', 'I', 'B', 'U', 'BI', 'UI');
        $contenu[1] = array(_('normal'), _('italique'), _('gras'), _('souligne'), _('italique').' '._('gras'), _('souligne').' '._('gras'));
        $form->setSelect('titreattribut', $contenu);
        $form->setSelect('corpsattribut', $contenu);
        $form->setSelect('footerattribut', $contenu);
        //
        $contenu = array();
        $contenu[0] = array('helvetica', 'times', 'arial', 'courier');
        $contenu[1] = array('helvetica', 'times', 'arial', 'courier');
        $form->setSelect('titrefont', $contenu);
        $form->setSelect('corpsfont', $contenu);
        $form->setSelect('footerfont', $contenu);
        $form->setSelect('se_font', $contenu);
        //
        $contenu = array();
        $contenu[0] = array('L', 'R', 'J', 'C');
        $contenu[1] = array(_('gauche'), _('droite'), _('justifie'), _('centre'));
        $form->setSelect('titrealign', $contenu);
        $form->setSelect('corpsalign', $contenu);
        //
        $contenu = array();
        $contenu[0] = array('0', '1');
        $contenu[1] = array(_('sans'), _('avec'));
        $form->setSelect('titrebordure', $contenu);
        $form->setSelect('corpsbordure', $contenu);

        // LOCALISATION EDITION
        $config = array(
            "format" => "format",
            "orientation" => "orientation"
        );
        // Logo
        $contenu = $config;
        $contenu["x"] = "logoleft";
        $contenu["y"] = "logotop";
        $form->setSelect("logotop", $contenu);
        // Titre
        $contenu = $config;
        $contenu["x"] = "titreleft";
        $contenu["y"] = "titretop";
        $form->setSelect("titretop", $contenu);
        // Corps
        $contenu = $config;
        $contenu["x"] = "corpsleft";
        $contenu["y"] = "corpstop";
        $form->setSelect("corpstop", $contenu);

        // SOUS-ETATS ET LOGO
        if (file_exists ("../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php")) {
            include ("../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php");
        }
        $this->init_select($form, $this->f->db, $maj, null, "logo",
                           $sql_om_logo, $sql_om_logo_by_id, false);

    }

    /**
     *
     */
    function setLayout(&$form, $maj) {

        $form->setFieldset($this->clePrimaire, 'D', _('Edition'), "collapsible");
            $form->setBloc($this->clePrimaire, 'D', "", "");
            $form->setBloc('actif', 'F', "", "");
            $form->setFieldset('orientation','D', _("Parametres generaux de l'edition"), "startClosed");
                $form->setBloc('orientation','D', "", "col_12");
                    $form->setBloc('orientation','D', _("Orientation et format"), "col_4");
                    $form->setBloc('format','F', "", "");
                    $form->setBloc('logo','D', _("Logo et positionnement"), "col_4");
                    $form->setBloc('logotop','F');
                $form->setBloc('margeleft','D', _("Marges du document"), "col_4");
                $form->setBloc('margebottom','F');
            $form->setFieldset('margebottom','F','');
        $form->setFieldset('margebottom','F','');

        $form->setFieldset('titre_om_htmletat','D', _('Titre'), "collapsible");
        $form->setBloc('titre_om_htmletat','DF', "", "fullwidth hidelabel");
        $form->setFieldset('titreleft','D', _("Parametres du titre de l'edition"), "startClosed");
        $form->setBloc('titreleft','D', _("Positionnement"));
        $form->setBloc('titreleft','D', "", "group");
        $form->setBloc('titretop','F');
        $form->setBloc('titrelargeur','D', "", "group");
        $form->setBloc('titrehauteur','F');
        $form->setBloc('titrehauteur','F');
        $form->setBloc('titrebordure','DF', _("Bordure"));
        $form->setFieldset('titrebordure','F','');
        $form->setFieldset('titrebordure','F','');

        $form->setFieldset('corps_om_htmletatex','D', _('Corps'), "collapsible");
        $form->setBloc('corps_om_htmletatex','DF', "", "fullwidth hidelabel");
        $form->setFieldset('corps_om_htmletatex','F','');

        $form->setFieldset('om_sql','D', _('Champ(s) de fusion'), "collapsible");
        $form->setFieldset('merge_fields','F','');

        $form->setFieldset('se_font','D', _("Sous-etat(s)"), "startClosed");
        $form->setFieldset('se_couleurtexte','F','');

    }

    /**
     *
     */
    function init_default_values() {
        //
        $this->form->setVal('orientation','P');
        $this->form->setVal('format','A4');
        //
        $this->form->setVal('logoleft', 10);
        $this->form->setVal('logotop', 10);
        //
        $this->form->setVal('titre',_('Texte du titre'));
        $this->form->setVal('titreleft',109);
        $this->form->setVal('titretop',16);
        $this->form->setVal('titrelargeur',0);
        $this->form->setVal('titrehauteur',10);
        $this->form->setVal('titrefont','arial');
        $this->form->setVal('titreattribut','B');
        $this->form->setVal('titretaille',20);
        $this->form->setVal('titrebordure',0);
        $this->form->setVal('titrealign','L');
        //
        $this->form->setVal('corps',_('Texte du corps'));
        $this->form->setVal('corpsleft',14);
        $this->form->setVal('corpstop',66);
        $this->form->setVal('corpslargeur',110);
        $this->form->setVal('corpshauteur',5);
        $this->form->setVal('corpsfont','times');
        $this->form->setVal('corpsattribut','');
        $this->form->setVal('corpstaille',10);
        $this->form->setVal('corpsbordure',0);
        $this->form->setVal('corpsalign','J');
        //
        $this->form->setVal('se_font','helvetica');
        $this->form->setVal('se_couleurtexte','0-0-0');
    }

    /**
     *
     */
    function setVal(&$form, $maj, $validation, &$dnu1 = null, $dnu2 = null) {
        //
        parent::setVal($form, $maj, $validation);
        //
        if ($validation == 0) {
            //
            if ($maj == 0) {
                //
                $this->init_default_values();
            }
        }
    }

    /**
     *
     */
    function setValsousformulaire(&$form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire, &$dnu1 = null, $dnu2 = null) {
        //
        parent::setValsousformulaire($form, $maj, $validation, $idxformulaire, $retourformulaire, $typeformulaire);
        //
        if ($validation == 0) {
            //
            if ($maj == 0) {
                //
                $this->init_default_values();
            }
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
        // vérification de l'utilisation des sous-états
        // il doit n'y avoir qu'une occurence de chaque
                if (file_exists ("../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php")) {
            include ("../sql/".OM_DB_PHPTYPE."/".$this->table.".form.inc.php");
        }
        // Exécution de la requête
        $res = $this->f->db->query($sql_om_sousetat);
        // Logger
        $this->addToLog(__METHOD__."(): db->query(\"".$sql_om_sousetat."\");", VERBOSE_MODE);
        // Vérification d'une éventuelle erreur de base de données
        $this->f->isDatabaseError($res);
        //
        while($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            // vérification du nombre d'occurence
            // affichage d'un message d'erreur si > 1
            if(mb_substr_count($this->valF["corps_om_htmletatex"], ' id=&quot;'.$row["id"]) > 1) {
                $this->correct=false;
                $error_message =
                _("Le champ %s ne peut pas contenir plusieurs occurences du sous-etat %s.");
                $this->addToMessage(
                    sprintf(
                        $error_message,
                        "<b>"._("corps_om_htmletatex")."</b>",
                        "<b>".$row["libelle"]."</b>"
                    )
                );
            }
        }
    }

    /**
     * verification sur existence d un etat deja actif pour la collectivite
     */
    function verifieractif($id, $val) {
        //
        $table = "om_etat";
        $primary_key = "om_etat";
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
            $msg .= _("etat(s) existant(s) dans l'etat actif. Il ".
                      "n'est pas possible d'avoir plus d'un etat");
            $msg .= " \"".$val["id"]."\" "._("actif par collectivite.");
            $this->addToMessage($msg);
        }
    }

    /**
     * TREATMENT - copier.
     * 
     * @return boolean
     */
    function copier($val = array(), &$dnu1 = null, $dnu2 = null) {
        // Logger
        $this->addToLog(__METHOD__."() - begin", EXTRA_VERBOSE_MODE);
        //
        $this->correct = false;
        // Récuperation de la valeur de la cle primaire de l'objet
        $id = $this->getVal($this->clePrimaire);
        // Récupération des valeurs de l'objet
        $this->setValFFromVal();
        // Maj des valeur de l'objet à copier
        $this->valF[$this->clePrimaire]=null;
        $this->valF["libelle"]=sprintf(_('copie du %s'), date('d/m/Y'));
        $this->valF["actif"]=false;
        // Si en sousform l'id de la collectivité est celle du formulaire principal
        if ($this->getParameter("retourformulaire") === "om_collectivite") {
            $this->valF["om_collectivite"] = $this->getParameter("idxformulaire");
        } else {
            $this->valF["om_collectivite"] = $_SESSION['collectivite'];
        }
        // Certains champs ne sont pas présent dans la table om_etat
        // (jointure sur om_requete dans om_etat.form.inc.php)
        unset($this->valF["merge_fields"]);
        //
        $this->deverrouille($this->getParameter("validation"));
        $res = $this->ajouter($this->valF);
        $this->verrouille();
        if ($res === true) {
            $this->correct = true;
            $this->addToMessage("L'element a ete correctement duplique.<br/>");
            return true;
        } else {
            $this->correct = false;
            return false;
        }
        // Logger
        $this->addToLog(__METHOD__."() - end", EXTRA_VERBOSE_MODE);
        //
        return true;
    }

    /**
     * Permet d’effectuer des actions après l’insertion des données dans la base.
     *
     * @param integer $id    Identifiant de l'objet
     * @param object  &$db   Instance de la bdd
     * @param array   $val   Liste des valeurs
     * @param mixed   $DEBUG Debug
     *
     * @return boolean
     */
    function triggerajouterapres($id, &$db, $val, $DEBUG) {
        //
        parent::triggerajouterapres($id, $db, $val, $DEBUG);
        $this->set_merge_fields();
    }

    /**
     * Permet d’effectuer des actions après la modification des données dans la
     * base.
     *
     * @param integer $id    Identifiant de l'objet
     * @param object  &$db   Instance de la bdd
     * @param array   $val   Liste des valeurs
     * @param mixed   $DEBUG Debug
     *
     * @return boolean
     */
    function triggerModifierApres($id, &$db, $val, $DEBUG) {
        //
        parent::triggerModifierApres($id, $db, $val, $DEBUG);
        $this->set_merge_fields();
    }

    /**
     * Met à jour l'aide à la saisie si requête objet sélectionnée
     */
    function set_merge_fields() {
        // Récupération de la requête
        require_once "../obj/om_requete.class.php";
        $om_requete = new om_requete($this->valF["om_sql"]);
        $type_requete = $om_requete->getVal('type');
        $methode = $om_requete->getVal('methode');
        // Si requête objet
        if ($type_requete == 'objet') {
            // récupération du(des) objet(s) et pour l'unique(premier)
            // son éventuelle méthode
            $classes = $om_requete->getVal('classe');
            $classes = explode(';', $classes);
            $nb_classes = count($classes);
            $i = 0;
            $labels = array();
            for ($i = 0; $i < $nb_classes; $i++) {
                $classe = $classes[$i];
                require_once "../obj/".$classe.".class.php";
                $sql_object = new $classe("]");
                // si unique(premier) objet
                if ($i == 0) {
                    // si une méthode custom existe on récupère ses libellés
                    if ($methode != null && $methode != ''
                        && method_exists($sql_object, $methode)) {
                        $custom = $sql_object->$methode('labels');
                        $labels = array_merge($labels, $custom);
                    }
                    // on récupère également les libellés par défaut
                    $default = $sql_object->get_merge_fields('labels');
                    $labels = array_merge($labels, $default);
                } else { // sinon traitement des éventuels objet supplémentaires
                    // on ne récupère que les libellés par défaut
                    $default = $sql_object->get_merge_fields('labels');
                    $labels = array_merge($labels, $default);
                }
            }
            // Modification de l'aide à la saisie dans la base de données
            // si des libellés existent
            if (!empty($labels)) {
                $om_requete->setValFFromVal();
                $valF = $om_requete->valF;
                $merge_fields = sprintf("<table><thead>");
                foreach ($labels as $object => $fields) {
                    // header : intitulé objet
                    $merge_fields .= sprintf('<tr>
                        <th colspan="2">%s</th></tr></thead><tbody>',
                        _("Enregistrement de type")." ".$object
                    );
                    // body : une ligne = un champ
                    foreach ($fields as $field => $label) {
                        $merge_fields .= sprintf("<tr><td>[%s]</td><td>%s</td></tr>",
                            $field, $label
                        );
                    }
                    // ligne séparatrice
                    $merge_fields .= sprintf('<tr style="%s"><td colspan="2"></td></tr>',
                        "height: 10px !important;");
                }
                $merge_fields .= sprintf("</tbody></table>");
                $valF["merge_fields"] = $merge_fields;
                $om_requete->modifier($valF);
            }
        }
    }

    /**
     * VIEW - view_edition
     *
     * @return void
     */
    function view_edition() {
        //
        $this->checkAccessibility();
        // Initialisation de l'objet à éditer
        $obj = $this->getVal("id");
        // Construction du lien de redirection
        $location = sprintf(
            "location:../pdf/pdfetat.php?obj=%s",
            $obj
        );
        // Redirection vers l'édition
        header($location);
    }

}

?>
