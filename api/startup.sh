#!/bin/sh

# Write runtime env vars to .env.local
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

php bin/console doctrine:schema:update --force --no-interaction 2>&1 || true
php bin/console cache:clear --env=prod --no-warmup 2>&1 || true
php bin/console cache:warmup --env=prod 2>&1 || true
php seed_user.php 2>&1 || true

exec php -S 0.0.0.0:10000 -t public
