<?php
/**
 * Add default passwords for existing customers without authentication
 */

$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Adding default passwords for existing customers...\n";
echo "===================================================\n\n";

// Find customers without password in customer_auth
$stmt = $pdo->query("
    SELECT c.id, c.name, c.email 
    FROM customers c
    LEFT JOIN customer_auth ca ON c.id = ca.customer_id
    WHERE ca.customer_id IS NULL
");

$customersWithoutAuth = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($customersWithoutAuth)) {
    echo "✓ All customers already have authentication set up.\n";
    exit(0);
}

echo "Found " . count($customersWithoutAuth) . " customer(s) without passwords:\n\n";

// Default password for testing (should be changed by customers)
$defaultPassword = 'TechTrack2025!';
$hash = password_hash($defaultPassword, PASSWORD_BCRYPT);

foreach ($customersWithoutAuth as $customer) {
    echo "Setting password for: {$customer['name']} ({$customer['email']})\n";
    
    $insertStmt = $pdo->prepare("
        INSERT INTO customer_auth (customer_id, password, created_at, updated_at)
        VALUES (?, ?, NOW(), NOW())
    ");
    $insertStmt->execute([$customer['id'], $hash]);
    
    echo "  ✓ Password set: $defaultPassword\n";
}

echo "\n===================================================\n";
echo "Default Password: $defaultPassword\n";
echo "===================================================\n";
echo "\nIMPORTANT: These customers should change their passwords!\n";
echo "You can implement a password reset/change feature for them.\n";
