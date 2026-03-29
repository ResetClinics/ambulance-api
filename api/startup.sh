#!/bin/sh

echo "==> Running schema update..."
php bin/console doctrine:schema:update --force --no-interaction 2>&1 || true

echo "==> Seeding test user..."
php seed_user.php 2>&1

echo "==> Starting server..."
exec php -S 0.0.0.0:10000 -t public
