<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Simple Cache Helper
 * 
 * Provides basic caching functionality using files
 */
class SimpleCache
{
    private $cacheDir;
    private $defaultTTL = 3600; // 1 hour
    
    public function __construct($cacheDir = null)
    {
        $this->cacheDir = $cacheDir ?? (ROOT_DIR . 'runtime/cache/');
        
        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Get cache file path for a key
     */
    private function getFilePath($key)
    {
        $hash = md5($key);
        return $this->cacheDir . $hash . '.cache';
    }
    
    /**
     * Store data in cache
     */
    public function set($key, $data, $ttl = null)
    {
        $ttl = $ttl ?? $this->defaultTTL;
        $filePath = $this->getFilePath($key);
        
        $cacheData = [
            'expires' => time() + $ttl,
            'data' => $data
        ];
        
        $encoded = serialize($cacheData);
        return file_put_contents($filePath, $encoded, LOCK_EX) !== false;
    }
    
    /**
     * Retrieve data from cache
     */
    public function get($key, $default = null)
    {
        $filePath = $this->getFilePath($key);
        
        if (!file_exists($filePath)) {
            return $default;
        }
        
        $contents = file_get_contents($filePath);
        if ($contents === false) {
            return $default;
        }
        
        $cacheData = @unserialize($contents);
        if ($cacheData === false) {
            return $default;
        }
        
        // Check if expired
        if (time() > $cacheData['expires']) {
            $this->delete($key);
            return $default;
        }
        
        return $cacheData['data'];
    }
    
    /**
     * Check if cache exists and is valid
     */
    public function has($key)
    {
        $filePath = $this->getFilePath($key);
        
        if (!file_exists($filePath)) {
            return false;
        }
        
        $contents = file_get_contents($filePath);
        $cacheData = @unserialize($contents);
        
        if ($cacheData === false || time() > $cacheData['expires']) {
            $this->delete($key);
            return false;
        }
        
        return true;
    }
    
    /**
     * Delete cache entry
     */
    public function delete($key)
    {
        $filePath = $this->getFilePath($key);
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return true;
    }
    
    /**
     * Clear all cache
     */
    public function clear()
    {
        $files = glob($this->cacheDir . '*.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }
    
    /**
     * Remember: Get from cache or execute callback and cache result
     */
    public function remember($key, $callback, $ttl = null)
    {
        $data = $this->get($key);
        
        if ($data !== null) {
            return $data;
        }
        
        $data = $callback();
        $this->set($key, $data, $ttl);
        
        return $data;
    }
    
    /**
     * Clean expired cache entries
     */
    public function cleanExpired()
    {
        $files = glob($this->cacheDir . '*.cache');
        $cleaned = 0;
        
        foreach ($files as $file) {
            if (!is_file($file)) continue;
            
            $contents = file_get_contents($file);
            $cacheData = @unserialize($contents);
            
            if ($cacheData === false || time() > $cacheData['expires']) {
                unlink($file);
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
}
