*** Settings ***
Documentation     TestCase "Actions"
# On inclut les mots-clefs
Resource    resources/resources.robot
# On ouvre et on ferme le navigateur respectivement au début et à la fin
# du Test Suite.
Suite Setup    For Suite Setup
Suite Teardown    For Suite Teardown
# A chaque début de Test Case on se positionne sur le tableau bord administrateur
Test Setup    Depuis la page d'accueil    admin    admin

*** Test Cases ***

Modifier (action-self) LettreType Dans Un Formulaire
    #
    Go To Submenu In Menu    parametrage    om_lettretype
    Page Should Not Contain Errors
    Page Title Should Be    Paramétrage > Lettre Type
    First Tab Title Should Be    lettre type
    Submenu In Menu Should Be Selected    paramétrage    om_lettretype
    #
    Click Element    css=#action-tab-om_lettretype-left-consulter-1
    Page Should Not Contain Errors
    Page Title Should Be    Paramétrage > Lettre Type > 1
    #
    Portlet Action Should Be In Form    om_lettretype    modifier
    Click On Form Portlet Action    om_lettretype    modifier
    #
    Click On Submit Button
    Valid Message Should Be    Vos modifications ont bien été enregistrées.
    Click On Back Button
    Page Title Should Be    Paramétrage > Lettre Type > 1


Modifier (action-self) LettreType Dans Un Sous Formulaire
    #
    Go To Submenu In Menu    administration    collectivite
    Page Should Not Contain Errors
    Page Title Should Be    Administration > Collectivité
    First Tab Title Should Be    collectivité
    Submenu In Menu Should Be Selected    administration    collectivite
    #
    Click Element    css=#action-tab-om_collectivite-left-consulter-1
    Page Should Not Contain Errors
    Page Title Should Be    Administration > Collectivité > 1
    #
    Click Element    css=#om_lettretype
    Page Should Not Contain Errors
    #
    Click Element    css=#action-soustab-om_lettretype-left-consulter-1
    Page Should Not Contain Errors
    #
    Portlet Action Should Be In SubForm    om_lettretype    modifier
    Click On SubForm Portlet Action    om_lettretype    modifier
    #
    Click On Submit Button In SubForm
    Valid Message Should Be    Vos modifications ont bien été enregistrées.
    Click On Back Button In Subform
    #
    Go To DashBoard


Supprimer (action-self) SousÉtat Dans Un Formulaire
    #
    Go To Submenu In Menu    parametrage    om_sousetat
    Page Should Not Contain Errors
    Page Title Should Be    Paramétrage > Sous État
    First Tab Title Should Be    sous état
    Submenu In Menu Should Be Selected    paramétrage    om_sousetat

    # Création d'un sous état pour le supprimer
    Click Element    css=#action-tab-om_sousetat-left-consulter-1
    Page Should Not Contain Errors
    Page Title Should Be    Paramétrage > Sous État > 1
    Click On Form Portlet Action    om_sousetat    copier
    #XXX
    Click Element    css=div.ui-dialog-buttonset button
    Sleep    3
    #
    Click On Back Button

    # Création d'un sous état pour le supprimer
    Click Element    css=#action-tab-om_sousetat-left-consulter-1
    Page Should Not Contain Errors
    Page Title Should Be    Paramétrage > Sous État > 1
    Click On Form Portlet Action    om_sousetat    copier
    #XXX
    Click Element    css=div.ui-dialog-buttonset button
    Sleep    3
    #
    Click On Back Button

    #
    Click Element    css=#action-tab-om_sousetat-left-consulter-2
    Page Should Not Contain Errors
    Page Title Should Be    Paramétrage > Sous État > 2
    #
    Portlet Action Should Be In Form    om_sousetat    supprimer
    Click On Form Portlet Action    om_sousetat    supprimer
    #
    Click On Submit Button
    Valid Message Should Be    La suppression a été correctement effectuée.
    Click On Back Button
    Page Title Should Be    Paramétrage > Sous État
    #
    Go To DashBoard


Supprimer (action-self) SousÉtat Dans Un Sous Formulaire
    #
    Go To Submenu In Menu    administration    collectivite
    Page Should Not Contain Errors
    Page Title Should Be    Administration > Collectivité
    First Tab Title Should Be    collectivité
    Submenu In Menu Should Be Selected    administration    collectivite
    #
    Click Element    css=#action-tab-om_collectivite-left-consulter-1
    Page Should Not Contain Errors
    Page Title Should Be    Administration > Collectivité > 1
    #
    Click Element    css=#om_sousetat
    Page Should Not Contain Errors
    #
    Click Element    css=#action-soustab-om_sousetat-left-consulter-3
    Page Should Not Contain Errors
    #
    Portlet Action Should Be In SubForm    om_sousetat    supprimer
    Click On SubForm Portlet Action    om_sousetat    supprimer
    #
    Click On Submit Button In SubForm
    Valid Message Should Be    La suppression a été correctement effectuée.
    Click On Back Button In Subform


Copier (action-direct-with-confirmation) État Dans Un Formulaire
    ${day} =    Get Time    day    NOW
    ${month} =    Get Time    month    NOW
    ${year} =    Get Time    year    NOW
    #
    Go To Submenu In Menu    parametrage    om_etat
    Page Should Not Contain Errors
    Page Title Should Be    Paramétrage > État
    First Tab Title Should Be    état
    Submenu In Menu Should Be Selected    paramétrage    om_etat
    #
    Click Element    css=#action-tab-om_etat-left-consulter-1
    Page Should Not Contain Errors
    Page Title Should Be    Paramétrage > État > 1
    #
    Portlet Action Should Be In Form    om_etat    copier
    Click On Form Portlet Action    om_etat    copier
    #XXX
    Click Element    css=div.ui-dialog-buttonset button
    Sleep    3
    #
    Page Title Should Be    Paramétrage > État > 1
    Valid Message Should Contain    L'element a ete correctement duplique.
    Click On Back Button
    Element Should Contain    css=table.tab-tab    copie du ${day}/${month}/${year}
    #
    Go To DashBoard


Copier (action-direct-with-confirmation) État Dans Un Sous Formulaire
    ${day} =    Get Time    day    NOW
    ${month} =    Get Time    month    NOW
    ${year} =    Get Time    year    NOW
    #
    Go To Submenu In Menu    administration    collectivite
    Page Should Not Contain Errors
    Page Title Should Be    Administration > Collectivité
    First Tab Title Should Be    collectivité
    Submenu In Menu Should Be Selected    administration    collectivite
    #
    Click Element    css=#action-tab-om_collectivite-left-consulter-1
    Page Should Not Contain Errors
    Page Title Should Be    Administration > Collectivité > 1
    #
    Click Element    css=#om_etat
    Page Should Not Contain Errors
    #
    Click Element    css=#action-soustab-om_etat-left-consulter-1
    Page Should Not Contain Errors
    #
    Portlet Action Should Be In SubForm    om_etat    copier
    Click On SubForm Portlet Action    om_etat    copier
    #XXX
    Click Element    css=div.ui-dialog-buttonset button
    Sleep    3
    #
    Valid Message Should Contain    L'element a ete correctement duplique.
    Click On Back Button In Subform
    Element Should Contain    css=table.tab-tab    copie du ${day}/${month}/${year}

