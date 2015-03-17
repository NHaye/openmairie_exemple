--------------------------------------------------------------------------------
-- Script de mise à jour vers la version v4.5.0-a1
--
-- Ce fichier devra être mergé avec les scripts de mise à jour de version
--
-- @package openfoncier
-- @version SVN : $Id$
-------------------------------------------------------------------------------

-- Récupération des sous-états et lettres-type (rétrocompatibilité)

-- Remplacement des & en &amp;
UPDATE om_etat set titre = regexp_replace(titre, E'[\&]', '&amp;', 'g');
UPDATE om_etat set corps = regexp_replace(corps, E'[\&]', '&amp;', 'g');
UPDATE om_lettretype set titre = regexp_replace(titre, E'[\&]', '&amp;', 'g');
UPDATE om_lettretype set corps = regexp_replace(corps, E'[\&]', '&amp;', 'g');

-- Retour à la ligne au format html
UPDATE om_etat set titre = regexp_replace(titre, E'[\\n\\r]', '&lt;br/&gt;', 'g');
UPDATE om_etat set corps = regexp_replace(corps, E'[\\n\\r]', '&lt;br/&gt;', 'g');
UPDATE om_lettretype set titre = regexp_replace(titre, E'[\\n\\r]', '&lt;br/&gt;', 'g');
UPDATE om_lettretype set corps = regexp_replace(corps, E'[\\n\\r]', '&lt;br/&gt;', 'g');

UPDATE om_etat set titre = regexp_replace(titre, E'\\s', '&nbsp;', 'g');
UPDATE om_etat set corps = regexp_replace(corps, E'\\s', '&nbsp;', 'g');
UPDATE om_lettretype set titre = regexp_replace(titre, E'\\s', '&nbsp;', 'g');
UPDATE om_lettretype set corps = regexp_replace(corps, E'\\s', '&nbsp;', 'g');

UPDATE om_etat set titre = replace(titre, '\p', '&lt;br pagebreak=&quot;true&quot; /&gt;');
UPDATE om_etat set corps = replace(corps, '\p', '&lt;br pagebreak=&quot;true&quot; /&gt;');
UPDATE om_lettretype set titre = replace(titre, '\p', '&lt;br pagebreak=&quot;true&quot; /&gt;');
UPDATE om_lettretype set corps = replace(corps, '\p', '&lt;br pagebreak=&quot;true&quot; /&gt;');



