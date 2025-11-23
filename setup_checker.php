<?php
/**
 * TechTrack Project Setup Checker
 * Run this after cloning the project to verify all requirements
 */

echo "=================================================\n";
echo "   TechTrack Project Setup Verification\n";
echo "=================================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Check PHP version
echo "[1] Checking PHP Version...\n";
$phpVersion = phpversion();
if (version_compare($phpVersion, '7.4.0', '>=')) {
    $success[] = "✓ PHP Version: $phpVersion (OK)";
} else {
    $errors[] = "✗ PHP Version: $phpVersion (Requires PHP 7.4 or higher)";
}

// 2. Check database connection
echo "[2] Checking Database Connection...\n";
try {
    $configFile = __DIR__ . '/app/config/database.php';
    if (!file_exists($configFile)) {
        $errors[] = "✗ Database config file not found: app/config/database.php";
    } else {
        // Try to connect
        $pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');
        $success[] = "✓ Database connection successful";
        
        // 3. Check required tables
        echo "[3] Checking Database Tables...\n";
        $requiredTables = [
            'users',
            'customers',
            'customer_auth',
            'products',
            'product_images',
            'orders',
            'order_items',
            'cart',
            'wishlist',
            'customer_addresses',
            'alerts',
            'inventory_transactions',
            'settings'
        ];
        
        $result = $pdo->query("SHOW TABLES");
        $existingTables = $result->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($requiredTables as $table) {
            if (in_array($table, $existingTables)) {
                $success[] = "✓ Table exists: $table";
            } else {
                $errors[] = "✗ Missing table: $table";
            }
        }
        
        // 4. Check table data
        echo "\n[4] Checking Table Data...\n";
        
        // Check if there are products
        $productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
        if ($productCount > 0) {
            $success[] = "✓ Products table has $productCount product(s)";
        } else {
            $warnings[] = "⚠ Products table is empty - please seed data";
        }
        
        // Check if there's at least one admin user
        $adminCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Admin'")->fetchColumn();
        if ($adminCount > 0) {
            $success[] = "✓ Admin user exists";
        } else {
            $errors[] = "✗ No admin user found in users table";
        }
        
        // Check customer_auth table structure
        $authCount = $pdo->query("SELECT COUNT(*) FROM customer_auth")->fetchColumn();
        $success[] = "✓ customer_auth table configured ($authCount password(s))";
        
    }
} catch (PDOException $e) {
    $errors[] = "✗ Database connection failed: " . $e->getMessage();
    $warnings[] = "  → Check if MySQL is running";
    $warnings[] = "  → Verify database 'techtrack_db' exists";
    $warnings[] = "  → Check username/password in app/config/database.php";
}

// 5. Check writable directories
echo "\n[5] Checking File Permissions...\n";
$writableDirs = [
    'runtime/cache',
    'runtime/logs',
    'public/uploads'
];

foreach ($writableDirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (is_dir($fullPath) && is_writable($fullPath)) {
        $success[] = "✓ Writable: $dir";
    } else {
        $warnings[] = "⚠ Not writable or missing: $dir";
    }
}

// 6. Check required PHP extensions
echo "\n[6] Checking PHP Extensions...\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];

foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        $success[] = "✓ PHP Extension loaded: $ext";
    } else {
        $errors[] = "✗ Missing PHP extension: $ext";
    }
}

// Print Results
echo "\n=================================================\n";
echo "                 RESULTS\n";
echo "=================================================\n\n";

if (!empty($success)) {
    echo "✓ SUCCESS (" . count($success) . "):\n";
    foreach ($success as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠ WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "✗ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

echo "=================================================\n";
if (empty($errors)) {
    echo "STATUS: ✓ READY TO USE\n";
} else {
    echo "STATUS: ✗ SETUP REQUIRED\n";
    echo "\nNext Steps:\n";
    echo "1. Import SQL file: mysql -u root techtrack_db < sql/techtrack_db.sql\n";
    echo "2. Run migrations: Open browser to /DevMigrate/migrate\n";
    echo "3. Create database tables if missing\n";
}
echo "=================================================\n";
