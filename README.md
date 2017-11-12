Le projet se décompose en 3 répertoires :

- public : 
    - les fichiers accessibles depuis le web, c'est la racine du virtual host du serveur, pour garantir une meilleure sécurité (impossible de remonter vers src ou tests)
    - index.php, le 'controlleur frontal' qui se charge de lancer l'application
- src : ce sont les sources 
    - Core : le kernel du mini-framework monté par dessus
    - Module : les sources du site découpées par module, chaque module va enregistré ses routes et ses actions avec un ou plusieurs controleurs.
      Actuellement, il y a un module par entités de base de données, pour simplifier l'architecture.
    - Module/config.php le fichier de configuration du site, avec notamment les accès base de données, et les modules chargés
- tests
    - le dossier de tests, seul le framewrok est testé et non le site
    ceux ci sont réalisés avec PhPUnit
    
Vous trouverez ci-joint le dossier vendor de composer (ne sachant pas si vous l'aviez).

Pour lancer le serveur :
./server.sh

Pour lancer les tests:
./vendor/bin/phpunit (3 fails sur ob, à ignorer).

Pour se connected au site (pour upload):
root root

Pour enregistrer les images dans la base de données (si les images ne sont pas les mêmes que données, sinon la table image est deja remplit, placez les images dans public/assets/img):
- Le chemin des images se trouve dans la config, dans le répertoire doit se trouver un dossier upload en 777 si possible, pour l'upload local
Sinon le chemin lambdas est public/assets/img, après quoi vous n'avez qu'a lancer la router /image/register dans la bare d'url

Fonctionnement du framework:
index.php -> new Request -> Bootstrap Container and load Modules -> Match Route (or 404) -> Dispatch Route handler -> load Controller -> invoke action with request -> get response back -> print response -> exit

Choses particulière au framework :
Pour rendre une vue: Renderer::render avec '@namespace/view' ou @namespace est un raccourcis pour un chemin définit dans la méthode register de chaque Controller de chaque module
Noms de route : séparés par des points pour les namespaces : image.show

Le framework est normalement commenté, enfin j'espère.

Bonne correction.

Gidon Rémi