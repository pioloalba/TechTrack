<?php
// Add google_id column to customers table

$mysqli = new mysqli('localhost', 'root', '', 'techtrack_db');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$sql = "ALTER TABLE customers ADD COLUMN google_id VARCHAR(255) NULL UNIQUE AFTER email";

if ($mysqli->query($sql)) {
    echo "SUCCESS: google_id column added to customers table\n";
} else {
    if (strpos($mysqli->error, 'Duplicate column') !== false) {
        echo "INFO: google_id column already exists\n";
    } else {
        echo "ERROR: " . $mysqli->error . "\n";
    }
}

$mysqli->close();
?>
