<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * QR Code Generator Controller
 * Generates QR codes using a simple PHP implementation
 */
class Qrcode extends Controller
{
    public function generate()
    {
        // Get parameters from URL
        $data = isset($_GET['data']) ? $_GET['data'] : '';
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 200;
        
        if (empty($data)) {
            // Generate error image instead of text
            $this->generateErrorImage($size);
            exit;
        }
        
        // Generate QR code using API.qrserver.com (free alternative)
        $url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data);
        
        // Try to fetch and serve the image
        $imageData = @file_get_contents($url);
        
        if ($imageData !== false) {
            header('Content-Type: image/png');
            header('Cache-Control: public, max-age=86400'); // Cache for 1 day
            echo $imageData;
        } else {
            // Fallback: Generate a simple QR-like placeholder
            $this->generatePlaceholder($size, $data);
        }
        exit;
    }
    
    /**
     * Generate a simple placeholder image if API fails
     */
    private function generatePlaceholder($size, $data)
    {
        $img = imagecreate($size, $size);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        $gray = imagecolorallocate($img, 200, 200, 200);
        
        // Fill background
        imagefill($img, 0, 0, $white);
        
        // Draw border
        imagerectangle($img, 0, 0, $size-1, $size-1, $black);
        
        // Draw grid pattern (simple QR-like appearance)
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
    private function generateErrorImage($size)
    {
        $img = imagecreate($size, $size);
        $white = imagecolorallocate($img, 255, 255, 255);
        $red = imagecolorallocate($img, 239, 68, 68);
        
        imagefill($img, 0, 0, $white);
        imagerectangle($img, 0, 0, $size-1, $size-1, $red);
        
        header('Content-Type: image/png');
        imagepng($img);
        imagedestroy($img);
    }
}
