#! /bin/bash
##
# Ce script permet de générer les fichiers sql d'initialisation de la base de
# données pour permettre de publier une nouvelle version facilement
#
# @package openmairie_exemple
# @version SVN : $Id: make_init.sh 3076 2015-03-02 07:44:13Z fmichon $
##

schema="openexemple"
database="openexemple"

# Génération du fichier init.sql
sudo su postgres -c "pg_dump --column-inserts -s -O -n $schema -t $schema.om_* $database" > init.sql

# Génération du fichier init_metier.sql
sudo su postgres -c "pg_dump --column-inserts -s -O -n $schema -T $schema.om_* $database" > init_metier.sql

# Génération du fichier init_parametrage.sql
sudo su postgres -c "pg_dump --column-inserts -a -O -n $schema -t $schema.om_collectivite -t $schema.om_parametre -t $schema.om_profil -t $schema.om_droit -t $schema.om_utilisateur $database" > init_parametrage.sql

# Génération du fichier init_data.sql
sudo su postgres -c "pg_dump --column-inserts -a -O -n $schema -t $schema.om_logo -t $schema.om_requete -t $schema.om_sousetat -t $schema.om_etat -t $schema.om_lettretype -t $schema.om_sig_* $database" > init_data.sql

# Suppression du schéma
sed -i "s/CREATE SCHEMA $schema;/-- CREATE SCHEMA $schema;/g" init*.sql
sed -i "s/^SET/-- SET/g" init*.sql

