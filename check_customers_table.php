<?php
// Check customers table structure
$mysqli = new mysqli('localhost', 'root', '', 'techtrack_db');
$result = $mysqli->query('DESCRIBE customers');
echo "Customers table columns:\n";
while ($row = $result->fetch_assoc()) {
    echo "- {$row['Field']} ({$row['Type']})\n";
}
$mysqli->close();
?>
