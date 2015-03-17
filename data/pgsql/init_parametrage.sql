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
-- Data for Name: om_collectivite; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_collectivite (om_collectivite, libelle, niveau) VALUES (1, 'LIBREVILLE', '1');
INSERT INTO om_collectivite (om_collectivite, libelle, niveau) VALUES (2, 'MULTI', '2');


--
-- Name: om_collectivite_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_collectivite_seq', 3, false);


--
-- Data for Name: om_profil; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_profil (om_profil, libelle, hierarchie) VALUES (1, 'ADMINISTRATEUR', 5);
INSERT INTO om_profil (om_profil, libelle, hierarchie) VALUES (2, 'SUPER UTILISATEUR', 4);
INSERT INTO om_profil (om_profil, libelle, hierarchie) VALUES (3, 'UTILISATEUR', 3);
INSERT INTO om_profil (om_profil, libelle, hierarchie) VALUES (4, 'UTILISATEUR LIMITE', 2);
INSERT INTO om_profil (om_profil, libelle, hierarchie) VALUES (5, 'CONSULTATION', 1);


--
-- Data for Name: om_droit; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (1, 'om_utilisateur', 1);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (2, 'om_droit', 1);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (3, 'om_profil', 1);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (4, 'om_collectivite', 1);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (5, 'om_parametre', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (6, 'om_etat', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (7, 'om_sousetat', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (8, 'om_lettretype', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (9, 'gen', 1);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (10, 'om_sig_map', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (11, 'om_sig_map_comp', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (12, 'om_sig_map_wms', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (13, 'om_sig_wms', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (14, 'directory', 1);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (15, 'import', 1);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (16, 'edition', 3);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (17, 'reqmo', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (18, 'password', 5);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (19, 'om_widget', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (20, 'om_tdb', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (21, 'om_requete', 1);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (22, 'om_logo', 2);
INSERT INTO om_droit (om_droit, libelle, om_profil) VALUES (23, 'om_dashboard', 1);


--
-- Name: om_droit_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_droit_seq', 24, false);


--
-- Data for Name: om_parametre; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_parametre (om_parametre, libelle, valeur, om_collectivite) VALUES (1, 'ville', 'LIBREVILLE', 1);
INSERT INTO om_parametre (om_parametre, libelle, valeur, om_collectivite) VALUES (2, 'ville', 'MULTI', 2);


--
-- Name: om_parametre_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_parametre_seq', 3, false);


--
-- Name: om_profil_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_profil_seq', 6, false);


--
-- Data for Name: om_utilisateur; Type: TABLE DATA; Schema: openexemple; Owner: -
--

INSERT INTO om_utilisateur (om_utilisateur, nom, email, login, pwd, om_collectivite, om_type, om_profil) VALUES (1, 'Administrateur', 'contact@openmairie.org', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 'DB', 1);


--
-- Name: om_utilisateur_seq; Type: SEQUENCE SET; Schema: openexemple; Owner: -
--

SELECT pg_catalog.setval('om_utilisateur_seq', 2, false);


--
-- PostgreSQL database dump complete
--

