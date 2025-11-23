<?php
$pdo = new PDO('mysql:host=localhost;dbname=techtrack_db', 'root', '');

$result = $pdo->query("SHOW CREATE TABLE customers");
$row = $result->fetch(PDO::FETCH_ASSOC);
echo "CUSTOMERS TABLE CREATE STATEMENT:\n";
echo "==================================\n";
echo $row['Create Table'] . "\n";
