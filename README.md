Partie backend de l'extranet
============================

Site réalisé avec Laravel (https://laravel.com/).

Ce site nécessite une version de PHP supérieure ou égale à 7.1.3

## Mise en route

Après avoir cloné le dépôt, il faudra commencer par installer les dépendences,
avec un simple coup de `composer install`.

Il faudra également mettre à jour les informations de la base de donnée, en
copiant le fichier `.env.example` en `.env` et en éditant ce dernier.

Les clés à éditer pour la base de donnée sont les suivantes :

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Une fois cela fait, il ne restera plus qu'à copier le fichier
`config.example.yml` en `config.yml`, et de compléter le fichier avec les
domaines supportés et les clés d'API, et de générer les clés secrètes à l'aide
des commandes suivantes :

```sh
php artisan key:generate
php artisan jwt:secret
```

et de lancer les différentes migrations avec la commande suivante :

```sh
php artisan migrate
```

Pour lancer le site en local, un simple coup de `php artisan serve` lancera un
serveur de développement sur http://localhost:8000.

Lors du déploiement du site, il faudra configurer nginx ou Apache pour servir
le dossier `public` uniquement.
