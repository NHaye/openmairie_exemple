*** Settings ***
Documentation     Fonctions et méthodes de traitement

*** Keywords ***

STR_PAD_LEFT
    # ${input}      Chaîne d'entrée.
    # ${pad_length} Taille de la chaîne à retourner.
    # ${pad_string} Caractère pour combler les vides de la chaîne à retourner.
    [Arguments]    ${input}    ${pad_length}    ${pad_string}
    # On récupère le nombre de caractère de ${input}
    ${input_lenght} =    Get Length    ${input}
    # On convertit les variables en integer
    ${pad_length} =    Convert to Integer    ${pad_length}
    ${input_lenght} =    Convert to Integer    ${input_lenght}
    # On récupère le nombre de ${pad_string} à ajouter
    ${lenght} =    Evaluate    ${pad_length}-${input_lenght}
    # On déclare la variable à retourner
    ${result}    Set Variable
    # On concatène ${pad_string} pour chaque ${lenght}
    :FOR    ${INDEX}    IN RANGE    0    ${lenght}
    \    ${result} =    Catenate    SEPARATOR=    ${result}    ${pad_string}
    # On concatène au résultat ${input}
    ${result} =    Catenate    SEPARATOR=    ${result}    ${input}
    # On retourne la valeur
    [return]    ${result}

STR_REPLACE
    # ${pattern}       Texte à remplacer.
    # ${replace_with}  Texte de remplacement.
    # ${string}        Chaîne à modifier.
    [Arguments]    ${pattern}    ${replace_with}    ${string}
    ${result} =    Replace String Using Regexp    ${string}    ${pattern}    ${replace_with}
    [return]    ${result}

Date du jour FR
    ${yyyy} =    Get Time    year
    ${mm} =    Get Time    month
    ${dd} =    Get Time    day
    [return]    ${dd}/${mm}/${yyyy}

Date du jour EN
    ${yyyy} =    Get Time    year
    ${mm} =    Get Time    month
    ${dd} =    Get Time    day
    [return]    ${yyyy}-${mm}-${dd}