/**
 * Ce script javascript ...
 *
 * @package openmairie_exemple
 * @version SVN : $Id: sig.js 2997 2014-12-11 10:23:06Z baldachino $
 */

// variable de stockage de la géométrie pour transmission à form_sig
var geomenr; // fenetre tab_sig.php
var fendata; // fenetre form.php (si popup=1)
// a traiter le pb f1 et f2 sous
// stocke le nom de la baselayer sélectionnée
function mapBaseLayerChanged(event) {
	if(event.property === "visibility") {
		mapChangeSession();
	}
}
function mapChangeZoom(evt) {	
	if (zoomSelected!=map.zoom) {
		zoomSelected=map.zoom;
		mapChangeSession();
	}
}
function mapChangeSession() {
	for(var i=0; i<ol_map.length; i++) {
		ol_visibility[i] = 'Non';
		if (ol_panier[i] == '' && ol_cache_type[i] !='IMP' && ol_baselayer[i] != "Oui" && wms_maps[i].visibility) 
			ol_visibility[i] = 'Oui';
	}
	baseLayerSelected=map.baseLayer.name;
	fichier_calc = '../scr/sig_session.php';
	$.ajax(
		{
			url: fichier_calc,
			type: 'POST',
			data: { 
				obj: obj, 
				zoom: zoomSelected,
				base: baseLayerSelected,
				visibility: ol_visibility,
				seli: seli
			},
			success: function(sResult) {
				if (sResult!='ok')
					alert(sResult);
			},
			timeout:1000
		}
	);
}
// installation du control de géolocalisation
function addLocalisation() {
	var geolocate = new OpenLayers.Control.Geolocate({id: 'locate-control',geolocationOptions: { enableHighAccuracy: false, maximumAge: 0, timeout: 7000}});
	var styleGeolocate = {fillOpacity: 0.1,fillColor: '#000',strokeColor: '#f00',strokeOpacity: 0.6};
	geolocate.events.register("locationupdated", this, function(e) {
		vector.removeAllFeatures();
		vector.addFeatures([ 
			new OpenLayers.Feature.Vector( e.point, {}, {graphicName: 'cross',strokeColor: '#f00',strokeWidth: 2,fillOpacity: 0,pointRadius: 10}),
			new OpenLayers.Feature.Vector( OpenLayers.Geometry.Polygon.createRegularPolygon( new OpenLayers.Geometry.Point(e.point.x, e.point.y), e.position.coords.accuracy / 2, 50, 0 ), {}, styleGeolocate)
		]);
		map.zoomToExtent(vector.getDataExtent());
	  });
	map.addControl(geolocate);
}
// activation de la géolocalisation
function locate() {
    var control = map.getControlsBy("id", "locate-control")[0];
    if (control.active) {
        control.getCurrentLocation();
    } else {
        control.activate();
    }
}
// AB_F08 F carto - Géolocalisation

function selected (evt) {
    // fonction de test evenement suite select objet
    alert(evt.feature.id + " selected on " + this.name);
}

// ouverture du popup couche json
function ouvre_popup(page) {
   if (fenetreouverte == true)
       pfenetre.close ();
   pfenetre=window.open(page,"nom_popup","resizable=no, location=no, width=700, height=500, menubar=no, status=no, scrollbars=yes, menubar=no, top=100,left=100");
   fenetreouverte=true;
}

// fermeture du popup avec la croix rouge
function onPopupClose(evt) {
     select_json.unselectAll();
	 // onPopupClose - gestion des popup
	 popupClose = true;
}
 
// selection de point json -> popup
function onFeatureSelect(event) {
	// -->  onFeatureSelect - traitement transféré à la fonction traiteclic
   feature = event.feature;
   selfeature = true;
   traiteclic(event);
}

// deselection de point json -> fermeture popup
function onFeatureUnselect(event) {
   var feature = event.feature;
   if(feature.popup) {
      map.removePopup(feature.popup);
      feature.popup.destroy();
      delete feature.popup;
   }
   // gestion variables selfeature et popupClose
   selfeature = false;
   popupClose = false;
}

// a la fin du chargement de la couche wkt
// si il y a un identifiant idx, zoom sur idx
// a voir : eviter de faire un center sur un bound alors qu on a le pt de centrage
function onLayerLoaded(evt) {
	if (idx!='') {
		if (vector_center.features.length>0) {
			// onLayerLoaded - utilisation de la couche vecteur pour centrer et suppression de cette couche
			map.setCenter(vector_center.features[0].geometry.getBounds().getCenterLonLat());
			if (lst_geom_type_geometrie.length > 1) {
				map.removeLayer(vector_center,false);
			}
		}
    } 
}

