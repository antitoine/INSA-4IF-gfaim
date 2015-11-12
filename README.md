# gfaim

## Backend

### Dépendances

- PHP >= 5.6
- curl et son module pour PHP
- sqlite3 et son module pour PHP

### Configuration

Les fichiers de configuration de l'application sont dans `backend/config` :

- `app.php` contient la configuration général de l'application (clés google API, sqlite3, ...)
- `route.php` contient les chemins pour accéder aux différents services de l'application

Il faut configurer le serveur (apache ou nginx) avec un virtualhost pour accéder directement à l'index présent dans `backend/index.php`.

## Frontend

### Dépendances

- [NodeJS](https://nodejs.org/) (avec `npm`) >= 4.0.0
- Bower (`npm install -g bower`)
- Gulp (`npm install -g gulp`)

### Configuration

Une fois le backend en place, il faut configurer l'adresse précédemment spécifiée (virtualhost, exemple : api.gfaim.mon-domaine.com) dans l'application frontend.
Pour cela, modifier le fichier suivant `frontend/src/app/app.config.js` et changer l'adresse pour accéder à l'API backend.

Puis mettre à jour les sources avec la commande : `gulp build` (lancer à la racine de `frontend`).

### Développement

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
