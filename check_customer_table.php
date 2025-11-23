<?php
$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');

echo "CUSTOMERS TABLE STRUCTURE:\n";
echo "==========================\n";
$result = $pdo->query("DESCRIBE customers");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $default = $row['Default'] ?? 'NULL';
    echo sprintf("%-20s %-20s %-10s %-10s %s\n", 
        $row['Field'], 
        $row['Type'], 
        $row['Null'], 
        $row['Key'],
        $default
    );
}

echo "\n\nUSERS TABLE STRUCTURE:\n";
echo "======================\n";
$result = $pdo->query("DESCRIBE users");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $default = $row['Default'] ?? 'NULL';
    echo sprintf("%-20s %-20s %-10s %-10s %s\n", 
        $row['Field'], 
        $row['Type'], 
        $row['Null'], 
        $row['Key'],
        $default
    );
}
