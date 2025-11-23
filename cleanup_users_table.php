<?php
$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');
$result = $pdo->exec("DELETE FROM users WHERE role = 'Customer'");
echo "✓ Deleted $result customer(s) from users table.\n";
echo "✓ Users table is now clean - only Admin/Manager/Cashier remain.\n";
