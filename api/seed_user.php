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

    // List columns for debugging
    $cols = $pdo->query("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'partner' ORDER BY ordinal_position")->fetchAll(PDO::FETCH_ASSOC);
    echo "Partner columns:\n";
    foreach ($cols as $c) {
        echo "  {$c['column_name']} ({$c['data_type']}) nullable={$c['is_nullable']} default={$c['column_default']}\n";
    }

    $cols2 = $pdo->query("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'partner_users' ORDER BY ordinal_position")->fetchAll(PDO::FETCH_ASSOC);
    echo "Partner_users columns:\n";
    foreach ($cols2 as $c) {
        echo "  {$c['column_name']} ({$c['data_type']}) nullable={$c['is_nullable']} default={$c['column_default']}\n";
    }

    // Create partner if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM partner WHERE id = 1");
    if ($stmt->fetchColumn() == 0) {
        try {
            $pdo->exec("INSERT INTO partner (id, name) VALUES (1, 'Test Partner')");
            echo "Partner created OK\n";
        } catch (Exception $e) {
            echo "Partner INSERT failed: " . $e->getMessage() . "\n";
            // Try with more columns based on NOT NULL constraints
            echo "Trying minimal insert with sequence...\n";
            $pdo->exec("INSERT INTO partner (name) VALUES ('Test Partner')");
            $partnerId = $pdo->lastInsertId();
            echo "Partner created with id=$partnerId\n";
        }
    } else {
        echo "Partner id=1 already exists\n";
    }

    // Get actual partner id
    $partnerId = $pdo->query("SELECT id FROM partner ORDER BY id LIMIT 1")->fetchColumn();
    echo "Using partner_id=$partnerId\n";

    // Create user if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM partner_users WHERE phone = '79998312232'");
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('111111', PASSWORD_BCRYPT);

        // Get next ID (no auto-increment on this table)
        $nextId = (int) $pdo->query("SELECT COALESCE(MAX(id), 0) + 1 FROM partner_users")->fetchColumn();
        echo "Next partner_users id=$nextId\n";

        try {
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
            echo "User created OK with id=$nextId phone=79998312232\n";
        } catch (Exception $e) {
            echo "User INSERT failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "User already exists\n";
    }

    // Verify
    $count = $pdo->query("SELECT COUNT(*) FROM partner_users WHERE phone = '79998312232'")->fetchColumn();
    echo "Verification: partner_users with phone 79998312232 count=$count\n";

} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
}
