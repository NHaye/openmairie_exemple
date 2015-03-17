/**
 * LAYOUT - jquerymobile
 * Ce script ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: layout_jquerymobile_after.js 2345 2013-05-29 08:49:50Z nhaye $
 */


// Chaines de caracteres et messages utilises dans le script dans des
// variables dans l'optique de les traduire
// XXX Mettre en place un systeme de traduction des chaines du javascript
var msg_alert_error_verifnum = "Vous ne devez saisir que des nombres";
var msg_alert_error_fdate = "La date saisie n'est pas valide";
var msg_alert_error_ftime = "L'heure saisie n'est pas valide";
var msg_alert_error_tabs = "Le contenu de l'onglet ne peut pas etre charge. Verifiez votre connexion reseau. Si le probleme persiste contactez votre administrateur.";
var msg_widget_action_retract = "Reduire/Deplier l'affichage du widget";
var msg_widget_action_delete = "Supprimer le widget";
var msg_popup_upload_title = "Upload";
var msg_popup_traces_title = "Traces";
var msg_popup_adresse_postale_title = "Adresse postale";
var msg_popup_genaff_title = "Fichier";
var msg_loading = "<img src=\"../img/loading.gif\" alt=\"Le traitement est en cours. Merci de patienter.\" /> Le traitement est en cours. Merci de patienter.";


$(function(){
    var menuStatus;

    $("#menu").toggle();

    $("a.showMenu").click(function(){
        if(menuStatus != true){
            $("#menu").toggle();
        $(".ui-page-active").animate({
            marginLeft: "265px",
          }, 300, function(){menuStatus = true});
          return false;
          } else {
            $(".ui-page-active").animate({
            marginLeft: "0px",
          }, 300, function(){menuStatus = false});
                        $("#menu").toggle();
            return false;
          }
    });
 
    $('.pages').live("swipeleft", function(){
        if (menuStatus){
        $(".ui-page-active").animate({
            marginLeft: "0px",
          }, 300, function(){menuStatus = false});
          }
    });
 
    $('.pages').live("swiperight", function(){
        if (!menuStatus){
        $(".ui-page-active").animate({
            marginLeft: "165px",
          }, 300, function(){menuStatus = true});
          }
    });
 
    $("#menu li a").click(function(){
        var p = $(this).parent();
        if($(p).hasClass('active')){
            $("#menu li").removeClass('active');
        } else {
            $("#menu li").removeClass('active');
            $(p).addClass('active');
        }
    });

});
$( "#popupPanel" ).on({
    popupbeforeposition: function() {
        var h = $( window ).height();

        $( "#popupPanel" ).css( "height", h );
    }
});

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
            $('.ui-page').trigger('create');
            //link_bind_button();// a voir
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
//
function load_form_in_modal_mobile(link,champ) {
    //
    var dialog = $("<div id=\"upload-container\" class=\"modal fade\" role=\"dialog\"></div>").insertAfter('#lib-'+champ);//mo
    //
    $.ajax({
        type: "GET",
        url: link,
        success: function(html){
            // chargement de l'url dans le conteneur dialog
            dialog.empty();
            dialog.append(html);
            $('.ui-page').trigger('create');// ajout 
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

// Function permettant de vider le champ de formulaire
function supprimerUpload(champ) {
    //
    if (document.f1.elements[champ].value != "") {
        document.f1.elements[champ].value = "";
        document.f1.elements[champ+'_upload'].value = "";
    }
}
function voir_mobile(champ, obj, id) {
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
        load_form_in_modal_mobile(link, champ);
    }
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

//upload
function upload_load_form_mobile(link,champ) {
    //
    var dialog = $("<div id=\"upload-container\" role=\"dialog\"></div>").insertAfter('#lib-'+champ);//mo
    //
    $.ajax({
        type: "GET",
        url: link,
        success: function(html){
            // chargement de l'url dans le conteneur dialog
            dialog.empty();
            dialog.append(html);
            $('.ui-page').trigger('create');// ajout 
            // initilisation du contenu
            //om_initialize_content(); //si actif empeche la fermeture ??
            // Gestion du skin des boutons, liens
            // link_bind_button();
            // Gestion du calendrier avec le widget datepicker de jqueryui
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
                //om_initialize_content(); // a voir
               // link_bind_button();
                // initialisation du formulaire
                upload_bind_form_submit();
            }});
        // retour de la valeur false pour que le formulaire ne soit pas soumis
        // de manière normale
        return false; 
    });
}
// Cette fonction permet de retourner les informations sur le fichier téléchargé
// du formulaire d'upload vers le formulaire d'origine
function upload_return(form, champ, value) {
    $("form[name|="+form+"] #"+champ).attr('value', value);
    $('#upload-container').dialog('close').remove();
}
// Appel depuis form.php
function vupload_mobile(champ, taille_maximale, extension) {
    var link = "../spg/upload.php?origine="+champ+"&form=f1"+
        "taille_max="+taille_maximale+"&extension="+extension;
    upload_load_form_mobile(link,champ);
}
/*
/**
 * BUTTON
 */
// Cette fonction permet d'associer au code html representant des boutons ou
// des liens le widget button de jqueryui
/*function link_bind_button() {
    //
    $("button, input:submit, input:reset, input:button, p.linkjsclosewindow, p.likeabutton").button();
}*/
// Cette fonction permet d'associer a un arbre html les fonctions jquery
// necessaires. Elle permet notamment lors du chargement d'une page en ajax
// d'associer le comportement du bouton, la gestion du calendrier et la gestion
// du fieldset.
/*function om_initialize_content() {
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
}*/
//textmultiarea
/*function selectauto(champ,selection)
{
if(document.f1.elements[champ].value=="")
   document.f1.elements[champ].value=document.f1.elements[selection].value;
else
   document.f1.elements[champ].value=document.f1.elements[champ].value+"\n"+document.f1.elements[selection].value;
   
document.f1.elements[selection].value="";
}
//localisation
function localisation(champ, chplan, positionx) {
    //
    var plan = document.f1.elements[chplan].value;
    var x = document.f1.elements[positionx].value;
    var y = document.f1.elements[champ].value;
    //
    link = "../spg/localisation.php?positiony="+champ+"&positionx="+positionx+"&plan="+plan+"&form=f1"+"&x="+x+"&y="+y;
    //
    load_form_in_modal_mobile(link,champ);
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
*/