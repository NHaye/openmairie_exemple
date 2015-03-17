*** Settings ***
Documentation     A test suite with a single test for valid login.
...
...               This test has a workflow that is created using keywords in
...               the imported resource file.
# On inclut les mots-clefs
Resource    resources/resources.robot

*** Test Cases ***
Valid Login
    Ouvrir le navigateur
    Go To Login Page
    Input Username    admin
    Input Password    admin
    Click Button    login.action.connect
    Wait Until Element Is Visible    css=#actions a.actions-logout
    Element Should Contain    css=#actions a.actions-logout    Déconnexion
    Valid Message Should Be    Votre session est maintenant ouverte.
    [Teardown]    Close Browser

Unvalid Login
    Ouvrir le navigateur
    Go To Login Page
    Input Username    admin
    Input Password    plop
    Click Button    login.action.connect
    Error Message Should Be    Votre identifiant ou votre mot de passe est incorrect.
    [Teardown]    Close Browser

Logout
    Ouvrir le navigateur et s'authentifier    admin    admin
    Se déconnecter
    [Teardown]    Close Browser
