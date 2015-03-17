/**
 * LAYOUT jqueryui
 *
 * @package openmairie_exemple
 * @version SVN : $Id: layout_jqueryui_after.js 2990 2014-12-01 14:07:54Z baldachino $
 */

// Initialisation des variables gerant les popup
var pfenetre;
var fenetreouverte = false;

/**
 * Au chargement de la page
 */
$(function() {
    // Gestion du menu
    menu_bind_accordion();
    // Gestion des formulaires et sous-formulaires avec le widget tabs de
    // jqueryui
    form_bind_tabs();
    // Gestion des sous-formulaires avec le widget accordion de jqueryui
    sousform_bind_accordion();
    // Gestion de tous les elements de contenus
    om_initialize_content(true);
    // Gestion des widgets sur le tableau de bord avec l'interaction sortable
    // de jqueryui
    widget_bind_move_actions();
    widget_bind_edit_actions();
    widget_bind_add_action();
    // Advanced search script
    toggle_advanced_search();
});

/**
 * Cette fonction permet d'associer a un arbre html les fonctions jquery
 * necessaires. Elle permet notamment lors du chargement d'une page en ajax
 * d'associer le comportement du bouton, la gestion du calendrier et la gestion
 * du fieldset.
 *
 * @param boolean tinymce_load permet de définir si les éditeurs tinyMCE doivent être chargés.
 */
function om_initialize_content(tinymce_load) {
    // Gestion des actions de portlet de formulaire
    form_bind_portlet_actions();
    // Gestion du skin des boutons, liens
    link_bind_button();
    // Gestion du calendrier avec le widget datepicker de jqueryui
    inputdate_bind_datepicker();
    // Gestion des fieldset avec le plugin collaspsible de jquery
    fieldset_bind_collapsible();
    // Gestion du picker color avec le plugin minicolors
    bind_form_field_rvb();
    // Gestion du widget aide à la saisie localisation avec le plugin draggable
    // de jqueryui
    localisation_bind_draggable();
    // Gestion de la redimension automatique des textarea
    textarea_autoresize();
    if(tinymce_load && tinymce_load == true) {
        // Gestion des WYSIWYG
        remove_tinymce()
        inputText_bind_tinyMCE_extended();
        inputText_bind_tinyMCE_simple();
        inputText_bind_tinyMCE();
    }
    // Lancement des scripts spécifiques à l'application
    app_initialize_content();
}

/**
 * Permet d'éventuellement lancer des scripts spécifiques à l'application.
 */
 function app_initialize_content() {}

/**
 * Permet de positionner l'attribut data-href sur un élémént en fonction de la 
 * valeur de son attribut href. L'objectif ici est de traiter en ajax ces liens
 * et donc de désactiver la possibilité d'ouvrir dans un nouvel onglet un lien
 * destiné à être uniquement récupéréré en ajax.
 */
function compose_data_href_on_link(identifier) {
    //
    $(identifier).each(function() {
        elem_href = $(this).attr('href');
        if (elem_href != '#') {
            $(this).attr('data-href', elem_href);
            $(this).attr('href', '#');
        }
    });
}

/**
 *
 */
function form_container_refresh(elem) {
    //
    if (elem == "form") {
        //
        $.get(window.location.href, function(data) {
            //
            $('#form-container').html(data);
            // Initialisation JS du nouveau contenu de la page
            om_initialize_content();
        });
    } else if (elem == "sousform") {
        //
        $.get($("#sousform-href").attr('data-href')+"&contentonly=true", function(data) {
            //
            $('#sousform-container').html(data);
            // Initialisation JS du nouveau contenu de la page
            om_initialize_content();
        });
    }
}

/**
 *
 */
function form_execute_action_direct(elem, action) {
    //
    $.ajax({
        type: "POST",
        url: $(action).attr('data-href')+"&validation=1&contentonly=true",
        cache: false,
        data: "submit=plop&",
        success: function(html){
            // Ajout du contenu récupéré (uniquement le bloc message)
            $('#'+elem+'-message').html($(html).find('div.message').get(0));
            // Rafraichissement du bloc de formulaire
            form_container_refresh(elem);
            // Initialisation JS du nouveau contenu de la page
            om_initialize_content();
        }
    });
}

/**
 *
 */
function form_confirmation_action(callback, elem, action) {
    //
    var dialogbloc = $("<div id=\"dialog-action-confirmation\">"+msg_form_action_confirmation+"</div>").insertAfter('#footer');
    //
    $(dialogbloc).dialog( "destroy" );
    $(dialogbloc).dialog({
        resizable: false,
        height:160,
        width:350,
        modal: true,
        buttons: [
            {
                text: msg_form_action_confirmation_button_confirm,
                    click: function() {
                        $(this).dialog("close");
                        callback(elem, action);
                    }
            }, {
                text: msg_form_action_confirmation_button_cancel,
                    click: function() {
                        $(this).dialog("close");
                    }
            }
        ]
    });
}

/**
 *
 */
function form_bind_portlet_actions() {
    //
    compose_data_href_on_link('#form-container a.action-direct');
    $("#form-container a.action-direct").click(function() {
        //
        if ($(this).attr('class').indexOf("action-with-confirmation") >= 0) {
            form_confirmation_action(form_execute_action_direct, "form", this);
        } else {
            form_execute_action_direct("form", this);
        }
        //
        return false;
    });
    //
    compose_data_href_on_link('#sousform-container a.action-direct');
    $("#sousform-container a.action-direct").click(function(event) {
        //
        if ($(this).attr('class').indexOf("action-with-confirmation") >= 0) {
            form_confirmation_action(form_execute_action_direct, "sousform", this);
        } else {
            form_execute_action_direct("sousform", this);
        }
        //
        return false;
    });
    //
    compose_data_href_on_link('#sousform-container a.action-self');
    $("#sousform-container a.action-self").click(function(event) {
        // On récupère l'objet du sous-formulaire en parsant l'attribut 
        // class de l'action qui commence par sousform-
        sousform_obj = $(this).attr('id').split('-')[2];
        //
        ajaxIt(sousform_obj, $(this).attr('data-href'));
        //
        return false;
    });
}

/**
 * MENU
 */
// Cette fonction permet d'associer au code html du menu la gestion du
// widget accordion de jqueryui
function menu_bind_accordion() {
    // Recherche de la variable $menuOpen: a-t-on une rubrique de menu ouverte ?
    var menuOpen = $("#menuopen_val").html();
    // Test si une rubrique est ouverte
    if (menuOpen == "") {
        // Si aucune rubrique n'est ouverte alors le menu apparaît fermé
        $("#menu-list").accordion({
            autoHeight: false,
            collapsible: true,
            active: false
        });
    } else {
        // Si une rubrique est ouverte alors le menu apparaît ouvert sur la
        // rubrique en question
        $("#menu-list").accordion({
            autoHeight: false,
            collapsible: true,
            active : parseInt(menuOpen)
        });
    }
}

/**
 * BUTTON
 */
