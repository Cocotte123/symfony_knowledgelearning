# Knowledge_Learning

Knowledge_Learning est une application de vente de formations en ligne.

## Environnement de développement

### Pré-requis

* PHP >=7.2.5
* Composer
* Symfony CLI
* Stripe

### Lancer l'environnement de développement

```bash
symfony serve -d
```

### Lancer les tests

```bash
php bin/phpunit --testdox
```

### Installer la base de tests

```bash
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:schema:create
php bin/console --env=test doctrine:fixtures:load
```

### Réinitialiser la base de tests

```bash
php bin/console --env=test doctrine:schema:drop --force
php bin/console --env=test doctrine:schema:update --force
php bin/console --env=test doctrine:fixtures:load -n
```