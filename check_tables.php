<?php
$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');
$result = $pdo->query("SHOW TABLES");
$tables = $result->fetchAll(PDO::FETCH_COLUMN);

echo "TechTrack Database Tables:\n";
echo "==========================\n";
foreach ($tables as $table) {
    if (in_array($table, ['cart', 'wishlist', 'customer_addresses'])) {
        echo "✓ $table (NEW)\n";
    } else {
        echo "  $table\n";
    }
}

// Check cart table structure
echo "\n\nCart Table Structure:\n";
echo "====================\n";
$result = $pdo->query("DESCRIBE cart");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
}

// Check wishlist table structure
echo "\n\nWishlist Table Structure:\n";
echo "========================\n";
$result = $pdo->query("DESCRIBE wishlist");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
}

// Check customer_addresses table structure
echo "\n\nCustomer Addresses Table Structure:\n";
echo "===================================\n";
$result = $pdo->query("DESCRIBE customer_addresses");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
}
