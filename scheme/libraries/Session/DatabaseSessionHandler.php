<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Database Session Handler for LavaLust
 * Stores sessions in database instead of files
 * Required for cloud deployments (Render, Heroku, etc.)
 */
class DatabaseSessionHandler implements SessionHandlerInterface
{
    private $db;
    private $table = 'sessions';
    private $lifetime;

    public function __construct()
    {
        $lava =& lava_instance();
        $this->db = $lava->db;
        $this->lifetime = ini_get('session.gc_maxlifetime');
        
        // Ensure sessions table exists
        $this->createTableIfNotExists();
    }

    /**
     * Create sessions table if it doesn't exist
     */
    private function createTableIfNotExists()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
                id VARCHAR(128) NOT NULL PRIMARY KEY,
                ip_address VARCHAR(45) NOT NULL,
                timestamp INT(10) UNSIGNED NOT NULL DEFAULT 0,
                data TEXT NOT NULL,
                INDEX idx_timestamp (timestamp)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->db->raw($sql);
        } catch (Exception $e) {
            error_log('Failed to create sessions table: ' . $e->getMessage());
        }
    }

    /**
     * Open session
     */
    public function open($save_path, $session_name): bool
    {
        return true;
    }

    /**
     * Close session
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * Read session data
     */
    public function read($session_id): string
    {
        try {
            $sql = "SELECT data FROM {$this->table} WHERE id = ? LIMIT 1";
            $result = $this->db->raw($sql, [$session_id])->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['data'];
            }
            
            return '';
        } catch (Exception $e) {
            error_log('Session read error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Write session data
     */
    public function write($session_id, $session_data): bool
    {
        try {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $timestamp = time();
            
            $sql = "INSERT INTO {$this->table} (id, ip_address, timestamp, data) 
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    ip_address = VALUES(ip_address),
                    timestamp = VALUES(timestamp),
                    data = VALUES(data)";
            
            $this->db->raw($sql, [$session_id, $ip_address, $timestamp, $session_data]);
            
            return true;
        } catch (Exception $e) {
            error_log('Session write error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Destroy session
     */
    public function destroy($session_id): bool
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $this->db->raw($sql, [$session_id]);
            return true;
        } catch (Exception $e) {
            error_log('Session destroy error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Garbage collection - remove old sessions
     */
    public function gc($maxlifetime): int
    {
        try {
            $expired = time() - $maxlifetime;
            $sql = "DELETE FROM {$this->table} WHERE timestamp < ?";
            $stmt = $this->db->raw($sql, [$expired]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            error_log('Session GC error: ' . $e->getMessage());
            return 0;
        }
    }
}
