<?php
/**
 * Migrate existing customers from users table to customers + customer_auth tables
 */

$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Migrating existing customers from users table...\n";
echo "=================================================\n\n";

try {
    // Find all users with role 'Customer'
    $stmt = $pdo->query("SELECT id, name, email, password, created_at FROM users WHERE role = 'Customer'");
    $customerUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($customerUsers)) {
        echo "No customers found in users table.\n";
        exit(0);
    }
    
    echo "Found " . count($customerUsers) . " customer(s) in users table.\n\n";
    
    $migrated = 0;
    $skipped = 0;
    
    foreach ($customerUsers as $user) {
        echo "Processing: {$user['name']} ({$user['email']})...\n";
        
        // Check if customer already exists in customers table
        $checkStmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
        $checkStmt->execute([$user['email']]);
        $existingCustomer = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingCustomer) {
            echo "  ⚠ Customer already exists in customers table (ID: {$existingCustomer['id']})\n";
            
            // Check if they have password in customer_auth
            $authCheckStmt = $pdo->prepare("SELECT id FROM customer_auth WHERE customer_id = ?");
            $authCheckStmt->execute([$existingCustomer['id']]);
            $hasAuth = $authCheckStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$hasAuth) {
                // Add password to customer_auth
                $insertAuthStmt = $pdo->prepare("
                    INSERT INTO customer_auth (customer_id, password, created_at, updated_at)
                    VALUES (?, ?, NOW(), NOW())
                ");
                $insertAuthStmt->execute([$existingCustomer['id'], $user['password']]);
                echo "  ✓ Added password to customer_auth\n";
                $migrated++;
            } else {
                echo "  → Already has password, skipping\n";
                $skipped++;
            }
        } else {
            // Insert new customer
            $insertCustomerStmt = $pdo->prepare("
                INSERT INTO customers (name, email, phone, total_orders, total_spent, is_vip, created_at)
                VALUES (?, ?, NULL, 0, 0.00, 0, ?)
            ");
            $insertCustomerStmt->execute([
                $user['name'],
                $user['email'],
                $user['created_at'] ?? date('Y-m-d H:i:s')
            ]);
            
            $customerId = $pdo->lastInsertId();
            
            // Insert password into customer_auth
            $insertAuthStmt = $pdo->prepare("
                INSERT INTO customer_auth (customer_id, password, created_at, updated_at)
                VALUES (?, ?, NOW(), NOW())
            ");
            $insertAuthStmt->execute([$customerId, $user['password']]);
            
            echo "  ✓ Created new customer (ID: $customerId) with password\n";
            $migrated++;
        }
    }
    
    echo "\n=================================================\n";
    echo "Migration Summary:\n";
    echo "- Migrated: $migrated\n";
    echo "- Skipped: $skipped\n";
    echo "- Total processed: " . count($customerUsers) . "\n";
    echo "\n✓ Migration completed!\n";
    
    // Ask if we should delete customers from users table
    echo "\nNote: Customer records still exist in users table.\n";
    echo "You can manually delete them with:\n";
    echo "DELETE FROM users WHERE role = 'Customer';\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
