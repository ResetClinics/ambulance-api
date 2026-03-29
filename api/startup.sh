#!/bin/sh

echo "========================================"
echo "==> STARTUP SCRIPT RUNNING"
echo "========================================"

echo "==> Running schema update..."
php bin/console doctrine:schema:update --force --no-interaction 2>&1 || echo "Schema update failed (non-fatal)"

echo "==> Seeding test user..."
php seed_user.php 2>&1 || echo "Seed script failed (non-fatal)"

echo "========================================"
echo "==> STARTUP COMPLETE, starting server"
echo "========================================"

exec php -S 0.0.0.0:10000 -t public
