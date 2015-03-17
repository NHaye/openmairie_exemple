Framework openMairie Tests unitaires et fonctionnels
====================================================

Installation
############

Pré-requis
----------

Le lancement des tests est destiné aux développeurs de l'application, il est
donc convenu que l'environnement de tests se déploit sur un poste de
développement linux. Les commandes d'installation indiquées ici supposent
quelques pré-requis (paquets systèmes) pour fonctionner.

Python
------

sudo apt-get install python


PIP
---

sudo apt-get install python-pip


RobotFramework
--------------

Installation :

sudo pip install robotframework
sudo pip install robotframework-selenium2library

Mise à jour :

sudo pip install robotframework --upgrade
sudo pip install robotframework-selenium2library --upgrade


Utilisation
###########

Pré-requis
----------

Les tests doivent être joués dans un environnement balisé et reproductible à
l'identique. Pour ce faire il est nécessaire avant chaque lancement de test,
de dérouler une routine qui permet de mettre en place un environnement de tests. 
Un script permet de dérouler cette routine de manière automatisée : ::

    ./init_testenv

Ce script permet de :

* supprimer la base de données
* créer la base de données
* initialiser la base de données grâce au script data/pgsql/install.sql
* redémarrer apache pour prendre les traductions en compte
* donner les droits à apache pour les dossiers dans lequel il peut écrire
* faire un lien symbolique vers le dossier de l'applicatif pour que les tests
  en question dans le dossier /var/www/


TestSuite
---------

Lancer le test suite avec initialisation de l'environnement de tests

    ./run_testsuite


TestCase
--------

Lancer un test case avec initialisation de l'environnement de tests

    ./run_testcase testALancer.txt



Développement
#############

Il est prévu de consigner ici les bonnes pratiques et les consignes pour pour
le développement des tests.

Documentation de la librairie Selenium2Library
----------------------------------------------

http://rtomac.github.io/robotframework-selenium2library/doc/Selenium2Library.html


Convention de nommage
---------------------

* Un fichier de test par thème fonctionnel, une TestCase par fonctionnalité.
* Convention de nommage :
    * des fichiers : mon_theme_fonctionnel.txt
    * des testcase : Saisir un nouvel élément


Bonnes pratiques
----------------

* Éviter d'utiliser les sélecteurs XPATH, les sélecteurs CSS ou par ID sont
  largement préférables.


