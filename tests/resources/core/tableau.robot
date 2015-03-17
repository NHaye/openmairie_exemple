*** Settings ***
Documentation     Actions dans un tableau

*** Keywords ***
Click On Search Button
    Click Element    css=#adv-search-submit
    Sleep    1
    Page Should Not Contain Errors

Click On Simple Search Button
    Click Element    css=div.tab-search form button
    Sleep    1
    Page Should Not Contain Errors

Elements From Column Should Be
    [Arguments]    ${column}    ${messagetext}
    Element Text Should Be    css=td.col-${column}    ${messagetext}

Elements From Column Should Contain
    [Arguments]    ${column}    ${messagetext}
    Element Should Contain    css=td.col-${column}    ${messagetext}

Click On Link
    [Arguments]    ${link}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Link      ${link}
    Sleep    1
    Page Should Not Contain Errors

Get Object In Tab
    ${url}    Get Location
    ${objInTab}    Fetch From Right    ${url}    obj=
    Set Suite Variable    ${objInTab}

Total Results Should Be Equal
    [Arguments]    ${totalAttendu}
    Get Object In Tab
    ${nombreResultats} =    Get Text    css=#tab-${objInTab} > .tab-pagination > .pagination-nb > span.pagination-text
    ${nombreResultats} =    Fetch From Right    ${nombreResultats}    sur
    Should Be Equal As Integers    ${nombreResultats}    ${totalAttendu}

Total Results In Subform Be Equal
    [Arguments]    ${totalAttendu}    ${obj}
    # Get Object In Tab
    ${nombreResultats} =    Get Text    css=#sousform-${obj} > .tab-pagination > .pagination-nb > span.pagination-text
    ${nombreResultats} =    Fetch From Right    ${nombreResultats}    sur
    Should Be Equal As Integers    ${nombreResultats}    ${totalAttendu}

Get Pagination Text
    [Documentation]    Permet de récupérer le nombre d'enregistrements par page.
    ${pagination_text} =    Get Text    css=div.tab-pagination div.pagination-nb span.pagination-text
    [return]    ${pagination_text}

Pagination Text Not Should Be
    [Documentation]    Permet de vérifier le nombre d'enregistrement d'une page.
    [Arguments]    ${pagination_expected}
    ${pagination_text} =    Get Pagination Text
    Should Not Be Equal    ${pagination_text}    ${pagination_expected}

Tab Should Not Contain Add Button
    Page Should Not Contain Element    css=#action-tab-om_collectivite-corner-ajouter

Use Simple Search
    [Arguments]    ${label_select}    ${search_text}
    # On sélectionne le champ sur lequel faire la recherche simple
    Select From List By Label    css=div.tab-search form select    ${label_select}
    # On saisie le texte recherché
    Input Text    css=div.tab-search form input    ${search_text}
    # On clique sur le bouton "Recherche"
    Click On Simple Search Button

L'action ajouter doit être disponible
    Page Should Contain Element    css=span.add-16

L'action ajouter ne doit pas être disponible
    Page Should Not Contain Element    css=span.add-16

Select Pagination
    [Documentation]    Permet de sélectionner une page du listing par la valeur.
    ...    Dépend du nombre d'enregistrement par page.
    [Arguments]    ${premier}
    # On sélectionne la page du listing
    Select From List By Value    css=div.tab-pagination div.pagination-select select    ${premier}
    # On vérifie qu'il n'y a pas d'erreur
    Page Should Not Contain Errors