// Cette fonction permet d'associer au code html representant des boutons ou
// des liens le widget button de jqueryui
function link_bind_button() {
    //
    $("input:submit, input:reset, input:button, p.linkjsclosewindow, p.likeabutton").button();
    $('button').not('.mce-tinymce button').button();
}

/**
 * TAB, FORM, SOUFORM
 */
// Cette fonction permet d'associer au code html des tableaux, formulaires,
// sous formulaires la gestion du widget tabs de jqueryui
function form_bind_tabs() {
    //
    var $tabs = $("#formulaire").tabs({
        load: function(event, ui) {
            //
            om_initialize_content(true);
            return true;
        },
        select: function(event, ui) {
            // Suppression du contenu de l'onglet precedemment selectionne
            // #ui-tabs-X correspond uniquement aux ids des onglets charges
            // dynamiquement
            selectedTabIndex = $tabs.tabs('option', 'selected');
            $("#ui-tabs-"+selectedTabIndex).empty();
            // Gestion de la recherche
            // Si le nouvel onglet clique est un onglet qui charge dynamiquement
            // son contenu
            var url = $.data(ui.tab, 'load.tabs');
            if (url) {
                // On affiche la recherche
                var recherchedyn = document.getElementById("recherchedyn");
                if (recherchedyn != null) {
                    var recherche = document.getElementById("recherchedyn").value;
                    url += "&recherche="+recherche;
                    $("#recherche_onglet").removeAttr("style");
                    $tabs.tabs("url", ui.index, url);
                }
            } else {
                // On cache la recherche
                $("#recherche_onglet").attr("style", "display:none;")
            }
            return true;
        },
        ajaxOptions: {
            error: function(xhr, status, index, anchor) {
                $(anchor.hash).html(msg_alert_error_tabs);
            }
        }
    });
}

// Cette fonction permet d'associer au code html des sous formulaires la
// gestion du widget accordion de jqueryui
function sousform_bind_accordion() {
    //
    $("#accordion").accordion({
        autoHeight: false,
        collapsible: true,
        active : false
    });
}

//
function ajaxIt(objsf, link) {
    // recuperation du terme recherche
    var recherche = document.getElementById("recherchedyn");
    if (recherche != null) {
        link += "&recherche="+recherche.value;
    }else {
        link += "&recherche=";
    }
    // execution de la requete en POST
    $.ajax({
        type: "GET",
        url: link,
        cache: false,
        success: function(html){
            $("#sousform-"+objsf).empty();
            $("#sousform-"+objsf).append(html);
            if ($("#sousform-href").length) {
                $("#sousform-href").attr("data-href", link);
            }
            om_initialize_content(true);
        }
    });
}

//
function recherche(link) {
    // recuperation de l'objsf
    var $tabs = $('#formulaire').tabs();
    var selected = $tabs.tabs('option', 'selected');
    $("#formulaire ul a").each(function(i){
        if (i === selected) {
            objsf =  $(this).attr("id");
        }
    }); 
    // recuperation du terme recherche
    link += "&obj="+objsf;
    //
    ajaxIt(objsf, link);
}

// Recherche avancée - vider le formulaire
function clear_form(form) {
    $(":input", 'form#'+form.attr('id'))
    .not(':button, :submit, :reset, :hidden') 
    .val('')
    .removeAttr('checked')
    .removeAttr('selected');
}

// Recherche avancée
function toggle_advanced_search() {

    function hideclassic_showadvanced () {

        // hide classic and show advanced
        $("div#adv-search-adv-fields").show();
        $("div#adv-search-classic-fields").hide();

        // reset class input val
        $("div.adv-search-widget input[name=recherche]").val("");

        // change submit button name
        $("#adv-search-submit").attr("name", "advanced-search-submit");

        // change toggle link and legend labels
        $("#toggle-advanced-display").html("Afficher la recherche simple");
        $("fieldset.adv-search-fieldset").children("legend").removeClass("collapicon_plus");
        $("fieldset.adv-search-fieldset").children("legend").addClass("collapicon_less");
    }

    function hideadvanced_showclassic () {

        // hide advanced and show classic
        $("div#adv-search-adv-fields").hide();
        $("div#adv-search-classic-fields").show();

        // change submit button name
        $("#adv-search-submit").attr("name", "classic-search-submit");

        // change toggle link and legend labels
        $("#toggle-advanced-display").html("Afficher la recherche avancée");
        $("fieldset.adv-search-fieldset").children("legend").removeClass("collapicon_less");
        $("fieldset.adv-search-fieldset").children("legend").addClass("collapicon_plus");
    }

    //
    if ($("#adv-search-submit").attr("name") == "advanced-search-submit") {
        $("div#adv-search-classic-fields").hide();
        $("#toggle-advanced-display").toggle(hideadvanced_showclassic,
                                              hideclassic_showadvanced);

        // change toggle link and legend labels
        $("#toggle-advanced-display").html("Afficher la recherche simple");
        $("fieldset.adv-search-fieldset").children("legend").removeClass("collapicon_plus");
        $("fieldset.adv-search-fieldset").children("legend").addClass("collapicon_less");

    } else {
        $("div#adv-search-adv-fields").hide();
        $("#toggle-advanced-display").toggle(hideclassic_showadvanced,
                                              hideadvanced_showclassic);

        // change toggle link and legend labels
        $("#toggle-advanced-display").html("Afficher la recherche avancée");
        $("fieldset.adv-search-fieldset").children("legend").removeClass("collapicon_less");
        $("fieldset.adv-search-fieldset").children("legend").addClass("collapicon_plus");

    }
}

//
function affichersform(objsf, link, formulaire) {
    // composition de la chaine data en fonction des elements du formulaire
    var data = "";
    //
    if (formulaire) {
        //
        for (i = 0; i < formulaire.elements.length; i++) {
            //
            field = $(formulaire.elements[i]);
            // Sauvegarde du contenu des tinymce avant serialisation
            if (tinyMCE.editors[formulaire.elements[i].name]) {
                //
                form_val = encodeURIComponent(tinyMCE.get(formulaire.elements[i].name).getContent());
                //
                data+=formulaire.elements[i].name+"="+form_val+"&";
            } else if (field.attr("multiple") == "multiple") {
                var multipleValues = field.val() || [];
                for (var j = 0; j < multipleValues.length; j++) {
                    data+=formulaire.elements[i].name+"="+multipleValues[j]+"&";
                };
            } else {
                form_val = encodeURIComponent(formulaire.elements[i].value);
                //
                data+=formulaire.elements[i].name+"="+form_val+"&";
            }
        }
    }
    // recuperation du terme recherche
    var recherchedyn = document.getElementById("recherchedyn");
    if (recherchedyn != null) {
        var recherche = recherchedyn.value;
    } else {
        var recherche = '';
    }
    link += "&recherche="+recherche;
    // execution de la requete en POST
    $.ajax({
        type: "POST",
        url: link,
        cache: false,
        data: data,
        success: function(html){
            $("#sousform-"+objsf).empty();
            $("#sousform-"+objsf).append(html);
            om_initialize_content(true);
        }
    });
}
// Fonction affichant une boite de confirmation
function showModalDialog(targetUrl,buttonY,buttonN) {
    $( "#dialog:ui-dialog" ).dialog( "destroy" );

    $( "#dialog-confirm" ).dialog({
        resizable: false,
        height:160,
        width:350,
        modal: true,
        buttons: [
            {
                text: buttonY,
                    click: function() {
                        $(this).dialog("close");
                        window.location.href = targetUrl;
                    }
            }, {
                text: buttonN,
                    click: function() {
                        $(this).dialog("close");
                    }
            }
        ]
    });
}
//
function load_form_in_modal(link) {
    // On enleve l'existant
    $( "#upload-container" ).remove();
    // On insert le conteneur de l'overlay
    $( "#upload-container" ).remove();
    var dialog = $("<div id=\"upload-container\" class=\"modal fade\" role=\"dialog\"></div>").insertAfter('#footer');
    $( "#upload-container" ).hide();
    //
    $.ajax({
        type: "GET",
        url: link,
        success: function(html){
            // chargement de l'url dans le conteneur dialog
            dialog.empty();
            dialog.append(html);
            // initilisation du contenu
            om_initialize_content();
            // affichage du dialog
            $(dialog).dialog({
                // a la fermeture du dialog
                close: function(ev, ui) {
                    // suppression du contenu
                    $(this).remove();
                },
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                position: 'center top'
            });
        }
    });
    // fermeture du dialog lors d'un clic sur le bouton retour
    dialog.on("click",'a.linkjsclosewindow',function() {
        $(dialog).dialog('close').remove();
        return false;
    });
}

