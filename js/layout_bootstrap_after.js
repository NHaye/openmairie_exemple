/**
 * LAYOUT bootstrap
 *
 * @package openmairie_exemple
 * @version SVN : $Id: layout_bootstrap_after.js 2345 2013-05-29 08:49:50Z nhaye $
 */

// Initialisation des variables gerant les popup
var pfenetre;
var fenetreouverte = false;

//
var msg_loading = "<img src=\"../img/loading.gif\" alt=\"Le traitement est en cours. Merci de patienter.\" /> Le traitement est en cours. Merci de patienter.";

/**
 * Au chargement de la page
 */
$(function() {
    // Gestion de tous les elements de contenus
    om_initialize_content();
});

/**
 *
 */
// Cette fonction permet d'associer a un arbre html les fonctions jquery
// necessaires. Elle permet notamment lors du chargement d'une page en ajax
// d'associer le comportement du bouton, la gestion du calendrier et la gestion
// du fieldset.
function om_initialize_content() {
    // Gestion du skin des boutons, liens
    link_bind_button();
    // Gestion du picker color avec le plugin minicolors
    bind_form_field_rvb();
}

/**
 * MENU
 */

/**
 * BUTTON
 */
// Cette fonction permet d'associer au code html representant des boutons ou
// des liens le widget button de jqueryui
function link_bind_button() {
    //
    $("button, input:submit, input:reset, input:button, p.linkjsclosewindow, p.likeabutton").addClass("btn");
    //
    $("input:submit").addClass("btn-primary");
    $("a.retour").addClass("btn");
}

/**
 * TAB, FORM, SOUFORM
 */
//
function load_form_in_modal(link) {
    //
    var dialog = $("<div id=\"upload-container\" class=\"modal fade\" role=\"dialog\"></div>").insertAfter('#footer');
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
            $(dialog).modal({
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
        $(dialog).modal('hide').remove();
        return false;
    });
}

/**
 * FIELDSET
 */

/**
 * WIDGET - DASHBOARD
 */

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
/**
 * Fonction permettant de visualiser le fichier chargé dans formulaire
 * @param  string champ Nom du champ de type upload
 * @param  string obj   Nom de la classe du formulaire
 * @param  string id    Clé primaire de l'objet
 */
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
/**
 * Fonction permettant de visualiser le fichier chargé dans sousformulaire
 * @param  string champ Nom du champ de type upload
 * @param  string obj   Nom de la classe du formulaire
 * @param  string id    Clé primaire de l'objet
 */
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
    var dialog = $("<div id=\"upload-container\" class=\"modal fade\" role=\"dialog\"></div>").insertAfter('#footer');
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
            $(dialog).modal({
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
        $(dialog).modal('hide').remove();
        return false;
    });
}
// Cette fonction permet de retourner les informations sur le fichier téléchargé
// du formulaire d'upload vers le formulaire d'origine
function upload_return(form, champ, value) {
    $("form[name|="+form+"] #"+champ).attr('value', value);
    $('#upload-container').modal('hide').remove();
}
// Appel depuis form.php
function vupload(champ, taille_maximale, extension) {
    var link = "../spg/upload.php?origine="+champ+"&form=f1"+
        "taille_max="+taille_maximale+"&extension="+extension;
    upload_load_form(link);
}
// Appel depuis sousform.php
function vupload2(champ, taille_maximale, extension) {
    var link = "../spg/upload.php?origine="+champ+"&form=f2"+
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
function localisation(champ, chplan, positionx) {
    //
    if (fenetreouverte == true) {
        pfenetre.close();
    }
    //
    var plan = document.f1.elements[chplan].value;
    var x = document.f1.elements[positionx].value;
    var y = document.f1.elements[champ].value;
    //
    pfenetre = window.open("../spg/localisation.php?positiony="+champ+"&positionx="+positionx+"&plan="+plan+"&form=f1"+"&x="+x+"&y="+y,"localisation","toolbar=no,scrollbars=yes,width=800,height=600,top=10,left=10");
    //
    fenetreouverte = true;
}
//
function localisation2(champ, chplan, positionx) {
    //
    if (fenetreouverte == true) {
        pfenetre.close();
    }
    //
    var plan = document.f2.elements[chplan].value;
    var x = document.f2.elements[positionx].value;
    var y = document.f2.elements[champ].value;
    //
    pfenetre = window.open("../spg/localisation.php?positiony="+champ+"&positionx="+positionx+"&plan="+plan+"&form=f2"+"&x="+x+"&y="+y,"localisation","toolbar=no,scrollbars=yes,width=800,height=600,top=10,left=10");
    //
    fenetreouverte = true;
}

/**
 * FORM WIDGET - DATEPICKER
 */

/**
 * FORM WIDGET - CORREL
 */

/**
 * MISC
 */