-- Décoration des corps et titre des états
UPDATE om_etat set titre = concat('&lt;span style=&quot;text-decoration: underline;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titreattribut LIKE 'U%';
UPDATE om_etat set corps = concat('&lt;span style=&quot;text-decoration: underline;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsattribut LIKE 'U%';
UPDATE om_etat set titre = concat('&lt;span style=&quot;font-style: italic;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titreattribut LIKE '%I';
UPDATE om_etat set corps = concat('&lt;span style=&quot;font-style: italic;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsattribut LIKE '%I';
UPDATE om_etat set titre = concat('&lt;span style=&quot;font-weight: bold;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titreattribut LIKE '%B';
UPDATE om_etat set corps = concat('&lt;span style=&quot;font-weight: bold;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsattribut LIKE '%B';
-- Font  des corps et titre des états
UPDATE om_etat set titre = concat('&lt;span style=&quot;font-family: helvetica;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titrefont = 'helvetica';
UPDATE om_etat set corps = concat('&lt;span style=&quot;font-family: helvetica;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsfont = 'helvetica';
UPDATE om_etat set titre = concat('&lt;span style=&quot;font-family: times;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titrefont = 'times';
UPDATE om_etat set corps = concat('&lt;span style=&quot;font-family: times;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsfont = 'times';
UPDATE om_etat set titre = concat('&lt;span style=&quot;font-family: arial;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titrefont = 'arial';
UPDATE om_etat set corps = concat('&lt;span style=&quot;font-family: arial;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsfont = 'arial';
UPDATE om_etat set titre = concat('&lt;span style=&quot;font-family: courier;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titrefont = 'courier';
UPDATE om_etat set corps = concat('&lt;span style=&quot;font-family: courier;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsfont = 'courier';
UPDATE om_etat set titre = concat('&lt;span style=&quot;font-size: ',titretaille , 'px;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titretaille NOTNULL;
UPDATE om_etat set corps = concat('&lt;span style=&quot;font-size: ',corpstaille , 'px;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpstaille NOTNULL;
-- Alignement des corps et titre des états
UPDATE om_etat set titre = concat('&lt;p style=&quot;text-align: left;&quot;&gt;',titre, '&lt;/p&gt;') WHERE titrealign = 'L';
UPDATE om_etat set corps = concat('&lt;p style=&quot;text-align: left;&quot;&gt;',corps, '&lt;/p&gt;') WHERE corpsalign = 'L';
UPDATE om_etat set titre = concat('&lt;p style=&quot;text-align: center;&quot;&gt;',titre, '&lt;/p&gt;') WHERE titrealign = 'C';
UPDATE om_etat set corps = concat('&lt;p style=&quot;text-align: center;&quot;&gt;',corps, '&lt;/p&gt;') WHERE corpsalign = 'C';
UPDATE om_etat set titre = concat('&lt;p style=&quot;text-align: right;&quot;&gt;',titre, '&lt;/p&gt;') WHERE titrealign = 'R';
UPDATE om_etat set corps = concat('&lt;p style=&quot;text-align: right;&quot;&gt;',corps, '&lt;/p&gt;') WHERE corpsalign = 'R';
UPDATE om_etat set titre = concat('&lt;p style=&quot;text-align: justify;&quot;&gt;',titre, '&lt;/p&gt;') WHERE titrealign = 'J';
UPDATE om_etat set corps = concat('&lt;p style=&quot;text-align: justify;&quot;&gt;',corps, '&lt;/p&gt;') WHERE corpsalign = 'J';

-- Décoration des corps et titre des lettres type
UPDATE om_lettretype set titre = concat('&lt;span style=&quot;text-decoration: underline;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titreattribut LIKE 'U%';
UPDATE om_lettretype set corps = concat('&lt;span style=&quot;text-decoration: underline;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsattribut LIKE 'U%';
UPDATE om_lettretype set titre = concat('&lt;span style=&quot;font-style: italic;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titreattribut LIKE '%I';
UPDATE om_lettretype set corps = concat('&lt;span style=&quot;font-style: italic;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsattribut LIKE '%I';
UPDATE om_lettretype set titre = concat('&lt;span style=&quot;font-weight: bold;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titreattribut LIKE '%B';
UPDATE om_lettretype set corps = concat('&lt;span style=&quot;font-weight: bold;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsattribut LIKE '%B';
-- Font  des corps et titre des lettres type
UPDATE om_lettretype set titre = concat('&lt;span style=&quot;font-family: helvetica;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titrefont = 'helvetica';
UPDATE om_lettretype set corps = concat('&lt;span style=&quot;font-family: helvetica;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsfont = 'helvetica';
UPDATE om_lettretype set titre = concat('&lt;span style=&quot;font-family: times;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titrefont = 'times';
UPDATE om_lettretype set corps = concat('&lt;span style=&quot;font-family: times;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsfont = 'times';
UPDATE om_lettretype set titre = concat('&lt;span style=&quot;font-family: arial;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titrefont = 'arial';
UPDATE om_lettretype set corps = concat('&lt;span style=&quot;font-family: arial;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsfont = 'arial';
UPDATE om_lettretype set titre = concat('&lt;span style=&quot;font-family: courier;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titrefont = 'courier';
UPDATE om_lettretype set corps = concat('&lt;span style=&quot;font-family: courier;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpsfont = 'courier';
UPDATE om_lettretype set titre = concat('&lt;span style=&quot;font-size: ',titretaille , 'px;&quot;&gt;',titre, '&lt;/span&gt;') WHERE titretaille NOTNULL;
UPDATE om_lettretype set corps = concat('&lt;span style=&quot;font-size: ',corpstaille , 'px;&quot;&gt;',corps, '&lt;/span&gt;') WHERE corpstaille NOTNULL;
-- Alignement des corps et titre des lettres type
UPDATE om_lettretype set titre = concat('&lt;p style=&quot;text-align: left;&quot;&gt;',titre, '&lt;/p&gt;') WHERE titrealign = 'L';
UPDATE om_lettretype set corps = concat('&lt;p style=&quot;text-align: left;&quot;&gt;',corps, '&lt;/p&gt;') WHERE corpsalign = 'L';
UPDATE om_lettretype set titre = concat('&lt;p style=&quot;text-align: center;&quot;&gt;',titre, '&lt;/p&gt;') WHERE titrealign = 'C';
UPDATE om_lettretype set corps = concat('&lt;p style=&quot;text-align: center;&quot;&gt;',corps, '&lt;/p&gt;') WHERE corpsalign = 'C';
UPDATE om_lettretype set titre = concat('&lt;p style=&quot;text-align: right;&quot;&gt;',titre, '&lt;/p&gt;') WHERE titrealign = 'R';
UPDATE om_lettretype set corps = concat('&lt;p style=&quot;text-align: right;&quot;&gt;',corps, '&lt;/p&gt;') WHERE corpsalign = 'R';
UPDATE om_lettretype set titre = concat('&lt;p style=&quot;text-align: justify;&quot;&gt;',titre, '&lt;/p&gt;') WHERE titrealign = 'J';
UPDATE om_lettretype set corps = concat('&lt;p style=&quot;text-align: justify;&quot;&gt;',corps, '&lt;/p&gt;') WHERE corpsalign = 'J';

-- Maj du corps des états avec les sous états

CREATE OR REPLACE FUNCTION updatecorps(corps TEXT, sousetat TEXT) RETURNS TEXT AS 
$BODY$
DECLARE
retour TEXT := '&lt;br /&gt;';
arr varchar[] := string_to_array(sousetat,';');
x TEXT;
BEGIN
    FOREACH x IN ARRAY arr
    LOOP
    retour := retour||'&lt;span class=&quot;mce_sousetat&quot; id=&quot;'||x||'&quot;&gt;'||x||'&lt;/span&gt;&lt;br /&gt;';
    END LOOP;
    RETURN concat(corps,retour);
END;
$BODY$
LANGUAGE 'plpgsql' VOLATILE;

UPDATE om_etat set corps = updatecorps(corps, sousetat);

-- Maj des codes barres
UPDATE om_etat set titre = regexp_replace(titre, E'[\|]{5}(.*)[\|]{5}', '&lt;span class=&quot;mce_codebarre&quot;&gt;\1&lt;/span&gt;', 'g');
UPDATE om_etat set corps = regexp_replace(corps, E'[\|]{5}(.*)[\|]{5}', '&lt;span class=&quot;mce_codebarre&quot;&gt;\1&lt;/span&gt;', 'g');
UPDATE om_lettretype set titre = regexp_replace(titre, E'[\|]{5}(.*)[\|]{5}', '&lt;span class=&quot;mce_codebarre&quot;&gt;\1&lt;/span&gt;', 'g');
UPDATE om_lettretype set corps = regexp_replace(corps, E'[\|]{5}(.*)[\|]{5}', '&lt;span class=&quot;mce_codebarre&quot;&gt;\1&lt;/span&gt;', 'g');

-- Maj des balises min/maj
UPDATE om_etat set titre = regexp_replace(titre, E'<min>(.*)</min>', '&lt;span class=&quot;mce_min&quot;&gt;\1&lt;/span&gt;', 'g');
UPDATE om_etat set corps = regexp_replace(corps, E'<min>(.*)</min>', '&lt;span class=&quot;mce_min&quot;&gt;\1&lt;/span&gt;', 'g');
UPDATE om_lettretype set titre = regexp_replace(titre, E'<min>(.*)</min>', '&lt;span class=&quot;mce_min&quot;&gt;\1&lt;/span&gt;', 'g');
UPDATE om_lettretype set corps = regexp_replace(corps, E'<min>(.*)</min>', '&lt;span class=&quot;mce_min&quot;&gt;\1&lt;/span&gt;', 'g');

UPDATE om_etat set titre = regexp_replace(titre, E'<MAJ>(.*)</MAJ>', '&lt;span class=&quot;mce_maj&quot;&gt;\1&lt;/span&gt;', 'g');
UPDATE om_etat set corps = regexp_replace(corps, E'<MAJ>(.*)</MAJ>', '&lt;span class=&quot;mce_maj&quot;&gt;\1&lt;/span&gt;', 'g');
UPDATE om_lettretype set titre = regexp_replace(titre, E'<MAJ>(.*)</MAJ>', '&lt;span class=&quot;mce_maj&quot;&gt;\1&lt;/span&gt;', 'g');
UPDATE om_lettretype set corps = regexp_replace(corps, E'<MAJ>(.*)</MAJ>', '&lt;span class=&quot;mce_maj&quot;&gt;\1&lt;/span&gt;', 'g');


-- Création des champs de marge
-- Création des champs de marge
ALTER TABLE om_etat ADD COLUMN margeleft integer NOT NULL default 10;
ALTER TABLE om_etat ADD COLUMN margetop integer NOT NULL default 10;
ALTER TABLE om_etat ADD COLUMN margeright integer NOT NULL default 10;
ALTER TABLE om_etat ADD COLUMN margebottom integer NOT NULL default 10;
COMMENT ON COLUMN om_etat.margeleft IS 'Marge gauche de l''édition';
COMMENT ON COLUMN om_etat.margetop IS 'Marge haute de l''édition';
COMMENT ON COLUMN om_etat.margeright IS 'Marge droite de l''édition';
COMMENT ON COLUMN om_etat.margebottom IS 'Marge basse de l''édition';

ALTER TABLE om_lettretype ADD COLUMN margeleft integer NOT NULL default 10;
ALTER TABLE om_lettretype ADD COLUMN margetop integer NOT NULL default 10;
ALTER TABLE om_lettretype ADD COLUMN margeright integer NOT NULL default 10;
ALTER TABLE om_lettretype ADD COLUMN margebottom integer NOT NULL default 10;
COMMENT ON COLUMN om_lettretype.margeleft IS 'Marge gauche de l''édition';
COMMENT ON COLUMN om_lettretype.margetop IS 'Marge haute de l''édition';
COMMENT ON COLUMN om_lettretype.margeright IS 'Marge droite de l''édition';
COMMENT ON COLUMN om_lettretype.margebottom IS 'Marge basse de l''édition';

-- Suppression des colonnes inutilisée
ALTER TABLE om_etat DROP COLUMN titrefont;
ALTER TABLE om_etat DROP COLUMN titreattribut;
ALTER TABLE om_etat DROP COLUMN titretaille;
ALTER TABLE om_etat DROP COLUMN titrealign;
ALTER TABLE om_etat DROP COLUMN corpstop;
ALTER TABLE om_etat DROP COLUMN corpsleft;
ALTER TABLE om_etat DROP COLUMN corpslargeur;
ALTER TABLE om_etat DROP COLUMN corpshauteur;
ALTER TABLE om_etat DROP COLUMN corpsfont;
ALTER TABLE om_etat DROP COLUMN corpsattribut;
ALTER TABLE om_etat DROP COLUMN corpstaille;
ALTER TABLE om_etat DROP COLUMN corpsbordure;
ALTER TABLE om_etat DROP COLUMN corpsalign;

ALTER TABLE om_lettretype DROP COLUMN titrefont;
ALTER TABLE om_lettretype DROP COLUMN titreattribut;
ALTER TABLE om_lettretype DROP COLUMN titretaille;
ALTER TABLE om_lettretype DROP COLUMN titrealign;
ALTER TABLE om_lettretype DROP COLUMN corpstop;
ALTER TABLE om_lettretype DROP COLUMN corpsleft;
ALTER TABLE om_lettretype DROP COLUMN corpslargeur;
ALTER TABLE om_lettretype DROP COLUMN corpshauteur;
ALTER TABLE om_lettretype DROP COLUMN corpsfont;
ALTER TABLE om_lettretype DROP COLUMN corpsattribut;
ALTER TABLE om_lettretype DROP COLUMN corpstaille;
ALTER TABLE om_lettretype DROP COLUMN corpsbordure;
ALTER TABLE om_lettretype DROP COLUMN corpsalign;

ALTER TABLE om_etat RENAME corps TO corps_om_htmletatex;
ALTER TABLE om_etat RENAME titre TO titre_om_htmletat;
ALTER TABLE om_lettretype RENAME corps TO corps_om_htmletatex;
ALTER TABLE om_lettretype RENAME titre TO titre_om_htmletat;

ALTER TABLE om_etat DROP COLUMN sousetat;

ALTER TABLE om_lettretype ADD COLUMN se_font character varying(20);
ALTER TABLE om_lettretype ADD COLUMN se_margeleft bigint;
ALTER TABLE om_lettretype ADD COLUMN se_margetop bigint;
ALTER TABLE om_lettretype ADD COLUMN se_margeright bigint;
ALTER TABLE om_lettretype ADD COLUMN se_couleurtexte character varying(11);

-- Homogénéisation des tailles de cellules par rapport à la taille de la fonte
UPDATE om_sousetat SET cellule_hauteur=tableau_fontaille WHERE tableau_fontaille>cellule_hauteur;

-- Suppression des marges des sous-états
ALTER TABLE om_etat DROP COLUMN se_margeleft;
ALTER TABLE om_etat DROP COLUMN se_margetop;
ALTER TABLE om_etat DROP COLUMN se_margeright;

ALTER TABLE om_lettretype DROP COLUMN se_margeleft;
ALTER TABLE om_lettretype DROP COLUMN se_margetop;
ALTER TABLE om_lettretype DROP COLUMN se_margeright;

-- Transformation des entitiés html
UPDATE om_etat set corps_om_htmletatex = replace(corps_om_htmletatex, '&lt;', '<');
UPDATE om_etat set titre_om_htmletat = replace(titre_om_htmletat, '&lt;', '<');
UPDATE om_lettretype set corps_om_htmletatex = replace(corps_om_htmletatex, '&lt;', '<');
UPDATE om_lettretype set titre_om_htmletat = replace(titre_om_htmletat, '&lt;', '<');

UPDATE om_etat set corps_om_htmletatex = replace(corps_om_htmletatex, '&gt;', '>');
UPDATE om_etat set titre_om_htmletat = replace(titre_om_htmletat, '&gt;', '>');
UPDATE om_lettretype set corps_om_htmletatex = replace(corps_om_htmletatex, '&gt;', '>');
UPDATE om_lettretype set titre_om_htmletat = replace(titre_om_htmletat, '&gt;', '>');

UPDATE om_etat set corps_om_htmletatex = replace(corps_om_htmletatex, '&quot;', '"');
UPDATE om_etat set titre_om_htmletat = replace(titre_om_htmletat, '&quot;', '"');
UPDATE om_lettretype set corps_om_htmletatex = replace(corps_om_htmletatex, '&quot;', '"');
UPDATE om_lettretype set titre_om_htmletat = replace(titre_om_htmletat, '&quot;', '"');

UPDATE om_etat set corps_om_htmletatex = replace(corps_om_htmletatex, '&amp;', '&');
UPDATE om_etat set titre_om_htmletat = replace(titre_om_htmletat, '&amp;', '&');
UPDATE om_lettretype set corps_om_htmletatex = replace(corps_om_htmletatex, '&amp;', '&');
UPDATE om_lettretype set titre_om_htmletat = replace(titre_om_htmletat, '&amp;', '&');

UPDATE om_etat set titre_om_htmletat = replace(titre_om_htmletat, '<b>', '<span style="font-weight: bold;">');
UPDATE om_etat set titre_om_htmletat = replace(titre_om_htmletat, '</b>', '</span>');
UPDATE om_etat set corps_om_htmletatex = replace(corps_om_htmletatex, '<b>', '<span style="font-weight: bold;">');
UPDATE om_etat set corps_om_htmletatex = replace(corps_om_htmletatex, '</b>', '</span>');
UPDATE om_lettretype set titre_om_htmletat = replace(titre_om_htmletat, '<b>', '<span style="font-weight: bold;">');
UPDATE om_lettretype set titre_om_htmletat = replace(titre_om_htmletat, '</b>', '</span>');
UPDATE om_lettretype set corps_om_htmletatex = replace(corps_om_htmletatex, '<b>', '<span style="font-weight: bold;">');
UPDATE om_lettretype set corps_om_htmletatex = replace(corps_om_htmletatex, '</b>', '</span>');

--
-- Ajout des commentaires de table
--

-- om_collectivite
COMMENT ON TABLE om_collectivite IS 'Ville utilisant openADS';
COMMENT ON COLUMN om_collectivite.om_collectivite IS 'Identifiant unique';
COMMENT ON COLUMN om_collectivite.libelle IS 'Libellé de la ville';
COMMENT ON COLUMN om_collectivite.niveau IS 'Niveau de la collectivité (1 = mono collectivité, 2 = gère plusieurs autres collectivité)';

-- om_dashboard
COMMENT ON TABLE om_dashboard IS 'Paramétrage du tableau de bord par profil';
COMMENT ON COLUMN om_dashboard.om_dashboard IS 'Identifiant unique';
COMMENT ON COLUMN om_dashboard.om_profil IS 'Profil auquel on affecte le tableau de ville';
COMMENT ON COLUMN om_dashboard.bloc IS 'Bloc de positionnement du widget';
COMMENT ON COLUMN om_dashboard.position IS 'Position du widget dans le bloc';
COMMENT ON COLUMN om_dashboard.om_widget IS 'Identifiant du widget';

-- om_droit
COMMENT ON TABLE om_droit IS 'Paramétrage des droits';
COMMENT ON COLUMN om_droit.om_droit IS 'Identifiant unique';
COMMENT ON COLUMN om_droit.libelle IS 'Libellé du droit';
COMMENT ON COLUMN om_droit.om_profil IS 'Type de profil auquel est lié le droit';

-- om_etat
COMMENT ON TABLE om_etat IS 'Paramétrage des états';
COMMENT ON COLUMN om_etat.om_etat IS 'Identifiant unique';
COMMENT ON COLUMN om_etat.om_collectivite IS 'Identifiant de la collectivité liée à l''état';
COMMENT ON COLUMN om_etat.id IS 'Identifiant de l''état';
COMMENT ON COLUMN om_etat.libelle IS 'Libellé de l''état';
COMMENT ON COLUMN om_etat.actif IS 'Défini si l''état est actif';
COMMENT ON COLUMN om_etat.orientation IS 'Défini l''orientation de la page';
COMMENT ON COLUMN om_etat.format IS 'Défini le format de la page';
COMMENT ON COLUMN om_etat.logo IS 'Défini le logo d''entête';
COMMENT ON COLUMN om_etat.logoleft IS 'Position du logo à gauche';
COMMENT ON COLUMN om_etat.logotop IS 'Position du logo en haut';
COMMENT ON COLUMN om_etat.titre_om_htmletat IS 'Bloc de titre contenant un éditeur de texte riche';
COMMENT ON COLUMN om_etat.titreleft IS 'Position du titre à gauche';
COMMENT ON COLUMN om_etat.titretop IS 'Position du titre en haut';
COMMENT ON COLUMN om_etat.titrelargeur IS 'Largeur du titre';
COMMENT ON COLUMN om_etat.titrehauteur IS 'Hauteur du titre';
COMMENT ON COLUMN om_etat.titrebordure IS 'Défini si les bordures du titre sont affichées';
COMMENT ON COLUMN om_etat.corps_om_htmletatex IS 'Bloc de corps contenant un éditeur de texte riche';
COMMENT ON COLUMN om_etat.om_sql IS 'Identifiant de la requête permettant de récupérer les champs de fusion de l''état';
COMMENT ON COLUMN om_etat.se_font IS 'Police du texte des sous-états';
COMMENT ON COLUMN om_etat.se_couleurtexte IS 'Couleur du texte des sous-états';


-- om_lettretype
COMMENT ON TABLE om_lettretype IS 'Paramétrage des lettre-types';
COMMENT ON COLUMN om_lettretype.om_lettretype IS 'Identifiant unique';
COMMENT ON COLUMN om_lettretype.om_collectivite IS 'Identifiant de la collectivité liée à la lettre-type';
COMMENT ON COLUMN om_lettretype.id IS 'Identifiant de la lettre-type';
COMMENT ON COLUMN om_lettretype.libelle IS 'Libellé de la lettre-type';
COMMENT ON COLUMN om_lettretype.actif IS 'Défini si la lettre-type est active';
COMMENT ON COLUMN om_lettretype.orientation IS 'Défini l''orientation de la page';
COMMENT ON COLUMN om_lettretype.format IS 'Défini le format de la page';
COMMENT ON COLUMN om_lettretype.logo IS 'Défini le logo d''entête';
COMMENT ON COLUMN om_lettretype.logoleft IS 'Position du logo à gauche';
COMMENT ON COLUMN om_lettretype.logotop IS 'Position du logo en haut';
COMMENT ON COLUMN om_lettretype.titre_om_htmletat IS 'Bloc de titre contenant un éditeur de texte riche';
COMMENT ON COLUMN om_lettretype.titreleft IS 'Position du titre à gauche';
COMMENT ON COLUMN om_lettretype.titretop IS 'Position du titre en haut';
COMMENT ON COLUMN om_lettretype.titrelargeur IS 'Largeur du titre';
COMMENT ON COLUMN om_lettretype.titrehauteur IS 'Hauteur du titre';
COMMENT ON COLUMN om_lettretype.titrebordure IS 'Défini si les bordures du titre sont affichées';
COMMENT ON COLUMN om_lettretype.corps_om_htmletatex IS 'Bloc de corps contenant un éditeur de texte riche';
COMMENT ON COLUMN om_lettretype.om_sql IS 'Identifiant de la requête permettant de récupérer les champs de fusion de la lettre-type';
COMMENT ON COLUMN om_lettretype.se_font IS 'Police du texte des sous-états';
COMMENT ON COLUMN om_lettretype.se_couleurtexte IS 'Couleur du texte des sous-états';

-- om_logo
COMMENT ON TABLE om_logo IS 'Paramétrage des logos de lettre-types et états';
COMMENT ON COLUMN om_logo.om_logo IS 'Identifiant unique';
COMMENT ON COLUMN om_logo.id IS 'Identifiant du logo';
COMMENT ON COLUMN om_logo.libelle IS 'Libellé du logo';
COMMENT ON COLUMN om_logo.description IS 'Description du logo';
COMMENT ON COLUMN om_logo.fichier IS 'Fichier de l''image';
COMMENT ON COLUMN om_logo.resolution IS 'Résolution de l''image';
COMMENT ON COLUMN om_logo.actif IS 'Défini si le logo est utilisable dans les éditions';
COMMENT ON COLUMN om_logo.om_collectivite IS 'Identifiant de la collectivité liée au logo';

-- om_parametre
COMMENT ON TABLE om_parametre IS 'Paramétrage de l''application';
COMMENT ON COLUMN om_parametre.om_parametre IS 'Identifiant unique';
COMMENT ON COLUMN om_parametre.libelle IS 'Libellé du paramètre';
COMMENT ON COLUMN om_parametre.valeur IS 'Valeur du paramètre';
COMMENT ON COLUMN om_parametre.om_collectivite IS 'Collectivité utilisant le paramètre';

-- om_profil
COMMENT ON TABLE om_profil IS 'Type de profil des utilisateurs';
COMMENT ON COLUMN om_profil.om_profil IS  'Identifiant unique';
COMMENT ON COLUMN om_profil.libelle IS 'Libellé du profil';
COMMENT ON COLUMN om_profil.hierarchie IS 'Permet de rendre hiérarchique certains profils';

-- om_requete
COMMENT ON TABLE om_requete IS 'Paramétrage des requêtes utilisées par les lettre-types et les états';
COMMENT ON COLUMN om_requete.om_requete IS 'Identifiant unique';
COMMENT ON COLUMN om_requete.code IS 'Code de la requête';
COMMENT ON COLUMN om_requete.libelle IS 'Libellé de la requête';
COMMENT ON COLUMN om_requete.description IS 'Description de la requête';
COMMENT ON COLUMN om_requete.requete IS 'Requête SQL';
COMMENT ON COLUMN om_requete.merge_fields IS 'Champs de fusion';

-- om_sig_map
COMMENT ON TABLE om_sig_map IS 'Table utile au SIG interne';

-- om_sig_map_comp
COMMENT ON TABLE om_sig_map_comp IS 'Table utile au SIG interne';

-- om_sig_map_wms
COMMENT ON TABLE om_sig_map_wms IS 'Table utile au SIG interne';

-- om_sig_wms
COMMENT ON TABLE om_sig_wms IS 'Table utile au SIG interne';

-- om_sousetat
COMMENT ON TABLE om_sousetat IS 'Types de profil des utilisateurs';
COMMENT ON COLUMN om_sousetat.om_sousetat IS 'Identifiant unique';
COMMENT ON COLUMN om_sousetat.om_collectivite IS 'Identifiant de la collectivité liée à la lettre-type';
COMMENT ON COLUMN om_sousetat.id IS 'Identifiant du sous-état';
COMMENT ON COLUMN om_sousetat.libelle IS 'Libellé du sous-état';
COMMENT ON COLUMN om_sousetat.actif IS 'Défini si le sous-état est utilisable';
COMMENT ON COLUMN om_sousetat.titre IS 'Titre affiché dans le sous-état';
COMMENT ON COLUMN om_sousetat.titrehauteur IS 'Hauteur du titre en cm';
COMMENT ON COLUMN om_sousetat.titrefont IS 'Font du texte du titre';
COMMENT ON COLUMN om_sousetat.titreattribut IS 'Attribut du texte du titre (italique, souligné, gras)';
COMMENT ON COLUMN om_sousetat.titretaille IS 'Taille du texte du titre';
COMMENT ON COLUMN om_sousetat.titrebordure IS 'Affiche ou non les bordures sur le titre';
COMMENT ON COLUMN om_sousetat.titrealign IS 'Alignement du texte du titre';
COMMENT ON COLUMN om_sousetat.titrefond IS 'Affiche ou non une couleur de fond au titre';
COMMENT ON COLUMN om_sousetat.titrefondcouleur IS 'Couleur de fond du titre';
COMMENT ON COLUMN om_sousetat.titretextecouleur IS 'Couleur du texte du titre';
COMMENT ON COLUMN om_sousetat.intervalle_debut IS 'Début du titre';
COMMENT ON COLUMN om_sousetat.intervalle_fin IS 'Fin du titre';
COMMENT ON COLUMN om_sousetat.entete_flag IS 'Défini si le tableau contient une ligne d''entête';
COMMENT ON COLUMN om_sousetat.entete_fond IS 'Défini si l''entête du tableau à une couleur de fond';
COMMENT ON COLUMN om_sousetat.entete_orientation IS 'Orientation du texte dans les entêtes';
COMMENT ON COLUMN om_sousetat.entete_hauteur IS 'Hauteur de la ligne d''entête';
COMMENT ON COLUMN om_sousetat.entetecolone_bordure IS 'Affichage ou non de chaque bordure des cellules d''entête';
COMMENT ON COLUMN om_sousetat.entetecolone_align IS 'Alignement du texte dans chaque cellule d''entête';
COMMENT ON COLUMN om_sousetat.entete_fondcouleur IS 'Couleur de fond de l''entête';
COMMENT ON COLUMN om_sousetat.entete_textecouleur IS 'Couleur du texte de l''entête';
COMMENT ON COLUMN om_sousetat.tableau_largeur IS 'Largeur du tableau';
COMMENT ON COLUMN om_sousetat.tableau_bordure IS 'Défini si on affiche les bordures du tableau';
COMMENT ON COLUMN om_sousetat.tableau_fontaille IS 'Taille du texte du tableau';
COMMENT ON COLUMN om_sousetat.bordure_couleur IS 'Couleur des bordures du tableau';
COMMENT ON COLUMN om_sousetat.se_fond1 IS 'Couleur de fond du tableau';
COMMENT ON COLUMN om_sousetat.se_fond2 IS 'Seconde couleur de fond du tableau';
COMMENT ON COLUMN om_sousetat.cellule_fond IS 'Défini si les cellules du tableau ont une couleur de fond';
COMMENT ON COLUMN om_sousetat.cellule_hauteur IS 'Hauteur des cellules';
COMMENT ON COLUMN om_sousetat.cellule_largeur IS 'Largeur des cellules';
COMMENT ON COLUMN om_sousetat.cellule_bordure_un IS 'Bordure des cellules';
COMMENT ON COLUMN om_sousetat.cellule_bordure IS 'Bordure des cellules';
COMMENT ON COLUMN om_sousetat.cellule_align IS 'Alignement du texte dans chaque cellule';
COMMENT ON COLUMN om_sousetat.cellule_fond_total IS 'Défini si la ligne des totaux a une couleur de fond';
COMMENT ON COLUMN om_sousetat.cellule_fontaille_total IS 'Taille du texte de la ligne des totaux';
COMMENT ON COLUMN om_sousetat.cellule_hauteur_total IS 'Hauteur de la ligne des totaux';
COMMENT ON COLUMN om_sousetat.cellule_fondcouleur_total IS 'Couleur de fond de la ligne des totaux';
COMMENT ON COLUMN om_sousetat.cellule_bordure_total IS 'Défini les bordures de la ligne des totaux';
COMMENT ON COLUMN om_sousetat.cellule_align_total IS 'Alignement du texte de la ligne des totaux';
COMMENT ON COLUMN om_sousetat.cellule_fond_moyenne IS 'Défini si la ligne des moyennes contient une couleur de fond';
COMMENT ON COLUMN om_sousetat.cellule_fontaille_moyenne IS 'Taille du texte de la ligne des moyennes';
COMMENT ON COLUMN om_sousetat.cellule_hauteur_moyenne IS 'Hauteur de la ligne des moyennes';
COMMENT ON COLUMN om_sousetat.cellule_fondcouleur_moyenne IS 'Couleur de fond de la ligne des moyennes';
COMMENT ON COLUMN om_sousetat.cellule_bordure_moyenne IS 'Défini les bordures de la ligne des moyennes';
COMMENT ON COLUMN om_sousetat.cellule_align_moyenne IS 'Alignement du texte de la ligne des moyennes';
COMMENT ON COLUMN om_sousetat.cellule_fond_nbr IS 'Defini si une couleur de fond du compte de ligne est affichée';
COMMENT ON COLUMN om_sousetat.cellule_fontaille_nbr IS 'Taille du texte du compte de lignes';
COMMENT ON COLUMN om_sousetat.cellule_hauteur_nbr IS 'Hauteur du compte de nombre de lignes';
COMMENT ON COLUMN om_sousetat.cellule_fondcouleur_nbr IS 'Couleur de fond du compte de nombre de lignes';
COMMENT ON COLUMN om_sousetat.cellule_bordure_nbr IS 'Défini les bordures du compte de lignes';
COMMENT ON COLUMN om_sousetat.cellule_align_nbr IS 'Alignement du texte du compte de lignes';
COMMENT ON COLUMN om_sousetat.cellule_numerique IS 'Formatage du texte de chaque cellule du tableau';
COMMENT ON COLUMN om_sousetat.cellule_total IS 'Formatage du texte de chaque cellule des totaux';
COMMENT ON COLUMN om_sousetat.cellule_moyenne IS 'Formatage du texte de chaque cellule des moyennes';
COMMENT ON COLUMN om_sousetat.cellule_compteur IS 'Formatage du texte de chaque cellule du compteur';
COMMENT ON COLUMN om_sousetat.om_sql IS 'Requête SQL permettant de récupérer les données à afficher';

-- om_utilisateur
COMMENT ON TABLE om_utilisateur IS 'Utilisateurs';
COMMENT ON COLUMN om_utilisateur.om_utilisateur IS 'Identifiant unique';
COMMENT ON COLUMN om_utilisateur.nom IS 'Nom de l''utilisateur';
COMMENT ON COLUMN om_utilisateur.email IS 'Mail de l''utilisateur';
COMMENT ON COLUMN om_utilisateur.login IS 'Identifiant de l''utilisateur';
COMMENT ON COLUMN om_utilisateur.pwd IS 'Mot de passe de l''utilisateur';
COMMENT ON COLUMN om_utilisateur.om_collectivite IS 'Collectivité de l''utilisateur';
COMMENT ON COLUMN om_utilisateur.om_type IS 'Type de l''utilisateur (LDAP = récupéré depuis un LDAP, DB = crée depuis l''application)';
COMMENT ON COLUMN om_utilisateur.om_profil IS 'Profil de l''utilisateur';

-- om_widget
COMMENT ON TABLE om_widget IS 'Widgets pour les tableaux de bord des profils';
COMMENT ON COLUMN om_widget.om_widget IS 'Identifiant unique';
COMMENT ON COLUMN om_widget.libelle IS 'Libellé du widget';
COMMENT ON COLUMN om_widget.lien IS 'Lien qui pointe vers le widget (peut être vers une URL ou un fichier)';
COMMENT ON COLUMN om_widget.texte IS 'Texte affiché dans le widget';
COMMENT ON COLUMN om_widget.type IS 'Type du widget (''web'' si pointe vers une URL ou ''file'' si pointe vers un fichier)';



--------------------------------------------------------------------------------
-- Script de mise à jour vers la version v4.5.0-a2-dev
--
-- Ce fichier devra être mergé avec les scripts de mise à jour de version
--
-- @package openmairie_exemple
-- @version SVN : $Id$
-------------------------------------------------------------------------------

----
-- BEGIN  / OM REQUETE
--
-- Ajout de champs pour la gestion objet,
-- dont un not null d'où gestion de l'existant.
-- Puis ajout nouvelle requête objet.
----

-- Modification de la structure
ALTER TABLE om_requete
    ADD type character varying(200) NULL,
    ADD classe character varying(200) NULL,
    ADD methode character varying(200) NULL;
COMMENT ON COLUMN om_requete.type IS 'Requête SQL ou objet ?';
COMMENT ON COLUMN om_requete.classe IS 'Nom de(s) la classe(s) contenant la méthode';
COMMENT ON COLUMN om_requete.methode IS 'Méthode (de la première classe si plusieurs définies) fournissant les champs de fusion. Si non spécifiée appel à une méthode générique';
-- Modification des données pour respect de la nouvelle contrainte
UPDATE om_requete SET type = 'sql';
-- Ajout de la nouvelle contrainte
ALTER TABLE om_requete
    ALTER type SET NOT NULL;

----
-- END  / OM REQUETE
----



--
-- START / Mise à jour framework - permissions
--

-- XXX rajouter les commentaaires
CREATE TABLE om_permission (
    om_permission integer NOT NULL,
    libelle character varying(100) NOT NULL,
    type character varying(100) NOT NULL
);
CREATE SEQUENCE om_permission_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE om_permission_seq OWNED BY om_permission.om_permission;


ALTER TABLE ONLY om_permission
    ADD CONSTRAINT om_permission_pkey PRIMARY KEY (om_permission);

--
-- END / Mise à jour framework - permissions
--
