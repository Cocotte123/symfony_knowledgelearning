# Knowledge_Learning

Knowledge_Learning is a webapp used to sell and follow e-learnings.

## Dev environment

### Prerequisites

- PHP >=7.2.5
- Composer
- Symfony CLI
- Stripe
- Apache-pack

### Launch dev environment

```bash
symfony serve -d
```

### Launch tests

```bash
php bin/phpunit --testdox
```

### Install and populate test database

```bash
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:schema:create
php bin/console --env=test doctrine:fixtures:load
```

### Remove and populate again test database

```bash
php bin/console --env=test doctrine:schema:drop --force
php bin/console --env=test doctrine:schema:update --force
php bin/console --env=test doctrine:fixtures:load -n
```