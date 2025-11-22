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
        // product with low stock
        $lowStockQuery = $this->db->raw("
            SELECT * FROM products 
            WHERE stock > 0 AND stock <= 10
            ORDER BY stock ASC
        ");
        $data['low_stock_items'] = $lowStockQuery->fetchAll(PDO::FETCH_ASSOC);
        
        // critical stock products (stock <= 5)
        $criticalStockQuery = $this->db->raw("
            SELECT * FROM products 
            WHERE stock > 0 AND stock <= 5
            ORDER BY stock ASC
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
}

?>