/**
 * FIELDSET
 */
//
function fieldset_bind_collapsible() {
    //
    $("fieldset.collapsible").collapse();
    $("fieldset.startClosed").collapse( { closed: true } );
}

/**
 * WIDGET - DASHBOARD
 */
// Cette fonction permet d'associer aux colonnes des actions de deplacement
// des widgets
function widget_bind_move_actions() {
    //
    $( ".column" ).sortable({
        connectWith: ".column",
        handle: ".widget-header-move",
        stop: function(event, ui) {
            var order = ""
            $(".column").each(function(){
                order += $(this).attr('id') + "=";
                order += $(this).sortable("toArray").join("x") + "&";
            })
            $("#info").load("../spg/widgetctl.php?action=update&"+order); 
        }
    });
}

// Cette fonction permet d'associer a l'icone "ajouter" l'action d'ajouter un
// nouveau widget
function widget_bind_add_action() {
    //
    $(".widget-add-action").click(function() {
        //
        profil = $(this).attr('id');
        //
        $("#widget_").remove();
        //
        $.get("../spg/widgetctl.php?action=insert&profil="+profil, function(data) {
            $(data).prependTo("#column_1").effect("highlight");
            widget_bind_edit_actions("#widget_");
            link_bind_button();
        });
    });
}

// Cette fonction permet d'associer a un widget ou a tous les widgets les
// differentes actions possibles sur ce ou ces derniers : plier/deplier,
// supprimer, ...
function widget_bind_edit_actions(widget_selector) {
    // Initialisation de l'argument de la fonction si besoin
    widget_selector = typeof(widget_selector) != 'undefined' ? widget_selector : "";
    // Ajout de l'icone "reduire"
    $(widget_selector+" .widget-header").prepend( "<span class=\"ui-icon ui-icon-minusthick\" title=\""+msg_widget_action_retract+"\">-/+&nbsp;</span>");
    // Ajout de l'icone "supprimer"
    $(widget_selector+" .widget-header-edit").prepend( "<span class='ui-icon ui-icon-closethick' title=\""+msg_widget_action_delete+"\">x&nbsp;</span>");
    // Bind de l'evenement click sur l'icone "reduire"
    $(widget_selector+" .widget-header .ui-icon-minusthick" ).click(function() {
        // On change l'icone "reduire" par l'icone "deplier" ou l'inverse
        $(this).toggleClass("ui-icon-minusthick").toggleClass("ui-icon-plusthick");
        // On cache le contenu du widget ou on l'affiche
        $(this).parents(".widget:first").find(".widget-content-wrapper").toggle();
    });
    // Bind de l'evenement click sur l'icone "supprimer"
    $(widget_selector+" .widget-header-edit .ui-icon-closethick" ).click(function() {
        // On recupere l'attribut id de l'element parent representant le widget
        widget = $(this).parents(".widget:first").attr('id');
        // Si le widget n'est pas le widget d'ajout
        if (widget != "widget_") {
            // On appelle le script permettant de realiser l'action supprimer dans
            // la base de donnees
            $("#info").load("../spg/widgetctl.php?action=delete&widget="+widget);
        }
        // On supprime le widget de l'affichage
        $(this).parents(".widget:first").hide("highlight", function() {
            $(this).remove();
        });
    });
}

// Cette fonction permet lors de la validation du formulaire d'ajout de widget,
// d'appeler le script PHP permettant de faire le traitement d'ajout dans la
// base puis d'ajouter a l'ecran le nouveau widget
function widget_add_form_post() {
    // serialisation des valeurs du formulaire
    data = "widget_add_form_valid=true&widget="+$("#widget_add_form select").val()+"&profil="+$("#widget_add_form_profil").val();
    // execution de la requete en POST pour ajouter le widget
    $.ajax({
        type: "POST",
        url: "../spg/widgetctl.php?action=insert",
        cache: false,
        data: data,
        success: function(html){
            // On cache le widget d'ajout
            $("#widget_").hide("highlight", function() {
                // On supprime le widget d'ajout
                $("#widget_").remove();
                // si le retour de l'appel n'est pas la chaine null
                if (html!="null") {
                    // On recupere la vue du widget que l'on vient d'ajouter
                    $.get("../spg/widgetctl.php?action=view&widget="+html, function(data) {
                        // On affiche le widget ajoute dans la premiere colonne
                        $(data).prependTo("#column_1").effect("highlight");
                        // On associe les actions du widget
                        widget_bind_edit_actions("#widget_"+html);
                    });
                }
            });
        }
    });
}


/**
 * FORMULAIRE
 */
// Cette fonction permet d'appliquer l'autoresize sur les champs textarea
function textarea_autoresize() {
    $('textarea').autosize();
}
// Cette fonction permet de verifier si la valeur du champ passe en parametre
// est un nombre valide ou non
function VerifNum(champ) {
    // On teste si la valeur saisit est un nombre valide
    if (isNaN(champ.value)) {
        // On avertit l'utilisateur qu'il y a une erreur dans le champs
        alert(msg_alert_error_verifnum);
        // On vide le champ
        champ.value = "";
        //
        return;
    }
    // Attention ici on supprime le caractere '.' donc si l'utilisateur saisit
    // 82.5 alors la valeur se tranformera en 825
    // XXX Reflechir au but de cette modification
    champ.value = champ.value.replace(".", "");
}

// Cette fonction permet de verifier si la valeur du champ passe en parametre
// est un nombre valide ou non
function VerifFloat(target) {
    
    // verification seulement si le champ n'est pas vide
    if (target.value != '') {
        
        target.value = target.value.replace(",", ".");
        target.value = parseFloat(target.value);
        
        if(isNaN(target.value)) {
            target.value = "";
            alert(msg_alert_error_veriffloat);
        }
    }
}