// en selection de la geometrie wkt : saisie de la geometrie selectionne
function onSaisieSelect(evt){
    //ne pas faire la transformation sur la couche mais sur un clone
    geom = evt.feature.geometry.clone()
    geom.transform(mercator, projection_externe);
	// affectation de la géométrie à enregistrer pour transmission à form_sig
	geomenr=geom;
	// affectation de la fenetre d appel de tab_sig.php dans le cas de popup = 1
	fendata=window.opener;
	//
    if(fenetreouverte==true)
        pfenetre.close();
    // compatibilite IE -> mettre des ' au lieu de "
	// onSaisieSelect - affichage de la dialog d'enregistrement uniquement en mode valider
	var action_panneau;
	action_panneau = document.getElementById("indication").innerHTML;
	if (action_panneau==msg_valider) {
		pfenetre=window.open("form_sig.php?obj="+obj+"&idx="+idx+"&table="+table+"&champ="+champ+"&popup="+ouv_popup, 'saisie_geometrie', 'width=400,height=300,top=120,left=120' );
		fenetreouverte=true;
	}
}

// deselection de la geometrie wkt
function onSaisieUnselect(evt){
    if(fenetreouverte==true)
        pfenetre.close();
}

//-- modification panneau_controle incluant tous les types de géométries
//-- modification panneau_controle et ajout panneau_controle_action
function panneau_controle(element) {
	panneau_controle_action(element.value);
}

function panneau_controle_action(element) {
    // gestion des controles
	selgeom = document.f1sig.selgeom.value;
	if (selgeom=='point') {
		if(element == 'Dessiner') {
			controls['draw'].activate();
			controls['select'].deactivate();
			controls['modify'].deactivate();
			controls['drag'].deactivate();
			select_json.deactivate(); 
		}
		if(element == 'Deplacer') {
			controls['drag'].activate();
			controls['select'].activate();
			select_json.deactivate(); 
		}
		if(element == 'Enregistrer') {
			controls['select'].activate();
			controls['modify'].activate();
			controls['draw'].deactivate();
			controls['drag'].deactivate();
			select_json.deactivate(); 
		}   
		if(element == 'Data') {
			select_json.activate();
			controls['select'].deactivate();
			controls['modify'].deactivate();
			controls['draw'].deactivate();
			controls['drag'].deactivate();
		}
		if(element == 'Panier') {
			select_json.activate();
			controls['select'].deactivate();
			controls['modify'].deactivate();
			controls['draw'].deactivate();
			controls['drag'].deactivate();
		}
	}
	if (selgeom=='line') {
		if(element == 'Dessiner') {
			controls['draw'].activate(); // ***
			controls['select'].deactivate();
			controls['modify'].activate();
			controls['drag'].deactivate();
			select_json.deactivate(); 
		}
		if(element == 'Deplacer') {
			controls['drag'].activate();
			controls['select'].activate();
			//select_json.deactivate(); 
		}
		if(element == 'Enregistrer') {
			controls['select'].activate();
			controls['modify'].activate();
			controls['draw'].deactivate(); // ***
			controls['drag'].deactivate();
			// select_json.deactivate(); 
		}   
		if(element == 'Data') {
			select_json.activate();
			controls['select'].deactivate();
			controls['modify'].deactivate();
			controls['draw'].deactivate(); // ***
			controls['drag'].deactivate();
		}
		if(element == 'Panier') {
			select_json.activate();
			controls['select'].deactivate();
			controls['modify'].deactivate();
			controls['draw'].deactivate(); // ***
			controls['drag'].deactivate();
		}
	}
	if (selgeom=='polygon') {
		if(element == 'Dessiner') {
			controls['draw'].activate(); // ***
			controls['select'].deactivate();
			controls['modify'].activate();
			controls['drag'].deactivate();
			select_json.deactivate(); 
		}
		if(element == 'Deplacer') {
			controls['drag'].activate();
			controls['select'].activate();
			select_json.deactivate(); 
		}
		if(element == 'Enregistrer') {
			controls['select'].activate();
			controls['modify'].activate();
			controls['draw'].deactivate();//***
			controls['drag'].deactivate();
			select_json.deactivate(); 
		}   
		if(element == 'Data') {
			select_json.activate();
			controls['select'].deactivate();
			controls['modify'].deactivate();
			controls['draw'].deactivate();//***
			controls['drag'].deactivate();
		}
		if(element == 'Panier') {
			select_json.deactivate();
			controls['select'].deactivate();
			controls['modify'].deactivate();
			controls['draw'].deactivate();//***
			controls['drag'].deactivate();
		}
	}
}

