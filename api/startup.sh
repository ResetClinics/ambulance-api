#!/bin/sh
set -e

echo "==> Running schema update..."
php bin/console doctrine:schema:update --force --no-interaction 2>&1 || true

echo "==> Seeding test user..."
php bin/console dbal:run-sql "
INSERT INTO partner (id, name, ambulance_commission, partner_commission, has_pharmacy)
SELECT 1, 'Тестовый партнёр', 0, 0, 0
WHERE NOT EXISTS (SELECT 1 FROM partner WHERE id = 1);
" 2>&1 || true

php bin/console dbal:run-sql "
INSERT INTO partner_users (phone, name, roles, password, partner_id)
SELECT '79998312232', 'Тестовый пользователь', '[\"ROLE_PARTNER_OWNER\"]', '\$2y\$12\$A/a/lIusTWL7pXa.pFNM1OuGLYfE8R/iLP/ep9bO1jTtjecB9NIlm', 1
WHERE NOT EXISTS (SELECT 1 FROM partner_users WHERE phone = '79998312232');
" 2>&1 || true

echo "==> Starting server..."
exec php -S 0.0.0.0:10000 -t public
