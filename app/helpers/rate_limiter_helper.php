<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Rate Limiting Helper
 * 
 * Provides rate limiting functionality for preventing brute force attacks
 */
class RateLimiter
{
    private $session;
    private $maxAttempts;
    private $decayMinutes;
    
    public function __construct($session, $maxAttempts = 5, $decayMinutes = 15)
    {
        $this->session = $session;
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }
    
    /**
     * Get unique key for rate limiting
     */
    private function getKey($identifier, $action = 'login')
    {
        return 'rate_limit_' . $action . '_' . md5($identifier);
    }
    
    /**
     * Record an attempt
     */
    public function hit($identifier, $action = 'login')
    {
        $key = $this->getKey($identifier, $action);
        $attempts = $this->session->userdata($key) ?? [];
        
        // Clean old attempts
        $cutoff = time() - ($this->decayMinutes * 60);
        $attempts = array_filter($attempts, function($timestamp) use ($cutoff) {
            return $timestamp > $cutoff;
        });
        
        // Add new attempt
        $attempts[] = time();
        
        $this->session->set_userdata($key, $attempts);
        
        return count($attempts);
    }
    
    /**
     * Check if too many attempts
     */
    public function tooManyAttempts($identifier, $action = 'login')
    {
        $key = $this->getKey($identifier, $action);
        $attempts = $this->session->userdata($key) ?? [];
        
        // Clean old attempts
        $cutoff = time() - ($this->decayMinutes * 60);
        $attempts = array_filter($attempts, function($timestamp) use ($cutoff) {
            return $timestamp > $cutoff;
        });
        
        return count($attempts) >= $this->maxAttempts;
    }
    
    /**
     * Get remaining attempts
     */
    public function retriesLeft($identifier, $action = 'login')
    {
        $key = $this->getKey($identifier, $action);
        $attempts = $this->session->userdata($key) ?? [];
        
        // Clean old attempts
        $cutoff = time() - ($this->decayMinutes * 60);
        $attempts = array_filter($attempts, function($timestamp) use ($cutoff) {
            return $timestamp > $cutoff;
        });
        
        return max(0, $this->maxAttempts - count($attempts));
    }
    
    /**
     * Get time until retry is available (in seconds)
     */
    public function availableIn($identifier, $action = 'login')
    {
        $key = $this->getKey($identifier, $action);
        $attempts = $this->session->userdata($key) ?? [];
        
        if (empty($attempts)) {
            return 0;
        }
        
        $oldestAttempt = min($attempts);
        $availableAt = $oldestAttempt + ($this->decayMinutes * 60);
        
        return max(0, $availableAt - time());
    }
    
    /**
     * Clear all attempts
     */
    public function clear($identifier, $action = 'login')
    {
        $key = $this->getKey($identifier, $action);
        $this->session->unset_userdata($key);
    }
    
    /**
     * Get formatted message for rate limit error
     */
    public function getLimitMessage($identifier, $action = 'login')
    {
        $seconds = $this->availableIn($identifier, $action);
        $minutes = ceil($seconds / 60);
        
        if ($minutes > 1) {
            return "Too many login attempts. Please try again in {$minutes} minutes.";
        } else {
            return "Too many login attempts. Please try again in 1 minute.";
        }
    }
}
