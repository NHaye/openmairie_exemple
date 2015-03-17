--------------------------------------------------------------------------------
-- Mise à jour des permissions
--
-- Ce traitement permet de : 
-- - mettre à jour la table de vocabulaire des permissions avec les 
--   permissions de l'application "calculées" directement à partir du code,
-- - supprimer tous les éléments obsolètes de la table de matrice des 
--   droits.
--
-- @package openmairie_exemple
-- @version SVN : $Id$
--------------------------------------------------------------------------------

-- Suppression des permissions existantes dans om_permission ayant le type GEN
DELETE FROM om_permission WHERE lower(om_permission.type) = 'gen';

-- Insertion de toutes les nouvelles permissions dans la table om_permission
\i init_permissions.sql

-- Suppression des lignes dans la table om_droit dont le libellé n'existe pas
-- dans la table permission
DELETE FROM om_droit WHERE om_droit.libelle NOT IN (SELECT om_permission.libelle FROM om_permission);