// Cette fonction permet de verifier si la valeur du champ passe en parametre
// est une date valide ou non (le format de sortie est : JJ/MM/AAAA, les
// formats de saisie possibles sont : JJ/MM/AA JJ/MM/AAAA JJMMAA JJMMAAAA)
// XXX Il faut reflechir a un systeme en cas de format de date different
function fdate(champ) {
    // Initialisation des variables
    var flag = 0;
    var jour = "";
    var mois = "";
    var annee = "";
    // Si il n'y a pas de separateur et que la longueur est de 6 ou de 8
    // caracteres, c'est-a-dire correspondant au format JJMMAA ou JJMMAAAA
    if (champ.value.lastIndexOf("/") == -1 && (champ.value.length == 6 || champ.value.length == 8)) {
        // On recupere le jour et le mois JJMM dans deux variables du meme nom
        jour = champ.value.substring(0,2);
        mois = champ.value.substring(2,4);
        // Si le format est JJMMAA, alors on ajoute 20 (JJMM20AA) puis on
        // recupere l'annee dans une variable du meme nom
        if (champ.value.length == 6) {
            annee = "20"+champ.value.substring(4,6);
        }
        // Si le format est JJMMAAAA, alors on recupere l'annee dans une
        // variable du meme nom
        if (champ.value.length == 8) {
            annee = champ.value.substring(4,8);
        }
    }
    // Si il y a au moins un separateur et que la longueur est de 8 ou de 10
    // caracteres, c'est-a-dire correspodnant au format JJ/MM/AA ou JJ/MM/AAAA
    if (champ.value.lastIndexOf("/") != -1 && (champ.value.length == 8 || champ.value.length == 10)) {
        // On recupere le jour et le mois JJ/MM dans deux variables du meme nom
        jour = champ.value.substring(0,2);
        mois = champ.value.substring(3,5);
        // Si le format est JJ/MM/AA, alors on ajoute 20 (JJ/MM/20AA) puis on
        // recupere l'annee dans une variable du meme nom
        if (champ.value.length == 8) {
            annee = "20"+champ.value.substring(6,8);
        }
        // Si le format est JJ/MM/AAAA, alors on recupere l'annee dans une
        // variable du meme nom
        if (champ.value.length == 10) {
            annee = champ.value.substring(6,10);
        }
    }
    // Si une des trois variables recuperees n'est pas un nombre alors il y a
    // une erreur
    if (isNaN(jour) || isNaN(mois) || isNaN(annee)) {
        // On positionne le flag d'erreur a 1
        flag = 1;
    }
    // Si une des trois variables recuperees n'est pas un nombre coherent
    // pour cette variable alors il y a une erreur
    if (jour < '01' || jour > '31' || mois < '01' || mois > '12' || annee < '0000' || annee > '9999') {
        // On positionne le flag d'erreur a 1
        flag = 1;
    }
    // Si il n'y a pas d'erreur alors on remplit le champ avec les valeurs
    // recuperees precedemment sinon on leve une erreur
    if (flag == 0) {
        // On remplit le champ
        champ.value = jour+"/"+mois+"/"+annee;
    } else {
        // On avertit l'utilisateur qu'il y a une erreur dans le champs
        alert(msg_alert_error_fdate);
        // On vide le champ
        champ.value = "";
        //
        return;
    }
}

// Cette fonction permet de verifier si la valeur du champ passe en parametre
// est une heure valide ou non (le format de sortie est : HH:MM:SS, les
// formats de saisie possibles sont : HH:MM:SS HH:MM HH HHMM HHMMSS)
function ftime(champ) {
    //
    var flag = 0;
    var heure = "";
    var minute = "00";
    var seconde = "00";
    // Si il n'y a pas de separateur et que la longueur est de 2 ou de 4 ou de 6
    // caracteres, c'est-a-dire correspondant au format HH ou HHMM ou HHMMSS
    if (champ.value.lastIndexOf(":") == -1 && (champ.value.length == 2 || champ.value.length == 4 || champ.value.length == 6)) {
        // On recupere l'heure dans une variable du meme nom
        heure = champ.value.substring(0,2);
        // Si le format est HHMM, on recupere les minutes dans une variable du
        // meme nom
        if (champ.value.length == 4) {
            minute = champ.value.substring(2,4);
        }
        // Si le format est HHMMSS, on recupere les minutes et les secondes
        // dans deux variables du meme nom
        if (champ.value.length == 6) {
            minute = champ.value.substring(2,4);
            seconde = champ.value.substring(4,6);
        }
    }
    // Si il y a au moins un separateur et que la longueur est de 5 ou de 8
    // caracteres, c'est-a-dire correspondant au format HH:MM ou HH:MM:SS
    if (champ.value.lastIndexOf(":") != -1 && (champ.value.length == 5 || champ.value.length == 8)) {
        // On recupere l'heure dans une variable du meme nom
        heure = champ.value.substring(0,2);
        // Si le format est HH:MM, on recupere les minutes dans une variable du
        // meme nom
        if (champ.value.length == 5) {
            minute = champ.value.substring(3,5);
        }
        // Si le format est HH:MM:SS, on recupere les minutes et les secondes
        // dans deux variables du meme nom
        if (champ.value.length == 8) {
            minute = champ.value.substring(3,5);
            seconde = champ.value.substring(6,8);
        }
    }
    // Si une des trois variables recuperees n'est pas un nombre alors il y a
    // une erreur
    if (isNaN(heure) || isNaN(minute) || isNaN(seconde)) {
        // On positionne le flag d'erreur a 1
        flag = 1;
    }
    // Si une des trois variables recuperees n'est pas un nombre coherent
    // pour cette variable alors il y a une erreur
    if (heure < '00' || heure > '23' || minute < '00' || minute > '59' || seconde < '00' || seconde > '59') {
        // On positionne le flag d'erreur a 1
        flag = 1;
    }
    // Si il n'y a pas d'erreur alors on remplit le champ avec les valeurs
    // recuperees precedemment sinon on leve une erreur
    if (flag == 0) {
        // On remplit le champ
        champ.value = heure+":"+minute+":"+seconde;
    } else {
        // On avertit l'utilisateur qu'il y a une erreur dans le champs
        alert(msg_alert_error_ftime);
        // On vide le champ
        champ.value = "";
        //
        return;
    }
}
// CHECKBOX
function changevaluecheckbox(object) {
    if (object.value == "Oui") {
        object.value = "";
    } else {
        object.value = "Oui";
    }
}
//
function changevaluecheckboxnum(object) {
    if (object.value == 1) {
        object.value = "0";
    } else {
        object.value = 1;
    }
}

/**
 * FORM WIDGET - VOIR
 */
