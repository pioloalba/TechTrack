<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * QR Code Helper
 * 
 * Provides QR code generation functionality for the TechTrack system
 * Uses Google Charts API for QR code generation
 */

if (!function_exists('generate_qr_code')) {
    /**
     * Generate QR code data URL using a PHP QR code endpoint
     * 
     * @param string $data The data to encode in the QR code
     * @param int $size Size of the QR code in pixels (default: 200)
     * @return string URL to the QR code generator endpoint
     */
    function generate_qr_code($data, $size = 200) {
        $encodedData = urlencode($data);
        return site_url("qrcode_generate.php?data={$encodedData}&size={$size}");
    }
}

if (!function_exists('generate_qr_code_base64')) {
    /**
     * Generate QR code as base64 encoded PNG (for PDF embedding)
     * 
     * @param string $data The data to encode in the QR code
     * @param int $size Size of the QR code in pixels (default: 200)
     * @return string Base64 encoded PNG image
     */
    function generate_qr_code_base64($data, $size = 200) {
        // Use API.qrserver.com directly for base64 encoding
        $url = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data);
        
        try {
            // Fetch the image from QR server API
            $imageData = @file_get_contents($url);
            
            if ($imageData !== false) {
                return 'data:image/png;base64,' . base64_encode($imageData);
            }
        } catch (Exception $e) {
            // Fallback: return a small placeholder
        }
        
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    }
}

if (!function_exists('save_qr_code')) {
    /**
     * Generate and save QR code as PNG file
     * 
     * @param string $data The data to encode in the QR code
     * @param string $filepath Path where to save the PNG file
     * @param int $size Size of the QR code in pixels (default: 200)
     * @return bool Success status
     */
    function save_qr_code($data, $filepath, $size = 200) {
        $url = generate_qr_code($data, $size);
        
        try {
            $imageData = @file_get_contents($url);
            
            if ($imageData !== false) {
                // Ensure directory exists
                $dir = dirname($filepath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                
                return file_put_contents($filepath, $imageData) !== false;
            }
        } catch (Exception $e) {
            return false;
        }
        
        return false;
    }
}

if (!function_exists('qr_code_img_tag')) {
    /**
     * Generate HTML img tag for QR code
     * 
     * @param string $data The data to encode in the QR code
     * @param int $size Size of the QR code in pixels (default: 200)
     * @param string $alt Alt text for the image
     * @param array $attributes Additional HTML attributes
     * @return string HTML img tag
     */
    function qr_code_img_tag($data, $size = 200, $alt = 'QR Code', $attributes = []) {
        $url = generate_qr_code($data, $size);
        
        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '" width="' . $size . '" height="' . $size . '"' . $attrString . '>';
    }
}

if (!function_exists('get_network_site_url')) {
    /**
     * Get site URL accessible from local network (not localhost)
     * 
     * @param string $path Path to append
     * @return string Full URL using server's IP address
     */
    function get_network_site_url($path = '') {
        // Get the server name/IP from the request
        $host = $_SERVER['HTTP_HOST'];
        
        // If it's localhost, try to get the actual IP address
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
            // Get server's local IP address
            $localIP = gethostbyname(gethostname());
            if ($localIP && $localIP !== gethostname()) {
                $host = str_replace(['localhost', '127.0.0.1'], $localIP, $host);
            }
        }
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $baseUrl = $protocol . '://' . $host . str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('product_qr_code')) {
    /**
     * Generate QR code for a product
     * 
     * @param int $productId Product ID
     * @param int $size Size of the QR code (default: 200)
     * @return string QR code URL
     */
    function product_qr_code($productId, $size = 200) {
        $url = get_network_site_url('product/' . $productId);
        return generate_qr_code($url, $size);
    }
}

if (!function_exists('order_tracking_qr_code')) {
    /**
     * Generate QR code for order tracking
     * 
     * @param int $orderId Order ID
     * @param int $size Size of the QR code (default: 200)
     * @return string QR code URL
     */
    function order_tracking_qr_code($orderId, $size = 200) {
        $url = get_network_site_url('track-order/' . $orderId);
        return generate_qr_code($url, $size);
    }
}

if (!function_exists('admin_dashboard_qr_code')) {
    /**
     * Generate QR code for admin dashboard
     * 
     * @param int $size Size of the QR code (default: 200)
     * @return string QR code URL
     */
    function admin_dashboard_qr_code($size = 200) {
        $url = get_network_site_url('admin/dashboard');
        return generate_qr_code($url, $size);
    }
}
