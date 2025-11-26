<?php
/**
 * Fix Google OAuth SSL Certificate Issue
 * Download and configure cacert.pem for WAMP
 */

echo "Downloading cacert.pem certificate bundle...\n";

// Download the certificate bundle
$certUrl = 'https://curl.se/ca/cacert.pem';
$certPath = 'C:/wamp64/bin/php/cacert.pem';

// Try to download
$certContent = @file_get_contents($certUrl);

if ($certContent === false) {
    echo "ERROR: Could not download certificate bundle.\n";
    echo "Please manually download from: $certUrl\n";
    echo "And save it to: $certPath\n";
    exit(1);
}

// Save the certificate
if (file_put_contents($certPath, $certContent)) {
    echo "SUCCESS: Certificate bundle saved to: $certPath\n\n";
    
    // Find php.ini
    $phpIni = php_ini_loaded_file();
    echo "Your php.ini file is located at: $phpIni\n\n";
    
    echo "NEXT STEPS:\n";
    echo "1. Open php.ini in a text editor\n";
    echo "2. Find the line: ;curl.cainfo =\n";
    echo "3. Change it to: curl.cainfo = \"C:/wamp64/bin/php/cacert.pem\"\n";
    echo "4. Save the file\n";
    echo "5. Restart WAMP\n";
    echo "6. Try Google login again\n";
} else {
    echo "ERROR: Could not save certificate bundle to: $certPath\n";
    echo "Please check folder permissions.\n";
}
?>
