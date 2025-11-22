<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Ensure AdminBase is loaded for inheritance
require_once APP_DIR . 'controllers' . DIRECTORY_SEPARATOR . 'AdminBase.php';

class AdminPOS extends AdminBase
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library(['session']);
        $this->call->model(['ProductModel','OrderModel']);
    }

    public function index()
    {
        $data['products'] = $this->ProductModel->all();
        
        // Attach main image for each product
        $productImages = [];
        foreach ($data['products'] as $p) {
            $imageStmt = $this->db->raw("SELECT image_url FROM product_images WHERE product_id = ? LIMIT 1", [(int)$p['id']]);
            $image = $imageStmt->fetch(PDO::FETCH_ASSOC);
            $productImages[$p['id']] = $image['image_url'] ?? null;
        }
        $data['product_images'] = $productImages;
        
        $this->call->view('admin/layouts/header', $data);
        $this->call->view('admin/layouts/sidebar', $data);
        $this->call->view('admin/layouts/topbar', $data);
        $this->call->view('admin/pos/index', $data);
    }
}

?>
