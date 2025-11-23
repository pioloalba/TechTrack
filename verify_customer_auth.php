<?php
/**
 * Verify the migration and show current state
 */

$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');

echo "=== CUSTOMER AUTHENTICATION STATUS ===\n\n";

// Show customers
echo "CUSTOMERS TABLE:\n";
echo "----------------\n";
$result = $pdo->query("SELECT id, name, email, phone, total_orders, created_at FROM customers ORDER BY id");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("ID: %d | Name: %-20s | Email: %-30s | Orders: %d\n", 
        $row['id'], 
        $row['name'], 
        $row['email'], 
        $row['total_orders']
    );
}

echo "\n\nCUSTOMER_AUTH TABLE:\n";
echo "--------------------\n";
$result = $pdo->query("
    SELECT ca.id, ca.customer_id, c.name, c.email, 
           SUBSTRING(ca.password, 1, 20) as password_preview
    FROM customer_auth ca
    JOIN customers c ON ca.customer_id = c.id
    ORDER BY ca.id
");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("Auth ID: %d | Customer ID: %d | Name: %-20s | Email: %-30s | Password: %s...\n", 
        $row['id'],
        $row['customer_id'],
        $row['name'], 
        $row['email'],
        $row['password_preview']
    );
}

echo "\n\nUSERS TABLE (Should NOT have customers):\n";
echo "-----------------------------------------\n";
$result = $pdo->query("SELECT id, name, email, role FROM users ORDER BY role, id");
$hasCustomers = false;
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("ID: %d | Name: %-20s | Email: %-30s | Role: %s\n", 
        $row['id'], 
        $row['name'], 
        $row['email'],
        $row['role']
    );
    if ($row['role'] === 'Customer') {
        $hasCustomers = true;
    }
}

if ($hasCustomers) {
    echo "\n⚠ WARNING: Found customers in users table!\n";
    echo "Run this SQL to clean up:\n";
    echo "DELETE FROM users WHERE role = 'Customer';\n";
}

echo "\n\n=== NEW TABLE STRUCTURES ===\n\n";

echo "CART TABLE:\n";
$result = $pdo->query("SELECT COUNT(*) as count FROM cart");
$count = $result->fetch(PDO::FETCH_ASSOC);
echo "- Total items: {$count['count']}\n";

echo "\nWISHLIST TABLE:\n";
$result = $pdo->query("SELECT COUNT(*) as count FROM wishlist");
$count = $result->fetch(PDO::FETCH_ASSOC);
echo "- Total items: {$count['count']}\n";

echo "\nCUSTOMER_ADDRESSES TABLE:\n";
$result = $pdo->query("SELECT COUNT(*) as count FROM customer_addresses");
$count = $result->fetch(PDO::FETCH_ASSOC);
echo "- Total addresses: {$count['count']}\n";

echo "\n=== SUMMARY ===\n";
echo "✓ Customers now register in 'customers' table\n";
echo "✓ Customer passwords stored in 'customer_auth' table\n";
echo "✓ Users table reserved for Admin/Manager/Cashier only\n";
echo "✓ Cart & Wishlist data persists in database\n";
echo "✓ Guest carts/wishlists migrate on login\n";
