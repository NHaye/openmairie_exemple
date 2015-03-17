*** Settings ***
Documentation     Actions dans un formulaire

*** Keywords ***
Click On Add Button
    Click Element    css=span.add-16
    Page Should Not Contain Errors

Click On Add Button JS
    [Arguments]    ${time}=1
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Element    css=span.add-16
    Sleep    ${time}
    Page Should Not Contain Errors

Click On Submit Button
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Button    css=#formulaire form div.formControls input
    Sleep    1
    Page Should Not Contain Errors

Click On Submit Button In Subform
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Button    css=#sformulaire form div.formControls input
    Sleep    1
    Page Should Not Contain Errors

Click On Submit Button In Overlay
    [Arguments]    ${obj}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Button    css=#form-${obj}-overlay form div.formControls input
    Sleep    1
    Page Should Not Contain Errors

Click On Submit Button In Reqmo
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Button    css=#reqmo-form form div.formControls input
    Sleep    1
    Page Should Not Contain Errors

Click On Submit Button In Import CSV
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Button    css=#form-csv-import form div.formControls input
    Sleep    1
    Page Should Not Contain Errors

Click On Back Button
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Element    css=a.retour
    Sleep    1
    Page Should Not Contain Errors

Click On Back Button In Subform
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Element    css=#sformulaire a.retour
    Sleep    1
    Page Should Not Contain Errors

Click On Back Button In Overlay
    [Arguments]    ${obj}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Element    css=#form-${obj}-overlay form a.retour
    Sleep    1
    Page Should Not Contain Errors

Click On Form Tab
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Link      main
    Sleep    1
    Page Should Not Contain Errors

Click On Httpclick Element
    [Documentation]    Clique sur un champ de type httpclick.
    [Arguments]    ${element_id}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Element    css=.field-type-httpclick #${element_id}
    Sleep    1
    Page Should Not Contain Errors

Click On Tab
    [Documentation]    Clique sur l'onglet passé en paramètre.
    [Arguments]    ${tab}    ${tab_title}
    ${locator} =    Catenate    SEPARATOR=    css=#formulaire ul.ui-tabs-nav li a#    ${tab}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Tab Title Should Be    ${tab}    ${tab_title}
    Click Element    ${locator}
    Sleep    1
    Page Should Not Contain Errors

First Tab Title Should Be
    [Arguments]    ${messagetext}
    Element Text Should Be    css=#formulaire ul.ui-tabs-nav li a    ${messagetext}

Tab Title Should Be
    [Documentation]    Vérifie le titre de l'onglet.
    [Arguments]    ${tab}    ${tab_title}
    ${locator} =    Catenate    SEPARATOR=    css=#formulaire ul.ui-tabs-nav li a#    ${tab}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Element Text Should Be    ${locator}    ${tab_title}

Input Username
    [Arguments]    ${username}
    Input Text    login    ${username}

Input Password
    [Arguments]    ${password}
    Input Text    password    ${password}

Input HTML
    [Arguments]    ${field}    ${value}
    Select Frame    ${field}_ifr
    Focus    tinymce
    Input Text    tinymce    ${value}
    Select Window    title=${TITLE}

Numeric Value Should Be
    [Arguments]    ${champ}    ${valeurAttendue}
    ${valeurRecuperee} =    Get Text    css=#${champ}
    Should Be Equal As Integers    ${valeurAttendue}    ${valeurRecuperee}

Form Value Should Be
    [Arguments]    ${champ}    ${valeurAttendue}
    ${valeurRecuperee} =    Get Value    ${champ}
    Should Be Equal    ${valeurAttendue}    ${valeurRecuperee}

Form Field Attribute Should Be
    [Documentation]    Vérifie la valeur de l'attribut du champ.
    [Arguments]    ${champ}    ${attribute}    ${expected_value}
    ${get_value} =    Get Element Attribute    css=#${champ}@${attribute}
    Should Be Equal    ${expected_value}    ${get_value}

Numeric Static Value Should Be
    [Arguments]    ${champ}    ${valeurAttendue}
    ${valeurRecuperee} =    Get Text    ${champ}
    Should Be Equal As Integers    ${valeurAttendue}    ${valeurRecuperee}

Form Static Value Should Be
    [Arguments]    ${champ}    ${valeurAttendue}
    ${valeurRecuperee} =    Get Text    ${champ}
    Should Be Equal    ${valeurAttendue}    ${valeurRecuperee}

