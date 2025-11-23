<?php
/**
 * Simple Migration Runner Script
 * Run migrations directly via SQL
 */

defined('PREVENT_DIRECT_ACCESS') OR define('PREVENT_DIRECT_ACCESS', FALSE);

// Load framework
define('ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_DIR', ROOT_DIR . 'app' . DIRECTORY_SEPARATOR);
define('PUBLIC_DIR', 'public');
define('SCHEME_DIR', ROOT_DIR . 'scheme' . DIRECTORY_SEPARATOR);

require_once SCHEME_DIR . 'kernel/LavaLust.php';

$lava = new LavaLust();
$lava->call->database();
$db = $lava->db;

echo "TechTrack - Running Custom Migrations\n";
echo "=====================================\n\n";

try {
    // Create cart table
    echo "Creating cart table...\n";
    $db->raw("
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
    
    // Add foreign keys for cart
    echo "Adding cart foreign keys...\n";
    $db->raw("
        ALTER TABLE cart
        ADD CONSTRAINT fk_cart_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
    ");
    $db->raw("
        ALTER TABLE cart
        ADD CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ");
    
    echo "✓ Cart table created successfully.\n\n";
    
    // Create wishlist table
    echo "Creating wishlist table...\n";
    $db->raw("
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
    echo "Adding wishlist foreign keys...\n";
    $db->raw("
        ALTER TABLE wishlist
        ADD CONSTRAINT fk_wishlist_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
    ");
    $db->raw("
        ALTER TABLE wishlist
        ADD CONSTRAINT fk_wishlist_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ");
    
    echo "✓ Wishlist table created successfully.\n\n";
    
    // Create customer_addresses table
    echo "Creating customer_addresses table...\n";
    $db->raw("
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
    echo "Adding customer_addresses foreign key...\n";
    $db->raw("
        ALTER TABLE customer_addresses
        ADD CONSTRAINT fk_address_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
    ");
    
    echo "✓ Customer addresses table created successfully.\n\n";
    
    echo "=====================================\n";
    echo "All migrations completed successfully!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
