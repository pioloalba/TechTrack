<?php
/**
 * Manual Rating Tables Setup Script
 * Run this file directly in browser: http://localhost:8080/techtrack1.3/setup_rating_tables.php
 */

// Database configuration
$host = 'localhost';
$dbname = 'techtrack_db';
$username = 'root';
$password = '';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Rating Tables Setup</title></head><body>";
echo "<h1>Rating Tables Setup</h1>";
echo "<pre>";

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to database: $dbname\n\n";
    
    // Create migrations table if doesn't exist
    echo "Creating migrations table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
        `version` bigint(20) NOT NULL,
        PRIMARY KEY (`version`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Migrations table ready\n\n";
    
    // Create product_ratings table
    echo "Creating product_ratings table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `product_ratings` (
        `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
        `product_id` INT(11) NOT NULL,
        `customer_id` INT(11) NULL COMMENT 'NULL for guest ratings',
        `session_id` VARCHAR(128) NULL COMMENT 'Track guest ratings',
        `rating` TINYINT(1) NOT NULL COMMENT '1-5 stars',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Product_ratings table created\n";
    
    // Create indexes for product_ratings
    echo "Creating indexes for product_ratings...\n";
    try {
        $pdo->exec("CREATE INDEX idx_product_ratings_product ON product_ratings(product_id)");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
        echo "  (Index already exists)\n";
    }
    
    try {
        $pdo->exec("CREATE INDEX idx_product_ratings_customer ON product_ratings(customer_id)");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
        echo "  (Index already exists)\n";
    }
    
    try {
        $pdo->exec("CREATE INDEX idx_product_ratings_session ON product_ratings(session_id)");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
        echo "  (Index already exists)\n";
    }
    
    try {
        $pdo->exec("CREATE UNIQUE INDEX idx_product_ratings_unique ON product_ratings(product_id, customer_id, session_id)");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
        echo "  (Unique index already exists)\n";
    }
    echo "✓ Indexes created\n\n";
    
    // Create product_reviews table
    echo "Creating product_reviews table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `product_reviews` (
        `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
        `product_id` INT(11) NOT NULL,
        `customer_id` INT(11) NULL,
        `rating_id` INT(11) NOT NULL COMMENT 'Links to product_ratings',
        `customer_name` VARCHAR(150) NULL,
        `review_title` VARCHAR(255) NULL,
        `review_text` TEXT NULL,
        `verified_purchase` BOOLEAN DEFAULT FALSE,
        `helpful_count` INT(11) DEFAULT 0,
        `status` ENUM('pending','approved','rejected') DEFAULT 'approved',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Product_reviews table created\n";
    
    // Create indexes for product_reviews
    echo "Creating indexes for product_reviews...\n";
    try {
        $pdo->exec("CREATE INDEX idx_product_reviews_product ON product_reviews(product_id)");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
        echo "  (Index already exists)\n";
    }
    
    try {
        $pdo->exec("CREATE INDEX idx_product_reviews_customer ON product_reviews(customer_id)");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
        echo "  (Index already exists)\n";
    }
    
    try {
        $pdo->exec("CREATE INDEX idx_product_reviews_rating ON product_reviews(rating_id)");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
        echo "  (Index already exists)\n";
    }
    
    try {
        $pdo->exec("CREATE INDEX idx_product_reviews_status ON product_reviews(status)");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
        echo "  (Index already exists)\n";
    }
    echo "✓ Indexes created\n\n";
    
    echo "========================================\n";
    echo "✓ SUCCESS! All rating tables created!\n";
    echo "========================================\n\n";
    echo "You can now:\n";
    echo "1. Go back to the product page\n";
    echo "2. Click on stars to rate products\n";
    echo "3. Write reviews\n\n";
    echo "Product page: http://localhost:8080/techtrack1.3/product/118\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString();
}

echo "</pre></body></html>";
?>
