#!/bin/sh

echo "========================================"
echo "==> STARTUP SCRIPT RUNNING"
echo "========================================"

echo "==> Running schema update..."
php bin/console doctrine:schema:update --force --no-interaction 2>&1 || echo "Schema update failed (non-fatal)"

echo "==> Clearing and warming cache with DB access..."
php bin/console cache:clear --env=prod --no-warmup 2>&1 || true
php bin/console cache:warmup --env=prod 2>&1 || true

echo "==> Generating Doctrine proxies..."
php bin/console doctrine:ensure-production-settings --env=prod 2>&1 || true

echo "==> Seeding test user..."
php seed_user.php 2>&1 || echo "Seed script failed (non-fatal)"

echo "========================================"
echo "==> STARTUP COMPLETE, starting server"
echo "========================================"

exec php -S 0.0.0.0:10000 -t public
