<?php

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'app';
$dbUser = getenv('DB_USER') ?: 'app';
$dbPass = getenv('DB_PASSWORD') ?: '';
$dbPort = getenv('DB_PORT') ?: '5432';

try {
    $pdo = new PDO("pgsql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "DB connected OK\n";

    // Check tables exist
    $tables = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . "\n";

    if (!in_array('partner', $tables) || !in_array('partner_users', $tables)) {
        echo "ERROR: Required tables not found!\n";
        exit(1);
    }

    // Create partner if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM partner WHERE id = 1");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO partner (id, name, ambulance_commission, partner_commission, has_pharmacy) VALUES (1, 'Test Partner', 0, 0, 0)");
        echo "Partner created\n";
    } else {
        echo "Partner already exists\n";
    }

    // Create user if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM partner_users WHERE phone = '79998312232'");
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('111111', PASSWORD_BCRYPT);
        $sql = "INSERT INTO partner_users (phone, name, roles, password, partner_id) VALUES (:phone, :name, :roles, :password, :partner_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'phone' => '79998312232',
            'name' => 'Test User',
            'roles' => '["ROLE_PARTNER_OWNER"]',
            'password' => $hash,
            'partner_id' => 1,
        ]);
        echo "User created with phone 79998312232\n";
    } else {
        echo "User already exists\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
