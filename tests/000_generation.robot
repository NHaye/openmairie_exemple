*** Settings ***
Documentation    Le 'Framework' de l'application permet de générer
...    automatiquement certains scripts en fonction du modèle de données. Lors
...    du développement la règle est la suivante : toute modification du
...    modèle de données doit entrainer une regénération complète de tous les
...    scripts. Pour vérifier à chaque modification du code que la règle a bien
...    été respectée, ce 'Test Suite' permet de lancer une génération complète.
...    Si un fichier est généré alors le test doit échoué.
# On inclut les mots-clefs
Resource    resources/resources.robot
# On ouvre et on ferme le navigateur respectivement au début et à la fin
# du Test Suite.
Suite Setup    For Suite Setup
Suite Teardown    For Suite Teardown
# A chaque début de Test Case on se positionne sur le tableau bord administrateur
Test Setup    Depuis la page d'accueil    admin    admin


*** Test Cases ***
Génération complète

    Générer tout

