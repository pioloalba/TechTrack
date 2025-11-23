<?php
/**
 * Direct MySQL Migration Script
 * Creates cart, wishlist, and customer_addresses tables
 */

// Database connection settings
$host = 'localhost';
$dbname = 'techtrack_db';
$username = 'root';
$password = '';

echo "TechTrack - Database Migration\n";
echo "==============================\n\n";

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n\n";
    
    // Create cart table
    echo "Creating cart table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cart (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(100) NULL DEFAULT NULL,
            customer_id INT(11) UNSIGNED NULL DEFAULT NULL,
            product_id INT(11) UNSIGNED NOT NULL,
            quantity INT(11) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX idx_session_id (session_id),
            INDEX idx_customer_id (customer_id),
            INDEX idx_product_id (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Add foreign keys for cart (handle errors gracefully)
    try {
        $pdo->exec("ALTER TABLE cart ADD CONSTRAINT fk_cart_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key') === false) {
            echo "Note: " . $e->getMessage() . "\n";
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE cart ADD CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key') === false) {
            echo "Note: " . $e->getMessage() . "\n";
        }
    }
    
    echo "✓ Cart table created successfully.\n\n";
    
    // Create wishlist table
    echo "Creating wishlist table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS wishlist (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            customer_id INT(11) UNSIGNED NULL DEFAULT NULL,
            session_id VARCHAR(100) NULL DEFAULT NULL,
            product_id INT(11) UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_customer_id (customer_id),
            INDEX idx_session_id (session_id),
            INDEX idx_product_id (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Add foreign keys for wishlist
    try {
        $pdo->exec("ALTER TABLE wishlist ADD CONSTRAINT fk_wishlist_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key') === false) {
            echo "Note: " . $e->getMessage() . "\n";
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE wishlist ADD CONSTRAINT fk_wishlist_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key') === false) {
            echo "Note: " . $e->getMessage() . "\n";
        }
    }
    
    echo "✓ Wishlist table created successfully.\n\n";
    
    // Create customer_addresses table
    echo "Creating customer_addresses table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS customer_addresses (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            customer_id INT(11) UNSIGNED NOT NULL,
            address TEXT NOT NULL,
            latitude DECIMAL(10,8) NULL DEFAULT NULL,
            longitude DECIMAL(11,8) NULL DEFAULT NULL,
            is_default TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX idx_customer_id (customer_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Add foreign key for customer_addresses
    try {
        $pdo->exec("ALTER TABLE customer_addresses ADD CONSTRAINT fk_address_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key') === false) {
            echo "Note: " . $e->getMessage() . "\n";
        }
    }
    
    echo "✓ Customer addresses table created successfully.\n\n";
    
    echo "==============================\n";
    echo "All migrations completed!\n";
    echo "==============================\n\n";
    echo "Summary:\n";
    echo "- cart: Shopping cart persistence\n";
    echo "- wishlist: Favorite products storage\n";
    echo "- customer_addresses: Saved shipping addresses\n\n";
    echo "Your data is now stored in the database instead of sessions!\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