Selected List Label Should Be
    [Documentation]    Vérifie le libellé de l'option sélectionné.
    [Arguments]    ${field}    ${expected_value}
    ${fied_value} =    Get Selected List Label    ${field}
    Should Be Equal    ${expected_value}    ${fied_value}

Select List Should Be
    [Arguments]    ${champ}    ${listeAttendue}
    ${listeRecuperee} =    Get List Items    ${champ}
    Lists Should Be Equal    ${listeAttendue}    ${listeRecuperee}

Link Value Should Be
    [Documentation]    Vérifie le texte du lien.
    [Arguments]    ${field}    ${expected_value}
    ${get_value} =    Get Text    css=#link_${field}
    Should Be Equal    ${expected_value}    ${get_value}

Message Should Be
    [Arguments]    ${messagetext}
    Element Text Should Be    css=div.message p span.text   ${messagetext}

Error Message Should Contain
    [Arguments]    ${messagetext}
    Element Should Contain    css=div.message.ui-state-error p span.text   ${messagetext}

Error Message Should Be
    [Arguments]    ${messagetext}
    Element Text Should Be    css=div.message.ui-state-error p span.text   ${messagetext}

Valid Message Should Be
    [Arguments]    ${messagetext}
    Element Text Should Be    css=div.message.ui-state-valid p span.text   ${messagetext}

Valid Message Should Be In Subform
    [Arguments]    ${messagetext}
    Element Text Should Be    css=#sformulaire div.message.ui-state-valid p span.text   ${messagetext}

Valid Message Should Contain
    [Arguments]    ${messagetext}
    Element Should Contain    css=div.message.ui-state-valid p span.text   ${messagetext}

Valid Message Should Contain In Subform
    [Arguments]    ${messagetext}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Element Should Contain    css=#sformulaire div.message.ui-state-valid p span.text   ${messagetext}

Get Object In Form
    ${url}    Get Location
    ${url}    Fetch From Right    ${url}    obj=
    ${objInForm}    Fetch From Left    ${url}    &
    Set Suite Variable    ${objInForm}

Add File
    [Arguments]    ${field}    ${file}
    Click Element    css=#${field}_upload + a.upload > span.ui-icon
    Sleep    1
    Choose File    css=#upload-form > input.champFormulaire    ${PATH_BIN_FILES}${file}
    Sleep    1
    Click Button    css=form#upload-form input.ui-button
    Sleep    1
    ${filename} =    Get Value    css=#${field}_upload
    Should Be Equal    ${filename}   ${file}

Form Actions Should Be
    [Arguments]    ${actions}
    ${length}    Get Length    ${actions}
    Log    Length = ${length}
    Xpath Should Match X Times    //div[@id="portlet-actions"]/ul/li    ${length}

    :FOR    ${index}    IN    @{actions}
    \    Element Should Contain    css=#portlet-actions ul.portlet-list   ${index}


Depuis le module de génération
    Go To    ${PROJECT_URL}scr/gen.php
    Page Should Not Contain Errors

Générer tout
    Depuis le module de génération
    Click Element    css=#gen-action-gen-all
    Page Should Not Contain    Erreur de droits d'écriture
    Page Should Not Contain    Génération de

Portlet Action Should Be In Form
    [Arguments]    ${obj}    ${action}
    Page Should Contain Element    css=#form-container #portlet-actions #action-form-${obj}-${action}

Portlet Action Should Not Be In Form
    [Arguments]    ${obj}    ${action}
    Page Should Not Contain Element    css=#form-container #portlet-actions #action-form-${obj}-${action}

Portlet Action Should Be In SubForm
    [Arguments]    ${obj}    ${action}
    Page Should Contain Element    css=#sousform-container #portlet-actions #action-sousform-${obj}-${action}

Portlet Action Should Not Be In SubForm
    [Arguments]    ${obj}    ${action}
    Page Should Not Contain Element    css=#sousform-container #portlet-actions #action-sousform-${obj}-${action}

Click On Form Portlet Action
    [Arguments]    ${obj}    ${action}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Element    css=#action-form-${obj}-${action}
    Sleep    1
    Page Should Not Contain Errors

Click On SubForm Portlet Action
    [Arguments]    ${obj}    ${action}
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Click Element    css=#action-sousform-${obj}-${action}
    Sleep    1
    Page Should Not Contain Errors

