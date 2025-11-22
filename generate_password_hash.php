<?php
/**
 * Password Hash Generator
 * 
 * This script helps you generate secure bcrypt password hashes
 * for updating user passwords in the database.
 * 
 * Usage: php generate_password_hash.php
 */

echo "🔐 TechTrack Password Hash Generator\n";
echo "=====================================\n\n";

// Function to generate strong random password
function generateStrongPassword($length = 16) {
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $special = '!@#$%^&*()-_=+[]{}|;:,.<>?';
    
    $all = $uppercase . $lowercase . $numbers . $special;
    
    // Ensure at least one of each type
    $password = '';
    $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
    $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
    $password .= $numbers[random_int(0, strlen($numbers) - 1)];
    $password .= $special[random_int(0, strlen($special) - 1)];
    
    // Fill the rest randomly
    for ($i = 4; $i < $length; $i++) {
        $password .= $all[random_int(0, strlen($all) - 1)];
    }
    
    // Shuffle the password
    return str_shuffle($password);
}

// Check if running in CLI
if (php_sapi_name() !== 'cli') {
    die("⚠️  This script must be run from the command line.\nUsage: php generate_password_hash.php\n");
}

echo "Choose an option:\n";
echo "1. Generate random strong passwords\n";
echo "2. Hash your own password\n";
echo "\nEnter choice (1 or 2): ";

$handle = fopen("php://stdin", "r");
$choice = trim(fgets($handle));

if ($choice === '1') {
    // Generate random passwords
    echo "\n🎲 Generating random strong passwords...\n\n";
    
    $accounts = [
        'Admin' => 'admin@techtrack.com',
        'Cashier' => 'cashier@techtrack.com'
    ];
    
    $sqlStatements = [];
    
    foreach ($accounts as $role => $email) {
        $password = generateStrongPassword(20);
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "👤 $role Account\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Email:    $email\n";
        echo "Password: $password\n";
        echo "Hash:     $hash\n\n";
        
        $sqlStatements[] = "UPDATE users SET password = '$hash', updated_at = NOW() WHERE email = '$email';";
    }
    
    echo "\n📋 SQL Statements (copy to phpMyAdmin):\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "USE techtrack_db;\n\n";
    foreach ($sqlStatements as $sql) {
        echo "$sql\n";
    }
    echo "\n";
    
    echo "💾 Save these passwords securely!\n";
    echo "⚠️  This is the only time you'll see the plain text passwords.\n\n";
    
} elseif ($choice === '2') {
    // Hash custom password
    echo "\nEnter password to hash (or press Enter to cancel): ";
    
    // Hide password input (works on Unix-like systems)
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        system('stty -echo');
    }
    
    $password = trim(fgets($handle));
    
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        system('stty echo');
    }
    
    echo "\n";
    
    if (empty($password)) {
        echo "❌ No password entered. Exiting.\n";
        exit;
    }
    
    // Validate password strength
    $strength = 0;
    if (strlen($password) >= 8) $strength++;
    if (strlen($password) >= 12) $strength++;
    if (preg_match('/[A-Z]/', $password)) $strength++;
    if (preg_match('/[a-z]/', $password)) $strength++;
    if (preg_match('/[0-9]/', $password)) $strength++;
    if (preg_match('/[^A-Za-z0-9]/', $password)) $strength++;
    
    echo "\n🔒 Password Strength: ";
    if ($strength < 3) {
        echo "❌ WEAK (Score: $strength/6)\n";
        echo "⚠️  Recommendation: Use at least 12 characters with uppercase, lowercase, numbers, and symbols.\n";
    } elseif ($strength < 5) {
        echo "⚠️  MODERATE (Score: $strength/6)\n";
        echo "💡 Tip: Add more character variety for better security.\n";
    } else {
        echo "✅ STRONG (Score: $strength/6)\n";
    }
    
    // Generate hash
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    echo "\n📋 Password Hash:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "$hash\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "📝 SQL Statement (choose account):\n\n";
    echo "For Admin:\n";
    echo "UPDATE users SET password = '$hash', updated_at = NOW() WHERE email = 'admin@techtrack.com';\n\n";
    
    echo "For Cashier:\n";
    echo "UPDATE users SET password = '$hash', updated_at = NOW() WHERE email = 'cashier@techtrack.com';\n\n";
    
} else {
    echo "❌ Invalid choice. Exiting.\n";
}

fclose($handle);

echo "✅ Done!\n\n";
