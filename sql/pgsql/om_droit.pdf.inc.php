<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

$DEBUG=0;
$orientation='L';// orientation P-> portrait L->paysage
$format='A4';// format A3 A4 A5
$police='arial';
$margeleft=10;// marge gauche
$margetop=5;// marge haut
$margeright=5;//  marge droite
$border=1; // 1 ->  bordure 0 -> pas de bordure
$C1=0;// couleur texte  R
$C2=0;// couleur texte  V
$C3=0;// couleur texte  B
$size=10; //taille POLICE
$height=4; // hauteur ligne tableau 
$align='L';
$fond=1;// 0- > FOND transparent 1 -> fond
$C1fond1=234;// couleur fond  R 241
$C2fond1=240;// couleur fond  V 241
$C3fond1=245;// couleur fond  B 241
$C1fond2=255;// couleur fond  R
$C2fond2=255;// couleur fond  V
$C3fond2=255;// couleur fond  B
$libtitre='Liste openexemple.om_droit'; // libelle titre
$flagsessionliste=0;// 1 - > affichage session liste ou 0 -> pas d'affichage
$bordertitre=0; // 1 ->  bordure 0 -> pas de bordure
$aligntitre='L'; // L,C,R
$heightitre=10;// hauteur ligne titre
$grastitre='B';//$gras='B' -> BOLD OU $gras=''
$fondtitre=0; //0- > FOND transparent 1 -> fond
$C1titrefond=181;// couleur fond  R
$C2titrefond=182;// couleur fond  V
$C3titrefond=188;// couleur fond  B
$C1titre=75;// couleur texte  R
$C2titre=79;// couleur texte  V
$C3titre=81;// couleur texte  B
$sizetitre=15;
$flag_entete=1;//entete colonne : 0 -> non affichage , 1 -> affichage
$fondentete=1;// 0- > FOND transparent 1 -> fond
$heightentete=10;//hauteur ligne entete colonne
$C1fondentete=210;// couleur fond  R
$C2fondentete=216;// couleur fond  V
$C3fondentete=249;// couleur fond  B
$C1entetetxt=0;// couleur texte R
$C2entetetxt=0;// couleur texte V
$C3entetetxt=0;// couleur texte B
$C1border=159;// couleur texte  R
$C2border=160;// couleur texte  V
$C3border=167;// couleur texte  B
$l0=22; // largeur colone -> champs 0 - om_droit
$be0='L';// border entete colone
$b0='L';// border cellule colone
$ae0='C'; // align cellule entete colone
$a0='L';
$l1=258; // largeur colone -> champs1 - libelle
$be1='LR';// border entete colone
$b1='LR';// border cellule colone
$ae1='C'; // align cellule entete colone
$a1='L';
$widthtableau=280;
$bt=1;// border 1ere  et derniere ligne  du tableau par page->0 ou 1
$sql="select om_droit, libelle from ".DB_PREFIXE."om_droit";
?>