// Function permettant de vider le champ de formulaire
function supprimerUpload(champ) {
    //
    if (document.f1.elements[champ].value != "") {
        document.f1.elements[champ].value = "";
        document.f1.elements[champ+'_upload'].value = "";
    }
}
// Function permettant de vider le champ de sousformulaire
function supprimerUpload2(champ) {
    //
    if (document.f2.elements[champ].value != "") {
        document.f2.elements[champ].value = "";
        document.f2.elements[champ+'_upload'].value = "";
    }
}
//
function voir(champ, obj, id) {
    //
    champ = (champ) ? champ : '';
    //L'uid du fichier
    var uid = document.f1.elements[champ].value;
    //Le paramètre champ est obligatoire
    if (uid != '') {
        //paramètres
        obj = (obj) ? obj : '';
        id = (id) ? id : '';
        //déclaration du mode
        var mode = ''

        //
        var champ_split = uid.split('|');
        //Si le champ possède la mentien tmp
        if (champ_split[0] == 'tmp') {
            //le mode devient temporary
            mode = 'temporary';
            //l'uid est en deuxième partie du champ
            uid = champ_split[1];
        }

        //lien de base vers voir.php
        link = "../spg/voir.php?fic="+uid;

        //Si obj et id sont renseigné
        if (obj != '' && id != '') {
            //le lien est modifié pour ajouter ces paramètres
            link = "../spg/voir.php?obj="+obj+"&champ="+champ+"&id="+id;
        }

        //Si le mode est renseigné, il est passé en paramètre
        if (mode != '') {
            //
            link = "../spg/voir.php?fic="+uid+"&mode="+mode;
        }
        
        //Affiche la fenètre modal
        load_form_in_modal(link);
    }
}
//
function voir2(champ, obj, id) {
    //
    champ = (champ) ? champ : '';
    //L'uid du fichier
    var uid = document.f2.elements[champ].value;
    //Le paramètre champ est obligatoire
    if (uid != '') {
        //paramètres
        obj = (obj) ? obj : '';
        id = (id) ? id : '';
        //déclaration du mode
        var mode = ''

        //
        var champ_split = uid.split('|');
        //Si le champ possède la mentien tmp
        if (champ_split[0] == 'tmp') {
            //le mode devient temporary
            mode = 'temporary';
            //l'uid est en deuxième partie du champ
            uid = champ_split[1];
        }

        //lien de base vers voir.php
        link = "../spg/voir.php?fic="+uid;

        //Si obj et id sont renseigné
        if (obj != '' && id != '') {
            //le lien est modifié pour ajouter ces paramètres
            link = "../spg/voir.php?obj="+obj+"&champ="+champ+"&id="+id;
        }

        //Si le mode est renseigné, il est passé en paramètre
        if (mode != '') {
            //
            link = "../spg/voir.php?fic="+uid+"&mode="+mode;
        }
        
        //Affiche la fenètre modal
        load_form_in_modal(link);
    }
}

/**
 * FORM WIDGET - UPLOAD
 */
////////////////////////////////////////////////////////////////////////////////
// UPLOAD
// spg/upload.php
// XXX faire une seule fonction (vupload, vupload2) en récupérant le formulaire
// parent et non en récupérant le nom du formulaire  (f1, f2) en paramètre
////////////////////////////////////////////////////////////////////////////////
// Cette fonction permet d'associer le formulaire à la fonction ajaxSubmit
// et de charger de nouveau le formulaire d'upload dans le dialog
function upload_bind_form_submit() {
    // 
    $("#upload-form").submit(function() {
        // submit the form 
        $(this).ajaxSubmit({
            // avant la soumission du formulaire
            beforeSend:function() {
                    // Affichage du spinner
                    $("#upload-container div.message").remove();
                    $("#upload-form").html(msg_loading);
                },
            // lors de la validation du formulaire
            success: function(html){
                // chargement de l'url en lieu et place de l'ancienne
                $("#upload-container").empty();
                $("#upload-container").append(html);
                // initilisation du contenu
                om_initialize_content();
                // initialisation du formulaire
                upload_bind_form_submit();
            }});
        // retour de la valeur false pour que le formulaire ne soit pas soumis
        // de manière normale
        return false; 
    });
}
// Cette fonction permet de charger le formulaire d'upload dans le dialog lors
// du premier appel
function upload_load_form(link) {
    //
    var dialog = $("<div id=\"upload-container\" role=\"dialog\"></div>").insertAfter('#footer');
    //
    $.ajax({
        type: "GET",
        url: link,
        success: function(html){
            // chargement de l'url dans le conteneur dialog
            dialog.empty();
            dialog.append(html);
            // initilisation du contenu
            om_initialize_content();
            // initialisation du formulaire
            upload_bind_form_submit();
            // affichage du dialog
            $(dialog).dialog({
                // a la fermeture du dialog
                close: function(ev, ui) {
                    // suppression du contenu
                    $(this).remove();
                },
                resizable: false,
                modal: true,
                width: 'auto',
                height: 'auto',
                position: 'center'
            });
        }
    });
    // fermeture du dialog lors d'un clic sur le bouton retour
    dialog.on("click",'a.linkjsclosewindow',function() {
        $(dialog).dialog('close').remove();
        return false;
    });
}
// Cette fonction permet de retourner les informations sur le fichier téléchargé
// du formulaire d'upload vers le formulaire d'origine
function upload_return(form, champ, value, file) {
    $("form[name|="+form+"] #"+champ).attr('value', value);
    $("form[name|="+form+"] #"+champ+"_upload").attr('value', file);
    $('#upload-container').dialog('close').remove();
}
// Appel depuis form.php
function vupload(champ, taille_maximale, extension) {
    var link = "../spg/upload.php?origine="+champ+"&form=f1&"+
        "taille_max="+taille_maximale+"&extension="+extension;
    upload_load_form(link);
}
// Appel depuis sousform.php
function vupload2(champ, taille_maximale, extension) {
    var link = "../spg/upload.php?origine="+champ+"&form=f2&"+
        "taille_max="+taille_maximale+"&extension="+extension;
    upload_load_form(link);
}

/**
 * FORM WIDGET - RVB
 * @requires lib miniColors
 */
//
function bind_form_field_rvb() {
    //
    $('input.rvb').not('[disabled="disabled"]').each(function() {
        this.value = rgb2hex($(this).val());
    });
    //
    $('input.rvb').not('[disabled="disabled"]').miniColors({});
    //
    $("form").submit(function() {
        $('input.rvb').not('[disabled="disabled"]').each(function() {
                this.value = hex2rgb($(this).val());
            });
        return true;
    });
} 
// Cette fonction permet de transformer une chaine RGB "255-0-255"
// en hexadécimale "#ff0ff"
function rgb2hex(rgb) {
    // Récupération de chacune des composantes
    a = rgb.split("-");
    r = a[0];
    g = a[1];
    b = a[2];
    // Définition ses caractères hexadécimaux
    HexDigits = "0123456789abcdef";
    // Transformation
    return "#"+HexDigits.substr(Math.floor(r / 16), 1) + HexDigits.substr(r % 16, 1)
    + HexDigits.substr(Math.floor(g / 16), 1) + HexDigits.substr(g % 16, 1)
    + HexDigits.substr(Math.floor(b / 16), 1) + HexDigits.substr(g % 16, 1)

}
// Cette fonction permet de transformer une chaine hexadécimale "#ff0ff"
// en RGB "255-0-255"
function hex2rgb(hex) {
    //
    hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
    //
    return (hex >> 16)+"-"+((hex & 0x00FF00) >> 8)+"-"+(hex & 0x0000FF);
}
// Fonction dépréciée
function rvb(champ) {}
// Fonction dépréciée
function rvb2(champ) {}

