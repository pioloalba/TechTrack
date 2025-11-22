<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * CSRF Protection Helper
 * 
 * Provides CSRF token generation and validation
 */
class CSRF
{
    private $session;
    private $tokenName = 'csrf_token';
    private $cookieName = 'csrf_cookie';
    
    public function __construct($session)
    {
        $this->session = $session;
    }
    
    /**
     * Generate a new CSRF token
     */
    public function generateToken()
    {
        $token = bin2hex(random_bytes(32));
        $this->session->set_userdata($this->tokenName, $token);
        
        // Also set as cookie for AJAX requests
        setcookie($this->cookieName, $token, [
            'expires' => time() + 7200,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        return $token;
    }
    
    /**
     * Get current CSRF token (generate if not exists)
     */
    public function getToken()
    {
        $token = $this->session->userdata($this->tokenName);
        if (!$token) {
            $token = $this->generateToken();
        }
        return $token;
    }
    
    /**
     * Validate CSRF token from request
     */
    public function validateToken($token = null)
    {
        // Get token from POST, GET, or headers
        if ($token === null) {
            $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }
        
        $sessionToken = $this->session->userdata($this->tokenName);
        
        if (empty($token) || empty($sessionToken)) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }
    
    /**
     * Generate HTML input field for forms
     */
    public function getInputField()
    {
        $token = $this->getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Get meta tag for AJAX requests
     */
    public function getMetaTag()
    {
        $token = $this->getToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
