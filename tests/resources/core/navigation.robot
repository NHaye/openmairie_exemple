*** Settings ***
Documentation     Actions navigation

*** Keywords ***
Depuis la page d'accueil
    [Arguments]    ${username}=null    ${password}=null
    [Documentation]    L'objet de ce 'Keyword' est de positionner l'utilisateur
    ...    sur la page de login ou son tableau de bord si on le fait se connecter.
    ...    De plus on vérifie qu'il n'y a qu'une seule fenetre d'ouverte.

    # On récupère le nombre de fenetres ouvertes
    ${listeFenetres} =    Get Window Titles
    ${nb_fenetres} =    Get Length    ${listeFenetres}
    # On ouvre la page d'accueil
    Go To    ${PROJECT_URL}
    Page Should Not Contain Errors
    # On teste si on est connecté
    Wait Until Element Is Visible    css=#title h2
    ${titre} =    Get Text    css=#title h2
    # Si tableau de bord, donc user déjà logué
    # on se reconnecte sauf si c'est l'user désiré qui est connecté
    # sinon on se connecte si spécifié
    Run Keyword If    '${titre}' == 'Tableau De Bord'    Reconnexion    ${username}    ${password}    ELSE IF    '${username}' != 'null' and '${password}' != 'null'    S'authentifier    ${username}    ${password}

Go To Login Page
    Go To    ${PROJECT_URL}
    Wait Until Element Is Visible    css=#title h2
    Element Text Should Be    css=#title h2    Veuillez Vous Connecter
    Title Should Be    ${TITLE}
    Page Should Not Contain Errors

Go To Dashboard
    Click Link    css=#logo h1 a.logo
    Page Title Should Be    Tableau De Bord
    Page Should Not Contain Errors

Go To Tab
    [Arguments]    ${obj}
    Go To    ${PROJECT_URL}scr/tab.php?obj=${obj}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Page Should Not Contain Errors

S'authentifier
    [Arguments]    ${username}=${ADMIN_USER}    ${password}=${ADMIN_PASSWORD}
    Input Username    ${username}
    Input Password    ${password}
    #
    Click Button    login.action.connect
    #
    Wait Until Element Is Visible    css=#actions a.actions-logout
    Element Should Contain    css=#actions a.actions-logout    Déconnexion
    #
    Valid Message Should Be    Votre session est maintenant ouverte.
    #
    Page Should Not Contain Errors

Se déconnecter
    Wait Until Element Is Visible    css=#title h2
    Element Text Should Be    css=#title h2    Tableau De Bord
    Click Link    css=#actions a.actions-logout
    Wait Until Element Is Visible    css=#title h2
    Element Text Should Be    css=#title h2    Veuillez Vous Connecter
    Page Should Not Contain Errors

Reconnexion
    [Arguments]    ${username}=null    ${password}=null
    ${connected_login} =    Get Text    css=#actions ul.actions-list li.action-login
    # On se déconnecte si user logué différent
    Run Keyword If   '${username}' != '${connected_login}'    Se déconnecter
    # On se reconnecte si user spécifié et différent du logué
    Run Keyword If   '${username}' != 'null' and '${password}' != 'null' and '${username}' != '${connected_login}'    S'authentifier    ${username}    ${password}

Ouvrir le navigateur
    [Arguments]    ${width}=1024    ${height}=768
    Open Browser    ${PROJECT_URL}    ${BROWSER}
    Set Window Size    ${width}    ${height}
    Sleep    1
    Set Selenium Speed    ${DELAY}
    Wait Until Element Is Visible    css=#title h2
    Element Text Should Be    css=#title h2    Veuillez Vous Connecter
    Title Should Be    ${TITLE}

Ouvrir le navigateur et s'authentifier
    [Arguments]    ${username}=${ADMIN_USER}    ${password}=${ADMIN_PASSWORD}
    Ouvrir le navigateur
    S'authentifier    ${username}    ${password}

Fermer le navigateur
    Close Browser

Page Title Should Be
    [Arguments]    ${messagetext}
    Wait Until Element Is Visible    css=#title h2
    Element Text Should Be    css=#title h2    ${messagetext}

Page Title Should Contain
    [Arguments]    ${messagetext}
    Wait Until Element Is Visible    css=#title h2
    Element Should Contain    css=#title h2    ${messagetext}

Page SubTitle Should Contain
    [Arguments]    ${subcontainer_id}    ${messagetext}
    Wait Until Element Is Visible    css=#${subcontainer_id} div.subtitle h3
    Element Should Contain    css=#${subcontainer_id} div.subtitle h3    ${messagetext}

Page SubTitle Should Be
    [Arguments]    ${messagetext}
    Wait Until Element Is Visible    css=div.subtitle h3
    Element Text Should Be    css=div.subtitle h3    ${messagetext}

Page Should Not Contain Errors
    Page Should Not Contain    Erreur de base de données.
    Page Should Not Contain    Fatal error
    Page Should Not Contain    Parse error
    Page Should Not Contain    Notice
    Page Should Not Contain    Warning

Depuis l'import
    [Arguments]    ${obj}
    Go To    ${PROJECT_URL}scr/import.php?obj=${obj}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Page Should Not Contain Errors

Open PDF
    [Arguments]    ${window}
    Select Window    ${window}.php
    Page Should Not Contain Errors

Previous Page PDF
    [Documentation]    Spécifique à la visionneuse de firefox
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Element    css=#previous

Next Page PDF
    [Documentation]    Spécifique à la visionneuse de firefox
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Element    css=#next

Close PDF
    Close Window
    Select Window

PDF Pages Number Should Be
    [Arguments]    ${total}
    [Documentation]    Spécifique à la visionneuse de firefox
    ${over} =    Convert to Integer    ${total}
    ${over} =    Evaluate    ${over}+1
    Page Should Contain Element    css=#pageContainer${total}
    Page Should Not Contain Element    css=#pageContainer${over}

L'onglet doit être présent
    [Documentation]
    [Arguments]    ${id}=null    ${libelle}=null

    #
    ${locator} =    Catenate    SEPARATOR=    css=#formulaire ul.ui-tabs-nav li a#    ${id}
    #
    Element Text Should Be    ${locator}    ${libelle}


L'onglet doit être sélectionné
    [Documentation]
    [Arguments]    ${id}=null    ${libelle}=null

    #
    ${locator} =    Catenate    SEPARATOR=    css=#formulaire ul.ui-tabs-nav li.ui-tabs-selected a#    ${id}
    #
    Element Text Should Be    ${locator}    ${libelle}


On clique sur l'onglet
    [Documentation]
    [Arguments]    ${id}=null    ${libelle}=null

    #
    ${locator} =    Catenate    SEPARATOR=    css=#formulaire ul.ui-tabs-nav li a#    ${id}
    #
    L'onglet doit être présent    ${id}    ${libelle}
    #
    Click Element    ${locator}
    #
    L'onglet doit être sélectionné    ${id}    ${libelle}
    #
    Sleep    1
    #
    Page Should Not Contain Errors