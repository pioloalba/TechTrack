<?php
/**
 * Add google_id column to customers table for OAuth integration
 */

$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Adding google_id column to customers table...\n";

try {
    // Check if column exists
    $result = $pdo->query("SHOW COLUMNS FROM customers LIKE 'google_id'");
    
    if ($result->rowCount() == 0) {
        // Add column
        $pdo->exec("ALTER TABLE customers ADD COLUMN google_id VARCHAR(255) NULL UNIQUE AFTER email");
        echo "✓ Successfully added google_id column to customers table\n";
    } else {
        echo "ℹ google_id column already exists\n";
    }
    
    echo "\nDone!\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
