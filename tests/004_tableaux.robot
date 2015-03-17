*** Settings ***
Documentation     TestCase "Tableaux"
# On inclut les mots-clefs
Resource    resources/resources.robot
# On ouvre et on ferme le navigateur respectivement au début et à la fin
# du Test Suite.
Suite Setup    For Suite Setup
Suite Teardown    For Suite Teardown
# A chaque début de Test Case on se positionne sur le tableau bord
# administrateur
Test Setup    Depuis la page d'accueil    admin    admin

*** Test Cases ***
Constitution d'un jeu de données

    [Documentation]    L'objet de ce 'Test Case' est de constituer un jeu de de
    ...    données cohérent pour les scénarios fonctionnels qui suivent.
    ...    Il faut que le listing soit affiche 15 résultats par page.

    ${profil}    Set Variable    PUBLIC
    Set Suite Variable    ${profil}
    Ajouter le profil depuis le menu    ${profil}    6
    # On ajoute 20 droits au profil
    :FOR    ${INDEX}    IN RANGE    0    20
    \    Ajouter le droit depuis le menu    public_${INDEX}    ${profil}


Pagination en formulaire

    [Documentation]    Vérifie la pagination sur un formulaire.

    Depuis le listing des droits
    # On récupère la plage d'enregistrement de la première page
    ${pagination_premiere_page} =    Get Pagination Text
    # On sélectionne la deuxième page
    Select Pagination    15
    # On vérifie que la page à changé avec la plage d'enregistrement
    Pagination Text Not Should Be    ${pagination_premiere_page}


Pagination en sous-formulaire

    [Documentation]    Vérifie la pagination sur un sous-formulaire.

    Depuis le listing des droit du profil    null    ${profil}
    # On récupère la plage d'enregistrement de la première page
    ${pagination_premiere_page} =    Get Pagination Text
    # On sélectionne la deuxième page
    Select Pagination    15
    # On vérifie que la page à changé avec la plage d'enregistrement
    Pagination Text Not Should Be    ${pagination_premiere_page}
