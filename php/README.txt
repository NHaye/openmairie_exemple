Developpement librairie openMairie
==================================

Ce fichier est destine aux developpeurs de la librairie openMairie, il permet
de donner des informations et des consignes pour pouvoir commiter directement
dans le projet de la librairie openMairie depuis le dossier consacré aux
librairies d'openmairie_exemple.

Problematique
-------------

Le depot SVN utilise ici pour la librairie openMairie est le tronc. Cette url
ne contient pas d'information concernant votre utilisateur ni concernant la
methode ssh a utiliser pour pouvoir commiter.

svn://scm.adullact.net/svnroot/openmairie/openmairie/trunk.


Nous avons besoin pour pouvoir commiter d'une url de ce type :

svn+ssh://nom-du-developpeur@scm.adullact.net/svnroot/openmairie/openmairie/trunk


Solution 1
----------

Il faut se placer dans le repertoire openmairie de ce dossier et executer la
commande suivante :

svn switch --relocate svn://scm.adullact.net/svnroot/openmairie/openmairie/trunk svn+ssh://nom-du-developpeur@scm.adullact.net/svnroot/openmairie/openmairie/trunk


Attention tout svn up dans ce dossier ou plus haut dans l'arborescence
remplacera ce changement par la valeur d'origine.


Solution 2
----------

Remplacer directement l'url du depot dans le fichier EXTERNALS.txt puis
appliquer le svn propset. Il faudra simplement faire attention de ne pas
commiter ce dossier et ces modifications.