//restrictedextend
function toggleRestrictedExtent() {
    if(map.restrictedExtent == null) {
        map.setOptions({restrictedExtent: extent});
    } else {
        map.setOptions({restrictedExtent: null});
    }
}

// Indicateur de l etat de l interface
function msg(a) {
	document.getElementById("indication").innerHTML=a;
	// msg - gestion de sortie du mode panier ou de changement de panier
	if (document.f1sig.panier_sel.value!="") {
		map.removeLayer(panier,false);
		map.removeLayer(catalogue,false);
	}
	document.f1sig.panier_sel.value = "";
}

// gestion de la liste de choix de la géométrie
function onChangeSelComp() {
	selcomp=document.f1sig.selcomp.value;
	tabselcomp=selcomp.split('¤');
	seli=tabselcomp[0]
	mapChangeSession();
	document.f1sig.seli.value=tabselcomp[0];
	document.f1sig.selmaj.value=tabselcomp[1];
	document.f1sig.selgeom.value=tabselcomp[2];
	document.f1sig.seltable.value=tabselcomp[3];
	document.f1sig.selchamp.value=tabselcomp[4];
	// traitement mode d'ouverture en popup
	window.location.reload(false);
	//window.location.href="tab_sig.php?&obj="+obj+"&idx="+idx+"&zoom="+zoom+"&seli="+tabselcomp[0]+"&popup="+ouv_popup;
}

