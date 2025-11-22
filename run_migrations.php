<?php
/**
 * Database Migration Runner
 * 
 * Run this script to apply database migrations
 * Usage: php run_migrations.php
 */

// Load LavaLust framework
define('ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_DIR', ROOT_DIR . 'app' . DIRECTORY_SEPARATOR);
define('PUBLIC_DIR', 'public');
define('SCHEME_DIR', ROOT_DIR . 'scheme' . DIRECTORY_SEPARATOR);

require_once SCHEME_DIR . 'kernel/LavaLust.php';

// Initialize framework
$lava = new LavaLust();

echo "TechTrack Database Migration Runner\n";
echo "=====================================\n\n";

try {
    // Load migration library
    $lava->call->library('migration');
    
    echo "Starting migration...\n";
    
    // Run migrations
    if ($lava->migration->migrate()) {
        echo "\n✅ Migrations completed successfully!\n\n";
        
        // Show applied migrations
        $db = $lava->get_database();
        $migrations = $db->table('migrations')->order_by('version DESC')->get_all();
        
        if (!empty($migrations)) {
            echo "Applied migrations:\n";
            foreach ($migrations as $m) {
                echo "  - Version {$m['version']}: {$m['name']} (applied at {$m['applied_at']})\n";
            }
        }
    } else {
        echo "\n⚠️  No new migrations to apply or migration failed.\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Migration error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\nDone!\n";
