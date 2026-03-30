<?php

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'app';
$dbUser = getenv('DB_USER') ?: 'app';
$dbPass = getenv('DB_PASSWORD') ?: '';
$dbPort = getenv('DB_PORT') ?: '5432';

try {
    $pdo = new PDO("pgsql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public'")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('partner', $tables) || !in_array('partner_users', $tables)) {
        echo "Required tables not found, skipping seed\n";
        exit(0);
    }

    // Create partner if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM partner WHERE id = 1");
    if ($stmt->fetchColumn() == 0) {
        try {
            $pdo->exec("INSERT INTO partner (id, name) VALUES (1, 'Test Partner')");
        } catch (Exception $e) {
            $pdo->exec("INSERT INTO partner (name) VALUES ('Test Partner')");
        }
    }

    $partnerId = $pdo->query("SELECT id FROM partner ORDER BY id LIMIT 1")->fetchColumn();

    // Create test user if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM partner_users WHERE phone = '79998312232'");
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('111111', PASSWORD_BCRYPT);
        $nextId = (int) $pdo->query("SELECT COALESCE(MAX(id), 0) + 1 FROM partner_users")->fetchColumn();

        $sql = "INSERT INTO partner_users (id, phone, name, roles, password, partner_id) VALUES (:id, :phone, :name, :roles, :password, :partner_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $nextId,
            'phone' => '79998312232',
            'name' => 'Test User',
            'roles' => '["ROLE_PARTNER_OWNER"]',
            'password' => $hash,
            'partner_id' => $partnerId,
        ]);
        echo "Test user created\n";
    } else {
        echo "Test user exists\n";
    }
} catch (Exception $e) {
    echo "Seed error: " . $e->getMessage() . "\n";
}