// ajout fonction traiteclic 
function traiteclic(e) {
	action_panneau = document.getElementById("indication").innerHTML;
	if (selfeature == true) {
		var contenuHtml, popup;
		var contenuHtml =  "<div  style='font-size:"+fontsize_popup+";margin:1em 1em 1em 1em;width:"+width_popup+"px'><font  style='color:"+couleurtitre_popup+";font-weight:"+weightitre_popup+";'>" + feature.attributes.titre + "<br></font>";
		contenuHtml = contenuHtml + feature.attributes.description + "</div>";      
		popup = new OpenLayers.Popup.Anchored("featurePopup",
			feature.geometry.getBounds().getCenterLonLat(),
			new OpenLayers.Size(200, 200),
			contenuHtml,
			{size: new OpenLayers.Size(0, 0), offset: new OpenLayers.Pixel(0, 0)},
			true,
		onPopupClose);
		popup.setBackgroundColor(fond_popup);
		popup.setBorder(cadre_popup+"px solid "+couleurcadre_popup);    
		popup.autoSize = true;
		feature.popup = popup;
		popup.feature = feature;
		popup.setOpacity(opacity_popup);
		// ouverture exclusive de popup (ferme les autres)
		map.addPopup(popup, true);
	} else {
		if (popupClose == true) {
			popupClose = false;
		} else {
			var res = "";
			if (action_panneau==msg_panier) {
				i = document.f1sig.panier_sel.value;
				if (i != '') {
					if (ol_panier[i] == 'Oui') {
						var bGetFeatureInfo=false;
						if (ol_cache_type[i] == "") {
							var params = { 
								REQUEST: "GetFeatureInfo", BBOX: map.getExtent().toBBOX(), SERVICE: "WMS", VERSION: "1.3.0", X: Math.round(e.xy.x), Y: Math.round(e.xy.y), INFO_FORMAT: 'text/html', QUERY_LAYERS: ol_pa_layer[i], 
								FEATURE_COUNT: 50, Layers: ol_pa_layer[i], WIDTH: map.size.w, HEIGHT: map.size.h, styles: catalogue.params.STYLES, srs: projection_mercator};
							var request = OpenLayers.Request.GET({ url: catalogue.url, params: params, async: false});
							var res = request.responseText;
							bGetFeatureInfo=true;
						} else if ( ol_cache_type[i] == "TCF"|| ol_cache_type[i] == "SMT") {
							if (ol_cache_gfi_chemin[i] != "" && ol_cache_gfi_couches[i] != "") {
								var params = {
									REQUEST: "GetFeatureInfo", BBOX: map.getExtent().toBBOX(), SERVICE: "WMS", VERSION: "1.3.0", X: Math.round(e.xy.x), Y: Math.round(e.xy.y), INFO_FORMAT: 'text/html', QUERY_LAYERS: ol_cache_gfi_couches[i], FEATURE_COUNT: 50,
									Layers:ol_cache_gfi_couches[i], WIDTH: map.size.w, HEIGHT: map.size.h, srs: projection_mercator};
								var request = OpenLayers.Request.GET({ url: ol_cache_gfi_chemin[i], params: params, async: false});
								var res = request.responseText;
								bGetFeatureInfo=true;
							}
						}
						if (bGetFeatureInfo==true) {
							var lines = res.split('\n');
							var layer="";
							for (lcv = 0; lcv < (lines.length); lcv++) {
								if (lines[lcv].indexOf('>Layer<') != -1) {
									layer=lines[lcv].substring(lines[lcv].indexOf('<TD>')+4, lines[lcv].indexOf('</TD>'));
								}
								if ((layer==ol_pa_layer[i]) && (lines[lcv].indexOf('<TH>'+ol_pa_attribut[i]+'</TH>') != -1)) {
									var rec=lines[lcv].substring(lines[lcv].indexOf('<TD>')+4, lines[lcv].indexOf('</TD>'));
									rec = ol_pa_encaps[i]+rec+ol_pa_encaps[i];
									var champ_panier = document.f1sig.panier_val.value;
									if (champ_panier.indexOf(rec) != -1) {
										champ_panier=champ_panier.replace(","+rec, "");
										champ_panier=champ_panier.replace(rec+",", "");
										champ_panier=champ_panier.replace(rec, "");
									} else {
										if (champ_panier == "") {
											champ_panier = rec;
										} else {
											champ_panier = champ_panier+","+rec;
										}
									}
									document.f1sig.panier_val.value = champ_panier;
								}
							}
							map.removeLayer(panier,false);	
							tmp="";
							tmp="sig_panier.php?idx="+ol_om_sig_map_wms[i]+"&lst="+document.f1sig.panier_val.value;
							panier = new OpenLayers.Layer.Vector(
								"panier : "+ol_pa_nom[i], {
									protocol: new OpenLayers.Protocol.HTTP({ url: tmp, format: new OpenLayers.Format.WKT({internalProjection:mercator,externalProjection:projection_externe})}),
									strategies: [new OpenLayers.Strategy.Fixed()],
									styleMap: new OpenLayers.StyleMap({"default": {strokeColor: "green",strokeWidth:3,strokeOpacity: 0.8,fillColor : "green", fillOpacity: 0.4, pointRadius : 5},"select": {strokeColor: "black",strokeWidth:3,strokeOpacity: 0.8,fillColor : "green", pointRadius : 5}})
								}
							);
							map.addLayer(panier);
						}
						
					}
				}

			} else if (action_panneau==msg_data) { // action data 
				for(var i=0; i<ol_map.length; i++) {
					if (ol_panier[i] != 'Oui') {
						if (typeof wms_maps[i] !== "undefined") {
							if (wms_maps[i].getVisibility() == true) {
								if (ol_cache_type[i] == "") {
									var params = { REQUEST: "GetFeatureInfo", BBOX: map.getExtent().toBBOX(), SERVICE: "WMS", VERSION: "1.3.0", X: Math.round(e.xy.x), Y: Math.round(e.xy.y), INFO_FORMAT: 'text/xml', QUERY_LAYERS: wms_maps[i].params.LAYERS,
										FEATURE_COUNT: 50, Layers: wms_maps[i].params.LAYERS, WIDTH: map.size.w, HEIGHT: map.size.h, styles: wms_maps[i].params.STYLES, srs: projection_mercator};
									if(wms_maps[i].params.CQL_FILTER != null) {params.cql_filter = wms_maps[i].params.CQL_FILTER;} 
									if(wms_maps[i].params.FILTER != null) {params.filter = wms_maps[i].params.FILTER;}
									if(wms_maps[i].params.FEATUREID) {params.featureid = wms_maps[i].params.FEATUREID;}
									var request = OpenLayers.Request.GET({url: wms_maps[i].url,params: params,async: false});
									res=traiteGetFeatureInfo(i,request.responseText,res);
								} else if ( ol_cache_type[i] == "TCF" || ol_cache_type[i] == "SMT") {
									if (ol_cache_gfi_chemin[i] != "" && ol_cache_gfi_couches[i] != "") {
										var params = { REQUEST: "GetFeatureInfo", BBOX: map.getExtent().toBBOX(), SERVICE: "WMS", VERSION: "1.3.0", X: Math.round(e.xy.x), Y: Math.round(e.xy.y), INFO_FORMAT: 'text/xml', QUERY_LAYERS:  ol_cache_gfi_couches[i],
											FEATURE_COUNT: 50, Layers:  ol_cache_gfi_couches[i], WIDTH: map.size.w, HEIGHT: map.size.h, srs: projection_mercator};
										var request = OpenLayers.Request.GET({ url: ol_cache_gfi_chemin[i], params: params, async: false});
										res=traiteGetFeatureInfo(i,request.responseText,res);
									}								
								}
							}
						}
					}
				}
				if (res != "") {
					res=traiteGetFeatureInfoPopup(res);
					popup = new OpenLayers.Popup.FramedCloud(
                        "chicken", 
                        map.getLonLatFromPixel(e.xy), 
						new OpenLayers.Size(500,180), 
						res, 
						null, 
						true);
					popup.setBackgroundColor("#ffffff");
					popup.setOpacity(.9);
					popup.setRicoCorners;
					map.addPopup(popup,true);
					popup.events.register("click", map, popupDestroy);
				}
			}
		}
	 }
}
// traitement standard des données XML récupérées par GetFeatureInfo , présentation sous forme de tableau attribut valeur
// cette fonction peut être surchargée dans  app/js/sig.js pour personnaliser l'affichage
function traiteGetFeatureInfo(i,rText,res) {
	var xmlf = new OpenLayers.Format.XML();
	var data = xmlf.read(rText).documentElement;
	featureInfo = {};
	var layerInfo = xmlf.getElementsByTagNameNS(data,'*','Layer');
	for (var i=0; i < layerInfo.length;i++) {
		var layer = layerInfo[i];
		var layerName = layer.getAttribute('name');
		featureInfo[layerName] = {};
		var features = xmlf.getElementsByTagNameNS(layer,'*','Feature');
		if (features.length>0) {
			res+="<TABLE border=1 width=100%><TR><TH width=100%><center>"+layerName+"</center></TH></TR><BR><TABLE border=1 width=100%>";
			for( var j=0; j<features.length;j++) {
				var feature=features[j];
				var featureId=feature.getAttribute('id');
				featureInfo[layerName][featureId] = {};
				var attributes = xmlf.getElementsByTagNameNS(feature, '*','Attribute');
				for (var k=0; k < attributes.length; k++) {
					var att=attributes[k];
					res+="<TR><TH>"+att.getAttribute('name')+'</TH><TD>'+att.getAttribute('value')+"</TD></TR>"; 
				}
			}
			res+="</BR></TABLE></BR><BR>";
		}
	}
	return res;
}
// encapsulation des données à afficher
// cette fonction peut être surchargée dans  app/js/sig.js pour personnaliser l'affichage
function traiteGetFeatureInfoPopup(res) {
	avant='<HEAD><TITLE>Résultat</TITLE><meta http-equiv="Content-Type" content="text/html;charset=utf-8"></HEAD><BODY>';
	apres='</BODY>';
	return avant+res+apres;
}
// récupère la valeur d'un attribut
function traiteGetFeatureInfoRecAttribut(attributes,name) {
	res="";
	for (var k=0; k < attributes.length; k++) {
		var att=attributes[k];
		if ( att.getAttribute('name')==name) { 
			res=att.getAttribute('value');
			break;
		}
	}
	return res;
}
// récupère la valeur d'un attribut et formate la restitution
function traiteGetFeatureInfoRecAttributFormat(attributes,name,title, formatage) {
	res=traiteGetFeatureInfoRecAttribut(attributes,name);
	if (res!="") 
	{
		tmp=formatage;
		tmp = tmp.split('[TITLE]').join(title);
		tmp = tmp.split('[VALUE]').join(res);
		res=tmp;
	}
	return res;
}
// récupère la valeur d'un attribut et formate la restitution avec deux boutons avec lien 
//[RLINK] affiche la fenètre dans l'onglet courant [NLINK] affiche la fenètre dans un nouvel onglet
function traiteGetFeatureInfoRecAttributFormatLink(attributes,name,title, formatage, link) {
	res=traiteGetFeatureInfoRecAttribut(attributes,name);
	if (res!="") 
	{ 
		tmp=formatage;
		tmp = tmp.split('[TITLE]').join(title);
		tmp = tmp.split('[RLINK]').join('<a class="upload ui-state-default ui-corner-all" href="[LINK]"><span class="ui-icon ui-icon-extlink" title="Cliquer pour aller à la fiche correspondante">aller</span></a>');
		tmp = tmp.split('[NLINK]').join('<a class="upload ui-state-default ui-corner-all" href="[LINK]" target="_blank"><span class="ui-icon ui-icon-newwin" title="Cliquer pour aller à la fiche correspondante dans une nouvelle fenètre">aller</span></a>');
		tmp = tmp.split('[LINK]').join(link);
		tmp = tmp.split('[VALUE]').join(res);
		res=tmp;
	}
	return res;
}
// ajout fonction choisirpanier
function choisirpanier(a,element) {
	msg(msg_panier);
	panneau_controle_action('Panier');
	if (document.f1sig.panier_sel.value!="") {
		map.removeLayer(panier,false);
		map.removeLayer(catalogue,false);
	}
	document.f1sig.panier_sel.value = a;
	document.f1sig.panier_val.value = '';
	tmp="";
	tmp="sig_panier.php?idx="+ol_om_sig_map_wms[a]+"&lst=";
	panier = new OpenLayers.Layer.Vector( "Panier", {
			protocol: new OpenLayers.Protocol.HTTP({url: tmp, format: new OpenLayers.Format.WKT({internalProjection:mercator,externalProjection:projection_externe})}),
			strategies: [new OpenLayers.Strategy.Fixed()],
			styleMap: new OpenLayers.StyleMap({"default": {strokeColor: "green",strokeWidth:3,strokeOpacity: 0.8,fillColor : "green", fillOpacity: 0.4, pointRadius : 5},"select": {strokeColor: "black",strokeWidth:3,strokeOpacity: 0.8,fillColor : "green", pointRadius : 5}})
	  });
	map.addLayer(panier);
	paramsWms = {};
	paramsWms.layers=ol_couches[a];
	paramsWms.transparent=true;
	if (ol_filter[a] != "") { paramsWms.filter=ol_filter[a];}
	optionsWms = {};
	optionsWms.isBaseLayer=false;
	optionsWms.visibility=true;
	if (ol_singletile[a] == 'Oui') { 
		optionsWms.singleTile= true;
		optionsWms.ratio=1;
	}
	catalogue = new OpenLayers.Layer.WMS( "catalogue",ol_chemin[a],paramsWms,optionsWms);
	map.addLayer(catalogue);
}

