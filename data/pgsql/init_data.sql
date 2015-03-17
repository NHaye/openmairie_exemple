--
-- PostgreSQL database dump
--

-- SET statement_timeout = 0;
-- SET client_encoding = 'UTF8';
-- SET standard_conforming_strings = on;
-- SET check_function_bodies = false;
-- SET client_min_messages = warning;

-- SET search_path = openexemple, pg_catalog;

--
-- Data for Name: om_requete; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_requete (om_requete, code, libelle, description, requete, merge_fields, type, classe, methode) VALUES (1, '-', 'Requête SQL', NULL, 'select nom,login,om_collectivite.libelle as collectivite from &DB_PREFIXEom_utilisateur inner join &DB_PREFIXEom_collectivite on om_collectivite.om_collectivite=om_utilisateur.om_collectivite where om_utilisateur= &idx', NULL, 'sql', NULL, NULL);
INSERT INTO om_requete (om_requete, code, libelle, description, requete, merge_fields, type, classe, methode) VALUES (2, '-', 'Requête SQL', NULL, 'select om_collectivite.om_collectivite as om_collectivite,om_collectivite.libelle as libelle,om_collectivite.niveau as niveau from &DB_PREFIXEom_collectivite where om_collectivite.om_collectivite=''&idx''', NULL, 'sql', NULL, NULL);


