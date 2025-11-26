<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// for admin inheritance
require_once APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . 'AdminBase.php';

class AdminAlerts extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
        $this->call->model(['ProductModel']);
    }

    public function index()
    {
        // Create dismissed_alerts table if it doesn't exist
        try {
            $this->db->raw("
                CREATE TABLE IF NOT EXISTS dismissed_alerts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    product_id INT NOT NULL,
                    dismissed_by INT NULL,
                    dismissed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_product (product_id),
                    INDEX idx_dismissed_at (dismissed_at)
                ) ENGINE=InnoDB
            ");
        } catch (Exception $e) {
            // Table might already exist
        }
        
        // product with low stock (excluding dismissed alerts from last 24 hours)
        $lowStockQuery = $this->db->raw("
            SELECT p.* FROM products p
            LEFT JOIN dismissed_alerts da ON p.id = da.product_id 
                AND da.dismissed_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
            WHERE p.stock > 0 AND p.stock <= 10
                AND da.id IS NULL
            ORDER BY p.stock ASC
        ");
        $data['low_stock_items'] = $lowStockQuery->fetchAll(PDO::FETCH_ASSOC);
        
        // critical stock products (stock <= 5, excluding dismissed)
        $criticalStockQuery = $this->db->raw("
            SELECT p.* FROM products p
            LEFT JOIN dismissed_alerts da ON p.id = da.product_id 
                AND da.dismissed_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
            WHERE p.stock > 0 AND p.stock <= 5
                AND da.id IS NULL
            ORDER BY p.stock ASC
        ");
        $data['critical_items'] = $criticalStockQuery->fetchAll(PDO::FETCH_ASSOC);
        
        // total count alerts
        $data['critical_count'] = count($data['critical_items']);
        $data['low_stock_count'] = count($data['low_stock_items']) - $data['critical_count'];
        $data['total_alerts'] = count($data['low_stock_items']);
        
        $this->call->library('session');
        $data['success'] = $this->session->flashdata('success');
        $data['error'] = $this->session->flashdata('error');
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/alerts/index', $data);
    }

    public function getNotifications()
    {
        header('Content-Type: application/json');
        
        try {
            // Get critical stock items (<=5)
            $criticalQuery = $this->db->raw("
                SELECT id, name, stock 
                FROM products 
                WHERE stock > 0 AND stock <= 5
                ORDER BY stock ASC
                LIMIT 3
            ");
            $criticalItems = $criticalQuery->fetchAll(PDO::FETCH_ASSOC);
            
            // Get low stock items (6-10)
            $lowStockQuery = $this->db->raw("
                SELECT id, name, stock 
                FROM products 
                WHERE stock > 5 AND stock <= 10
                ORDER BY stock ASC
                LIMIT 3
            ");
            $lowStockItems = $lowStockQuery->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count for badge
            $totalQuery = $this->db->raw("
                SELECT COUNT(*) as count 
                FROM products 
                WHERE stock > 0 AND stock <= 10
            ");
            $totalRow = $totalQuery->fetch(PDO::FETCH_ASSOC);
            $totalCount = (int)$totalRow['count'];
            
            $notifications = [];
            
            // Add critical items
            foreach ($criticalItems as $item) {
                $notifications[] = [
                    'id' => $item['id'],
                    'type' => 'critical',
                    'title' => 'Critical Stock Alert',
                    'message' => $item['name'] . ' - Only ' . $item['stock'] . ' units left!',
                    'product_id' => $item['id']
                ];
            }
            
            // Add low stock items
            foreach ($lowStockItems as $item) {
                $notifications[] = [
                    'id' => $item['id'],
                    'type' => 'warning',
                    'title' => 'Low Stock Warning',
                    'message' => $item['name'] . ' - ' . $item['stock'] . ' units remaining',
                    'product_id' => $item['id']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'count' => $totalCount,
                'notifications' => $notifications
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Dismiss an alert (mark as acknowledged)
     */
    public function dismiss($product_id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        try {
            $product_id = (int)$product_id;
            
            if ($product_id <= 0) {
                throw new Exception('Invalid product ID');
            }
            
            // Check if product exists
            $checkQuery = $this->db->raw("SELECT id, name FROM products WHERE id = ?", [$product_id]);
            $product = $checkQuery->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                throw new Exception('Product not found');
            }
            
            // Create dismissed_alerts table if it doesn't exist
            $this->db->raw("
                CREATE TABLE IF NOT EXISTS dismissed_alerts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    product_id INT NOT NULL,
                    dismissed_by INT NULL,
                    dismissed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_product (product_id),
                    INDEX idx_dismissed_at (dismissed_at)
                ) ENGINE=InnoDB
            ");
            
            // Insert dismissed alert record
            $this->db->raw("
                INSERT INTO dismissed_alerts (product_id, dismissed_by) 
                VALUES (?, ?)
            ", [$product_id, $this->session->userdata('user')['id'] ?? null]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Alert dismissed successfully'
            ]);
            
        } catch (Exception $e) {
            error_log('Dismiss alert error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}

?>
