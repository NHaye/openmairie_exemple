*** Settings ***
Documentation     Ressources (librairies, ressources, variables et keywords)

# Librairies
Library           Selenium2Library
Library           String
Library           RequestsLibrary
Library           Collections

# Mots-clefs Framework
Resource          formulaire.robot
Resource          menu.robot
Resource          navigation.robot
Resource          tableau.robot
Resource          utils.robot

# Mots-clefs objet Framework
Resource          om_droit.robot
Resource          om_profil.robot