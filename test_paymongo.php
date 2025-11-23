<?php
/**
 * Test script to verify PayMongo configuration
 */

// Load config
require_once 'app/config/paymongo.php';
$config = $config['paymongo'];

echo "PayMongo Configuration Test\n";
echo "============================\n\n";

// Check if keys are configured
if ($config['test_public_key'] === 'pk_test_YOUR_TEST_PUBLIC_KEY_HERE') {
    echo "❌ Test Public Key: NOT CONFIGURED\n";
    echo "   Please update app/config/paymongo.php with your actual keys\n\n";
} else {
    echo "✓ Test Public Key: Configured\n";
}

if ($config['test_secret_key'] === 'sk_test_YOUR_TEST_SECRET_KEY_HERE') {
    echo "❌ Test Secret Key: NOT CONFIGURED\n\n";
} else {
    echo "✓ Test Secret Key: Configured\n";
}

// Test API connection
echo "\nTesting PayMongo API Connection...\n";

if ($config['test_secret_key'] !== 'sk_test_YOUR_TEST_SECRET_KEY_HERE') {
    $url = 'https://api.paymongo.com/v1/payment_methods';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($config['test_secret_key'] . ':')
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200 || $http_code == 404) {
        echo "✓ API Connection: SUCCESS\n";
        echo "✓ Your PayMongo keys are valid!\n\n";
    } else {
        echo "❌ API Connection: FAILED (HTTP $http_code)\n";
        echo "   Check if your secret key is correct\n\n";
    }
} else {
    echo "⚠ Skipping API test - Please configure keys first\n\n";
}

// Check database columns
echo "Checking Database...\n";
$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');
$result = $pdo->query("SHOW COLUMNS FROM orders LIKE 'payment_intent_id'");
if ($result->rowCount() > 0) {
    echo "✓ Database columns: Ready\n";
} else {
    echo "❌ Database columns: Missing\n";
    echo "   Run: php add_paymongo_columns.php\n";
}

echo "\n";
echo "Next Steps:\n";
echo "-----------\n";

if ($config['test_public_key'] === 'pk_test_YOUR_TEST_PUBLIC_KEY_HERE') {
    echo "1. Sign up at: https://dashboard.paymongo.com/signup\n";
    echo "2. Get your test API keys\n";
    echo "3. Update app/config/paymongo.php\n";
    echo "4. Run this test again\n";
} else {
    echo "✓ Configuration complete!\n";
    echo "\nTo test payments:\n";
    echo "1. Go to: http://localhost:8080/techtrack1.3/shop\n";
    echo "2. Add products to cart\n";
    echo "3. Go to checkout\n";
    echo "4. Select GCash and use test card: 4343434343434345\n";
    echo "5. Check order status in database\n";
}

echo "\n";
