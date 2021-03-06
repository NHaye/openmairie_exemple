*** Settings ***
Documentation     Actions spécifiques aux droits.

*** Keywords ***
Depuis le listing des droits

    [Documentation]    Permet d'accéder au listing des droits.

    # On se positionne sur le tableau de bord
    Go To Dashboard
    # On ouvre le menu
    Go To Tab    droit


Depuis le listing des droit du profil

    [Documentation]    Permet d'accéder au listing des droits depuis le
    ...    formulaire d'un profil.

    [Arguments]    ${om_profil}=null    ${om_profil_libelle}=null

    #
    Depuis le contexte du profil    ${om_profil}    ${om_profil_libelle}
    # On clique sur l'onglets des droits
    Click On Tab    om_droit    droit


Saisir le droit

    [Documentation]    Permet de remplir le formulaire om_droit.

    [Arguments]    ${libelle}    ${om_profil}=null

    # On saisit le libellé
    Input Text    css=#libelle    ${libelle}
    # On sélectionne le profil par son libellé
    Run Keyword If    '${om_profil}' != 'null'    Select From List By Label    css=#om_profil    ${om_profil}


Ajouter le droit depuis le menu

    [Documentation]    Permet d'ajouter un droit depuis le formulaire om_droit.

    [Arguments]    ${libelle}    ${om_profil}

    #
    Depuis le listing des droits
    # On clique sur l'action Ajouter
    Click On Add Button
    #
    Saisir le droit    ${libelle}    ${om_profil}
    # On valide le formulaire
    Click On Submit Button
    # On vérifie le message de validation
    Valid Message Should Contain    Vos modifications ont bien été enregistrées.


Ajouter le droit depuis le profil

    [Documentation]    Permet d'ajouter un droit sur un profil.

    [Arguments]    ${om_droit_libelle}    ${om_profil}=null    ${om_profil_libelle}=null

    #
    Depuis le listing des droit du profil    ${om_profil}    ${om_profil_libelle}
    # On clique sur l'action Ajouter
    Click On Add Button JS
    #
    Saisir le droit    ${om_droit_libelle}
    # On valide le formulaire
    Click On Submit Button
    # On vérifie le message de validation
    Valid Message Should Contain    Vos modifications ont bien été enregistrées.