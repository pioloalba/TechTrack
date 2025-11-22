<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Ensure AdminBase is loaded for inheritance
require_once APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . 'AdminBase.php';

class AdminInventory extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('session');
        $this->call->model(['ProductModel','InventoryTransactionModel']);
    }

    public function index()
    {
        // Get all products with stock info
        $data['products'] = $this->ProductModel->all();
        
        // Attach main image for each product
        $productImages = [];
        foreach ($data['products'] as $p) {
            // Just get the first image for each product
            $imageStmt = $this->db->raw("SELECT image_url FROM product_images WHERE product_id = ? LIMIT 1", [(int)$p['id']]);
            $image = $imageStmt->fetch(PDO::FETCH_ASSOC);
            $productImages[$p['id']] = $image['image_url'] ?? null;
        }
        $data['product_images'] = $productImages;
        
        // Flash messages
        $data['flash_success'] = $this->session->flashdata('success');
        $data['flash_error'] = $this->session->flashdata('error');
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/inventory/index', $data);
    }
    
    public function adjust()
    {
        // Handle stock adjustment via AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            try {
                $productId = (int)($_POST['product_id'] ?? 0);
                $quantity = (int)($_POST['quantity'] ?? 0);
                $type = $_POST['type'] ?? 'add'; // add or remove
                $reason = $_POST['reason'] ?? '';
                $notes = $_POST['notes'] ?? '';
                
                if ($productId <= 0 || $quantity <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
                    exit;
                }
                
                // Get current product
                $product = $this->ProductModel->find($productId);
                if (!$product) {
                    echo json_encode(['success' => false, 'message' => 'Product not found.']);
                    exit;
                }
                
                $currentStock = (int)($product['stock'] ?? 0);
                $newStock = $type === 'add' ? $currentStock + $quantity : $currentStock - $quantity;
                $newStock = max(0, $newStock); // Can't go below 0
                
                // Update product stock
                $this->ProductModel->update($productId, ['stock' => $newStock]);
                
                // Log transaction
                $this->db->table('inventory_transactions')->insert([
                    'product_id' => $productId,
                    'type' => $type,
                    'transaction_type' => $type,
                    'quantity' => $quantity,
                    'reason' => $reason ?: ($type === 'add' ? 'restock' : 'adjustment'),
                    'notes' => $notes,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Stock adjusted successfully.',
                    'new_stock' => $newStock
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
        
        // Traditional form submission
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        $type = $_POST['type'] ?? 'add';
        
        if ($productId > 0 && $quantity > 0) {
            $product = $this->ProductModel->find($productId);
            if ($product) {
                $currentStock = (int)($product['stock'] ?? 0);
                $newStock = $type === 'add' ? $currentStock + $quantity : $currentStock - $quantity;
                $newStock = max(0, $newStock);
                
                $this->ProductModel->update($productId, ['stock' => $newStock]);
                
                $this->db->table('inventory_transactions')->insert([
                    'product_id' => $productId,
                    'type' => $type,
                    'transaction_type' => $type,
                    'quantity' => $quantity,
                    'reason' => $_POST['reason'] ?? ($type === 'add' ? 'restock' : 'adjustment'),
                    'notes' => $_POST['notes'] ?? '',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                
                $this->session->set_flashdata('success', 'Stock adjusted successfully.');
            }
        }
        
        redirect('admin/inventory');
    }
}

?>
