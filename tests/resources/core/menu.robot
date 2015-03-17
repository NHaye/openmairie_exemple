*** Settings ***
Documentation     Actions menu

*** Keywords ***
Click On Menu Rubrik
    [Arguments]    ${rubrikclass}
    Click Element    css=#menu ul#menu-list li.rubrik h3 > a.${rubrikclass}-20
    Sleep    1
    Page Should Not Contain Errors

Open Menu
    [Arguments]    ${rubrikclass}
    Go To Dashboard
    Click On Menu Rubrik    ${rubrikclass}

Submenu In Menu Should Be Selected
    [Arguments]    ${rubrikclass}    ${elemclass}
    Element Should Be Visible    css=#menu ul#menu-list li.rubrik ul.rubrik li.elem.ui-state-focus.${elemclass}

Menu Should Contain Submenu
    [Arguments]    ${elemclass}
    Page Should Contain Element    css=#menu-list a.${elemclass}-16

Menu Should Not Contain Submenu
    [Arguments]    ${elemclass}
    Page Should Not Contain Element    css=#menu-list a.${elemclass}-16

Page Should Contain Menu
    [Arguments]    ${rubrikclass}
    Page Should Contain Element    css=#menu-list a.${rubrikclass}-20

Page Should Not Contain Menu
    [Arguments]    ${rubrikclass}
    Page Should Not Contain Element    css=#menu-list a.${rubrikclass}-20

Go To Submenu
    [Arguments]    ${elemclass}
    Click Element    css=#menu ul#menu-list li.rubrik ul.rubrik li.elem a.${elemclass}-16
    Sleep    1
    Page Should Not Contain Errors

Go To Submenu In Menu
    [Arguments]    ${rubrikclass}    ${elemclass}
    Click On Menu Rubrik    ${rubrikclass}
    Go To Submenu    ${elemclass}
    Submenu In Menu Should Be Selected    ${rubrikclass}    ${elemclass}