# Vendor API

API REST construite avec Symfony 8 et API Platform 4 pour la gestion de plateformes, produits, commandes, paiements et documents.

## Caractéristiques

- Stack moderne: Symfony 8, API Platform 4.2, Doctrine ORM 3.6
- Authentification JWT (LexikJWTAuthenticationBundle)
- Bus de commandes et requêtes (Symfony Messenger)
- Événements applicatifs pour le suivi d’activité et les transitions d’état
- Téléversement de fichiers avec VichUploaderBundle
- Permissions centralisées et exposées via une ressource API

## Prérequis

- PHP 8.4+
- Composer 2+
- PostgreSQL 16 (ou via Docker compose)
- OpenSSL (pour les clés JWT si régénération)

## Installation

1. Cloner le dépôt puis installer les dépendances:

   ```bash

   composer install
   ```

2. Configurer l’environnement:

   - Copier/adapter `.env` ou `.env.local`
   - Mettre à jour `DATABASE_URL` et les variables JWT si besoin
3. Base de données:

   ```bash

   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. Démarrer le serveur de dev:

   - Avec le binaire Symfony:

     ```bash

     symfony server:start -d
     ```

   - Ou PHP natif:

     ```bash

     php -S localhost:8000 -t public
     ```

## Documentation API

- Endpoint de base des APIs: `/api`
- Documentation interactive (Swagger UI): `/api` (API Platform)

## Authentification

- Endpoint de login JSON: `POST /api/authentication_token`
- Corps de requête:

  ```json

  { "username": "email_ou_phone", "password": "mot_de_passe" }
  ```

- Utiliser le token JWT en `Authorization: Bearer <token>` pour les appels suivants

## Architecture applicative

- Entités (Domaines principaux): User, Platform, Product, Order, Payment, Document
- DTO/Models: Validation des entrées et transport des données
- Managers: Logique métier (ex. OrderManager, PaymentManager)
- State Processors (API Platform): Orchestration des opérations API
- Événements:
  - `ActivityEvent` (suivi et log de l’activité)
  - `payment.created` déclenché après la création d’un paiement
  - Abonnés:
    - `PaymentEventSubscriber`: traite les paiements CASH (marque SUCCESS)
    - `PaymentSuccessListener`: marque la commande comme PAID après succès

## Permissions et sécurité

- Permissions définies dans `config/permissions.php`
- Exposées via la ressource API `PermissionResource`
- Règles d’accès appliquées dans les opérations API Platform (ex: `ROLE_ORDER_CREATE`)
- Authentification stateless via JWT

## Téléversement de fichiers

- VichUploaderBundle configuré (mapping `media_object`)
- Destination: `public/media`
- Entité `Document` avec contraintes de type et métadonnées

## Paiements et commandes

- Méthodes de paiement: `CARD`, `CASH`, `MOBILE_MONEY`
- Statuts paiement: `P` (PENDING), `S` (SUCCESS), `F` (FAILED)
- Cycle commande: `D` (DRAFT), `K` (SENT_TO_KITCHEN), `R` (READY), `S` (SERVED), `P` (PAID), `C` (CANCELLED)
- Flux cash:
  - Création → `payment.created`
  - `PaymentEventSubscriber` marque paiement `SUCCESS` si `CASH`
  - `PaymentSuccessListener` met la commande `PAID`

## Tests

- Lancer la suite:

  ```bash

  bin/phpunit

  ```

- Fichier de config: `phpunit.dist.xml`

## Docker (optionnel)

- Base de données PostgreSQL via `docker compose`:

  ```bash
  docker compose up -d database
  ```

- Mailer de dev (Mailpit):

  ```bash
  docker compose up -d mailer
  ```

## Dépannage

- Vérifier que `MESSENGER_TRANSPORT_DSN` est correctement configuré dans `.env`
- En cas d’erreurs d’accès, contrôler les permissions nécessaires dans `config/permissions.php`
- Pour l’API de fichiers, vérifier les droits sur `public/media`