--
-- Data for Name: om_etat; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_etat (om_etat, om_collectivite, id, libelle, actif, orientation, format, logo, logoleft, logotop, titre_om_htmletat, titreleft, titretop, titrelargeur, titrehauteur, titrebordure, corps_om_htmletatex, om_sql, se_font, se_couleurtexte, margeleft, margetop, margeright, margebottom) VALUES (1, 1, 'om_collectivite', 'om_collectivite gen le 12/11/2010', true, 'P', 'A4', 'logopdf.png', 58, 7, '<p style=''text-align: center;''><span style=''font-size: 15px;''><span style=''font-family: helvetica;''><span style=''font-weight: bold;''>le&nbsp;&amp;aujourdhui</span></span></span></p>', 41, 36, 130, 10, '0', '<p style=''text-align: justify;''><span style=''font-size: 10px;''><span style=''font-family: helvetica;''>[om_collectivite]<br />[libelle]<br />[niveau]</span></span></p>
<p><br /><span id=''om_parametre.om_collectivite'' class=''mce_sousetat''>om_parametre.om_collectivite</span></p>', 2, 'helvetica', '0-0-0', 10, 10, 10, 10);


--
-- Name: om_etat_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_etat_seq', 2, false);


--
-- Data for Name: om_lettretype; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_lettretype (om_lettretype, om_collectivite, id, libelle, actif, orientation, format, logo, logoleft, logotop, titre_om_htmletat, titreleft, titretop, titrelargeur, titrehauteur, titrebordure, corps_om_htmletatex, om_sql, margeleft, margetop, margeright, margebottom, se_font, se_couleurtexte) VALUES (1, 1, 'om_utilisateur', 'lettre aux utilisateurs', true, 'P', 'A4', 'logopdf.png', 10, 10, '<p style="text-align: left;"><span style="font-size: 14px;"><span style="font-family: arial;">le&nbsp;&datecourrier</span></span></p>', 130, 16, 0, 10, '0', '<p style="text-align: justify;"><span style="font-size: 10px;"><span style="font-family: times;">Nous&nbsp;avons&nbsp;le&nbsp;plaisir&nbsp;de&nbsp;vous&nbsp;envoyer&nbsp;votre&nbsp;login&nbsp;et&nbsp;votre&nbsp;mot&nbsp;de&nbsp;passevotre&nbsp;login&nbsp;[login]&nbsp;Vous&nbsp;souhaitant&nbsp;bonne&nbsp;receptionVotre&nbsp;administrateur</span></span></p>', 1, 10, 10, 10, 10, NULL, NULL);


--
-- Name: om_lettretype_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_lettretype_seq', 2, false);


--
-- Data for Name: om_logo; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_logo (om_logo, id, libelle, description, fichier, resolution, actif, om_collectivite) VALUES (1, 'logopdf.png', 'logopdf.png', NULL, 'b449b5fae2367bf41ccee5cf974de989', NULL, true, 1);


--
-- Name: om_logo_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_logo_seq', 2, false);


--
-- Name: om_requete_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_requete_seq', 3, false);


--
-- Data for Name: om_sig_map; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_sig_map (om_sig_map, om_collectivite, id, libelle, actif, zoom, fond_osm, fond_bing, fond_sat, layer_info, etendue, projection_externe, url, om_sql, maj, table_update, champ, retour, type_geometrie, lib_geometrie) VALUES (1, 1, 'om_utilisateur', 'om_utilisateur', false, '6', 'Oui', '', '', '', '4.5868,43.6518,4.6738,43.7018', 'EPSG:2154', 'x', 'x', 'Oui', 'om_utilisateur', 'geom', '../scr/tab.php?objet=om_utilisateur', 'point', 'om_utilisateur');


--
-- Data for Name: om_sig_map_comp; Type: TABLE DATA; Schema: openexemple; Owner: -
--



--
-- Name: om_sig_map_comp_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_sig_map_comp_seq', 1, false);


--
-- Name: om_sig_map_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_sig_map_seq', 2, false);


--
-- Data for Name: om_sig_wms; Type: TABLE DATA; Schema: openexemple; Owner: -
--



--
-- Data for Name: om_sig_map_wms; Type: TABLE DATA; Schema: openexemple; Owner: -
--



--
-- Name: om_sig_map_wms_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_sig_map_wms_seq', 1, false);


--
-- Name: om_sig_wms_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_sig_wms_seq', 1, false);


--
-- Data for Name: om_sousetat; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_sousetat (om_sousetat, om_collectivite, id, libelle, actif, titre, titrehauteur, titrefont, titreattribut, titretaille, titrebordure, titrealign, titrefond, titrefondcouleur, titretextecouleur, intervalle_debut, intervalle_fin, entete_flag, entete_fond, entete_orientation, entete_hauteur, entetecolone_bordure, entetecolone_align, entete_fondcouleur, entete_textecouleur, tableau_largeur, tableau_bordure, tableau_fontaille, bordure_couleur, se_fond1, se_fond2, cellule_fond, cellule_hauteur, cellule_largeur, cellule_bordure_un, cellule_bordure, cellule_align, cellule_fond_total, cellule_fontaille_total, cellule_hauteur_total, cellule_fondcouleur_total, cellule_bordure_total, cellule_align_total, cellule_fond_moyenne, cellule_fontaille_moyenne, cellule_hauteur_moyenne, cellule_fondcouleur_moyenne, cellule_bordure_moyenne, cellule_align_moyenne, cellule_fond_nbr, cellule_fontaille_nbr, cellule_hauteur_nbr, cellule_fondcouleur_nbr, cellule_bordure_nbr, cellule_align_nbr, cellule_numerique, cellule_total, cellule_moyenne, cellule_compteur, om_sql) VALUES (1, 1, 'om_parametre.om_collectivite', 'gen le 12/11/2010', true, 'liste om_parametre', 10, 'helvetica', 'B', 10, '0', 'L', '0', '255-255-255', '0-0-0', 0, 5, '1', '1', '0|0|0', 7, 'TLB|TLB|LTBR', 'C|C|C', '255-255-255', '0-0-0', 195, '1', 10, '0-0-0', '243-246-246', '255-255-255', '1', 10, '65|65|65', 'TLB|TLB|LTBR', 'TLB|TLB|LTBR', 'C|C|C', '1', 10, 15, '255-255-255', 'TLB|TLB|LTBR', 'C|C|C', '1', 10, 5, '212-219-220', 'TLB|TLB|LTBR', 'C|C|C', '1', 10, 7, '255-255-255', 'TLB|TLB|LTBR', 'C|C|C', '999|999|999', '0|0|0', '0|0|0', '0|0|0', 'select om_parametre.om_parametre as om_parametre,om_parametre.libelle as libelle,om_parametre.valeur as valeur from &DB_PREFIXEom_parametre where om_parametre.om_collectivite=''&idx''');


--
-- Name: om_sousetat_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_sousetat_seq', 2, false);


--
-- PostgreSQL database dump complete
--

