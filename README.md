fbx.io
======
>Module de gestion de téléchargement Put.io &gt; Freebox

À la base, ce module a été conçu dans un but personnel afin de pouvoir lancer le téléchargement d'un fichier présent sur **Put.io** dans la **Seedbox HTTP** de la **Freebox**.

M'étant aperçu que cela pouvait intéresser quelques personnes (*qui souhaitent également y contribuer*), je le met à disposition sur ce dépot.

Préambule
---------
Avant toute chose, je tiens à dire que ça a été un peu codé à l'arrache.
Autrement dit, pas de framework, mais ça tourne.

J'ai nettoyé au max, et du coup rajouté un script d'installation et de gestion de configuration histoire de pas se prendre la tête si vous souhaitez juste l'installer et l'utiliser.

Fonctionnalités
---------------
Actuellement, le module permet :

* De lancer le téléchargement d'un fichier distant ( *en url http* ) sur la **Freebox**, et d'en choisir le nom
* De parcourir le compte **Put.io**, et de lancer un téléchargement vers la **Freebox**
* De voir les téléchargements en cours et terminés, et leur état d'avancement en temps réel
* De rechercher les sous-titres sur **Betaseries** d'un fichier **Put.io**, si celui-ci est dans un dossier portant le nom de la série
* D'afficher la liste des périphériques de stockages connectés au **Freebox Server** ( *y compris le disque interne* ), et l'espace utilisé/disponible
* D'afficher la liste des fichiers/dossiers présents dans le dossier de Téléchargement (et de les supprimer)
* De télécharger un sous-titre en fonction du dossier Put.io en cours
* Deviner automatiquement le nom des vidéos et sous-titres via Betaseries

Avertissement
-----------------------
Ce module est expérimental, vous l'utilisez donc de votre plein gré en sachant qu'il peut ne pas fonctionner à 100%.
Celui-ci stocke votre configuration sur votre serveur, dans un fichier.
Vous pouvez copier l'installation un peu partout, le fichier de configuration dépend de l'url d'appel du module. Si le fichier n'est pas trouvé, vous passerez automatiquement par le module d'installation.

Ce module n'est pas sous license particulière, mais si vous forkez le projet pour le modifier de votre côté, et que certaines de vos modifications peuvent servir à d'autres, n'hésitez pas à les soumettre ;)

Configuration requise
---------------------
Rien de compliqué : **Apache** et **PHP**.
Actuellement, le script a été testé sur une plate-forme **Linux** ( *PHP 5.2.17* ) et a été développée sous environnement **Mac** ( *PHP 5.3.14* ), et fonctionne très bien sur les deux.

Clients PHP
-----------
Pour fonctionner le script utilise deux scripts PHP :

* **Mafreebox** : https://github.com/mqu/mafreebox
* **Betaseries** : https://github.com/Moinax/BetaSeries

Ce ne sont pas des fork directs car j'ai du ajouter/modifier 1 ou 2 méthodes dans chacune d'elle.

API Put.io & Betaseries
-----------------------
Pour fonctionner, le module a besoin d'un accès **API** **Put.io**.
Si vous avez un accès API **Betaseries**, vous pouvez également l'utiliser pour la recherche de sous-titres.
Les indications sont données dans le module d'installation.

Trafic
------
En théorie, vous n'avez pas trop à vous soucier du nombre de requêtes effectuées à votre **Freebox**, puisqu'il s'agit de votre connexion personnelle et que les appels y sont faits en direct (via CURL).

Pour ce qui est de **Put.io**, les appels sont fait à chaque fois que vous cliquez sur un dossier (ou un retour au dossier parent).

Enfin, pour **Betaseries** il y a d'avantages d'appels effectués puisque celui-ci va à la fois chercher l'url de la série correspondante à votre dossier Put.io, puis vérifier l'existence de sous-titres pour chacun des fichiers présents dans le dossier. C'est assez minime dans le cadre d'un usage personnel, mais ça peut vite monter si vous utilisez le module constamment.
***Il y a encore du travail à faire à ce niveau, pour réduire les requêtes, voir les lancer sur demande depuis un dossier, quand on le souhaite et non automatiquement.***


Frontend
--------
Niveau *look & feel* le script utilise la version **WIP** de **Bootstrap 3**.

Installation
------------
Pour l'installer, rien de plus simple, soit vous télécharger le contenu de ce dépot, soit vous forkez le projet, vous le copier dans un répertoire sur votre serveur, et vous accédez à ce répertoire via votre navigateur.

TODO
----
- Script type "CRON" gérant les nouveaux téléchargements automatiquement