// ajout fonction recuppanier
function recuppanier(a,element) {
	vector.addFeatures(panier['features']);
	vector.refresh;
	msg(msg_dessin);
	panneau_controle_action('Dessiner');
}

// mobile
function locatemobile() {
	alert("mobile");
   var control = map.getControlsBy("id", "locate-control")[0];
    if (control.active) {
        control.getCurrentLocation();
    } else {
        control.activate();
    }
}
// lancement impression
function imprimer() {
	selimp=document.f1sig.selimp.value;
	if (selimp==-1) {
		alert("Vous devez choisir un rapport");
	} else {
		url_imp=ol_chemin[selimp];
		if (ol_pa_layer[selimp] != "") {
			url_imp=url_imp.replace("[LAYERS]",ol_pa_layer[selimp]);
		} else {
			url_imp=url_imp.replace("[LAYERS]",ol_couches[selimp]);		
		}		
		url_imp=url_imp.replace("[EXTENT]",this.map.getExtent().toBBOX());
		if (ol_filter[selimp] != "") {
		}
		window.location.href=url_imp+"&FILTER="+ol_filter[selimp]+ol_imp_titre[selimp];
	}
}
// modification de la taille de la carte en fonction de l'impression
function onChangeSelImp() {
	selimp=document.f1sig.selimp.value;
	if (selimp==-1) {
		document.getElementById("map-id").style.width="100%";
		document.getElementById("map-id").style.height="90%";
		map.updateSize();
	} else {
		document.getElementById("map-id").style.width=ol_cache_gfi_chemin[selimp]+"px";
		document.getElementById("map-id").style.height=ol_cache_gfi_couches[selimp]+"px";
		map.updateSize();
	}
}