/**
 * FORM WIDGET - LOCALISATION
 */
//
function localisation_bind_draggable() {
    $("#draggable").draggable({ containment: "parent" });
    $("#draggable").dblclick(function() {
        infos = $(this).attr("class");
        infos = infos.split(" ");
        infos = infos[0].split(";");
        // Récupération de la position de l'élément
        position = $(this).position();
        x = parseInt(position.left);
        y = parseInt(position.top);
        // 
        localisation_return(infos[0], infos[1], x, infos[2], y);
        return true;
    });
}
// Cette fonction permet de retourner les informations sur le fichier téléchargé
// du formulaire d'upload vers le formulaire d'origine
function localisation_return(form, champ_x, value_x, champ_y, value_y) {
    $("form[name|="+form+"] #"+champ_x).attr('value', value_x);
    $("form[name|="+form+"] #"+champ_y).attr('value', value_y);
    $('#upload-container').dialog('close').remove();
}
//
function localisation(champ, chplan, positionx) {
    //
    var plan = document.f1.elements[chplan].value;
    var x = document.f1.elements[positionx].value;
    var y = document.f1.elements[champ].value;
    //
    link = "../spg/localisation.php?positiony="+champ+"&positionx="+positionx+"&plan="+plan+"&form=f1"+"&x="+x+"&y="+y;
    //
    load_form_in_modal(link);
}
function localisation2(champ, chplan, positionx) {
    //
    var plan = document.f2.elements[chplan].value;
    var x = document.f2.elements[positionx].value;
    var y = document.f2.elements[champ].value;
    //
    link = "../spg/localisation.php?positiony="+champ+"&positionx="+positionx+"&plan="+plan+"&form=f2"+"&x="+x+"&y="+y;
    //
    load_form_in_modal(link);
}
//
function localisation_sig(siglien, idx, obj, seli) {
    //
    if (fenetreouverte == true) {
        pfenetre.close();
    }
    pfenetre = window.open(siglien+idx+"&obj="+obj+"&seli="+seli+"&popup=1","localisation","location=no,toolbar=no,scrollbars=yes");
    //
    fenetreouverte = true;
}
//
function localisation_edition(form, format, orientation, positionx, positiony) {
    //
    if (form.elements[format] == undefined
        || form.elements[orientation] == undefined
        || form.elements[positionx] == undefined
        || form.elements[positiony] == undefined) {
        //
        alert("Une erreur s'est produite. Contactez votre administrateur.")
        return;
    }
    //
    var val_format = form.elements[format].value
    var val_orientation = form.elements[orientation].value;
    var val_x = form.elements[positionx].value;
    var val_y = form.elements[positiony].value;
    //
    link = "../spg/localisation.php?format="+val_format+"&orientation="+val_orientation+"&positionx="+positionx+"&positiony="+positiony+"&x="+val_x+"&y="+val_y+"&form="+form.name;
    //
    load_form_in_modal(link);
}

/**
 * FORM WIDGET - DATEPICKER
 */
// Parametrage du calendrier jquery ui
// XXX Il faut reflechir a un systeme en cas de format de date different
var currDate = new Date ();
var currYear = currDate.getFullYear();
var maxYear = currYear + 20;
var minYear = currYear - 120;
var dateFormat = 'dd/mm/yy';
// Cette fonction permet d'associer a un champ input la gestion du widget
// datepicker de jqueryui
function inputdate_bind_datepicker() {
    // 
    $(".datepicker").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
        yearRange: minYear+':'+maxYear,
        showOn: 'button',
        buttonImage: '../img/calendar.png',
        buttonImageOnly: true,
        constrainInput: true
    });
}

/**
 * FORM WIDGET - HTML tinyMCE Complet pour les états et lettre type
 */
function inputText_bind_tinyMCE_extended() {
    tinymce.init({
        selector: "textarea.htmletatex",
        // modifier le language via l'appel à la LOCALE
        language : locale,
        theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
        font_size_style_values: "12px,13px,14px,16px,18px,20px",
        entity_encoding : "raw",
        plugins: [
            "advlist lists link preview hr pagebreak",
            "searchreplace wordcount fullscreen",
            "insertdatetime nonbreaking save table",
            "template paste textcolor autoresize code"
        ],
        // Custom CSS
        content_css: "../css/layout_jqueryui_before.css?"+ new Date().getTime(),
        // Style inline
        inline_styles : true,
        paste_auto_cleanup_on_paste : true,
        paste_word_valid_elements: "b,strong,i,em,h1,h2",
        // 
        contextmenu : "cut copy paste pastetext selectall | removeformat | link insertdate inserttable",
        insertdatetime_formats : ["%d/%m/%Y", "%H:%M"],
        tools: "inserttable",
        browser_spellcheck : true,
        debug : true,
        pagebreak_separator : '<br pagebreak="true" />',
        invalid_elements : "script,applet,iframe,tcpdf",
        toolbar1: "undo redo | styleselect | bold italic underline | fontselect |"+
        " fontsizeselect | alignleft aligncenter alignright alignjustify |"+
        " bullist numlist | forecolor backcolor | majmin | codebarre | fullscreen",
        formats : {
            bold: {inline: 'span',  styles: {'font-weight': 'bold'}},
            mce_minformat: {inline: 'span', 'classes': 'mce_min'},
            mce_majformat: {inline: 'span', 'classes': 'mce_maj'},
            mce_codebarreformat: {inline: 'span', 'classes': 'mce_codebarre'},
        },

        // Liste des polices
        font_formats: "Courier New=courier new,courier;"+
            "Helvetica=helvetica;"+
            "Times New Roman=times new roman,times",

        // Interdiction de redimentionner une table
        object_resizing : false,
        setup: function (editor) {
            addMajMinButton(editor);
            addCodeBarreButton(editor);
            addSEMenu(editor);
            editor.on('SetContent', function(e) {
                editor.save();
            });
        },
        // Colle le texte brut sans style, ni balise
        paste_as_text: true,
    });
}
/**
 * Button permettant de passer un text ou champ de fusion en minuscule
 * @param tinymce.activeeditor ed tinymce.activeeditor
 */
function addMajMinButton(editor) {
    editor.addButton('majmin', {
        text: false,
        tooltip: mce_majmin_tooltip,
        image: '../img/majmin.png',
        onclick: function() {
            if(editor.formatter.match('mce_majformat') == false) {
                editor.formatter.remove('mce_minformat');
                editor.formatter.apply('mce_majformat');
            } else {
                editor.formatter.apply('mce_minformat');
                editor.formatter.remove('mce_majformat');
            }
        }
    });
}

/**
 * Button permettant de passer un text ou champ de fusion en codebarre
 * @param tinymce.activeeditor ed tinymce.activeeditor
 */
