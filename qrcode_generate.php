<?php
/**
 * QR Code Generator Endpoint
 * Generates QR codes using api.qrserver.com
 */

// Get parameters
$data = isset($_GET['data']) ? $_GET['data'] : '';
$size = isset($_GET['size']) ? (int)$_GET['size'] : 200;

// Validate
if (empty($data)) {
    generateErrorImage($size);
    exit;
}

// Generate QR code using API.qrserver.com
$url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data);

// Try to fetch and serve the image
$imageData = @file_get_contents($url);

if ($imageData !== false) {
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=86400');
    echo $imageData;
} else {
    // Fallback: Generate a placeholder
    generatePlaceholder($size, $data);
}

/**
 * Generate placeholder image if API fails
 */
function generatePlaceholder($size, $data) {
    $img = imagecreate($size, $size);
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    
    imagefill($img, 0, 0, $white);
    imagerectangle($img, 0, 0, $size-1, $size-1, $black);
    
    // Draw simple grid pattern
    $gridSize = 10;
    $hash = md5($data);
    for ($x = 0; $x < $size; $x += $gridSize) {
        for ($y = 0; $y < $size; $y += $gridSize) {
            $index = (($x / $gridSize) + ($y / $gridSize)) % 32;
            if ($hash[$index] > '7') {
                imagefilledrectangle($img, $x, $y, $x + $gridSize - 1, $y + $gridSize - 1, $black);
            }
        }
    }
    
    header('Content-Type: image/png');
    imagepng($img);
    imagedestroy($img);
}

/**
 * Generate error image
 */
function generateErrorImage($size) {
    $img = imagecreate($size, $size);
    $white = imagecolorallocate($img, 255, 255, 255);
    $red = imagecolorallocate($img, 239, 68, 68);
    
    imagefill($img, 0, 0, $white);
    imagerectangle($img, 0, 0, $size-1, $size-1, $red);
    
    header('Content-Type: image/png');
    imagepng($img);
    imagedestroy($img);
}
