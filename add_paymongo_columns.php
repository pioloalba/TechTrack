<?php
/**
 * Add PayMongo tracking columns to orders table
 */

$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Adding PayMongo columns to orders table...\n";

try {
    // Check if columns exist
    $result = $pdo->query("SHOW COLUMNS FROM orders LIKE 'payment_intent_id'");
    
    if ($result->rowCount() == 0) {
        // Add columns
        $pdo->exec("ALTER TABLE orders 
            ADD COLUMN payment_intent_id VARCHAR(255) NULL AFTER payment_status,
            ADD COLUMN payment_source_id VARCHAR(255) NULL AFTER payment_intent_id");
        echo "✓ Successfully added payment_intent_id and payment_source_id columns\n";
    } else {
        echo "ℹ PayMongo columns already exist\n";
    }
    
    echo "\nDone!\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