function addCodeBarreButton(editor) {
    editor.addButton('codebarre', {
        text: false,
        image: '../img/barcode.png',
        tooltip: mce_codebarre_tooltip,
        onclick: function() {
            editor.formatter.toggle('mce_codebarreformat');
        }
    });
}

/**
 * Récupération des sous états et affichage dans un menu
 */
function addSEMenu(editor) {
    var menuItems = [];
    //Récupération des sous-états
    $.getJSON( "../scr/sousetat_json.php", function( data ) {
        $.each(data, function(id, libelle) {
            menuItems.push({
                text: libelle,
                onclick: function() {
                    text = '<span class="mce_sousetat" id="'+id+'">'+libelle+'</span>';
                    text += '<p></p>';
                    editor.selection.setContent(text);
                }
            });
        });
    });
    editor.addMenuItem('sousetats', {
        text: titre_menu_sousetat,
        // icon: 'mce_sousetat',
        image: '../img/sousetat.png',
        tooltip: titre_menu_sousetat_tooltip,
        titre: 'menu',
        context: 'insert',
        menu: menuItems,
    });
    editor.addMenuItem('separator', {
        text: '-',
        context: 'insert'
    });

}
/**
 * FORM WIDGET - HTML Simplifié - Zones de texte des formulaires
 */
function inputText_bind_tinyMCE() {
    tinymce.init({
        selector: "textarea.html",
        // modifier le language via l'appel à la LOCALE
        language : locale,
        theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
        font_size_style_values: "12px,13px,14px,16px,18px,20px",
        plugins: [
            "advlist lists link preview hr",
            "searchreplace wordcount fullscreen",
            "insertdatetime nonbreaking save",
            "paste textcolor autoresize"
        ],
        entity_encoding : "raw",
        contextmenu : "cut copy paste pastetext selectall | removeformat | link insertdate",
        insertdatetime_formats : ["%d/%m/%Y", "%H:%M"],
        paste_word_valid_elements: "b,strong,i,em,h1,h2",
        // Custom CSS
        content_css: "../css/layout_jqueryui_before.css?"+ new Date().getTime(),
        // Style inline
        inline_styles : true,
        paste_auto_cleanup_on_paste : true,
        // Spell check (pas de contextmenu...)
        browser_spellcheck : true,
        // Liste des polices
        font_formats: "Courier New=courier new,courier;"+
            "Helvetica=helvetica;"+
            "Times New Roman=times new roman,times",

        toolbar1: "undo | styleselect | bold italic underline | fontselect | "+
            " fontsizeselect | alignleft aligncenter alignright alignjustify |"+
            " bullist numlist | forecolor backcolor | fullscreen",
        formats : {
            bold: {inline: 'span',  styles: {'font-weight': 'bold'}},
        },
        setup: function(editor) {
            editor.on('SetContent', function(e) {
                editor.save();
            });
        },
        // Colle le texte brut sans style, ni balise
        paste_as_text: true
    });
}
/**
 * FORM WIDGET - HTML Simplifié pour champs de fusions des états et lettretype
 */
function inputText_bind_tinyMCE_simple() {
    tinymce.init({
        selector: "textarea.htmletat",

        // modifier le language via l'appel à la LOCALE
        language : locale,
        theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
        font_size_style_values: "12px,13px,14px,16px,18px,20px",
        entity_encoding : "raw",
        plugins: [
            "advlist lists link preview hr",
            "searchreplace wordcount fullscreen",
            "insertdatetime nonbreaking save table",
            "template paste textcolor autoresize code"
        ],
        // Custom CSS
        content_css: "../css/layout_jqueryui_before.css?"+ new Date().getTime(),
        // Style inline
        inline_styles : true,
        paste_auto_cleanup_on_paste : true,
        paste_word_valid_elements: "b,strong,i,em,h1,h2",
        // 
        contextmenu : "cut copy paste pastetext selectall | removeformat | link insertdate inserttable",
        insertdatetime_formats : ["%d/%m/%Y", "%H:%M"],
        tools: "inserttable",
        pagebreak_separator : '<br pagebreak="true" />',
        invalid_elements : "script,applet,iframe,tcpdf",
        toolbar1: "undo redo | styleselect | bold italic underline | fontselect |"+
        " fontsizeselect | alignleft aligncenter alignright alignjustify |"+
        " bullist numlist | forecolor backcolor | majmin | codebarre | fullscreen",
        templates: [
            {title: 'Modèle courrier', content: '<p>&nbsp;</p><table><tbody><tr><td style=\'width: 50%;\'><p>Civilit&eacute; Nom Pr&eacute;nom emetteur</p><p>adresse</p><p>compl&eacute;ment</p><p>cp ville</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p></td><td><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>Civilit&eacute; Nom Pr&eacute;nom destinataire</p><p>adresse</p><p>compl&eacute;ment</p><p>cp ville</p></td></tr></tbody></table>'}            ],
        
        formats : {
            bold: {inline: 'span',  styles: {'font-weight': 'bold'}},
            mce_minformat: {inline: 'span', 'classes': 'mce_min'},
            mce_majformat: {inline: 'span', 'classes': 'mce_maj'},
            mce_codebarreformat: {inline: 'span', 'classes': 'mce_codebarre'},
        },

        // Liste des polices
        font_formats: "Courier New=courier new,courier;"+
            "Helvetica=helvetica;"+
            "Times New Roman=times new roman,times",

        // Interdiction de redimentionner une table
        object_resizing : false,
        setup: function (editor) {
            addMajMinButton(editor);
            addCodeBarreButton(editor);
            editor.on('SetContent', function(e) {
                editor.save();
            });
        },
        // Colle le texte brut sans style, ni balise
        paste_as_text: true,
    });
}

function remove_tinymce() {
    var editorArr = tinymce.editors,
    l = editorArr.length,
    i;
            
    if ( l ) {
        for ( i = l-1; i >= 0; i-- ) {
            if ( editorArr[i] !== undefined ) {
                tinyMCE.execCommand("mceRemoveControl",false,editorArr[i].id);
            }
        }
    } 
}

/**
 * FORM WIDGET - CORREL
 */
////////////////////////////////////////////////////////////////////////////////
// CORREL
////////////////////////////////////////////////////////////////////////////////
// comboG comboD
function vcorrel(champ, zcorrel2, params) {
    //
    if (fenetreouverte == true) {
        pfenetre.close();
    }
    //
    var rec = document.f1.elements[champ].value;
    var temp = zcorrel2;
    //
    if (temp == "s1") {
        zcorrel2 = "";
        temp = "s1";
    } else {
        zcorrel2 = document.f1.elements[zcorrel2].value;
    }
    //
    pfenetre = window.open("../spg/combo.php?origine="+champ+"&recherche="+rec+params+"&zcorrel2="+zcorrel2+"&form=f1","Correspondance","width=600,height=300,top=120,left=120");
    //
    fenetreouverte = true;
}
// comboG2 et comboD2
function vcorrel2(champ, zcorrel2, params) {
    //
    if (fenetreouverte == true) {
        pfenetre.close();
    }
    //
    var rec = document.f2.elements[champ].value;
    var temp = zcorrel2;
    //
    if (temp == "s1") {
        zcorrel2 = "";
        temp = "s1";
    } else {
        zcorrel2 = document.f2.elements[zcorrel2].value;
    }
    //
    pfenetre = window.open("../spg/combo.php?origine="+champ+"&recherche="+rec+params+"&zcorrel2="+zcorrel2+"&form=f2","Correspondance","width=600,height=300,top=120,left=120");
    //
    fenetreouverte = true;
}
// comboC
function vcorrel3(champ) {
    //
    if (fenetreouverte == true) {
        pfenetre.close();
    }
    //
    var val = document.f1.elements[champ].value;
    //
    pfenetre = window.open("../spg/combobba.php?table="+champ+"&val="+val,champ,"width=500,height=150,top=120,left=120");
    //
    fenetreouverte = true;
}

