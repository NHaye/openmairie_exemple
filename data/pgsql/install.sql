--------------------------------------------------------------------------------
-- Script d'installation
--
-- ATTENTION ce script peut supprimer des données de votre base de données
-- il n'est à utiliser qu'en connaissance de cause
--
-- @package openmairie_exemple
-- @version SVN : $Id: install.sql 3076 2015-03-02 07:44:13Z fmichon $
--------------------------------------------------------------------------------

-- Usage :
-- cd data/pgsql/
-- dropdb openexemple && createdb openexemple && psql openexemple -f install.sql

--
START TRANSACTION;

-- Initialisation de postgis : A CHANGER selon les configurations
-- A commenter/décommenter pour initialiser postgis
-- --> postgis 1.5 / postgresql 9.1
--\i /usr/share/postgresql/9.1/contrib/postgis-1.5/postgis.sql
--\i /usr/share/postgresql/9.1/contrib/postgis-1.5/spatial_ref_sys.sql
-- --> postgis 2
CREATE EXTENSION IF NOT EXISTS postgis;

-- Suppression, Création et Utilisation du schéma
DROP SCHEMA IF EXISTS openexemple CASCADE;
CREATE SCHEMA openexemple;
SET search_path = openexemple, public, pg_catalog;

-- Instructions de base du framework openmairie
\i init.sql

-- Instructions de base de l'applicatif 
\i init_metier.sql

-- Instructions pour l'utilisation de postgis
\i init_metier_sig.sql

-- Initialisation du paramétrage
\i init_parametrage.sql
\i init_permissions.sql

-- Initialisation d'un jeu de données
-- A commenter/décommenter pour installer un jeu de données
\i init_data.sql

-- Mise à jour des séquences
\set schema '\'openexemple\''
\i update_sequences.sql

-- Mise à jour depuis la dernière version
-- A commenter/décommenter en cours de développement
\i v4.5.0.dev0.sql

-- Mise à jour des séquences
\set schema '\'openexemple\''
\i update_sequences.sql

--
COMMIT;

