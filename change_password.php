<?php
/**
 * Password Change Utility
 * 
 * Run this script from command line to change user passwords securely.
 * Usage: php change_password.php
 */

// Prevent direct browser access
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from command line.');
}

echo "\n==============================================\n";
echo "  TechTrack Password Change Utility\n";
echo "==============================================\n\n";

// Load database configuration
require_once __DIR__ . '/app/config/database.php';

// Connect to database
try {
    $dsn = $database['driver'] . ':host=' . $database['hostname'] . ';port=' . $database['port'] . ';dbname=' . $database['database'];
    $pdo = new PDO($dsn, $database['username'], $database['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connected successfully\n\n";
} catch (PDOException $e) {
    die("✗ Database connection failed: " . $e->getMessage() . "\n");
}

// Get user email
echo "Enter user email: ";
$email = trim(fgets(STDIN));

// Check if user exists
$stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("\n✗ User not found with email: $email\n\n");
}

echo "\n✓ User found:\n";
echo "  ID: {$user['id']}\n";
echo "  Name: {$user['name']}\n";
echo "  Email: {$user['email']}\n";
echo "  Role: {$user['role']}\n\n";

// Get new password
echo "Enter new password (min 8 characters): ";
$password = trim(fgets(STDIN));

if (strlen($password) < 8) {
    die("\n✗ Password must be at least 8 characters long\n\n");
}

// Confirm password
echo "Confirm new password: ";
$confirmPassword = trim(fgets(STDIN));

if ($password !== $confirmPassword) {
    die("\n✗ Passwords do not match\n\n");
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Update password
$stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
$stmt->execute([$hashedPassword, $user['id']]);

echo "\n✓ Password changed successfully!\n";
echo "✓ User '{$user['name']}' can now login with the new password.\n\n";

// Show password strength recommendations
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Password Security Recommendations:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✓ Minimum 8 characters (you used: " . strlen($password) . ")\n";
echo "✓ Mix of uppercase and lowercase letters\n";
echo "✓ Include numbers\n";
echo "✓ Include special characters (!@#$%^&*)\n";
echo "✓ Avoid common words or patterns\n";
echo "✓ Don't reuse passwords from other accounts\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

exit(0);