/**
 * MISC
 */
//
function warn_user_query_change(form) {
    $("#merge_fields").html("[...] -> Vous devez enregistrer le formulaire pour que les champs de fusion soient mis a jour.");
}
// Cette fonction permet d'afficher un fichier du dossier tmp passe en
// parametre dans une popup
function traces(fichier) {
    //
    if (fenetreouverte == true) {
        pfenetre.close();
    }
    //
    pfenetre = window.open("../tmp/"+fichier, msg_popup_traces_title, "toolbar=no, scrollbars=yes, status=no, width=600, height=400, top=120, left=120");
    //
    fenetreouverte = true;
}

// Cette fonction permet d'afficher dans une popup ...
function genaff(file) {
    //
    if (fenetreouverte == true) {
        pfenetre.close();
    }
    //
    pfenetre = window.open("../spg/genaff.php?file="+file, msg_popup_genaff_title, "width=600, height=400, top=120, left=120, scrollbars=yes");
    //
    fenetreouverte = true;
}

// Cette fonction permet d'afficher dans une popup ...
function adresse_postale(form, libelle_voie, numero_voie) {
    //
    if (fenetreouverte == true) {
        pfenetre.close();
    }
    //
    pfenetre = window.open("../spg/adresse_postale.php?form="+form+"&libelle_voie="+libelle_voie.replace('\'','\\\'')+"&numero_voie="+numero_voie, msg_popup_adresse_postale_title, "width=400, height=400, top=120, left=120");
    //
    fenetreouverte = true;
}

////////////////////////////////////////////////////////////////////////////////
// 
////////////////////////////////////////////////////////////////////////////////
//

//textmultiarea
function selectauto(champ,selection)
{
if(document.f1.elements[champ].value=="")
   document.f1.elements[champ].value=document.f1.elements[selection].value;
else
   document.f1.elements[champ].value=document.f1.elements[champ].value+"\n"+document.f1.elements[selection].value;
   
document.f1.elements[selection].value="";
}
//selectlistemulti
function refresh_ids(champ,champ3) {
 var tids=document.f1.elements[champ3];
 var lids=document.f1.elements[champ];
 tids.value="";
 if (lids.options.length>0) {
    for (i=0;i<lids.options.length;i++) 
      if (lids.options[i].value) tids.value+=lids.options[i].value+",";
    tids.value=tids.value.substring(0,tids.value.length-1);
 }
}
function addlist(champ,champ2,champ3) {
  var linst=document.f1.elements[champ2];
  var lids=document.f1.elements[champ];
  if (linst.selectedIndex>=0) {
    lids.options[lids.options.length]=new Option(linst.options[linst.selectedIndex].text,linst.options[linst.selectedIndex].value);  
    refresh_ids(champ,champ3);
  }
}
function removelist(champ,champ3) {
  var lids=document.f1.elements[champ];
  if (lids.selectedIndex>=0) {
    lids.remove(lids.selectedIndex);  
    refresh_ids(champ,champ3);
  }                    
}
function removealllist(champ,champ3) {
  var lids=document.f1.elements[champ];
  lids.options.length=0;
  refresh_ids(champ,champ3);
  document.f1.elements["_unselect+champ"].disabled=false;
  document.f1.elements["_select+champ"].disabled=false;
}

/**
 * Gestion de l'ergonomie du formulaire OM_WIDGET
 */
// Au chargement de la page
$(function() {
    // Au chargement de la page donc du formulaire on appelle la fonction
    change_form_om_widget_type($("select#type").val());
    // On bind sur l'événement change l'appel de la fonction sur le champs
    // select du type
    $("select#type").change(function() {
        change_form_om_widget_type($(this).val());
    });
});
// Fonction qui remplace les libellés des champs en fonction de la valeur passée
// en paramètre
function change_form_om_widget_type(field_select_type_value) {
    //
    if (field_select_type_value == undefined) {
        return;
    }
    //
    if (field_select_type_value == "file") {
        $("#lib-lien").html("script");
        $("#lib-texte").html("arguments");
    } else {
        $("#lib-lien").html("lien");
        $("#lib-texte").html("texte");
    }
}


/**
 * Fonction permettant d'ajouter des temporisations
 * jusqu'a ce que l'élément passé en paramètre soit
 * dans le dom.
 */

/*
 * Wait Until Exists Version v0.2 - http://javascriptisawesome.blogspot.com/
 *
 *
 * TERMS OF USE - Wait Until Exists
 * 
 * Open source under the BSD License. 
 * 
 * Copyright © 2011 Ivan Castellanos
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
*/
(function(){
    var _waitUntilExists = {
        pending_functions : [],
        loop_and_call : function()
        {
            if(!_waitUntilExists.pending_functions.length){return}
            for(var i=0;i<_waitUntilExists.pending_functions.length;i++)
            {   
                var obj = _waitUntilExists.pending_functions[i];
                var resolution = document.getElementById(obj.id);
                if(obj.id == document){
                    resolution = document.body;
                }
                if(resolution){
                    var _f = obj.f;
                    _waitUntilExists.pending_functions.splice(i, 1)
                    if(obj.c == "itself"){obj.c = resolution}
                    _f.call(obj.c)                          
                    i--                 
                }
            }
        },
        global_interval : setInterval(function(){_waitUntilExists.loop_and_call()},5)
    }
    if(document.addEventListener){
        document.addEventListener("DOMNodeInserted", _waitUntilExists.loop_and_call, false);
        clearInterval(_waitUntilExists.global_interval);
    }
    window.waitUntilExists = function(id,the_function,context){
        context = context || window
        if(typeof id == "function"){context = the_function;the_function = id;id=document}
        _waitUntilExists.pending_functions.push({f:the_function,id:id,c:context})
    }
    waitUntilExists.stop = function(id,f){
        for(var i=0;i<_waitUntilExists.pending_functions.length;i++){
            if(_waitUntilExists.pending_functions[i].id==id && (typeof f == "undefined" || _waitUntilExists.pending_functions[i].f == f))
            {
                _waitUntilExists.pending_functions.splice(i, 1)
            }
        }
    }
    waitUntilExists.stopAll = function(){
        _waitUntilExists.pending_functions = []
    }
})()