Input Datepicker
    [Arguments]    ${champ}    ${date}
    # On clique sur l'image du datepicker
    Click Image    css=input#${champ} + .ui-datepicker-trigger
    # On récupère le jour
    ${day} =    Get Substring    ${date}    0    2
    # On récupère le mois
    ${month} =     Get Substring    ${date}    3    5
    # On récupère l'année
    ${year} =    Get Substring    ${date}    6
    # Récupère le premier chiffre de la date
    ${day_first_character} =    Get Substring    ${day}    0    1
    # Récupère le deuxième chiffre de la date
    ${day_second_character} =    Get Substring    ${day}    1    2
    # On fait -1 sur le mois pour avoir la value du datepicker
    ${month} =    Convert to Integer    ${month}
    ${datepicker_month} =    Evaluate    ${month}-1
    ${datepicker_month} =    Convert to String    ${datepicker_month}
    # On sélectionne le mois
    Wait Until Keyword Succeeds     5 sec     0.2 sec    Select From List By Value    css=.ui-datepicker-month    ${datepicker_month}
    # On sélectionne l'année
    Select From List By Value    css=.ui-datepicker-year    ${year}
    # On sélectionne le jour, sur un caractère ou deux selon la valeur du premier
    Run keyword If    '${day_first_character}' == '0'    Click Link    ${day_second_character}    ELSE    Click Link    ${day}
    # On attend le temps que le datepicker ne soit plus affiché
    Sleep     1

Input Hour Minute
    [Arguments]    ${champ}    ${hour}=null    ${minute}=null
    # On sélectionne l'heure
    Run Keyword If    '${hour}' != 'null'    Select From List By Label    css=#${champ}_heure    ${hour}
    # On sélectionne la minute
    Run Keyword If    '${minute}' != 'null'    Select From List By Label    css=#${champ}_minute    ${minute}

Input Value With JS
    [Arguments]    ${champ}    ${value}
    # On écrit la valeur directement dans l'attribut de l'input
    Execute JavaScript    window.jQuery("#${champ}").val('${value}');
    # On déclenche l'évènement onchange
    Execute JavaScript    window.jQuery("#${champ}").trigger("change");

Input Value With JS Failed
    [Arguments]    ${champ}    ${value}    ${error}
    #
    Input Value With JS    ${champ}    ${value}
    # Vérification de l'erreur
    ${alert} =    Get Alert Message
    Should Be Equal As Strings    ${alert}    ${error}

Breadcrumb Should Be
    [Arguments]    ${value}
    Element Text Should Be    css=#title h2    ${value}

Breadcrumb Should Contain
    [Arguments]    ${value}
    Element Should Contain    css=#title h2    ${value}

Selected Tab Title Should Be
    [Arguments]    ${id}    ${libelle}
    Element Text Should Be    css=li.ui-tabs-selected #${id}    ${libelle}

Select Checkbox From List
    [Documentation]    Sélectionne une liste de case à cocher.
    [Arguments]    @{list_checkbox}
    :FOR    ${checkbox}    IN    @{list_checkbox}
    \    Select Checkbox    ${checkbox}

Form Value Should Contain From List
    [Documentation]    Vérifie la présence d'élément dans un champ de formulaire
    ...    depuis une liste.
    [Arguments]    ${field}    @{list_expected}
    :FOR    ${element}    IN    @{list_expected}
    \    Element Should Contain    ${field}    ${element}

Form HTML Should Contain From List
    [Documentation]    Vérifie la présence d'élément dans un champ HTML de
    ...    formulaire depuis une liste.
    [Arguments]    ${field}    @{list_expected}
    Select Frame    ${field}_ifr
    Focus    tinymce
    :FOR    ${element}    IN    @{list_expected}
    \    Element Should Contain    tinymce    ${element}
    Select Window    title=${TITLE}

Open Fieldset
    [Documentation]    Déplie un fieldset et attend qu'il soit ouvert
    [Arguments]    ${obj}    ${fieldset}
    Click Element    css=#fieldset-form-${obj}-${fieldset} > legend.collapsible
    Wait Until Element Is Visible    css=#fieldset-form-${obj}-${fieldset} > div.fieldsetContent

Open Fieldset In Subform
    [Documentation]    Déplie un fieldset et attend qu'il soit ouvert
    [Arguments]    ${obj}    ${fieldset}
    Click Element    css=#fieldset-sousform-${obj}-${fieldset} > legend.collapsible
    Wait Until Element Is Visible    css=#fieldset-sousform-${obj}-${fieldset} > div.fieldsetContent