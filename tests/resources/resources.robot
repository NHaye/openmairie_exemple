*** Settings ***
Documentation     Surcharges des ressources du framework (librairies, ressources, variables et keywords).

#
Resource          core/om_resources.robot

*** Variables ***
${SERVER}            localhost
${PROJECT_NAME}      openexemple
${BROWSER}           firefox
${DELAY}             0
${RESOURCES}         resources
${ADMIN_USER}        admin
${ADMIN_PASSWORD}    admin
${PROJECT_URL}       http://${SERVER}/${PROJECT_NAME}/
${PATH_BIN_FILES}    ${EXECDIR}${/}binary_files${/}
${TITLE}             :: openMairie :: openExemple - Framework

*** Keywords ***
For Suite Setup
    # Les keywords définit dans le resources.robot sont prioritaires
    Set Library Search Order    resources
    Ouvrir le navigateur

For Suite Teardown
    Fermer le navigateur