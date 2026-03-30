#!/bin/sh

echo "========================================"
echo "==> STARTUP SCRIPT RUNNING"
echo "========================================"

# Write runtime env vars to .env.local so Symfony picks them up
echo "==> Writing .env.local with runtime env vars..."
cat > .env.local <<ENVEOF
APP_ENV=prod
DB_DRIVER=pdo_pgsql
DB_PORT=5432
CORS_ALLOW_ORIGIN=^https?://.*$
SMSRU_API_ID=${SMSRU_API_ID}
JWT_PASSPHRASE=testpassphrase123
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
ENVEOF
echo "SMSRU_API_ID length: ${#SMSRU_API_ID}"

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
