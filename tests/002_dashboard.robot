*** Settings ***
Documentation     TestCase "Dashboard"
# On inclut les mots-clefs
Resource    resources/resources.robot
# On ouvre et on ferme le navigateur respectivement au début et à la fin
# du Test Suite.
Suite Setup    For Suite Setup
Suite Teardown    For Suite Teardown
# A chaque début de Test Case on se positionne sur le tableau bord administrateur
Test Setup    Depuis la page d'accueil    admin    admin

*** Test Cases ***
Créer un nouveau widget
    Go To Submenu In Menu    administration    om_widget
    Page Title Should Be    Administration > Tableaux De Bord > Widget
    First Tab Title Should Be    widget
    Submenu In Menu Should Be Selected    administration    om_widget

    Click Element    css=#action-tab-om_widget-corner-ajouter
    Page Should Not Contain Errors
    Page Title Should Be    Administration > Tableaux De Bord > Widget
    First Tab Title Should Be    widget
    Submenu In Menu Should Be Selected    administration    om_widget

    Click Button    css=#tabs-1 form div.formControls input
    Page Should Not Contain Errors
    Error Message Should Contain    Le champ libellé est obligatoire
    Error Message Should Contain    SAISIE NON ENREGISTRÉE
    Page Title Should Be    Administration > Tableaux De Bord > Widget
    First Tab Title Should Be    widget
    Submenu In Menu Should Be Selected    administration    om_widget

    Element Text Should Be    css=#lib-lien    lien
    Element Text Should Be    css=#lib-texte    texte
    Select From List    css=#type    file
    Element Text Should Be    css=#lib-lien    script
    Element Text Should Be    css=#lib-texte    arguments
    Click Button    css=#tabs-1 form div.formControls input
    Page Should Not Contain Errors
    Error Message Should Contain    Le champ libellé est obligatoire
    Error Message Should Contain    Le script n'existe pas.
    Error Message Should Contain    SAISIE NON ENREGISTRÉE
    Select From List    css=#type    web
    Element Text Should Be    css=#lib-lien    lien
    Element Text Should Be    css=#lib-texte    texte

    Input Text    css=#libelle    widget a
    Input Text    css=#texte    Donec sed tristique lectus. Nullam blandit leo vitae lectus suscipit dignissim. Vestibulum adipiscing nisi vel tortor tempus dignissim ac a magna. Mauris vestibulum in orci in volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Aliquam malesuada purus aliquet iaculis hendrerit. Phasellus sagittis sed diam ac blandit. Proin molestie justo vel velit imperdiet, a congue sem egestas. Integer id nibh volutpat felis interdum pretium.
    Click Button    css=#tabs-1 form div.formControls input
    Page Should Not Contain Errors
    Valid Message Should Be    Vos modifications ont bien été enregistrées.

    Click On Back Button
    Page Title Should Be    Administration > Tableaux De Bord > Widget
    First Tab Title Should Be    widget
    Submenu In Menu Should Be Selected    administration    om_widget
    Table Should Contain    css=table.tab-tab    widget a

    Click Element    css=#action-tab-om_widget-corner-ajouter
    Page Should Not Contain Errors
    Input Text    css=#libelle    widget b
    Input Text    css=#texte    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer facilisis risus id turpis eleifend, sed facilisis lectus congue. Nulla mattis ultricies euismod. Praesent faucibus in ipsum at sodales. Maecenas lectus massa, dapibus ut tortor ac, viverra egestas mauris. Morbi mi elit, ullamcorper sed tincidunt nec, fermentum sed nisi. Mauris a feugiat nisl. Maecenas nunc lorem, vehicula eu fermentum non, ullamcorper sed eros. Phasellus porttitor massa nec nisi facilisis, non pulvinar enim ullamcorper. Cras ac ante luctus, fringilla enim sed, malesuada elit. Nunc ultricies, dui non sollicitudin accumsan, diam purus porttitor sem, rhoncus placerat ante quam vel nisl. Nam adipiscing mauris risus, id iaculis est volutpat eget. Curabitur tortor lacus, pharetra ultricies tristique eu, consequat et odio. Morbi vestibulum nec lorem quis luctus. Etiam non varius quam. Ut vehicula, neque vel blandit malesuada, nisi nunc dignissim odio, et pellentesque dolor augue ac ipsum.
    Click Button    css=#tabs-1 form div.formControls input
    Page Should Not Contain Errors
    Valid Message Should Be    Vos modifications ont bien été enregistrées.
    Click On Back Button
    Page Title Should Be    Administration > Tableaux De Bord > Widget
    First Tab Title Should Be    widget
    Submenu In Menu Should Be Selected    administration    om_widget
    Table Should Contain    css=table.tab-tab    widget b


Composer un tableau de bord
    Go To Submenu In Menu    administration    om_dashboard
    Page Title Should Be    Administration > Tableaux De Bord > Composition
    First Tab Title Should Be    composition
    Submenu In Menu Should Be Selected    administration    om_dashboard

    Select From List    css=#om_profil    1
    Wait Until Element Is Visible    css=.widget-add-action
    Click Element    css=.widget-add-action
    Wait Until Element Is Visible    css=#widget_add_form
    Select From List    name=widget    2
    Click Button    widget.add.form.valid
    Wait Until Element Is Visible    css=#widget_1
    Drag And Drop    css=#widget_1 div.widget-header-move    css=#column_3

    Go To DashBoard
    Element Text Should Be    css=#dashboard div.col3 #column_3 #widget_1 h3    widget b

