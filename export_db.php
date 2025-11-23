<?php
/**
 * Database Export via PHP
 */

echo "================================================\n";
echo "   TechTrack Database Export\n";
echo "================================================\n\n";

$outputFile = 'sql/techtrack_db_complete.sql';

// Try different mysqldump paths
$possiblePaths = [
    'C:/wamp64/bin/mysql/mysql8.3.0/bin/mysqldump.exe',
    'C:/wamp64/bin/mysql/mysql8.0.31/bin/mysqldump.exe',
    'C:/xampp/mysql/bin/mysqldump.exe',
    'mysqldump' // System PATH
];

$mysqldump = null;
foreach ($possiblePaths as $path) {
    if (file_exists($path) || $path === 'mysqldump') {
        $mysqldump = $path;
        break;
    }
}

if (!$mysqldump) {
    echo "✗ mysqldump not found\n\n";
    echo "Alternative method - Use phpMyAdmin:\n";
    echo "1. Open: http://localhost/phpmyadmin\n";
    echo "2. Select 'techtrack_db'\n";
    echo "3. Click 'Export' tab\n";
    echo "4. Click 'Go'\n";
    echo "5. Save to: sql/techtrack_db_complete.sql\n";
    exit(1);
}

echo "Exporting database...\n";
$command = "\"{$mysqldump}\" -uroot techtrack_db > {$outputFile} 2>&1";
exec($command, $output, $returnCode);

if ($returnCode === 0 && file_exists($outputFile)) {
    $size = filesize($outputFile);
    echo "\n================================================\n";
    echo "   ✓ SUCCESS!\n";
    echo "================================================\n\n";
    echo "File: {$outputFile}\n";
    echo "Size: " . round($size / 1024, 2) . " KB\n\n";
    echo "Share this with your groupmate!\n";
    echo "They can import with:\n";
    echo "  mysql -uroot techtrack_db < sql/techtrack_db_complete.sql\n";
} else {
    echo "\n✗ Export failed\n";
    echo "Error output:\n";
    print_r($output);
}
