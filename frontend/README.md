GFAIM !!!!!
============

## Lancer et tester l'application

Pour lancer l'appli, il suffit d'avoir [node JS](https://nodejs.org/) et de taper successivement : 

### /!\ il faut NodeJs en version 4 

- ``npm install``
- ``npm install -g gulp`` (pour installer Gulp globalement)
- ``bower install``
- ``gulp``

[Browser-Sync](http://www.browsersync.io/) s'occupe alors de lancer automatiquement Chrome (ou un autre navigateur, cf ``gulpfile.js`` (tout en bas)).
Toutes les modifications effectuées dans les fichiers sont automatiquement appliqués, la fenêtre du navigateur dse recharge automatiquement !

-------------------------------------
    
[Gulp](http://gulpjs.com/) permet d'automatiser le workflow et s'occupe de placer l'ensemble des fichiers
 compilés et minifiés dans le dossier ``/dist``.
 
--------------------------------------

### Liste des modules utilisés

- [bootstrap](http://getbootstrap.com/) : Framework CSS, seul le CSS est utilisé,
- [angular-bootstrap](https://angular-ui.github.io/bootstrap/) : Angular JS pour les composants Bootstrap 
- [bootswatch](https://bootswatch.com/) : Thème pour Bootstrap
- [bootstrap-social](http://lipis.github.io/bootstrap-social/) : Social Buttons pour Bootsrap,
- [restangular](https://github.com/mgonto/restangular) : Pour utiliser une API REST proprement et facilement,
- [angular-loading-bar](https://github.com/chieffancypants/angular-loading-bar) : Ajoute une barre de chargement pendant les requêtes XHR,
- [angular-ui-notification](https://github.com/alexcrack/angular-ui-notification) : Service de notification avec Bootstrap 
- [angular-file-upload](https://github.com/nervgh/angular-file-upload) : Upload des fichiers

