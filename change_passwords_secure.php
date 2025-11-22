<?php
/**
 * TechTrack - Secure Password Change Script
 * 
 * This script updates the default weak passwords to strong, secure passwords.
 * Run this script ONCE, then delete it for security.
 * 
 * Usage: Access via browser: http://localhost:8080/TECH%20TRACK%20LAVALUST%201.2/change_passwords_secure.php
 * Or run via CLI: php change_passwords_secure.php
 */

// Prevent running this script multiple times accidentally
define('PASSWORD_CHANGE_SCRIPT', true);

// Load the framework
require_once 'index.php';

// Check if already run (you can set this to false to re-run)
$alreadyRun = false; // Change to true after first run

if ($alreadyRun) {
    die("⚠️ This script has already been run. If you need to run it again, edit this file and set \$alreadyRun to false.");
}

echo "🔒 TechTrack Password Security Update\n";
echo "=====================================\n\n";

// New strong passwords (CHANGE THESE TO YOUR OWN SECURE PASSWORDS)
$newPasswords = [
    'admin@techtrack.com' => 'TechTrack@Admin2025!', // Change this!
    'cashier@techtrack.com' => 'TechTrack@Cashier2025!' // Change this!
];

echo "⚠️  IMPORTANT: Edit this file and change the passwords in the \$newPasswords array first!\n";
echo "Current passwords set in this script:\n";
foreach ($newPasswords as $email => $password) {
    echo "  - $email: " . str_repeat('*', strlen($password)) . "\n";
}
echo "\n";

// Ask for confirmation
if (php_sapi_name() !== 'cli') {
    echo "<pre>";
}

echo "Type 'YES' to continue with password update: ";

if (php_sapi_name() === 'cli') {
    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);
} else {
    // For web access, require a GET parameter
    $confirmation = isset($_GET['confirm']) ? $_GET['confirm'] : '';
    if ($confirmation !== 'YES') {
        echo "\n\nTo run this script via web browser, add ?confirm=YES to the URL\n";
        echo "Example: http://localhost:8080/TECH%20TRACK%20LAVALUST%201.2/change_passwords_secure.php?confirm=YES\n";
        exit;
    }
}

if (strtoupper($confirmation) !== 'YES') {
    echo "❌ Password update cancelled.\n";
    exit;
}

echo "\n🔄 Starting password update...\n\n";

// Database connection
$lava = lava_instance();

// Get current users
$users = $lava->db->table('users')->get_all();

if (empty($users)) {
    echo "⚠️  No users found in database. Creating default users...\n\n";
    
    // Create default users with strong passwords
    foreach ($newPasswords as $email => $password) {
        $role = (strpos($email, 'admin') !== false) ? 'Admin' : 'Cashier';
        $name = ucfirst(str_replace(['@techtrack.com', '.'], ['', ' '], $email));
        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $lava->db->table('users')->insert([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        echo "✅ Created user: $email ($role)\n";
    }
} else {
    // Update existing users
    $updated = 0;
    
    foreach ($newPasswords as $email => $newPassword) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        // Update the user
        $result = $lava->db->table('users')
            ->where('email', $email)
            ->update([
                'password' => $hashedPassword,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        
        if ($result) {
            echo "✅ Updated password for: $email\n";
            $updated++;
        } else {
            echo "⚠️  User not found or no changes: $email\n";
        }
    }
    
    echo "\n📊 Summary: Updated $updated password(s)\n";
}

echo "\n✅ Password update complete!\n\n";
echo "🔐 New Credentials:\n";
echo "==================\n";
foreach ($newPasswords as $email => $password) {
    $role = (strpos($email, 'admin') !== false) ? 'Admin' : 'Cashier';
    echo "\n$role Account:\n";
    echo "  Email: $email\n";
    echo "  Password: $password\n";
}

echo "\n⚠️  IMPORTANT SECURITY STEPS:\n";
echo "============================\n";
echo "1. ✅ Passwords have been updated\n";
echo "2. 🗑️  DELETE this script immediately: change_passwords_secure.php\n";
echo "3. 📝 Save the new passwords in a secure password manager\n";
echo "4. 🔄 Consider changing them again through the admin interface\n";
echo "5. 🚫 Never commit passwords to version control\n\n";

if (php_sapi_name() !== 'cli') {
    echo "</pre>";
}

// Test login with new password for admin
echo "\n🧪 Testing admin login with new password...\n";
$adminUser = $lava->db->table('users')->where('email', 'admin@techtrack.com')->get();
if ($adminUser) {
    $testPassword = $newPasswords['admin@techtrack.com'];
    if (password_verify($testPassword, $adminUser['password'])) {
        echo "✅ Admin password verification successful!\n";
    } else {
        echo "❌ Admin password verification FAILED - something went wrong!\n";
    }
}

echo "\n📖 You can now login with the new credentials.\n";
echo "🗑️  Remember to DELETE this file after use!\n\n";
