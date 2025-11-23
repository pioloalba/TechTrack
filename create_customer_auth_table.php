<?php
/**
 * Create customer_auth table for customer passwords
 */

$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Creating customer_auth table...\n";
echo "================================\n\n";

try {
    // First, convert customers table to InnoDB to support foreign keys
    echo "Converting customers table to InnoDB...\n";
    try {
        $pdo->exec("ALTER TABLE customers ENGINE=InnoDB");
        echo "✓ Customers table converted to InnoDB\n\n";
    } catch (PDOException $e) {
        echo "Note: " . $e->getMessage() . "\n\n";
    }
    
    // Also ensure id column is UNSIGNED to match customer_auth
    try {
        $pdo->exec("ALTER TABLE customers MODIFY id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT");
        echo "✓ Customers id column set to UNSIGNED\n\n";
    } catch (PDOException $e) {
        echo "Note: " . $e->getMessage() . "\n\n";
    }
    
    // Create customer_auth table
    echo "Creating customer_auth table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS customer_auth (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            customer_id INT(11) UNSIGNED NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX idx_customer_id (customer_id),
            CONSTRAINT fk_customer_auth_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✓ customer_auth table created successfully!\n\n";
    
    echo "Table structure:\n";
    echo "----------------\n";
    $result = $pdo->query("DESCRIBE customer_auth");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-20s %-20s %-10s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null']
        );
    }
    
    echo "\n================================\n";
    echo "Migration completed!\n\n";
    echo "Customer authentication now uses:\n";
    echo "- customers table: Basic customer info\n";
    echo "- customer_auth table: Passwords (hashed)\n";
    echo "- users table: Admin, Manager, Cashier only\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
