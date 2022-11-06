# BileMo

Création d'une API Rest pour BileMo, une entreprise de vente de téléphones mobiles.

Environnement utilisé durant le développement:

Symfony 5.4.17
Composer 2.3.10
PhpMyAdmin 5.1.2
Symfony CLI 5.4.17
PHP 8.1.0
MySQL 5.7.36

Informations sur l'API:

- L'obtention du token afin de s'authentifier à l'API se fait via l'envoie des identifiants sur l'URI /api/login_check
- Les opérations "GET" sont accéssibles à tout utilisateur authentifié.
- Par sécurité, les autres opérations (POST/PUT/DELETE) ne sont accéssibles qu'aux utilisateurs qui possédent le rôle ROLE_ADMIN.

Installation:

Clonez ou téléchargez le repository GitHub dans le dossier voulu :     git clone https://github.com/Rte29/BileMo.git
    
Configurez vos variables d'environnement tel que la connexion à la base de données dans le fichier  .env

Téléchargez et installez les dépendances du projet avec Composer :     composer install
    
Créez la base de données dans le répertoire du projet :     php bin/console doctrine:database:create

Appliquer les migrations des entités:     php bin/console doctrine:migrations:migrate

Générez les clés SSH (Solution alternative pour OpenSSL sur Windows) Et noter votre passphrase à la ligne "JWT_PASSPHRASE=" de votre fichier .env.local
$ mkdir config/jwt
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

Installez les fixtures de données fictives :     php bin/console doctrine:fixtures:load

Le projet est installé.
